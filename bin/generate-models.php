<?php

/**
 * Generates the SDK code that follows the vendored OpenAPI spec.
 *
 *   php bin/generate-models.php          write every generated file (see below)
 *   php bin/generate-models.php --check  fail (exit 1) if any generated file is out of date or left over (CI)
 *
 * Output:
 * - src/Model/*.php, src/Model/Response/*.php  request and response models
 * - src/Exception/ServiceCodes.php               service code table (spec/error-codes.json)
 * - src/Internal/Operations.php                  operation registry (method, path template, retry, rate bucket, ...)
 * - src/Resource/** (except the hand-written Send/Files/Reports/Messages), src/Resource/*Operations.php traits,
 *   src/GeneratedResources.php, src/Webhook/WebhookParsers.php  resource methods and webhook parsers (bin/lib/resources.php)
 *
 * Request models (reachable from a request body) validate in their constructor and reject unknown keys in
 * fromArray(). Everything else is a response model: every field optional, unknown fields kept in $extra. A schema
 * used by both a request and a response gets a request model in Bizgo\Model and a lenient twin in
 * Bizgo\Model\Response. Unsupported schema keywords make the generator fail instead of being ignored silently.
 */

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/lib/resources.php';
require __DIR__ . '/lib/required-if.php';

define('ROOT', str_replace('\\', '/', dirname(__DIR__)));
define('SPEC', ROOT . '/spec/openapi.yaml');
define('ERROR_CODES', ROOT . '/spec/error-codes.json');
define('MODEL_DIR', ROOT . '/src/Model');
define('SERVICE_CODES', ROOT . '/src/Exception/ServiceCodes.php');
define('REQUIRED_IF_RULES', ROOT . '/src/Internal/RequiredIfRules.php');
const HEADER = '// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.';
const FLOW_UNION = 'MessageFlowItem';
const DOC_WIDTH = 104;
const RESPONSE_NS = 'Response';

/** Schema keywords the generator understands. Anything else fails the build. */
const KNOWN_KEYWORDS = [
    'type', 'description', 'properties', 'required', 'items', '$ref', 'enum', 'default', 'example', 'examples',
    'maxLength', 'minLength', 'maxItems', 'minItems', 'pattern', 'minimum', 'maximum', 'format', 'title',
    'additionalProperties', 'allOf', 'oneOf', 'x-max-bytes', 'x-charset', 'x-format', 'x-known-values',
    'x-unverified', 'x-verified', 'x-source', 'deprecated', 'x-sdk-required-if',
];

final class Prop
{
    public string $name = '';
    /** PHP property name (the API name, except `extra` of a response model, which is `extraField`). */
    public string $php = '';
    // string|int|number|bool|model|modelList|stringList|stringMap|object|objectList|flowList
    public string $kind = 'string';
    /** Registry key of the model class (`Name` or `Response\Name`). */
    public ?string $class = null;
    /** PHP reference to $class from the emitting model's namespace (set while emitting). */
    public string $ref = '';
    /** Allowed channel keys of a flowList that accepts only some channels. */
    /** @var list<string>|null */
    public ?array $channels = null;
    public bool $required = false;
    public string $description = '';
    public ?string $unverified = null;
    /** @var array<string, mixed> */
    public array $schema = [];
}

final class Model
{
    public string $name = '';
    /** '' (Bizgo\Model) or 'Response' (Bizgo\Model\Response, response twins of shared schemas). */
    public string $ns = '';
    public bool $request = false;
    public ?string $channelKey = null;
    public string $description = '';
    public ?string $unverified = null;
    /** @var list<Prop> */
    public array $props = [];
    /** @var list<RequiredIfRule> `x-sdk-required-if` (request models only) */
    public array $requiredIf = [];

    public function key(): string
    {
        return $this->ns === '' ? $this->name : $this->ns . '\\' . $this->name;
    }
}

final class Registry
{
    /** @var array<string, Model> registry key => model */
    public static array $models = [];

    /** @var array<string, true> schemas that also have a response twin */
    public static array $shared = [];

    /** @var array<string, list<string>|null> flow union schema => allowed channel keys (null: all) */
    public static array $flowUnions = [];

    /** @var array<string, true> request models whose constructor calls RequiredIf::check() */
    public static array $requiredIfChecked = [];
}

/** @var list<string> $cliArgs */
$cliArgs = array_values(array_filter(is_array($_SERVER['argv'] ?? null) ? $_SERVER['argv'] : [], 'is_string'));
main($cliArgs);

// ---------------------------------------------------------------------------------------------------------------
// typed access to the decoded YAML

/** @return array<string, mixed> */
function map(mixed $value, string $where): array
{
    if (!is_array($value)) {
        throw new RuntimeException("{$where}: expected an object");
    }
    $out = [];
    foreach ($value as $key => $item) {
        $out[(string) $key] = $item;
    }

    return $out;
}

/** @return list<mixed> */
function items(mixed $value, string $where): array
{
    if (!is_array($value) || !array_is_list($value)) {
        throw new RuntimeException("{$where}: expected a list");
    }

    return $value;
}

function str(mixed $value, string $where): string
{
    if (is_string($value)) {
        return $value;
    }
    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }
    throw new RuntimeException("{$where}: expected a string");
}

/** @return list<string> */
function strings(mixed $value, string $where): array
{
    return array_map(static fn (mixed $v): string => str($v, $where), items($value, $where));
}

/** @param array<string, mixed> $schema */
function optionalStr(array $schema, string $key, string $where): ?string
{
    return isset($schema[$key]) ? str($schema[$key], "{$where}.{$key}") : null;
}

