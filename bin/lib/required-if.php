<?php

/**
 * `x-sdk-required-if` (conditionally required fields, bizgo-api-spec AGENTS.md rule 11, SDK-DESIGN.md §4):
 * parsing and checking of the rules, and src/Internal/RequiredIfRules.php, the table that
 * src/Internal/RequiredIf.php evaluates in the request model constructors.
 */

declare(strict_types=1);

const REQUIRED_IF_OPERATORS = ['equals', 'notEquals', 'in', 'notIn'];

final class RequiredIfRule
{
    public string $field = '';
    /** equals|notEquals|in|notIn */
    public string $op = '';
    /** @var list<string> */
    public array $values = [];
    /** @var list<string> own properties */
    public array $required = [];
    /** @var list<string> paths relative to the object */
    public array $paths = [];
    /** @var list<string> paths from the request body root, without the `$.` */
    public array $rootPaths = [];
}

/**
 * The rules of a schema (and of its allOf members), unchecked.
 *
 * @param array<string, mixed> $schema
 * @param array<string, mixed> $schemas
 *
 * @return list<mixed>
 */
function rawRequiredIf(array $schema, array $schemas, string $where): array
{
    if (isset($schema['$ref'])) {
        $name = refName(str($schema['$ref'], $where));

        return rawRequiredIf(map($schemas[$name] ?? null, $name), $schemas, $name);
    }
    $rules = [];
    foreach (isset($schema['allOf']) ? items($schema['allOf'], "{$where}.allOf") : [] as $part) {
        $rules = array_merge($rules, rawRequiredIf(map($part, "{$where}.allOf"), $schemas, $where));
    }
    if (isset($schema['x-sdk-required-if'])) {
        $rules = array_merge($rules, items($schema['x-sdk-required-if'], "{$where}.x-sdk-required-if"));
    }

    return $rules;
}

/**
 * Parses and checks the rules of a model: `when.field`, `required` and the first segment of a relative path must
 * be properties of the model; exactly one operator; at least one of `required`/`requiredPaths`.
 *
 * @param list<mixed> $raw
 * @param list<string> $properties
 *
 * @return list<RequiredIfRule>
 */
function parseRequiredIf(array $raw, array $properties, string $where): array
{
    $rules = [];
    foreach ($raw as $i => $item) {
        $at = "{$where}.x-sdk-required-if[{$i}]";
        $item = map($item, $at);
        foreach (array_keys($item) as $key) {
            if (!in_array($key, ['when', 'required', 'requiredPaths'], true)) {
                throw new RuntimeException("{$at}: unsupported key '{$key}'");
            }
        }
        $when = map($item['when'] ?? null, "{$at}.when");
        $rule = new RequiredIfRule();
        $rule->field = str($when['field'] ?? null, "{$at}.when.field");
        if (!in_array($rule->field, $properties, true)) {
            throw new RuntimeException("{$at}.when.field: {$rule->field} is not a property");
        }
        $ops = array_values(array_diff(array_keys($when), ['field']));
        if (count($ops) !== 1 || !in_array($ops[0], REQUIRED_IF_OPERATORS, true)) {
            throw new RuntimeException("{$at}.when: exactly one of " . implode(', ', REQUIRED_IF_OPERATORS) . ' is required');
        }
        $rule->op = $ops[0];
        $value = $when[$rule->op];
        $values = in_array($rule->op, ['in', 'notIn'], true) ? items($value, "{$at}.when.{$rule->op}") : [$value];
        foreach ($values as $v) {
            if (!is_string($v) && !is_int($v) && !is_float($v) && !is_bool($v)) {
                throw new RuntimeException("{$at}.when.{$rule->op}: values must be scalars");
            }
            $rule->values[] = is_bool($v) ? ($v ? 'true' : 'false') : (string) $v;
        }
        if ($rule->values === []) {
            throw new RuntimeException("{$at}.when.{$rule->op}: no value");
        }
        $rule->required = isset($item['required']) ? strings($item['required'], "{$at}.required") : [];
        foreach ($rule->required as $name) {
            if (!in_array($name, $properties, true)) {
                throw new RuntimeException("{$at}.required: {$name} is not a property");
            }
        }
        $paths = isset($item['requiredPaths']) ? strings($item['requiredPaths'], "{$at}.requiredPaths") : [];
        foreach ($paths as $path) {
            $root = str_starts_with($path, '$.');
            $relative = $root ? substr($path, 2) : $path;
            if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\[\])?(\.[A-Za-z_][A-Za-z0-9_]*(\[\])?)*$/', $relative) !== 1) {
                throw new RuntimeException("{$at}.requiredPaths: unsupported path {$path}");
            }
            if ($root) {
                $rule->rootPaths[] = $relative;
                continue;
            }
            $first = explode('.', str_replace('[]', '', $relative))[0];
            if (!in_array($first, $properties, true)) {
                throw new RuntimeException("{$at}.requiredPaths: {$first} is not a property");
            }
            $rule->paths[] = $relative;
        }
        if ($rule->required === [] && $paths === []) {
            throw new RuntimeException("{$at}: required or requiredPaths is needed");
        }
        $rules[] = $rule;
    }

    return $rules;
}