/** @param list<string> $argv */
function main(array $argv): void
{
    $check = in_array('--check', $argv, true);
    $spec = map(Yaml::parseFile(SPEC), 'spec');
    $schemas = map(map($spec['components'] ?? null, 'components')['schemas'] ?? null, 'components.schemas');

    // messageFlow: MessageFlowItem = oneOf [SmsFlowItem{sms: SmsMessage}, ...]; other unions list a subset
    $channels = [];
    $flowItems = [];
    $unions = [];
    foreach ($schemas as $name => $schema) {
        $schema = map($schema, $name);
        if (!isset($schema['oneOf'])) {
            continue;
        }
        $keys = [];
        foreach (items($schema['oneOf'], "{$name}.oneOf") as $member) {
            $flowName = refName(str(map($member, $name)['$ref'] ?? null, "{$name}.oneOf"));
            $flow = map($schemas[$flowName] ?? null, $flowName);
            $key = strings($flow['required'] ?? null, "{$flowName}.required")[0];
            $property = map(map($flow['properties'] ?? null, "{$flowName}.properties")[$key] ?? null, "{$flowName}.{$key}");
            $class = refName(str($property['$ref'] ?? null, "{$flowName}.{$key}"));
            if (isset($channels[$key]) && $channels[$key] !== $class) {
                throw new RuntimeException("{$name}: channel key {$key} maps to two classes");
            }
            $channels[$key] = $class;
            $flowItems[] = $flowName;
            $keys[] = $key;
        }
        $unions[$name] = $keys;
    }
    if (!isset($unions[FLOW_UNION])) {
        throw new RuntimeException(FLOW_UNION . ' is missing');
    }
    foreach ($unions as $name => $keys) {
        if (array_diff($keys, $unions[FLOW_UNION]) !== []) {
            throw new RuntimeException("{$name}: every channel must also be in " . FLOW_UNION);
        }
        Registry::$flowUnions[$name] = $name === FLOW_UNION ? null : $keys;
    }
    $union = map($schemas[FLOW_UNION], FLOW_UNION);

    $operations = specOperations($spec);
    $skip = array_values(array_unique(array_merge($flowItems, array_keys($unions), multipartSchemas($operations))));
    $roots = requestRoots($operations, $schemas);
    $request = array_values(array_diff(closure($schemas, $roots), $skip));
    $response = array_values(array_diff(closure($schemas, responseRoots($spec, $operations)), $skip));
    foreach (array_intersect($request, $response) as $name) {
        Registry::$shared[$name] = true;
    }
    foreach ($schemas as $name => $schema) {
        if (in_array($name, $skip, true)) {
            continue; // flow wrappers are generated as MessageFlowItem; multipart bodies are method parameters
        }
        addModel($name, map($schema, $name), in_array($name, $request, true), $schemas);
    }
    foreach (array_keys(Registry::$shared) as $name) {
        addModel($name, map($schemas[$name], $name), false, $schemas, RESPONSE_NS);
    }
    foreach ($channels as $key => $class) {
        Registry::$models[$class]->channelKey = $key;
    }
    $requiredIf = requiredIfTable($roots);
    foreach (array_merge(array_keys($requiredIf['models']), array_keys($requiredIf['roots'])) as $name) {
        Registry::$requiredIfChecked[$name] = true;
    }

    $outputs = [];
    foreach (Registry::$models as $model) {
        $dir = MODEL_DIR . ($model->ns === '' ? '' : '/' . $model->ns);
        $outputs[$dir . '/' . $model->name . '.php'] = $model->request ? requestClass($model) : responseClass($model);
    }
    $outputs[MODEL_DIR . '/ChannelMessage.php'] = channelInterface($channels);
    $outputs[MODEL_DIR . '/MessageFlowItem.php'] = flowItemClass($channels, $union);
    $outputs[SERVICE_CODES] = serviceCodes();
    $outputs[REQUIRED_IF_RULES] = requiredIfRulesFile($requiredIf);
    $outputs += resourceOutputs($spec, $operations);

    $stale = [];
    foreach ($outputs as $path => $code) {
        $current = is_file($path) ? file_get_contents($path) : false;
        if ($current === $code) {
            continue;
        }
        if ($check) {
            $stale[] = relative($path) . ($current === false ? ' (missing)' : firstDifference($current, $code));
            continue;
        }
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0o777, true);
        }
        file_put_contents($path, $code);
        echo 'wrote ', relative($path), PHP_EOL;
    }
    // generated files that the spec no longer produces
    $existing = [];
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ROOT . '/src', FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        if ($file instanceof SplFileInfo && $file->getExtension() === 'php') {
            $existing[] = $file->getPathname();
        }
    }
    sort($existing);
    foreach ($existing as $path) {
        $path = str_replace('\\', '/', $path);
        if (isset($outputs[$path]) || !str_contains((string) file_get_contents($path), HEADER)) {
            continue;
        }
        if ($check) {
            $stale[] = relative($path) . ' (no longer generated)';
        } else {
            unlink($path);
            echo 'removed ', relative($path), PHP_EOL;
        }
    }
    if ($stale !== []) {
        fwrite(STDERR, "generated files are out of date: run php bin/generate-models.php\n  " . implode("\n  ", $stale) . "\n");
        exit(1);
    }
    if ($check) {
        echo 'generated files are up to date (', count($outputs), ' files)', PHP_EOL;
    }
}

function relative(string $path): string
{
    return ltrim(substr(str_replace('\\', '/', $path), strlen(ROOT)), '/');
}

function firstDifference(string $a, string $b): string
{
    $la = explode("\n", $a);
    $lb = explode("\n", $b);
    foreach ($lb as $i => $line) {
        if (($la[$i] ?? null) !== $line) {
            return ' (line ' . ($i + 1) . ')';
        }
    }

    return ' (line ' . (count($lb) + 1) . ')';
}

function refName(string $ref): string
{
    if (!str_starts_with($ref, '#/components/schemas/')) {
        throw new RuntimeException("unsupported \$ref {$ref}");
    }

    return substr($ref, strlen('#/components/schemas/'));
}

/**
 * @param array<string, mixed> $schemas
 * @param list<string> $roots
 *
 * @return list<string>
 */
function closure(array $schemas, array $roots): array
{
    $seen = [];
    $stack = $roots;
    while ($stack !== []) {
        $name = array_pop($stack);
        if (isset($seen[$name])) {
            continue;
        }
        $seen[$name] = true;
        $schema = map($schemas[$name] ?? null, $name);
        array_walk_recursive($schema, static function (mixed $value, int|string $key) use (&$stack): void {
            if ($key === '$ref' && is_string($value)) {
                $stack[] = refName($value);
            }
        });
    }

    return array_map('strval', array_keys($seen));
}

/**
 * @param array<string, mixed> $schema
 * @param array<string, mixed> $schemas
 */
function addModel(string $name, array $schema, bool $request, array $schemas, string $ns = ''): void
{
    checkKeywords($schema, $name);
    [$properties, $required] = flatten($schema, $schemas, $name);
    $model = new Model();
    $model->name = $name;
    $model->ns = $ns;
    $model->request = $request;
    $model->description = trim(optionalStr($schema, 'description', $name) ?? '');
    $model->unverified = optionalStr($schema, 'x-unverified', $name);
    Registry::$models[$model->key()] = $model;
    if ($request) {
        $model->requiredIf = parseRequiredIf(rawRequiredIf($schema, $schemas, $name), array_keys($properties), $name);
    }
    foreach ($properties as $jsonName => $prop) {
        $where = "{$name}.{$jsonName}";
        checkKeywords($prop, $where);
        if (isset($prop['x-sdk-required-if'])) {
            throw new RuntimeException("{$where}: x-sdk-required-if belongs on the object schema, not on a property");
        }
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $jsonName) !== 1) {
            throw new RuntimeException("{$where}: not a valid PHP identifier");
        }
        if (!$request && $jsonName === 'extraField') {
            throw new RuntimeException("{$name}: a response field named 'extraField' collides with the renamed 'extra' field");
        }
        $p = new Prop();
        $p->name = $jsonName;
        $p->php = !$request && $jsonName === 'extra' ? 'extraField' : $jsonName; // $extra is the unknown-field bag
        $p->required = in_array($jsonName, $required, true);
        $p->description = trim(optionalStr($prop, 'description', $where) ?? '');
        $p->unverified = optionalStr($prop, 'x-unverified', $where);
        $p->schema = $prop;
        resolveType($model, $p, $prop, $schemas);
        $model->props[] = $p;
    }
}