/** `sendType != template` */
function requiredIfCondition(RequiredIfRule $rule): string
{
    $symbol = ['equals' => '==', 'notEquals' => '!=', 'in' => 'in', 'notIn' => 'not in'][$rule->op];
    $values = in_array($rule->op, ['in', 'notIn'], true) ? '[' . implode(', ', $rule->values) . ']' : $rule->values[0];

    return "`{$rule->field}` {$symbol} `{$values}`";
}

/**
 * The class doc paragraph that lists the rules of a request model.
 *
 * @param list<RequiredIfRule> $rules
 */
function requiredIfDoc(array $rules): ?string
{
    if ($rules === []) {
        return null;
    }
    $parts = [];
    foreach ($rules as $rule) {
        $fields = array_merge(
            $rule->required,
            $rule->paths,
            array_map(static fn (string $p): string => '$.' . $p, $rule->rootPaths),
        );
        $parts[] = requiredIfCondition($rule) . '이면 ' . implode(', ', array_map(static fn (string $f): string => "`{$f}`", $fields));
    }

    return '조건부 필수(`x-sdk-required-if`, 보내기 전에 검사하며 `$.` 경로는 요청 본문 전체를 만들 때 검사): ' . implode('; ', $parts) . '.';
}

/**
 * The rules table: rules of each request model (own fields and relative paths), and for each request body root the
 * rules below it with `$.` paths (skipped when the first property of the path is not in the root).
 *
 * @param list<string> $roots request body schemas
 *
 * @return array{models: array<string, list<RequiredIfRule>>, roots: array<string, list<array{string, RequiredIfRule, list<string>}>>}
 */
function requiredIfTable(array $roots): array
{
    $models = [];
    foreach (Registry::$models as $model) {
        if (!$model->request || $model->ns !== '') {
            continue;
        }
        $local = array_values(array_filter($model->requiredIf, static fn (RequiredIfRule $r): bool => $r->required !== [] || $r->paths !== []));
        if ($local !== []) {
            $models[$model->name] = $local;
        }
    }
    $channels = [];
    foreach (Registry::$models as $model) {
        if ($model->channelKey !== null && $model->ns === '') {
            $channels[$model->channelKey] = $model->name;
        }
    }
    $out = [];
    foreach ($roots as $root) {
        $model = Registry::$models[$root] ?? null;
        if ($model === null) {
            continue;
        }
        $properties = array_map(static fn (Prop $p): string => $p->name, $model->props);
        $entries = [];
        foreach (requiredIfLocations($model, '', [], $channels) as [$at, $found]) {
            foreach ($found->requiredIf as $rule) {
                $paths = array_values(array_filter(
                    $rule->rootPaths,
                    static fn (string $p): bool => in_array(explode('.', str_replace('[]', '', $p))[0], $properties, true),
                ));
                if ($paths !== []) {
                    $entries[] = [$at, $rule, $paths];
                }
            }
        }
        if ($entries !== []) {
            $out[$root] = $entries;
        }
    }
    ksort($models);
    ksort($out);

    return ['models' => $models, 'roots' => $out];
}