/**
 * @param array<string, mixed> $schema
 */
function checkKeywords(array $schema, string $where): void
{
    foreach (array_keys($schema) as $key) {
        if (!in_array($key, KNOWN_KEYWORDS, true)) {
            throw new RuntimeException("{$where}: unsupported schema keyword '{$key}'");
        }
    }
}

/**
 * Merges allOf members into one property list.
 *
 * @param array<string, mixed> $schema
 * @param array<string, mixed> $schemas
 *
 * @return array{0: array<string, array<string, mixed>>, 1: list<string>}
 */
function flatten(array $schema, array $schemas, string $where): array
{
    if (isset($schema['$ref'])) {
        $name = refName(str($schema['$ref'], $where));

        return flatten(map($schemas[$name] ?? null, $name), $schemas, $name);
    }
    $properties = [];
    $required = [];
    foreach (isset($schema['allOf']) ? items($schema['allOf'], "{$where}.allOf") : [] as $part) {
        [$p, $r] = flatten(map($part, "{$where}.allOf"), $schemas, $where);
        $properties = array_merge($properties, $p);
        $required = array_merge($required, $r);
    }
    foreach (isset($schema['properties']) ? map($schema['properties'], $where) : [] as $name => $prop) {
        $properties[$name] = map($prop, "{$where}.{$name}");
    }
    $own = isset($schema['required']) ? strings($schema['required'], "{$where}.required") : [];
    $required = array_values(array_unique(array_merge($required, $own)));

    return [$properties, $required];
}

/**
 * @param array<string, mixed> $schema
 */
function primaryType(array $schema, string $where): string
{
    $type = $schema['type'] ?? null;
    if (is_array($type)) {
        $types = array_values(array_diff(strings($type, $where), ['null']));
        if (count($types) !== 1) {
            throw new RuntimeException("{$where}: unsupported type union");
        }

        return $types[0];
    }

    return str($type, "{$where}.type");
}

/**
 * @param array<string, mixed> $schema
 * @param array<string, mixed> $schemas
 */
function resolveType(Model $owner, Prop $p, array $schema, array $schemas): void
{
    $where = "{$owner->name}.{$p->name}";
    if (isset($schema['$ref'])) {
        $p->kind = 'model';
        $p->class = targetKey($owner, refName(str($schema['$ref'], $where)), $where);

        return;
    }
    $type = primaryType($schema, $where);
    switch ($type) {
        case 'string':
            $p->kind = 'string';

            return;
        case 'integer':
            $p->kind = 'int';

            return;
        case 'number':
            $p->kind = 'number';

            return;
        case 'boolean':
            $p->kind = 'bool';

            return;
        case 'array':
            $items = map($schema['items'] ?? null, "{$where}.items");
            checkKeywords($items, "{$where}[]");
            if (isset($items['$ref'])) {
                $class = refName(str($items['$ref'], "{$where}[]"));
                if (array_key_exists($class, Registry::$flowUnions)) {
                    if (!$owner->request) {
                        throw new RuntimeException("{$where}: a message flow in a response is not supported");
                    }
                    $p->kind = 'flowList';
                    $p->class = FLOW_UNION;
                    $p->channels = Registry::$flowUnions[$class];

                    return;
                }
                $p->kind = 'modelList';
                $p->class = targetKey($owner, $class, $where);

                return;
            }
            $itemType = primaryType($items, "{$where}[]");
            if ($itemType === 'string') {
                foreach (['maxLength', 'minLength', 'pattern', 'enum', 'x-max-bytes'] as $unsupported) {
                    if (isset($items[$unsupported])) {
                        throw new RuntimeException("{$where}[]: item constraint {$unsupported} is not supported");
                    }
                }
                $p->kind = 'stringList';

                return;
            }
            if ($itemType === 'object' && isset($items['properties'])) {
                $p->kind = 'modelList';
                $p->class = inlineModel($owner, $p->name . 'Item', $items, $schemas);

                return;
            }
            if ($itemType === 'object') {
                $p->kind = 'objectList'; // free-form objects

                return;
            }
            throw new RuntimeException("{$where}: unsupported array item type {$itemType}");
        case 'object':
            if (isset($schema['properties'])) {
                $p->kind = 'model';
                $p->class = inlineModel($owner, $p->name, $schema, $schemas);

                return;
            }
            $additional = $schema['additionalProperties'] ?? null;
            if (is_array($additional) && ($additional['type'] ?? null) === 'string') {
                $p->kind = 'stringMap';

                return;
            }
            $p->kind = 'object';

            return;
        default:
            throw new RuntimeException("{$where}: unsupported type {$type}");
    }
}

/**
 * @param array<string, mixed> $schema
 * @param array<string, mixed> $schemas
 */
function inlineModel(Model $owner, string $prop, array $schema, array $schemas): string
{
    $name = $owner->name . ucfirst($prop);
    $key = $owner->ns === '' ? $name : $owner->ns . '\\' . $name;
    if (!isset(Registry::$models[$key])) {
        if (isset($schemas[$name])) {
            throw new RuntimeException("inline schema name {$name} collides with a component schema");
        }
        addModel($name, $schema, $owner->request, $schemas, $owner->ns);
    }

    return $key;
}

/**
 * The registry key of the model a property of `$owner` refers to: request models refer to request models,
 * response models to the response twin of a shared schema.
 */
function targetKey(Model $owner, string $schema, string $where): string
{
    if (array_key_exists($schema, Registry::$flowUnions)) {
        throw new RuntimeException("{$where}: a message flow union is only supported as array items");
    }

    return !$owner->request && isset(Registry::$shared[$schema]) ? RESPONSE_NS . '\\' . $schema : $schema;
}

/**
 * The PHP name of registry key `$key` as seen from `$from`'s namespace; adds a `use` when needed.
 *
 * @param list<string> $uses
 */
function classRef(Model $from, string $key, array &$uses): string
{
    $twin = str_starts_with($key, RESPONSE_NS . '\\');
    if ($from->ns === '') {
        return $key; // `Response\X` resolves relative to Bizgo\Model
    }
    if ($twin) {
        return substr($key, strlen(RESPONSE_NS) + 1);
    }
    $uses[] = 'Bizgo\\Model\\' . $key;

    return $key;
}

/**
 * Resolves the PHP references of every property of the model being emitted.
 *
 * @param list<string> $uses
 */
function prepareRefs(Model $model, array &$uses): void
{
    foreach ($model->props as $p) {
        $p->ref = $p->class === null ? '' : classRef($model, $p->class, $uses);
    }
}

// ---------------------------------------------------------------------------------------------------------------
// documentation

function escapeDoc(string $text): string
{
    $text = str_replace('*/', '*\/', $text);

    return preg_replace('/^@/m', '\\@', $text) ?? $text;
}

/**
 * Wraps text into doc comment lines (without the leading " * "). Markdown tables are kept as is.
 *
 * @return list<string>
 */
function wrap(string $text, int $width): array
{
    $out = [];
    foreach (explode("\n", escapeDoc(trim($text))) as $line) {
        $line = rtrim($line);
        if ($line === '' || str_starts_with(ltrim($line), '|') || mb_strlen($line) <= $width) {
            $out[] = $line;
            continue;
        }
        $lead = substr($line, 0, strlen($line) - strlen(ltrim($line)));
        $continuation = $lead . (preg_match('/^\s*[-*] /', $line) === 1 ? '  ' : '');
        $prefix = $lead;
        $current = '';
        foreach (explode(' ', ltrim($line)) as $word) {
            if ($current !== '' && mb_strlen($prefix . $current . ' ' . $word) > $width) {
                $out[] = $prefix . $current;
                $prefix = $continuation;
                $current = $word;
                continue;
            }
            $current = $current === '' ? $word : $current . ' ' . $word;
        }
        if ($current !== '') {
            $out[] = $prefix . $current;
        }
    }

    return $out;
}

/**
 * @param list<string> $lines
 *
 * @return list<string>
 */
function docBlock(array $lines, string $indent): array
{
    while ($lines !== [] && $lines[count($lines) - 1] === '') {
        array_pop($lines);
    }
    if ($lines === []) {
        return [];
    }
    $out = [$indent . '/**'];
    foreach ($lines as $line) {
        $out[] = rtrim($indent . ' * ' . $line);
    }
    $out[] = $indent . ' */';

    return $out;
}

function firstSentence(string $text): string
{
    $text = trim(explode("\n", trim($text))[0]);
    if (preg_match('/^(.+?[.다])(\s|$)/u', $text, $m) === 1) {
        $text = $m[1];
    }
    if (mb_strlen($text) > 80) {
        $text = mb_substr($text, 0, 77) . '...';
    }

    return escapeDoc($text);
}

function export(mixed $value): string
{
    if (is_string($value)) {
        return "'" . str_replace(['\\', "'"], ['\\\\', "\\'"], $value) . "'";
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }
    if ($value === null) {
        return 'null';
    }
    if (is_array($value) && array_is_list($value)) {
        return '[' . implode(', ', array_map('export', $value)) . ']';
    }
    throw new RuntimeException('cannot export value');
}

/**
 * Scalar keywords of a schema as strings, for string interpolation.
 *
 * @param array<string, mixed> $schema
 *
 * @return array<string, string>
 */
function scalars(array $schema): array
{
    $out = [];
    foreach ($schema as $key => $value) {
        if (is_string($value) || is_int($value) || is_float($value)) {
            $out[$key] = (string) $value;
        }
    }

    return $out;
}

/** @param list<string> $values */
function codeList(array $values): string
{
    return implode(', ', array_map(static fn (string $v): string => '`' . $v . '`', $values));
}

/** Korean summary of the constraints of a property, for its doc comment. */
function constraints(Prop $p, bool $request): string
{
    $s = $p->schema;
    $c = scalars($s);
    $parts = [];
    if ($request && $p->required) {
        $parts[] = '필수';
    }
    if (isset($s['maxLength'])) {
        $parts[] = "최대 {$c['maxLength']}자";
    }
    if (isset($s['minLength'])) {
        $parts[] = "최소 {$c['minLength']}자";
    }
    if (isset($s['x-max-bytes'])) {
        $parts[] = "최대 {$c['x-max-bytes']}byte" . (isset($s['x-charset']) ? "({$c['x-charset']})" : '');
    }
    if (isset($s['minItems'], $s['maxItems'])) {
        $parts[] = "항목 {$c['minItems']}~{$c['maxItems']}개";
    } elseif (isset($s['minItems'])) {
        $parts[] = "항목 최소 {$c['minItems']}개";
    } elseif (isset($s['maxItems'])) {
        $parts[] = "항목 최대 {$c['maxItems']}개";
    }
    if (isset($s['minimum'], $s['maximum'])) {
        $parts[] = "범위 {$c['minimum']}~{$c['maximum']}";
    } elseif (isset($s['minimum'])) {
        $parts[] = "{$c['minimum']} 이상";
    } elseif (isset($s['maximum'])) {
        $parts[] = "{$c['maximum']} 이하";
    }
    if (isset($s['pattern'])) {
        $parts[] = "형식 `{$c['pattern']}`";
    }
    if (isset($s['x-format'])) {
        $parts[] = "시각 형식 `{$c['x-format']}`";
    }
    if (isset($s['enum'])) {
        $parts[] = ($request ? '허용 값: ' : '값: ') . codeList(strings($s['enum'], $p->name));
    }
    if (isset($s['x-known-values'])) {
        $parts[] = '알려진 값: ' . codeList(strings($s['x-known-values'], $p->name));
    }
    if (array_key_exists('default', $s) && $request) {
        $parts[] = '서버 기본값 `' . export($s['default']) . '` (설정하지 않으면 보내지 않음)';
    }
    if ($p->channels !== null) {
        $parts[] = '채널 키: ' . codeList($p->channels);
    }
    if (($s['deprecated'] ?? false) === true) {
        $parts[] = '사용 중단(deprecated)';
    }

    return implode(' · ', $parts);
}

/** @return list<string> */
function propDoc(Prop $p, bool $request): array
{
    $lines = wrap($p->description, DOC_WIDTH - 8);
    $summary = constraints($p, $request);
    if ($summary !== '') {
        $lines[] = '';
        $lines = array_merge($lines, wrap($summary, DOC_WIDTH - 8));
    }
    if ($p->unverified !== null) {
        $lines[] = '';
        $lines = array_merge($lines, wrap('확인 필요(x-unverified): ' . $p->unverified, DOC_WIDTH - 8));
    }

    return docBlock($lines, '        ');
}

// ---------------------------------------------------------------------------------------------------------------
// code