/**
 * Every request model reachable from `$model`, with its location pattern (`messageFlow[].alimtalk`).
 *
 * @param array<string, true> $stack
 * @param array<string, string> $channels channel key => model
 *
 * @return list<array{string, Model}>
 */
function requiredIfLocations(Model $model, string $at, array $stack, array $channels): array
{
    if (isset($stack[$model->key()])) {
        return [];
    }
    $stack[$model->key()] = true;
    $out = [[$at, $model]];
    $join = static fn (string $name): string => $at === '' ? $name : "{$at}.{$name}";
    foreach ($model->props as $p) {
        $children = match ($p->kind) {
            'model' => [[$join($p->name), (string) $p->class]],
            'modelList' => [[$join($p->name) . '[]', (string) $p->class]],
            'flowList' => array_map(
                static fn (string $key): array => [$join($p->name) . "[].{$key}", $channels[$key]],
                $p->channels ?? array_keys($channels),
            ),
            default => [],
        };
        foreach ($children as [$childAt, $class]) {
            $child = Registry::$models[$class] ?? throw new RuntimeException("unknown model {$class}");
            $out = array_merge($out, requiredIfLocations($child, $childAt, $stack, $channels));
        }
    }

    return $out;
}

function requiredIfCode(RequiredIfRule $rule, string $indent): string
{
    return "{$indent}'when' => ['field' => " . export($rule->field) . ", 'op' => " . export($rule->op)
        . ", 'values' => " . export($rule->values) . '],';
}

/**
 * @param array{models: array<string, list<RequiredIfRule>>, roots: array<string, list<array{string, RequiredIfRule, list<string>}>>} $table
 */
function requiredIfRulesFile(array $table): string
{
    $lines = [
        '<?php', '', HEADER, '', 'declare(strict_types=1);', '', 'namespace Bizgo\\Internal;', '',
        '/**',
        ' * `x-sdk-required-if` rules of the spec, evaluated by {@see RequiredIf} in the request model constructors.',
        ' *',
        ' * - MODELS: rules of each request model: own fields (`required`) and paths relative to the model (`paths`).',
        ' * - ROOTS: for each request body root, the rules below it with `$.` paths: `at` is the location pattern of',
        ' *   the object the condition reads (`name[]` = every element), `paths` are from the root (without `$.`).',
        ' *',
        ' * @internal',
        ' *',
        ' * @phpstan-type Condition array{field: string, op: \'equals\'|\'notEquals\'|\'in\'|\'notIn\', values: non-empty-list<string>}',
        ' * @phpstan-type ModelRule array{when: Condition, required: list<string>, paths: list<string>}',
        ' * @phpstan-type RootRule array{at: string, when: Condition, paths: list<string>}',
        ' */',
        'final class RequiredIfRules',
        '{',
        '    /** @var array<string, list<ModelRule>> schema => rules */',
        '    public const MODELS = [',
    ];
    foreach ($table['models'] as $name => $rules) {
        $lines[] = '        ' . export($name) . ' => [';
        foreach ($rules as $rule) {
            $lines[] = '            [';
            $lines[] = requiredIfCode($rule, '                ');
            $lines[] = "                'required' => " . export($rule->required) . ',';
            $lines[] = "                'paths' => " . export($rule->paths) . ',';
            $lines[] = '            ],';
        }
        $lines[] = '        ],';
    }
    $lines[] = '    ];';
    $lines[] = '';
    $lines[] = '    /** @var array<string, list<RootRule>> request body schema => rules with `$.` paths */';
    $lines[] = '    public const ROOTS = [';
    foreach ($table['roots'] as $name => $entries) {
        $lines[] = '        ' . export($name) . ' => [';
        foreach ($entries as [$at, $rule, $paths]) {
            $lines[] = '            [';
            $lines[] = "                'at' => " . export($at) . ',';
            $lines[] = requiredIfCode($rule, '                ');
            $lines[] = "                'paths' => " . export($paths) . ',';
            $lines[] = '            ],';
        }
        $lines[] = '        ],';
    }
    $lines[] = '    ];';
    $lines[] = '';
    $lines[] = '    private function __construct()';
    $lines[] = '    {';
    $lines[] = '    }';
    $lines[] = '}';

    return implode("\n", $lines) . "\n";
}