function phpType(Prop $p, bool $nullable): string
{
    $type = match ($p->kind) {
        'string' => 'string',
        'int' => 'int',
        'number' => 'int|float',
        'bool' => 'bool',
        'model' => $p->ref,
        default => 'array',
    };
    if (!$nullable) {
        return $type;
    }

    return str_contains($type, '|') ? $type . '|null' : '?' . $type;
}

function docType(Prop $p, bool $nullable, bool $request): string
{
    $type = match ($p->kind) {
        'string' => 'string',
        'int' => 'int',
        'number' => 'int|float',
        'bool' => 'bool',
        'model' => $p->ref,
        'modelList' => "list<{$p->ref}>",
        'objectList' => 'list<array<string, mixed>>',
        'flowList' => 'list<MessageFlowItem|ChannelMessage>',
        'stringList' => 'list<string>',
        'stringMap' => 'array<string, string>',
        'object' => 'array<string, mixed>',
        default => throw new RuntimeException($p->kind),
    };

    return $nullable ? $type . '|null' : $type;
}

/**
 * @param list<string> $lines
 * @param list<string> $uses
 */
function phpFile(array $lines, array $uses, string $ns = ''): string
{
    sort($uses);
    $namespace = 'Bizgo\\Model' . ($ns === '' ? '' : '\\' . $ns);
    $head = ['<?php', '', HEADER, '', 'declare(strict_types=1);', '', 'namespace ' . $namespace . ';', ''];
    if ($uses !== []) {
        foreach (array_unique($uses) as $use) {
            $head[] = "use {$use};";
        }
        $head[] = '';
    }

    return implode("\n", array_merge($head, $lines)) . "\n";
}

/**
 * @param list<string> $extra
 *
 * @return list<string>
 */
function classDoc(Model $model, array $extra): array
{
    $lines = wrap($model->description, DOC_WIDTH - 4);
    if ($model->unverified !== null) {
        $lines[] = '';
        $lines = array_merge($lines, wrap('확인 필요(x-unverified): ' . $model->unverified, DOC_WIDTH - 4));
    }
    foreach ($extra as $paragraph) {
        if ($lines !== []) {
            $lines[] = '';
        }
        $lines = array_merge($lines, wrap($paragraph, DOC_WIDTH - 4));
    }

    return docBlock($lines, '');
}

/** @return list<string> */
function fieldsConst(Model $model): array
{
    $names = array_map(static fn (Prop $p): string => export($p->name), $model->props);
    $line = '    public const FIELDS = [' . implode(', ', $names) . '];';
    if (strlen($line) <= 116) {
        return ['    /** API field names, in spec order. */', $line];
    }
    $out = ['    /** API field names, in spec order. */', '    public const FIELDS = ['];
    foreach ($names as $name) {
        $out[] = "        {$name},";
    }
    $out[] = '    ];';

    return $out;
}

/** @return list<Prop> */
function ordered(Model $model): array
{
    $required = array_values(array_filter($model->props, static fn (Prop $p): bool => $p->required));
    $optional = array_values(array_filter($model->props, static fn (Prop $p): bool => !$p->required));

    return array_merge($required, $optional);
}

/** @return list<string> constructor checks */
function checks(Prop $p): array
{
    $s = $p->schema;
    $c = scalars($s);
    $n = export($p->name);
    $v = '$' . $p->name;
    $out = [];
    if ($p->kind === 'string') {
        if (isset($s['maxLength'])) {
            $out[] = "Check::maxLength({$n}, {$v}, {$c['maxLength']})";
        }
        if (isset($s['minLength'])) {
            $out[] = "Check::minLength({$n}, {$v}, {$c['minLength']})";
        }
        if (isset($s['x-max-bytes'])) {
            $out[] = "Check::maxBytes({$n}, {$v}, {$c['x-max-bytes']}, " . export($s['x-charset'] ?? null) . ')';
        }
        if (isset($s['pattern'])) {
            $out[] = "Check::pattern({$n}, {$v}, " . export($s['pattern']) . ')';
        }
        if (isset($s['enum'])) {
            $out[] = "Check::enum({$n}, {$v}, " . export(strings($s['enum'], $p->name)) . ')';
        }
    }
    if (($p->kind === 'int' || $p->kind === 'number') && (isset($s['minimum']) || isset($s['maximum']))) {
        $out[] = "Check::range({$n}, {$v}, " . export($s['minimum'] ?? null) . ', ' . export($s['maximum'] ?? null) . ')';
    }
    if (in_array($p->kind, ['modelList', 'flowList', 'stringList', 'objectList'], true)) {
        if (isset($s['minItems']) || isset($s['maxItems'])) {
            $out[] = "Check::items({$n}, {$v}, " . export($s['minItems'] ?? null) . ', ' . export($s['maxItems'] ?? null) . ')';
        }
        $out[] = match ($p->kind) {
            'modelList' => "Check::listOf({$n}, {$v}, [{$p->ref}::class])",
            'objectList' => "Check::objectList({$n}, {$v})",
            'flowList' => "Check::listOf({$n}, {$v}, [MessageFlowItem::class, ChannelMessage::class])",
            default => "Check::stringList({$n}, {$v})",
        };
        if ($p->kind === 'flowList' && $p->channels !== null) {
            $out[] = "Check::channels({$n}, {$v}, " . export($p->channels) . ')';
        }
    }
    if ($p->kind === 'stringMap') {
        $out[] = "Check::stringMap({$n}, {$v})";
    }
    foreach (['exclusiveMinimum', 'exclusiveMaximum', 'multipleOf', 'uniqueItems'] as $unsupported) {
        if (isset($s[$unsupported])) {
            throw new RuntimeException("{$p->name}: {$unsupported} is not supported");
        }
    }
    if ($p->kind !== 'string' && (isset($s['maxLength']) || isset($s['pattern']) || isset($s['x-max-bytes']))) {
        throw new RuntimeException("{$p->name}: string constraint on a non-string field");
    }

    return $out;
}

function reader(Prop $p): string
{
    $n = export($p->name);
    $r = $p->required;

    return match ($p->kind) {
        'string' => $r ? "\$in->requiredString({$n})" : "\$in->string({$n})",
        'int' => $r ? "\$in->requiredInt({$n})" : "\$in->int({$n})",
        'number' => $r ? "\$in->requiredNumber({$n})" : "\$in->number({$n})",
        'bool' => $r ? "\$in->requiredBool({$n})" : "\$in->bool({$n})",
        'stringList' => $r ? "\$in->requiredStringList({$n})" : "\$in->stringList({$n})",
        'stringMap' => $r ? "\$in->requiredStringMap({$n})" : "\$in->stringMap({$n})",
        'object' => $r ? "\$in->requiredObject({$n})" : "\$in->object({$n})",
        'objectList' => $r ? "\$in->requiredObjectList({$n})" : "\$in->objectList({$n})",
        'model' => "\$in->model({$n}, {$p->ref}::class" . ($r ? ', true' : '') . ')',
        'modelList', 'flowList' => "\$in->modelList({$n}, {$p->ref}::class" . ($r ? ', true' : '') . ')',
        default => throw new RuntimeException($p->kind),
    };
}

function requestClass(Model $model): string
{
    $uses = ['Bizgo\\Exception\\ValidationException', 'Bizgo\\Internal\\RequestData', 'Bizgo\\Internal\\Serialize'];
    $props = ordered($model);
    prepareRefs($model, $uses);
    $extraDoc = [];
    if ($model->channelKey !== null) {
        $extraDoc[] = "`messageFlow[].{$model->channelKey}`로 보냅니다. `\$client->send->omni(messages: [...])`에 넣으면 SDK가 감쌉니다.";
    }
    $conditional = requiredIfDoc($model->requiredIf);
    if ($conditional !== null) {
        $extraDoc[] = $conditional;
    }
    $extraDoc[] = '요청 모델입니다. 만들 때 검증하므로(보내기 전) 잘못된 요청은 서버로 나가지 않습니다. '
        . '`fromArray()`는 알 수 없는 필드도 거절합니다. `toArray()`는 설정한 필드만 API 이름으로 돌려줍니다.';
    $implements = $model->channelKey !== null ? 'ChannelMessage' : 'RequestModel';
    $lines = classDoc($model, $extraDoc);
    $lines[] = "final readonly class {$model->name} implements {$implements}";
    $lines[] = '{';
    if ($model->channelKey !== null) {
        $lines[] = '    /** `messageFlow` channel key of this message. */';
        $lines[] = "    public const CHANNEL_KEY = '{$model->channelKey}';";
        $lines[] = '';
    }
    $lines = array_merge($lines, fieldsConst($model));
    $lines[] = '';

    // constructor
    $paramDocs = [];
    foreach ($props as $p) {
        $paramDocs[] = '@param ' . docType($p, !$p->required, true) . ' $' . $p->name . ' ' . firstSentence($p->description);
    }
    $checks = [];
    foreach ($props as $p) {
        $checks = array_merge($checks, checks($p));
    }
    $conditionalCheck = isset(Registry::$requiredIfChecked[$model->name]) && $model->ns === '';
    if ($checks !== [] || $conditionalCheck) {
        $paramDocs[] = '';
        $paramDocs[] = '@throws ValidationException';
    }
    $lines = array_merge($lines, docBlock($paramDocs, '    '));
    $lines[] = '    public function __construct(';
    foreach ($props as $p) {
        $lines = array_merge($lines, propDoc($p, true));
        $lines[] = '        #[\\SensitiveParameter]';
        $lines[] = '        public ' . phpType($p, !$p->required) . ' $' . $p->name . ($p->required ? '' : ' = null') . ',';
    }
    if ($checks === [] && !$conditionalCheck) {
        $lines[] = '    ) {';
        $lines[] = '    }';
    } else {
        $uses[] = 'Bizgo\\Internal\\Check';
        $lines[] = '    ) {';
        if ($checks !== []) {
            $lines[] = '        Check::all(';
            foreach ($checks as $check) {
                $lines[] = "            {$check},";
            }
            $lines[] = '        );';
        }
        if ($conditionalCheck) {
            // after the type checks above: toArray() needs well-typed lists
            $uses[] = 'Bizgo\\Internal\\RequiredIf';
            $lines[] = "        Check::all(RequiredIf::check('{$model->name}', \$this->toArray()));";
        }
        $lines[] = '    }';
    }
    $lines[] = '';

    // fromArray
    $lines[] = '    /**';
    $lines[] = '     * Builds the model from an array with API field names (for example decoded JSON).';
    $lines[] = '     * Unknown keys, missing required fields and wrong types are reported together, by field path.';
    $lines[] = '     *';
    $lines[] = '     * @param array<array-key, mixed> $data';
    $lines[] = '     *';
    $lines[] = '     * @throws ValidationException';
    $lines[] = '     */';
    $lines[] = '    public static function fromArray(#[\SensitiveParameter] array $data, string $path = \'\'): static';
    $lines[] = '    {';
    $lines[] = '        $in = new RequestData($data, $path, self::FIELDS);';
    foreach ($props as $p) {
        $lines[] = '        $' . $p->name . ' = ' . reader($p) . ';';
    }
    $lines[] = '';
    $lines[] = '        return $in->create(static fn (): static => new static(';
    foreach ($props as $p) {
        $value = '$' . $p->name;
        if ($p->required && in_array($p->kind, ['model', 'modelList', 'flowList'], true)) {
            $value .= " ?? throw new \\LogicException('checked by RequestData::create()')";
        }
        $lines[] = "            {$p->name}: {$value},";
    }
    $lines[] = '        ));';
    $lines[] = '    }';
    $lines[] = '';

    // toArray / jsonSerialize
    $lines = array_merge($lines, serializer($model, $props, false), [''], serializer($model, $props, true));
    if ($model->channelKey !== null) {
        $lines[] = '';
        $lines[] = '    public function channelKey(): string';
        $lines[] = '    {';
        $lines[] = '        return self::CHANNEL_KEY;';
        $lines[] = '    }';
    }
    $lines = array_merge($lines, maskMethods($model, $uses));
    $lines[] = '}';

    return phpFile($lines, $uses, $model->ns);
}

/**
 * @param list<Prop> $props
 *
 * @return list<string>
 */
function serializer(Model $model, array $props, bool $json): array
{
    $lines = [];
    if ($json) {
        $lines[] = '    /**';
        $lines[] = '     * The value for json_encode(): like toArray(), with maps as JSON objects even when empty.';
        $lines[] = '     *';
        $lines[] = '     * @return array<string, mixed>';
        $lines[] = '     */';
        $lines[] = '    public function jsonSerialize(): array';
    } else {
        $lines[] = '    /**';
        $lines[] = '     * The request body with API field names. Only fields you set are included.';
        $lines[] = '     *';
        $lines[] = '     * @return array<string, mixed>';
        $lines[] = '     */';
        $lines[] = '    public function toArray(): array';
    }
    $lines[] = '    {';
    $lines[] = '        return Serialize::compact([';
    // the API field order of the spec, not the constructor order
    foreach ($model->props as $p) {
        $v = '$this->' . $p->name;
        $expr = match ($p->kind) {
            'model' => $json ? $v : $v . ($p->required ? '' : '?') . '->toArray()',
            'modelList' => $json ? $v : "Serialize::models({$v})",
            'flowList' => $json ? "MessageFlowItem::wrapAll({$v})" : "MessageFlowItem::listToArray({$v})",
            'stringMap', 'object' => $json ? "Serialize::jsonObject({$v})" : $v,
            'objectList' => $json ? "Serialize::jsonObjects({$v})" : $v,
            default => $v,
        };
        $lines[] = '            ' . export($p->name) . " => {$expr},";
    }
    $lines[] = '        ]);';
    $lines[] = '    }';

    return $lines;
}

function responseReader(Prop $p): string
{
    $n = export($p->name);

    return match ($p->kind) {
        'string' => "\$in->string({$n})",
        'int' => "\$in->int({$n})",
        'number' => "\$in->number({$n})",
        'bool' => "\$in->bool({$n})",
        'stringList' => "\$in->stringList({$n})",
        'stringMap' => "\$in->stringMap({$n})",
        'object' => "\$in->object({$n})",
        'objectList' => "\$in->objectList({$n})",
        'model' => "\$in->model({$n}, {$p->ref}::class)",
        'modelList' => "\$in->modelList({$n}, {$p->ref}::class)",
        default => throw new RuntimeException($p->kind),
    };
}

function responseClass(Model $model): string
{
    $uses = ['Bizgo\\Exception\\InvalidResponseException', 'Bizgo\\Internal\\ResponseData', 'Bizgo\\Internal\\Serialize'];
    prepareRefs($model, $uses);
    foreach ($model->props as $p) {
        if ($p->class !== null && Registry::$models[$p->class]->request) {
            throw new RuntimeException("{$model->key()}.{$p->name}: a response model refers to request model {$p->class}");
        }
    }
    if ($model->ns !== '') {
        $uses[] = 'Bizgo\\Model\\ResponseModel';
    }
    $lines = classDoc($model, [
        '응답 모델입니다. 모든 필드는 없을 수 있고(null), 스펙에 없는 새 필드는 `$extra`에 그대로 담깁니다.',
    ]);
    $lines[] = "final readonly class {$model->name} implements ResponseModel";
    $lines[] = '{';
    $lines = array_merge($lines, fieldsConst($model));
    $requiredProps = array_values(array_filter($model->props, static fn (Prop $p): bool => $p->required));
    $required = array_map(static fn (Prop $p): string => export($p->name), $requiredProps);
    $lines[] = '';
    $lines[] = '    /** Fields the spec marks as required. They are not enforced when parsing a response. */';
    $lines[] = '    public const REQUIRED = [' . implode(', ', $required) . '];';
    $lines[] = '';
    $paramDocs = [];
    foreach ($model->props as $p) {
        $paramDocs[] = '@param ' . docType($p, true, false) . ' $' . $p->php . ' ' . firstSentence($p->description);
    }
    $paramDocs[] = '@param array<string, mixed> $extra fields the spec does not describe (new server fields), by API name';
    $lines = array_merge($lines, docBlock($paramDocs, '    '));
    $lines[] = '    public function __construct(';
    foreach ($model->props as $p) {
        $lines = array_merge($lines, propDoc($p, false));
        $lines[] = '        public ' . phpType($p, true) . ' $' . $p->php . ' = null,';
    }
    $lines[] = '        /** Fields the spec does not describe (new server fields), by API name. */';
    $lines[] = '        public array $extra = [],';
    $lines[] = '    ) {';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /**';
    $lines[] = '     * @param array<array-key, mixed> $data decoded JSON object';
    $lines[] = '     *';
    $lines[] = '     * @throws InvalidResponseException when a field has an incompatible type';
    $lines[] = '     */';
    $lines[] = '    public static function fromArray(#[\SensitiveParameter] array $data, string $path = \'\'): static';
    $lines[] = '    {';
    $lines[] = '        $in = new ResponseData($data, $path, self::FIELDS);';
    $lines[] = '';
    $lines[] = '        return new static(';
    foreach ($model->props as $p) {
        $lines[] = "            {$p->php}: " . responseReader($p) . ',';
    }
    $lines[] = '            extra: $in->extra(),';
    $lines[] = '        );';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /**';
    $lines[] = '     * The fields with API names, followed by the unknown fields from `$extra`. Unset fields are omitted.';
    $lines[] = '     *';
    $lines[] = '     * @return array<string, mixed>';
    $lines[] = '     */';
    $lines[] = '    public function toArray(): array';
    $lines[] = '    {';
    $lines[] = '        return Serialize::compact([';
    foreach ($model->props as $p) {
        $v = '$this->' . $p->php;
        $expr = match ($p->kind) {
            'model' => $v . '?->toArray()',
            'modelList' => "Serialize::models({$v})",
            default => $v,
        };
        $lines[] = '            ' . export($p->name) . " => {$expr},";
    }
    $lines[] = '        ]) + $this->extra;';
    $lines[] = '    }';
    $lines = array_merge($lines, maskMethods($model, $uses));
    $lines[] = '}';

    return phpFile($lines, $uses, $model->ns);
}

/**
 * `__debugInfo()` and `__toString()` that mask phone numbers and hide message content (SDK-DESIGN.md §12.11).
 * The property values themselves are unchanged.
 *
 * @param list<string> $uses
 *
 * @return list<string>
 */
function maskMethods(Model $model, array &$uses): array
{
    $uses[] = 'Bizgo\\Internal\\Mask';

    return [
        '',
        '    /**',
        '     * The fields for var_dump()/print_r(): phone numbers masked (`010****0000`), message content and personal',
        '     * data shown as their length only. The properties keep the real values.',
        '     *',
        '     * @return array<string, mixed>',
        '     */',
        '    public function __debugInfo(): array',
        '    {',
        '        return Mask::fields(get_object_vars($this));',
        '    }',
        '',
        '    /** A log-safe description, masked like {@see self::__debugInfo()}. */',
        '    public function __toString(): string',
        '    {',
        "        return Mask::describe('{$model->name}', get_object_vars(\$this));",
        '    }',
    ];
}

/** @param array<string, string> $channels */
function channelInterface(array $channels): string
{
    $lines = ['/**', ' * A channel message you can put in `$client->send->omni(messages: [...])`.', ' *'];
    $lines[] = ' * | messageFlow key | class |';
    $lines[] = ' * |---|---|';
    foreach ($channels as $key => $class) {
        $lines[] = " * | `{$key}` | {@see {$class}} |";
    }
    $lines[] = ' */';
    $lines[] = 'interface ChannelMessage extends RequestModel';
    $lines[] = '{';
    $lines[] = '    /** The `messageFlow` channel key, for example `sms` or `alimtalk`. */';
    $lines[] = '    public function channelKey(): string;';
    $lines[] = '}';

    return phpFile($lines, []);
}

/**
 * @param array<string, string> $channels
 * @param array<string, mixed> $schema
 */
function flowItemClass(array $channels, array $schema): string
{
    $keys = implode(', ', array_keys($channels));
    $lines = classDoc(
        (static function () use ($schema): Model {
            $m = new Model();
            $m->description = trim(optionalStr($schema, 'description', FLOW_UNION) ?? '');

            return $m;
        })(),
        ['`messageFlow` 항목 1개입니다. 채널 메시지 하나를 `{"<채널 키>": {...}}` 형태로 감쌉니다.'],
    );
    $lines[] = 'final readonly class MessageFlowItem implements RequestModel';
    $lines[] = '{';
    $lines[] = '    /** @var array<string, class-string<ChannelMessage>> channel key => message class */';
    $lines[] = '    public const CHANNELS = [';
    foreach ($channels as $key => $class) {
        $lines[] = "        '{$key}' => {$class}::class,";
    }
    $lines[] = '    ];';
    $lines[] = '';
    $lines[] = '    public function __construct(';
    $lines[] = '        /** The channel message. */';
    $lines[] = '        public ChannelMessage $message,';
    $lines[] = '    ) {';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /** Wraps a channel message; a flow item is returned as is. */';
    $lines[] = '    public static function of(ChannelMessage|self $item): self';
    $lines[] = '    {';
    $lines[] = '        return $item instanceof self ? $item : new self($item);';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /** The channel key, for example `sms`. */';
    $lines[] = '    public function channelKey(): string';
    $lines[] = '    {';
    $lines[] = '        return $this->message->channelKey();';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /**';
    $lines[] = "     * Builds the item from `['<channel key>' => [...]]`. Exactly one channel key ({$keys}) is allowed.";
    $lines[] = '     *';
    $lines[] = '     * @param array<array-key, mixed> $data';
    $lines[] = '     *';
    $lines[] = '     * @throws ValidationException';
    $lines[] = '     */';
    $lines[] = '    public static function fromArray(#[\SensitiveParameter] array $data, string $path = \'\'): static';
    $lines[] = '    {';
    $lines[] = '        $keys = array_map(\'strval\', array_keys($data));';
    $lines[] = '        if (count($keys) !== 1 || !isset(self::CHANNELS[$keys[0]])) {';
    $lines[] = '            throw ValidationException::of(';
    $lines[] = '                $path,';
    $lines[] = "                'messageFlow 항목에는 채널 키({$keys}) 중 정확히 하나가 있어야 합니다',";
    $lines[] = '            );';
    $lines[] = '        }';
    $lines[] = '        $key = $keys[0];';
    $lines[] = '        $value = $data[$key];';
    $lines[] = '        if (!is_array($value)) {';
    $lines[] = '            throw ValidationException::of(ValidationException::join($path, $key), \'객체(키-값 배열)여야 합니다\');';
    $lines[] = '        }';
    $lines[] = '';
    $lines[] = '        return new static(self::CHANNELS[$key]::fromArray($value, ValidationException::join($path, $key)));';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /** @return array<string, mixed> */';
    $lines[] = '    public function toArray(): array';
    $lines[] = '    {';
    $lines[] = '        return [$this->channelKey() => $this->message->toArray()];';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /** @return array<string, mixed> */';
    $lines[] = '    public function jsonSerialize(): array';
    $lines[] = '    {';
    $lines[] = '        return [$this->channelKey() => $this->message];';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /**';
    $lines[] = '     * @param list<self|ChannelMessage>|null $items';
    $lines[] = '     *';
    $lines[] = '     * @return list<self>|null';
    $lines[] = '     */';
    $lines[] = '    public static function wrapAll(?array $items): ?array';
    $lines[] = '    {';
    $lines[] = '        return $items === null ? null : array_map(self::of(...), $items);';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /**';
    $lines[] = '     * @param list<self|ChannelMessage>|null $items';
    $lines[] = '     *';
    $lines[] = '     * @return list<array<string, mixed>>|null';
    $lines[] = '     */';
    $lines[] = '    public static function listToArray(?array $items): ?array';
    $lines[] = '    {';
    $lines[] = '        if ($items === null) {';
    $lines[] = '            return null;';
    $lines[] = '        }';
    $lines[] = '';
    $lines[] = '        return array_map(static fn (self|ChannelMessage $i): array => self::of($i)->toArray(), $items);';
    $lines[] = '    }';
    $lines[] = '}';

    return phpFile($lines, ['Bizgo\\Exception\\ValidationException']);
}

function serviceCodes(): string
{
    $data = map(json_decode((string) file_get_contents(ERROR_CODES), true, 512, JSON_THROW_ON_ERROR), 'error-codes');
    $rows = [];
    foreach (items($data['service'] ?? null, 'error-codes.service') as $row) {
        $entry = map($row, 'error-codes.service[]');
        $code = str($entry['code'] ?? null, 'error-codes.service[].code');
        $status = (int) (optionalStr($entry, 'httpStatus', $code) ?? '0');
        $text = optionalStr($entry, 'descriptionKo', $code) ?? '';
        $text = $text !== '' ? $text : (optionalStr($entry, 'result', $code) ?? '');
        if (isset($rows[$code])) {
            throw new RuntimeException("error-codes: duplicate service code {$code}");
        }
        $rows[$code] = '        ' . export($code) . " => [{$status}, " . export($text) . '],';
    }
    $lines = [
        '<?php',
        '',
        '// Generated from spec/error-codes.json by bin/generate-models.php. Do not edit.',
        '',
        'declare(strict_types=1);',
        '',
        'namespace Bizgo\\Exception;',
        '',
        '/**',
        ' * Service-layer (`data.code`) result codes with their documented HTTP status and Korean description.',
        ' *',
        ' * Gateway-layer codes (`common.authCode`) are not in this table: the same code can mean something else there.',
        ' */',
        'final class ServiceCodes',
        '{',
        '    public const SOURCE = ' . export(optionalStr($data, 'source', 'error-codes') ?? '') . ';',
        '',
        '    /** @var array<string, array{int, string}> code => [documented HTTP status (0 = not documented), description] */',
        '    public const CODES = [',
        ...array_values($rows),
        '    ];',
        '',
        '    private function __construct()',
        '    {',
        '    }',
        '',
        '    /** The documented HTTP status of a service code, or null if unknown. */',
        '    public static function httpStatus(string $code): ?int',
        '    {',
        '        $status = self::CODES[$code][0] ?? 0;',
        '',
        '        return $status > 0 ? $status : null;',
        '    }',
        '',
        '    /** The Korean description of a service code, or null if unknown. */',
        '    public static function describe(string $code): ?string',
        '    {',
        '        return self::CODES[$code][1] ?? null;',
        '    }',
        '}',
    ];

    return implode("\n", $lines) . "\n";
}
