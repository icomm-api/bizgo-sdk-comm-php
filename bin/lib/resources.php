<?php

/**
 * Resource methods, the operation registry and webhook parsers, generated from the `x-sdk-*` metadata of the
 * spec (bizgo-api-spec AGENTS.md rule 11, SDK-DESIGN.md §10). Used by bin/generate-models.php, which builds the
 * model registry first: result and pagination paths are resolved against the generated models.
 */

declare(strict_types=1);

const RESOURCE_DIR = 'src/Resource';
/** Resources whose class is written by hand. Their generated methods go into a `<Class>Operations` trait. */
const HANDWRITTEN_RESOURCES = ['send' => 'Send', 'files' => 'Files', 'reports' => 'Reports', 'messages' => 'Messages'];
/** Webhooks parsed by hand-written methods of WebhookReceiver. */
const HANDWRITTEN_WEBHOOKS = ['report', 'mo'];
/** Header parameters that mark a signed webhook (report, MO); webhooks without them (counsel) are not signed. */
const SIGNATURE_HEADERS = ['X-IB-Timestamp', 'X-IB-Signature'];
/** `x-format` values of date/time query parameters that also accept a DateTimeInterface. */
const DATE_FORMATS = ['YYYYMMDD', 'yyyyMMdd', 'yyyy-MM-dd HH:mm:ss', "yyyy-MM-dd'T'HH:mm:ss", "yyyy-MM-dd'T'HH:mm:ssXXX", 'yyyyMMddHHmmss'];
const WIDTH = 108;

/** The repository root with forward slashes (the keys of the generated file map). */
function sdkRoot(): string
{
    return str_replace('\\', '/', dirname(__DIR__, 2));
}

final class OpParam
{
    public string $api = '';
    /** path|query|header|form */
    public string $in = 'query';
    public bool $required = false;
    /** text|int|number|bool|array|date|file|files|json */
    public string $kind = 'text';
    public ?string $format = null;
    /** Request model of a JSON part. */
    public ?string $model = null;
    /** @var list<string> fields a JSON part additionally requires (allOf + required) */
    public array $extraRequired = [];
    public string $description = '';
    public ?string $unverified = null;
    /** @var array<string, mixed> */
    public array $schema = [];
}

final class Op
{
    public string $id = '';
    public string $method = '';
    public string $path = '';
    public string $resource = '';
    public string $name = '';
    public string $retry = 'safe';
    public string $rate = 'other';
    public string $summary = '';
    public string $description = '';
    public ?string $unverified = null;
    public bool $deprecated = false;
    public ?string $source = null;
    /** @var list<OpParam> */
    public array $params = [];
    /** JSON request body model. */
    public ?string $body = null;
    /** The JSON body schema has both `idempotencyKey` and `idempotencyTtl` (Transport fills the default TTL). */
    public bool $idempotency = false;
    public bool $form = false;
    /** Multipart body schema. */
    public ?string $formSchema = null;
    public string $response = '';
    /** @var list<string> */
    public array $result = ['data', 'data'];
    public bool $explicitResult = false;
    /** @var array<string, string>|null */
    public ?array $pagination = null;
    public bool $handwritten = false;
    /** The multipart form variant of an operation that also takes JSON. */
    public ?Op $withFiles = null;
    /** True for that variant: same operationId, not a registry entry of its own. */
    public bool $variant = false;
}

// ---------------------------------------------------------------------------------------------------------------
// reading the spec

/**
 * @param array<string, mixed> $spec
 *
 * @return list<Op>
 */
function specOperations(array $spec): array
{
    $components = map($spec['components'] ?? null, 'components');
    $shared = map($components['parameters'] ?? [], 'components.parameters');
    $schemas = map($components['schemas'] ?? null, 'components.schemas');
    $ops = [];
    $ids = [];
    foreach (map($spec['paths'] ?? null, 'paths') as $path => $item) {
        $item = map($item, $path);
        $common = isset($item['parameters']) ? items($item['parameters'], "{$path}.parameters") : [];
        foreach ($item as $method => $raw) {
            if ($method === 'parameters') {
                continue;
            }
            if (!in_array($method, ['get', 'post', 'put', 'delete', 'patch'], true)) {
                throw new RuntimeException("{$path}: unsupported key {$method}");
            }
            $op = parseOperation($path, strtoupper($method), map($raw, "{$path}.{$method}"), $common, $shared, $schemas);
            if (isset($ids[$op->id])) {
                throw new RuntimeException("duplicate operationId {$op->id}");
            }
            $ids[$op->id] = true;
            $ops[] = $op;
            if ($op->withFiles !== null) {
                $ops[] = $op->withFiles;
            }
        }
    }
    $names = [];
    foreach ($ops as $op) {
        $key = $op->resource . '.' . $op->name;
        if (isset($names[$key])) {
            throw new RuntimeException("{$op->id}: x-sdk-resource/x-sdk-method {$key} is also used by {$names[$key]}");
        }
        $names[$key] = $op->id;
    }

    return $ops;
}

/**
 * @param array<string, mixed> $raw
 * @param list<mixed> $common
 * @param array<string, mixed> $shared
 * @param array<string, mixed> $schemas
 */
function parseOperation(string $path, string $method, array $raw, array $common, array $shared, array $schemas): Op
{
    $op = new Op();
    $op->id = str($raw['operationId'] ?? null, "{$path}.operationId");
    $where = $op->id;
    $op->method = $method;
    $op->path = $path;
    $op->resource = str($raw['x-sdk-resource'] ?? null, "{$where}.x-sdk-resource");
    $op->name = str($raw['x-sdk-method'] ?? null, "{$where}.x-sdk-method");
    $op->retry = str($raw['x-sdk-retry'] ?? null, "{$where}.x-sdk-retry");
    $op->rate = isset($raw['x-sdk-rate']) ? str($raw['x-sdk-rate'], "{$where}.x-sdk-rate") : 'other';
    if (!in_array($op->retry, ['safe', 'rate_limit_only'], true) || !in_array($op->rate, ['send', 'other'], true)) {
        throw new RuntimeException("{$where}: unknown x-sdk-retry/x-sdk-rate value");
    }
    if (preg_match('/^[a-z][A-Za-z0-9]*(\.[a-z][A-Za-z0-9]*)*$/', $op->resource) !== 1 || preg_match('/^[a-z][A-Za-z0-9]*$/', $op->name) !== 1) {
        throw new RuntimeException("{$where}: x-sdk-resource/x-sdk-method must be camelCase identifiers");
    }
    $op->summary = trim(optionalStr($raw, 'summary', $where) ?? '');
    $op->description = trim(optionalStr($raw, 'description', $where) ?? '');
    $op->unverified = optionalStr($raw, 'x-unverified', $where);
    $op->deprecated = ($raw['deprecated'] ?? false) === true;
    $op->source = optionalStr($raw, 'x-source', $where);
    if (isset($raw['x-sdk-result'])) {
        $op->result = explode('.', str($raw['x-sdk-result'], "{$where}.x-sdk-result"));
        $op->explicitResult = true;
    }
    if (isset($raw['x-sdk-pagination'])) {
        $pagination = [];
        foreach (map($raw['x-sdk-pagination'], "{$where}.x-sdk-pagination") as $key => $value) {
            $pagination[$key] = str($value, "{$where}.x-sdk-pagination.{$key}");
        }
        if (!in_array($pagination['style'] ?? '', ['cursor', 'page', 'offset'], true) || !isset($pagination['request'], $pagination['items'])) {
            throw new RuntimeException("{$where}: unsupported x-sdk-pagination");
        }
        $op->pagination = $pagination;
    }
    foreach (array_keys($raw) as $key) {
        $known = ['operationId', 'tags', 'summary', 'description', 'x-source', 'x-sdk-resource', 'x-sdk-method', 'x-sdk-retry',
            'x-sdk-rate', 'x-sdk-result', 'x-sdk-pagination', 'x-unverified', 'deprecated', 'requestBody', 'responses', 'parameters'];
        if (!in_array($key, $known, true)) {
            throw new RuntimeException("{$where}: unsupported operation key {$key}");
        }
    }

    // parameters: path-level first, operation-level override by (in, name)
    $byKey = [];
    foreach (array_merge($common, isset($raw['parameters']) ? items($raw['parameters'], "{$where}.parameters") : []) as $p) {
        $p = map($p, "{$where}.parameters[]");
        if (isset($p['$ref'])) {
            $ref = str($p['$ref'], $where);
            $p = map($shared[substr($ref, strlen('#/components/parameters/'))] ?? null, $ref);
        }
        $param = new OpParam();
        $param->api = str($p['name'] ?? null, "{$where}.parameters[].name");
        $param->in = str($p['in'] ?? null, "{$where}.{$param->api}.in");
        $param->required = ($p['required'] ?? false) === true;
        $param->description = trim(optionalStr($p, 'description', $where) ?? '');
        $param->unverified = optionalStr($p, 'x-unverified', $where);
        $param->schema = map($p['schema'] ?? null, "{$where}.{$param->api}.schema");
        if (!in_array($param->in, ['path', 'query', 'header'], true)) {
            throw new RuntimeException("{$where}.{$param->api}: unsupported parameter location {$param->in}");
        }
        if (($p['explode'] ?? false) === true) {
            throw new RuntimeException("{$where}.{$param->api}: exploded query arrays are not supported");
        }
        $param->kind = paramKind($param, "{$where}.{$param->api}");
        $byKey[$param->in . ' ' . $param->api] = $param;
    }
    preg_match_all('/\{([A-Za-z0-9_]+)\}/', $path, $m);
    $params = [];
    foreach ($m[1] as $name) {
        $param = $byKey['path ' . $name] ?? throw new RuntimeException("{$where}: path parameter {$name} is not declared");
        $param->required = true;
        $params[] = $param;
        unset($byKey['path ' . $name]);
    }
    foreach ($byKey as $key => $param) {
        if ($param->in === 'path') {
            throw new RuntimeException("{$where}: path parameter {$key} is not in the path");
        }
        $params[] = $param;
    }

    // request body
    if (isset($raw['requestBody'])) {
        $content = map(map($raw['requestBody'], "{$where}.requestBody")['content'] ?? null, "{$where}.requestBody.content");
        if (isset($content['application/json'])) {
            $schema = map(map($content['application/json'], $where)['schema'] ?? null, "{$where}.requestBody.schema");
            $op->body = refName(str($schema['$ref'] ?? null, "{$where}.requestBody.schema.\$ref"));
            $bodyProps = map(map($schemas[$op->body] ?? null, $op->body)['properties'] ?? [], "{$op->body}.properties");
            $op->idempotency = isset($bodyProps['idempotencyKey'], $bodyProps['idempotencyTtl']);
            if (isset($content['multipart/form-data'])) {
                // JSON and multipart (requestAlimtalkTemplateInspection): `<method>WithFiles()` sends the multipart form
                $media = map($content['multipart/form-data'], $where);
                $variant = clone $op;
                $variant->name = $op->name . 'WithFiles';
                $variant->body = null;
                $variant->idempotency = false;
                $variant->form = true;
                $variant->variant = true;
                $variant->formSchema = refName(str(map($media['schema'] ?? null, $where)['$ref'] ?? null, "{$where}.multipart"));
                $variant->params = array_merge($params, formParams($variant, map($schemas[$variant->formSchema] ?? null, $variant->formSchema), $media));
                $op->withFiles = $variant;
            }
        } elseif (isset($content['multipart/form-data'])) {
            $media = map($content['multipart/form-data'], $where);
            $op->form = true;
            $op->formSchema = refName(str(map($media['schema'] ?? null, $where)['$ref'] ?? null, "{$where}.multipart"));
            $params = array_merge($params, formParams($op, map($schemas[$op->formSchema] ?? null, $op->formSchema), $media));
        } else {
            throw new RuntimeException("{$where}: unsupported request content type");
        }
    }

    // response
    $responses = map($raw['responses'] ?? null, "{$where}.responses");
    $ok = map(map(map($responses['200'] ?? null, "{$where}.200")['content'] ?? null, "{$where}.200.content")['application/json'] ?? null, $where);
    $op->response = refName(str(map($ok['schema'] ?? null, $where)['$ref'] ?? null, "{$where}.200.schema"));
    if ($op->withFiles !== null) {
        $op->withFiles->response = $op->response;
    }

    $seen = ['body' => true];
    foreach ($params as $param) {
        if (preg_match('/^[a-z][A-Za-z0-9]*$/', $param->api) !== 1 || isset($seen[$param->api])) {
            throw new RuntimeException("{$where}.{$param->api}: not usable as a PHP parameter name");
        }
        $seen[$param->api] = true;
    }
    $op->params = $params;

    return $op;
}

function paramKind(OpParam $param, string $where): string
{
    $type = primaryType($param->schema, $where);
    $format = optionalStr($param->schema, 'x-format', $where);
    $param->format = $format;
    foreach (array_keys($param->schema) as $key) {
        $known = ['type', 'description', 'example', 'format', 'x-format', 'pattern', 'enum', 'default', 'maxLength', 'minLength',
            'minimum', 'maximum', 'items', 'minItems', 'maxItems', 'x-known-values', 'x-unverified'];
        if (!in_array($key, $known, true)) {
            throw new RuntimeException("{$where}: unsupported parameter schema keyword {$key}");
        }
    }
    if ($param->in !== 'query' && $type !== 'string') {
        throw new RuntimeException("{$where}: only string path/header parameters are supported");
    }

    return match ($type) {
        'string' => $format !== null && in_array($format, DATE_FORMATS, true) ? 'date' : 'text',
        'integer' => 'int',
        'number' => 'number',
        'boolean' => 'bool',
        'array' => primaryType(map($param->schema['items'] ?? null, $where), "{$where}[]") === 'string'
            ? 'array' : throw new RuntimeException("{$where}: only string arrays are supported"),
        default => throw new RuntimeException("{$where}: unsupported parameter type {$type}"),
    };
}

/**
 * @param array<string, mixed> $schema multipart body schema
 * @param array<string, mixed> $media
 *
 * @return list<OpParam>
 */
function formParams(Op $op, array $schema, array $media): array
{
    $where = "{$op->id}.multipart";
    $encoding = isset($media['encoding']) ? map($media['encoding'], "{$where}.encoding") : [];
    $required = isset($schema['required']) ? strings($schema['required'], "{$where}.required") : [];
    $out = [];
    foreach (map($schema['properties'] ?? null, "{$where}.properties") as $name => $prop) {
        $prop = map($prop, "{$where}.{$name}");
        $param = new OpParam();
        $param->api = $name;
        $param->in = 'form';
        $param->required = in_array($name, $required, true);
        $param->description = trim(optionalStr($prop, 'description', $where) ?? '');
        $param->unverified = optionalStr($prop, 'x-unverified', $where);
        $param->schema = $prop;
        $json = isset($encoding[$name]) && (map($encoding[$name], $where)['contentType'] ?? null) === 'application/json';
        if ($json || isset($prop['allOf']) || isset($prop['$ref'])) {
            $param->kind = 'json';
            $parts = isset($prop['allOf']) ? items($prop['allOf'], "{$where}.{$name}.allOf") : [$prop];
            foreach ($parts as $part) {
                $part = map($part, "{$where}.{$name}");
                if (isset($part['$ref'])) {
                    $param->model = refName(str($part['$ref'], $where));
                } else {
                    $param->extraRequired = array_merge($param->extraRequired, strings($part['required'] ?? [], $where));
                    if (isset($part['properties'])) {
                        throw new RuntimeException("{$where}.{$name}: extra properties on a JSON part are not supported");
                    }
                }
            }
            if ($param->model === null) {
                throw new RuntimeException("{$where}.{$name}: a JSON part needs a \$ref model");
            }
        } elseif (($prop['format'] ?? null) === 'binary') {
            $param->kind = 'file';
        } elseif (($prop['type'] ?? null) === 'array' && (map($prop['items'] ?? null, $where)['format'] ?? null) === 'binary') {
            $param->kind = 'files';
        } elseif (($prop['type'] ?? null) === 'string') {
            $param->kind = 'text';
        } else {
            throw new RuntimeException("{$where}.{$name}: unsupported multipart field");
        }
        $out[] = $param;
    }

    return $out;
}

/**
 * @param list<Op> $ops
 *
 * @return list<string>
 */
function multipartSchemas(array $ops): array
{
    return array_values(array_filter(array_map(static fn (Op $op): ?string => $op->formSchema, $ops), static fn (?string $s): bool => $s !== null));
}

/**
 * Schemas sent to the API: JSON bodies and JSON parts of multipart bodies.
 *
 * @param list<Op> $ops
 * @param array<string, mixed> $schemas
 *
 * @return list<string>
 */
function requestRoots(array $ops, array $schemas): array
{
    $roots = [];
    foreach ($ops as $op) {
        if ($op->body !== null) {
            $roots[] = $op->body;
        }
        foreach ($op->params as $param) {
            if ($param->model !== null) {
                $roots[] = $param->model;
            }
        }
    }
    foreach (array_unique($roots) as $root) {
        if (!isset($schemas[$root])) {
            throw new RuntimeException("unknown request schema {$root}");
        }
    }

    return array_values(array_unique($roots));
}

/**
 * Schemas received from the API or from Bizgo webhooks.
 *
 * @param array<string, mixed> $spec
 * @param list<Op> $ops
 *
 * @return list<string>
 */
function responseRoots(array $spec, array $ops): array
{
    $roots = array_map(static fn (Op $op): string => $op->response, $ops);
    foreach (specWebhooks($spec) as $hook) {
        $roots[] = $hook['payload'];
        $roots[] = $hook['ack'];
    }

    return array_values(array_unique($roots));
}

/**
 * @param array<string, mixed> $spec
 *
 * @return list<array{name: string, id: string, payload: string, ack: string, signed: bool, summary: string, description: string}>
 */
function specWebhooks(array $spec): array
{
    $shared = map(map($spec['components'] ?? null, 'components')['parameters'] ?? [], 'components.parameters');
    $out = [];
    foreach (map($spec['webhooks'] ?? [], 'webhooks') as $key => $item) {
        $op = map(map($item, "webhooks.{$key}")['post'] ?? null, "webhooks.{$key}.post");
        $name = str($op['x-sdk-webhook'] ?? null, "webhooks.{$key}.x-sdk-webhook");
        // a webhook is signed when the spec declares both signature headers (required); one without them is unsigned
        $headers = [];
        foreach (isset($op['parameters']) ? items($op['parameters'], $key) : [] as $p) {
            $p = map($p, $key);
            if (isset($p['$ref'])) {
                $p = map($shared[substr(str($p['$ref'], $key), strlen('#/components/parameters/'))] ?? null, $key);
            }
            if (in_array($p['name'] ?? null, SIGNATURE_HEADERS, true)) {
                if (($p['required'] ?? false) !== true) {
                    throw new RuntimeException("webhooks.{$key}: signature header {$p['name']} must be required when declared");
                }
                $headers[] = $p['name'];
            }
        }
        if ($headers !== [] && count(array_unique($headers)) !== count(SIGNATURE_HEADERS)) {
            throw new RuntimeException("webhooks.{$key}: declare both signature headers or neither");
        }
        $signed = $headers !== [];
        $body = map(map(map($op['requestBody'] ?? null, $key)['content'] ?? null, $key)['application/json'] ?? null, $key);
        $ack = map(map(map(map($op['responses'] ?? null, $key)['200'] ?? null, $key)['content'] ?? null, $key)['application/json'] ?? null, $key);
        $out[] = [
            'name' => $name,
            'id' => str($op['operationId'] ?? null, $key),
            'payload' => refName(str(map($body['schema'] ?? null, $key)['$ref'] ?? null, $key)),
            'ack' => refName(str(map($ack['schema'] ?? null, $key)['$ref'] ?? null, $key)),
            'signed' => $signed,
            'summary' => trim(optionalStr($op, 'summary', $key) ?? ''),
            'description' => trim(optionalStr($op, 'description', $key) ?? ''),
        ];
    }

    return $out;
}

// ---------------------------------------------------------------------------------------------------------------
// output

/**
 * @param array<string, mixed> $spec
 * @param list<Op> $ops
 *
 * @return array<string, string> absolute path => code
 */
function resourceOutputs(array $spec, array $ops): array
{
    foreach ($ops as $op) {
        $root = explode('.', $op->resource)[0];
        if (isset(HANDWRITTEN_RESOURCES[$root])) {
            if ($root !== $op->resource) {
                throw new RuntimeException("{$op->id}: nested resources under the hand-written {$root} are not supported");
            }
            $op->handwritten = in_array($op->name, handwrittenMethods(HANDWRITTEN_RESOURCES[$root]), true);
        }
    }
    $tree = [];
    foreach ($ops as $op) {
        $tree[$op->resource]['ops'][] = $op;
        $parts = explode('.', $op->resource);
        for ($i = 1; $i < count($parts); $i++) {
            $parent = implode('.', array_slice($parts, 0, $i));
            $tree[$parent]['children'][$parts[$i]] = true;
        }
    }
    ksort($tree);
    $out = [];
    foreach ($tree as $resource => $node) {
        $nodeOps = $node['ops'] ?? [];
        $children = array_keys($node['children'] ?? []);
        $root = explode('.', $resource)[0];
        if (isset(HANDWRITTEN_RESOURCES[$resource])) {
            $generated = array_values(array_filter($nodeOps, static fn (Op $op): bool => !$op->handwritten));
            if ($generated === []) {
                continue;
            }
            $class = HANDWRITTEN_RESOURCES[$resource];
            $source = (string) file_get_contents(sdkRoot() . '/' . RESOURCE_DIR . "/{$class}.php");
            if (!str_contains($source, "use {$class}Operations;")) {
                throw new RuntimeException(RESOURCE_DIR . "/{$class}.php must `use {$class}Operations;` for the generated methods");
            }
            $out[sdkRoot() . '/' . RESOURCE_DIR . "/{$class}Operations.php"] = resourceFile($resource, $generated, [], true);
            continue;
        }
        if (isset(HANDWRITTEN_RESOURCES[$root])) {
            throw new RuntimeException("{$resource}: unexpected");
        }
        $out[sdkRoot() . '/' . RESOURCE_DIR . '/' . str_replace('\\', '/', classPath($resource)) . '.php'] = resourceFile($resource, $nodeOps, $children, false);
    }
    $top = array_values(array_filter(array_keys($tree), static fn (string $r): bool => !str_contains($r, '.') && !isset(HANDWRITTEN_RESOURCES[$r])));
    $out[sdkRoot() . '/src/GeneratedResources.php'] = clientTrait($top);
    $out[sdkRoot() . '/src/Internal/Operations.php'] = registryFile($ops);
    $out[sdkRoot() . '/src/Webhook/WebhookParsers.php'] = webhookTrait(specWebhooks($spec));

    return $out;
}

/** @return list<string> public method names of a hand-written resource class */
function handwrittenMethods(string $class): array
{
    $source = (string) file_get_contents(sdkRoot() . '/' . RESOURCE_DIR . "/{$class}.php");
    preg_match_all('/public function ([A-Za-z0-9_]+)\(/', $source, $m);

    return $m[1];
}

/** `alimtalk.templates` => `Alimtalk\Templates` */
function classPath(string $resource): string
{
    return implode('\\', array_map('ucfirst', explode('.', $resource)));
}

function classShort(string $resource): string
{
    $parts = explode('.', $resource);

    return ucfirst($parts[count($parts) - 1]);
}

function classNamespace(string $resource): string
{
    $parts = explode('.', $resource);
    array_pop($parts);

    return 'Bizgo\\Resource' . ($parts === [] ? '' : '\\' . implode('\\', array_map('ucfirst', $parts)));
}

/**
 * @param list<string> $lines
 * @param list<string> $uses
 */
function sourceFile(string $namespace, array $uses, array $lines): string
{
    $uses = array_values(array_unique($uses));
    sort($uses);
    $head = ['<?php', '', HEADER, '', 'declare(strict_types=1);', '', "namespace {$namespace};", ''];
    foreach ($uses as $use) {
        $head[] = "use {$use};";
    }
    if ($uses !== []) {
        $head[] = '';
    }

    return implode("\n", array_merge($head, $lines)) . "\n";
}

/**
 * @param list<Op> $ops
 * @param list<string> $children
 */
function resourceFile(string $resource, array $ops, array $children, bool $trait): string
{
    $uses = [];
    $body = [];
    $methods = [];
    foreach ($ops as $op) {
        foreach (operationMethods($op, $uses) as $name => $code) {
            if (isset($methods[$name])) {
                throw new RuntimeException("{$resource}: method {$name} is generated twice");
            }
            $methods[$name] = $code;
        }
    }
    if ($trait) {
        $class = HANDWRITTEN_RESOURCES[$resource] . 'Operations';
        $lines = [
            '/**',
            " * Methods of `\$client->{$resource}` generated from the spec. Used by the hand-written {@see "
                . HANDWRITTEN_RESOURCES[$resource] . '} class,',
            ' * whose convenience methods take precedence over generated ones with the same name.',
            ' *',
            ' * @internal',
            ' */',
            "trait {$class}",
            '{',
        ];
        $namespace = 'Bizgo\\Resource';
    } else {
        $class = classShort($resource);
        $namespace = classNamespace($resource);
        $uses[] = 'Bizgo\\Http\\Transport';
        $access = '$client->' . str_replace('.', '->', $resource);
        $lines = ['/**'];
        foreach (wrap("`{$access}`" . ($ops === [] ? ' (groups the resources below).' : ': ' . resourceSummary($ops) . '.'), WIDTH) as $line) {
            $lines[] = rtrim(' * ' . $line);
        }
        if ($ops !== []) {
            $lines[] = ' *';
            $lines[] = ' * Generated from the `x-sdk-*` metadata of the spec (SDK-DESIGN.md §10). Request bodies are validated before sending;';
            $lines[] = ' * results are the `x-sdk-result` part of the response (an empty object or list when the server sends none).';
        }
        $lines[] = ' */';
        $lines[] = "final class {$class}";
        $lines[] = '{';
        foreach ($children as $child) {
            $childClass = classShort($resource . '.' . $child);
            $uses[] = 'Bizgo\\Resource\\' . classPath($resource . '.' . $child);
            $lines[] = "    /** `{$access}->{$child}`. */";
            $lines[] = "    public readonly {$childClass} \${$child};";
            $lines[] = '';
        }
        $lines[] = '    /** @internal */';
        $lines[] = '    public function __construct(' . ($ops === [] ? 'Transport $transport' : 'private readonly Transport $transport') . ')';
        $lines[] = '    {';
        foreach ($children as $child) {
            $lines[] = "        \$this->{$child} = new " . classShort($resource . '.' . $child) . '($transport);';
        }
        $lines[] = '    }';
        if ($methods !== []) {
            $lines[] = '';
        }
        if (array_intersect($children, array_keys($methods)) !== []) {
            throw new RuntimeException("{$resource}: a child resource and a method share a name");
        }
    }
    $first = true;
    foreach ($methods as $code) {
        if (!$first) {
            $lines[] = '';
        }
        $first = false;
        array_push($lines, ...$code);
    }
    $lines[] = '}';

    return sourceFile($namespace, $uses, array_merge($lines, $body));
}

/** @param list<Op> $ops */
function resourceSummary(array $ops): string
{
    $tags = [];
    foreach ($ops as $op) {
        $tags[] = $op->name;
    }

    return implode(', ', array_map(static fn (string $n): string => $n . '()', $tags));
}

// ---------------------------------------------------------------------------------------------------------------
// one operation

/**
 * @param list<string> $uses
 *
 * @return array<string, list<string>> method name => lines
 */
function operationMethods(Op $op, array &$uses): array
{
    foreach (
        ['Bizgo\\Exception\\ApiException', 'Bizgo\\Exception\\ConnectionException', 'Bizgo\\Exception\\InvalidResponseException',
        'Bizgo\\Exception\\ValidationException', 'Bizgo\\Model'] as $use
    ) {
        $uses[] = $use;
    }
    $result = resolveResult($op);
    $params = orderedParams($op);
    $signature = signature($op, $params, $uses);
    $prepare = prepare($op, $params, $uses);
    $call = callExpression($op, $params, $prepare);
    $prepare = $prepare['lines'];
    $responseClass = 'Model\\' . $op->response;
    $throws = ['@throws ValidationException the request is invalid (checked before anything is sent)', '@throws ApiException',
        '@throws ConnectionException', '@throws InvalidResponseException'];
    $doc = methodDoc($op, $params);
    $out = [];

    if ($op->pagination !== null) {
        // a private method returns the whole response for the iterator; the public one picks x-sdk-result
        $raw = $op->name . 'Response';
        $out[$raw] = array_merge(
            ['    /**', "     * The whole response of {@see self::{$op->name}()}, for the iterator.", '     *'],
            array_map(static fn (string $t): string => '     * ' . $t, $throws),
            ['     */'],
            openMethod('private', $raw, $signature['lines'], $responseClass),
            $prepare,
            ['        return ' . $call . ';', '    }'],
        );
        $pick = pickExpression('$response', $result, $op);
        $args = array_map(static fn (OpParam $p): string => "{$p->api}: \${$p->api}", $params);
        if ($op->body !== null) {
            array_unshift($args, 'body: $body');
        }
        $out[$op->name] = array_merge(
            docLines($doc, $signature['docs'], $result['doc'], $throws),
            openMethod('public', $op->name, $signature['lines'], $result['type']),
            callLines('        $response = ', "\$this->{$raw}", $args, '        '),
            ['', "        return {$pick};", '    }'],
        );
        $iterator = 'iter' . ucfirst($op->name);
        $out[$iterator] = iteratorMethod($op, $params, $signature, $iterator, $raw, $throws, $uses);

        return $out;
    }

    $lines = docLines($doc, $signature['docs'], $result['doc'], $throws);
    array_push($lines, ...openMethod('public', $op->name, $signature['lines'], $result['type']));
    array_push($lines, ...$prepare);
    if ($result['kind'] === 'void') {
        $lines[] = "        {$call};";
    } else {
        $lines[] = "        \$response = {$call};";
        $lines[] = '';
        $lines[] = '        return ' . pickExpression('$response', $result, $op) . ';';
    }
    $lines[] = '    }';
    $out[$op->name] = $lines;

    return $out;
}

/** @return list<OpParam> path, then required, then optional (body is added separately after the path) */
function orderedParams(Op $op): array
{
    $path = array_values(array_filter($op->params, static fn (OpParam $p): bool => $p->in === 'path'));
    $rest = array_values(array_filter($op->params, static fn (OpParam $p): bool => $p->in !== 'path'));
    $required = array_values(array_filter($rest, static fn (OpParam $p): bool => $p->required));
    $optional = array_values(array_filter($rest, static fn (OpParam $p): bool => !$p->required));

    return array_merge($path, $required, $optional);
}

function modelRef(string $key): string
{
    return 'Model\\' . $key;
}

/**
 * @param list<OpParam> $params
 * @param list<string> $uses
 *
 * @return array{lines: list<string>, docs: list<string>}
 */
function signature(Op $op, array $params, array &$uses): array
{
    $lines = [];
    $docs = [];
    $pathCount = count(array_filter($params, static fn (OpParam $p): bool => $p->in === 'path'));
    $emit = static function (OpParam|string $p) use (&$lines, &$docs, &$uses): void {
        if (is_string($p)) { // the JSON body
            $model = modelRef($p);
            $lines[] = '        #[\\SensitiveParameter]';
            $lines[] = "        {$model}|array \$body,";
            $docs[] = "@param {$model}|array<string, mixed> \$body request body: a model, or an array with API field names "
                . '(unknown fields are rejected)';

            return;
        }
        [$type, $docType] = paramTypes($p, $uses);
        // path, query, header and form values can all carry phone numbers or other personal data
        $lines[] = '        #[\\SensitiveParameter]';
        $lines[] = "        {$type} \${$p->api}" . ($p->required ? '' : ' = null') . ',';
        $docs[] = "@param {$docType} \${$p->api} " . paramSummary($p);
    };
    foreach (array_slice($params, 0, $pathCount) as $p) {
        $emit($p);
    }
    if ($op->body !== null) {
        $emit($op->body);
    }
    foreach (array_slice($params, $pathCount) as $p) {
        $emit($p);
    }

    return ['lines' => $lines, 'docs' => $docs];
}

/**
 * @param list<string> $uses
 *
 * @return array{string, string} PHP type, PHPDoc type
 */
function paramTypes(OpParam $p, array &$uses): array
{
    $null = $p->required ? '' : '|null';
    switch ($p->kind) {
        case 'text':
            return [($p->required ? '' : '?') . 'string', 'string' . $null];
        case 'date':
            return ['\\DateTimeInterface|string' . $null, '\\DateTimeInterface|string' . $null];
        case 'int':
            return [($p->required ? '' : '?') . 'int', 'int' . $null];
        case 'number':
            return ['int|float' . $null, 'int|float' . $null];
        case 'bool':
            return [($p->required ? '' : '?') . 'bool', 'bool' . $null];
        case 'array':
            return ['string|array' . $null, 'string|list<string>' . $null];
        case 'file':
            $uses[] = 'Bizgo\\Upload';

            return ['mixed', 'string|\\SplFileInfo|Upload|resource' . $null];
        case 'files':
            $uses[] = 'Bizgo\\Upload';

            return [($p->required ? '' : '?') . 'array', 'list<string|\\SplFileInfo|Upload|resource>' . $null];
        case 'json':
            $model = modelRef((string) $p->model);

            return ["{$model}|array{$null}", "{$model}|array<string, mixed>{$null}"];
    }
    throw new RuntimeException($p->kind);
}

function paramSummary(OpParam $p): string
{
    $s = $p->schema;
    $text = $p->description === '' ? '' : firstSentence($p->description);
    $parts = [];
    $parts[] = match ($p->in) {
        'path' => '경로',
        'header' => '헤더',
        'form' => 'multipart',
        default => '쿼리',
    };
    if ($p->format !== null) {
        $parts[] = "형식 `{$p->format}`" . ($p->kind === 'date' ? '(DateTimeInterface는 KST로 변환)' : '');
    }
    if (isset($s['enum'])) {
        $parts[] = '허용 값: ' . codeList(strings($s['enum'], $p->api));
    }
    if (isset($s['default'])) {
        $parts[] = '서버 기본값 `' . export($s['default']) . '`';
    }
    if (isset($s['minimum']) || isset($s['maximum'])) {
        $c = scalars($s);
        $parts[] = '범위 ' . ($c['minimum'] ?? '') . '~' . ($c['maximum'] ?? '');
    }
    if (isset($s['maxLength'])) {
        $parts[] = '최대 ' . scalars($s)['maxLength'] . '자';
    }
    if ($p->kind === 'array') {
        $parts[] = '여러 값은 쉼표로 이어 보냄';
    }

    return trim($text . ' (' . implode(', ', $parts) . ')');
}

/**
 * Validation and conversion of the parameters, before anything is sent.
 *
 * @param list<OpParam> $params
 * @param list<string> $uses
 *
 * @return array{lines: list<string>, path: list<string>, headers: list<string>}
 */
function prepare(Op $op, array $params, array &$uses): array
{
    $lines = [];
    if ($op->body !== null) {
        $model = modelRef($op->body);
        $lines[] = "        \$body = is_array(\$body) ? {$model}::fromArray(\$body) : \$body;";
    }
    $checks = [];
    $query = [];
    $headers = [];
    $pathValues = [];
    $form = [];
    foreach ($params as $p) {
        $n = export($p->api);
        $v = '$' . $p->api;
        switch ($p->in) {
            case 'path':
                $pathValues[] = "{$n} => {$v}";
                break;
            case 'header':
                $uses[] = 'Bizgo\\Internal\\Params';
                $headers[] = "{$n} => Params::header({$n}, {$v})";
                break;
            case 'query':
                $uses[] = 'Bizgo\\Internal\\Params';
                $query[] = [$p, queryExpression($p)];
                break;
            case 'form':
                $form[] = $p;
                break;
        }
    }
    if ($query !== []) {
        $lines[] = '        $query = [';
        foreach ($query as [$p, $expr]) {
            $lines[] = '            ' . export($p->api) . " => {$expr},";
        }
        $lines[] = '        ];';
        foreach ($query as [$p]) {
            $checks = array_merge($checks, valueChecks($p, '$query[' . export($p->api) . ']'));
        }
    }
    foreach ($form as $p) {
        if ($p->kind === 'text') {
            $checks = array_merge($checks, valueChecks($p, '$' . $p->api));
        }
        if ($p->kind === 'json' && $p->extraRequired !== []) {
            $checks[] = 'Params::requiredFields(' . export($p->api) . ", \${$p->api}, " . export($p->extraRequired) . ')';
            $uses[] = 'Bizgo\\Internal\\Params';
        }
    }
    if ($checks !== []) {
        $uses[] = 'Bizgo\\Internal\\Check';
        $lines[] = '        Check::all(';
        foreach ($checks as $check) {
            $lines[] = "            {$check},";
        }
        $lines[] = '        );';
    }
    if ($form !== []) {
        $uses[] = 'Bizgo\\Internal\\Form';
        $lines[] = '        $form = (new Form())';
        foreach ($form as $p) {
            $n = export($p->api);
            $v = '$' . $p->api;
            $s = scalars($p->schema);
            $lines[] = match ($p->kind) {
                'file' => "            ->file({$n}, {$v}, " . export($p->required) . ', ' . ($s['x-max-bytes'] ?? 'null') . ')',
                'files' => "            ->files({$n}, {$v}, " . ($s['maxItems'] ?? 'null') . ')',
                'json' => "            ->json({$n}, " . (($p->required ? '' : "{$v} === null ? null : ") . "(is_array({$v}) ? "
                    . modelRef((string) $p->model) . "::fromArray({$v}, {$n}) : {$v})") . ')',
                default => "            ->text({$n}, {$v})",
            };
        }
        $lines[count($lines) - 1] .= ';';
    }
    if ($lines !== []) {
        $lines[] = '';
    }

    return ['lines' => $lines, 'path' => $pathValues, 'headers' => $headers];
}

function queryExpression(OpParam $p): string
{
    $n = export($p->api);
    $v = '$' . $p->api;

    return match ($p->kind) {
        'date' => "Params::date({$n}, {$v}, " . export((string) $p->format) . ')',
        'bool' => "Params::bool({$v})",
        'array' => "Params::csv({$n}, {$v}, " . export($p->schema['minItems'] ?? null) . ')',
        default => $v,
    };
}

/** @return list<string> Check calls for a text/int value */
function valueChecks(OpParam $p, string $v): array
{
    $s = $p->schema;
    $c = scalars($s);
    $n = export($p->api);
    $out = [];
    if (in_array($p->kind, ['text', 'date'], true)) {
        if (isset($s['maxLength'])) {
            $out[] = "Check::maxLength({$n}, {$v}, {$c['maxLength']})";
        }
        if (isset($s['minLength'])) {
            $out[] = "Check::minLength({$n}, {$v}, {$c['minLength']})";
        }
        if (isset($s['pattern'])) {
            $out[] = "Check::pattern({$n}, {$v}, " . export($s['pattern']) . ')';
        }
        if (isset($s['enum'])) {
            $out[] = "Check::enum({$n}, {$v}, " . export(strings($s['enum'], $p->api)) . ')';
        }
    }
    if (in_array($p->kind, ['int', 'number'], true) && (isset($s['minimum']) || isset($s['maximum']))) {
        $out[] = "Check::range({$n}, {$v}, " . export($s['minimum'] ?? null) . ', ' . export($s['maximum'] ?? null) . ')';
    }

    return $out;
}

/**
 * @param list<OpParam> $params
 * @param array{lines: list<string>, path: list<string>, headers: list<string>} $prepared
 */
function callExpression(Op $op, array $params, array $prepared): string
{
    $pathValues = $prepared['path'];
    $headers = $prepared['headers'];
    $args = [export($op->id)];
    if ($pathValues !== []) {
        $args[] = 'path: [' . implode(', ', $pathValues) . ']';
    }
    if (array_filter($params, static fn (OpParam $p): bool => $p->in === 'query') !== []) {
        $args[] = 'query: $query';
    }
    if ($op->body !== null) {
        $args[] = 'json: $body';
    }
    if ($op->form) {
        $args[] = 'multipart: $form->build()';
    }
    if ($headers !== []) {
        $args[] = 'headers: [' . implode(', ', $headers) . ']';
    }
    $args[] = 'response: Model\\' . $op->response . '::class';

    return "\$this->transport->call(\n            " . implode(",\n            ", $args) . ",\n        )";
}

/**
 * What a method returns: the `x-sdk-result` part of the response.
 *
 * @return array{kind: string, type: string, doc: string, chain: list<Prop>, fallback: ?string}
 */
function resolveResult(Op $op): array
{
    $chain = resolvePath($op->response, $op->result, $op->id, !$op->explicitResult);
    if ($chain === null) {
        return ['kind' => 'void', 'type' => 'void', 'doc' => '', 'chain' => [], 'fallback' => null];
    }
    $last = $chain[count($chain) - 1];
    $class = $last->class === null ? '' : modelRef($last->class);

    return match ($last->kind) {
        'model' => ['kind' => 'model', 'type' => $class, 'doc' => $class, 'chain' => $chain, 'fallback' => "new {$class}()"],
        'modelList' => ['kind' => 'list', 'type' => 'array', 'doc' => "list<{$class}>", 'chain' => $chain, 'fallback' => '[]'],
        'stringList' => ['kind' => 'list', 'type' => 'array', 'doc' => 'list<string>', 'chain' => $chain, 'fallback' => '[]'],
        'objectList' => ['kind' => 'list', 'type' => 'array', 'doc' => 'list<array<string, mixed>>', 'chain' => $chain, 'fallback' => '[]'],
        'object' => ['kind' => 'list', 'type' => 'array', 'doc' => 'array<string, mixed>', 'chain' => $chain, 'fallback' => '[]'],
        'stringMap' => ['kind' => 'list', 'type' => 'array', 'doc' => 'array<string, string>', 'chain' => $chain, 'fallback' => '[]'],
        'string' => ['kind' => 'scalar', 'type' => '?string', 'doc' => 'string|null', 'chain' => $chain, 'fallback' => null],
        'int' => ['kind' => 'scalar', 'type' => '?int', 'doc' => 'int|null', 'chain' => $chain, 'fallback' => null],
        'bool' => ['kind' => 'scalar', 'type' => '?bool', 'doc' => 'bool|null', 'chain' => $chain, 'fallback' => null],
        default => throw new RuntimeException("{$op->id}: unsupported result kind {$last->kind}"),
    };
}

/**
 * Walks `$path` (API names; `name[-1]` is the last list item) through the response models.
 *
 * @param list<string> $path
 *
 * @return list<Prop>|null null when the path does not exist and `$lenient` is set
 */
function resolvePath(string $model, array $path, string $where, bool $lenient): ?array
{
    $chain = [];
    $key = $model;
    foreach ($path as $i => $segment) {
        $current = Registry::$models[$key] ?? throw new RuntimeException("{$where}: unknown model {$key}");
        $last = str_ends_with($segment, '[-1]');
        $name = $last ? substr($segment, 0, -4) : $segment;
        $found = null;
        foreach ($current->props as $p) {
            if ($p->name === $name) {
                $found = $p;
            }
        }
        if ($found === null) {
            if ($lenient) {
                return null;
            }
            throw new RuntimeException("{$where}: {$key} has no field {$name} (path " . implode('.', $path) . ')');
        }
        if ($last) {
            if ($found->kind !== 'modelList') {
                throw new RuntimeException("{$where}: [-1] needs a list of models");
            }
            $item = clone $found;
            $item->kind = 'lastItem';
            $chain[] = $item;
        } else {
            $chain[] = $found;
        }
        if ($i < count($path) - 1 && !in_array($chain[count($chain) - 1]->kind, ['model', 'lastItem'], true)) {
            throw new RuntimeException("{$where}: {$name} is not an object (path " . implode('.', $path) . ')');
        }
        $key = (string) $found->class;
    }

    return $chain;
}

/**
 * `$response->data?->data?->x` for a resolved chain.
 *
 * @param list<Prop> $chain
 */
function chainExpression(string $root, array $chain, bool $coalesce = false): string
{
    // on the left of ??, plain -> is enough (isset semantics); elsewhere ?-> guards the null levels
    $coalesce = $coalesce && array_filter($chain, static fn (Prop $p): bool => $p->kind === 'lastItem') === [];
    $expr = $root;
    foreach ($chain as $i => $p) {
        $expr .= ($i === 0 || $coalesce ? '->' : '?->') . $p->php;
        if ($p->kind === 'lastItem') {
            $expr = "Pages::last({$expr})";
        }
    }

    return $expr;
}

/** @param array{kind: string, type: string, doc: string, chain: list<Prop>, fallback: ?string} $result */
function pickExpression(string $root, array $result, Op $op): string
{
    $expr = chainExpression($root, $result['chain'], $result['fallback'] !== null);

    return $result['fallback'] === null ? $expr : "{$expr} ?? {$result['fallback']}";
}

/**
 * @param list<OpParam> $params
 *
 * @return list<string>
 */
function methodDoc(Op $op, array $params): array
{
    $lines = [];
    $lines = array_merge($lines, wrap($op->summary === '' ? $op->id : $op->summary, WIDTH));
    if ($op->description !== '') {
        $lines[] = '';
        $lines = array_merge($lines, wrap(firstParagraph($op->description), WIDTH));
    }
    if ($op->unverified !== null) {
        $lines[] = '';
        $lines = array_merge($lines, wrap('확인 필요(x-unverified): ' . $op->unverified, WIDTH));
    }
    $lines[] = '';
    $retry = $op->retry === 'safe' ? '429·5xx·네트워크 오류 시 자동 재시도' : '429만 자동 재시도(두 번 실행되면 안 되는 요청)';
    $rate = $op->rate === 'send' ? 'send 버킷(수신자 수 기준)' : 'other 버킷(요청 1건)';
    $lines = array_merge($lines, wrap("`{$op->method} {$op->path}` (operationId `{$op->id}`). {$retry}. 속도 제한: {$rate}.", WIDTH));
    if ($op->source !== null) {
        $lines[] = '';
        $lines[] = '@see ' . $op->source;
    }
    if ($op->deprecated) {
        $lines[] = '';
        $lines[] = '@deprecated 스펙에서 사용 중단(deprecated)으로 표시된 API입니다.';
    }

    return $lines;
}

function firstParagraph(string $text): string
{
    return trim(preg_split('/\n\s*\n/', trim($text))[0] ?? '');
}

/**
 * @param list<string> $doc
 * @param list<string> $params
 * @param list<string> $throws
 *
 * @return list<string>
 */
function docLines(array $doc, array $params, string $return, array $throws): array
{
    $lines = $doc;
    if ($params !== []) {
        $lines[] = '';
        foreach ($params as $param) {
            $lines = array_merge($lines, wrapTag($param));
        }
    }
    if ($return !== '') {
        $lines[] = '';
        $lines[] = "@return {$return}";
    }
    $lines[] = '';
    $lines = array_merge($lines, $throws);

    return docBlock($lines, '    ');
}

/**
 * @param list<OpParam> $params
 * @param array{lines: list<string>, docs: list<string>} $signature
 * @param list<string> $throws
 * @param list<string> $uses
 *
 * @return list<string>
 */
function iteratorMethod(Op $op, array $params, array $signature, string $name, string $raw, array $throws, array &$uses): array
{
    $pg = $op->pagination ?? throw new LogicException();
    $style = $pg['style'];
    $request = $pg['request'];
    $items = resolvePath($op->response, explode('.', $pg['items']), $op->id, false) ?? throw new LogicException();
    $itemsProp = $items[count($items) - 1];
    if ($itemsProp->kind !== 'modelList') {
        throw new RuntimeException("{$op->id}: pagination items must be a list of models");
    }
    $itemClass = modelRef((string) $itemsProp->class);
    $requestParam = null;
    foreach ($params as $p) {
        if ($p->api === $request) {
            $requestParam = $p;
        }
    }
    if ($requestParam === null || $requestParam->required) {
        throw new RuntimeException("{$op->id}: pagination parameter {$request} must be an optional query parameter");
    }
    // the iterator takes every parameter but the page selector
    $lines = [];
    $docs = [];
    $keep = [];
    $i = 0;
    $pathCount = count(array_filter($params, static fn (OpParam $p): bool => $p->in === 'path'));
    $sigIndex = 0;
    $ordered = [];
    foreach (array_slice($params, 0, $pathCount) as $p) {
        $ordered[] = $p;
    }
    if ($op->body !== null) {
        $ordered[] = 'body';
    }
    foreach (array_slice($params, $pathCount) as $p) {
        $ordered[] = $p;
    }
    // signature lines come in the same order; a parameter spans 1 or 2 lines (#[\SensitiveParameter])
    $chunks = [];
    $current = [];
    foreach ($signature['lines'] as $line) {
        $current[] = $line;
        if (!str_ends_with(trim($line), ']')) {
            $chunks[] = $current;
            $current = [];
        }
    }
    foreach ($ordered as $idx => $p) {
        $isRequest = $p instanceof OpParam && $p->api === $request;
        if ($isRequest) {
            continue;
        }
        array_push($lines, ...$chunks[$idx]);
        $docs[] = $signature['docs'][$idx];
        $keep[] = $p instanceof OpParam ? $p->api : 'body';
    }
    unset($i, $sigIndex);
    $args = array_map(static fn (string $n): string => "{$n}: \${$n}", $keep);
    foreach ($keep as $n) {
        if (
            in_array($n, ['pageResponse', 'pageItems', 'pageItem', 'itemCount', 'itemsSeen', 'pagePosition', 'pageCursor', 'nextCursor',
            'hasNext', 'pageTotal'], true)
        ) {
            throw new RuntimeException("{$op->id}: parameter {$n} collides with an iterator variable");
        }
    }
    $itemsExpr = chainExpression('$pageResponse', $items, true) . ' ?? []';

    $doc = array_merge(
        ["Iterates over every item of {@see self::{$op->name}()}, fetching the following pages as needed."],
        [''],
        wrap(match ($style) {
            'cursor' => "`{$request}` 커서를 따라 다음 페이지를 가져옵니다. `hasNext`가 false이거나 커서가 없거나 움직이지 않으면 멈춥니다.",
            'page' => "`{$request}`를 1씩 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지"
                . (isset($pg['total']) ? ', `total` 도달' : '') . (isset($pg['hasNext']) ? ', `hasNext` false' : '') . '에서 멈춥니다.',
            default => "`{$request}`를 받은 항목 수만큼 늘리며 가져옵니다. 빈 페이지, 요청한 크기보다 작은 페이지"
                . (isset($pg['total']) ? ', `total` 도달' : '') . '에서 멈춥니다.',
        }, WIDTH),
    );
    $out = docLines($doc, $docs, "\\Generator<int, {$itemClass}>", $throws);
    array_push($out, ...openMethod('public', $name, $lines, '\\Generator'));
    if ($style === 'cursor') {
        $cursor = resolvePath($op->response, explode('.', $pg['response'] ?? throw new RuntimeException("{$op->id}: cursor needs response")), $op->id, false)
            ?? throw new LogicException();
        $hasNext = resolvePath($op->response, explode('.', $pg['hasNext'] ?? throw new RuntimeException("{$op->id}: cursor needs hasNext")), $op->id, false)
            ?? throw new LogicException();
        if (str_contains(chainExpression('', $cursor), 'Pages::')) {
            $uses[] = 'Bizgo\\Internal\\Pages';
        }
        $out[] = '        $pageCursor = null;';
        $out[] = '        while (true) {';
        array_push($out, ...callLines('            $pageResponse = ', "\$this->{$raw}", array_merge($args, ["{$request}: \$pageCursor"]), '            '));
        $out[] = "            foreach ({$itemsExpr} as \$pageItem) {";
        $out[] = '                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item';
        $out[] = '            }';
        $out[] = '            $nextCursor = ' . chainExpression('$pageResponse', $cursor) . ';';
        $out[] = '            $hasNext = ' . chainExpression('$pageResponse', $hasNext) . ';';
        $out[] = '            if ($hasNext !== true || $nextCursor === null || $nextCursor === $pageCursor) {';
        $out[] = '                return;';
        $out[] = '            }';
        $out[] = '            $pageCursor = $nextCursor;';
        $out[] = '        }';
        $out[] = '    }';

        return $out;
    }
    $sizeName = $pg['size'] ?? null;
    $sizeParam = null;
    foreach ($params as $p) {
        if ($p->api === $sizeName) {
            $sizeParam = $p;
        }
    }
    $default = $requestParam->schema['default'] ?? ($style === 'page' ? 1 : 0);
    if (!is_int($default)) {
        throw new RuntimeException("{$op->id}: page default must be an integer");
    }
    $out[] = "        \$pagePosition = {$default};";
    if (isset($pg['total'])) {
        $out[] = '        $itemsSeen = 0;';
    }
    $out[] = '        while (true) {';
    array_push($out, ...callLines('            $pageResponse = ', "\$this->{$raw}", array_merge($args, ["{$request}: \$pagePosition"]), '            '));
    $out[] = "            \$pageItems = {$itemsExpr};";
    $out[] = '            foreach ($pageItems as $pageItem) {';
    $out[] = '                yield $pageItem; // sequential keys across pages, so iterator_to_array() keeps every item';
    $out[] = '            }';
    $out[] = '            $itemCount = count($pageItems);';
    if (isset($pg['total'])) {
        $out[] = '            $itemsSeen += $itemCount;';
    }
    $sizeDefault = $sizeParam?->schema['default'] ?? null;
    if ($sizeParam !== null && is_int($sizeDefault)) {
        $out[] = "            if (\$itemCount === 0 || \$itemCount < (\${$sizeParam->api} ?? {$sizeDefault})) {";
    } elseif ($sizeParam !== null) {
        $out[] = "            if (\$itemCount === 0 || (\${$sizeParam->api} !== null && \$itemCount < \${$sizeParam->api})) {";
    } else {
        $out[] = '            if ($itemCount === 0) {';
    }
    $out[] = '                return;';
    $out[] = '            }';
    if (isset($pg['hasNext'])) {
        $hasNext = resolvePath($op->response, explode('.', $pg['hasNext']), $op->id, false) ?? throw new LogicException();
        $out[] = '            if (' . chainExpression('$pageResponse', $hasNext) . ' === false) {';
        $out[] = '                return;';
        $out[] = '            }';
    }
    if (isset($pg['total'])) {
        $total = resolvePath($op->response, explode('.', $pg['total']), $op->id, false) ?? throw new LogicException();
        $kind = $total[count($total) - 1]->kind;
        if (!in_array($kind, ['int', 'string'], true)) {
            throw new RuntimeException("{$op->id}: total must be an integer (or numeric string) field");
        }
        $expr = chainExpression('$pageResponse', $total);
        if ($kind === 'string') {
            $uses[] = 'Bizgo\Internal\Pages';
            $expr = "Pages::int({$expr})"; // documented as a string
        }
        $out[] = '            $pageTotal = ' . $expr . ';';
        $out[] = '            if ($pageTotal !== null && $itemsSeen >= $pageTotal) {';
        $out[] = '                return;';
        $out[] = '            }';
    }
    $out[] = $style === 'page' ? '            $pagePosition++;' : '            $pagePosition += $itemCount;';
    $out[] = '        }';
    $out[] = '    }';

    return $out;
}

// ---------------------------------------------------------------------------------------------------------------
// client, registry, webhooks

/** @param list<string> $top */
function clientTrait(array $top): string
{
    $uses = ['Bizgo\\Http\\Transport'];
    $lines = [
        '/**',
        ' * The resources generated from the spec, as properties of {@see BizgoClient}.',
        ' *',
        ' * @internal',
        ' */',
        'trait GeneratedResources',
        '{',
    ];
    foreach ($top as $resource) {
        $uses[] = 'Bizgo\\Resource\\' . classPath($resource);
        $lines[] = "    /** `\$client->{$resource}`. */";
        $lines[] = '    public readonly ' . classShort($resource) . " \${$resource};";
        $lines[] = '';
    }
    $lines[] = '    private function initGeneratedResources(Transport $transport): void';
    $lines[] = '    {';
    foreach ($top as $resource) {
        $lines[] = "        \$this->{$resource} = new " . classShort($resource) . '($transport);';
    }
    $lines[] = '    }';
    $lines[] = '}';

    return sourceFile('Bizgo', $uses, $lines);
}

/** @param list<Op> $ops */
function registryFile(array $ops): string
{
    $lines = [
        '/**',
        ' * Every operation of the spec: HTTP method, path template, `<resource>.<method>`, retry policy (`x-sdk-retry`),',
        ' * rate limit bucket (`x-sdk-rate`), whether a hand-written method implements it, the body type, whether the',
        ' * body has `idempotencyKey` and `idempotencyTtl` (then `Transport` sends the default TTL with a key that has none),',
        ' * the parameters (API name, location, required, kind) and the pagination style with its iterator method.',
        ' *',
        ' * Hooks and the testing tools see only these values (the path **template**, never real path values).',
        ' *',
        ' * @internal',
        ' *',
        ' * @phpstan-type Param array{string, string, bool, string}',
        ' * @phpstan-type Operation array{',
        ' *     method: non-empty-string,',
        ' *     path: non-empty-string,',
        ' *     name: string,',
        ' *     retry: string,',
        ' *     rate: string,',
        ' *     handwritten: bool,',
        ' *     body: string|null,',
        ' *     idempotency: bool,',
        ' *     params: list<Param>,',
        ' *     pagination: string|null,',
        ' *     iterator: string|null',
        ' * }',
        ' */',
        'final class Operations',
        '{',
        '    /** @var array<string, Operation> operationId => operation */',
        '    public const ALL = [',
    ];
    foreach (array_filter($ops, static fn (Op $o): bool => !$o->variant) as $op) {
        $lines[] = '        ' . export($op->id) . ' => [';
        $lines[] = "            'method' => " . export($op->method) . ',';
        $lines[] = "            'path' => " . export($op->path) . ',';
        $lines[] = "            'name' => " . export($op->resource . '.' . $op->name) . ',';
        $lines[] = "            'retry' => " . export($op->retry) . ',';
        $lines[] = "            'rate' => " . export($op->rate) . ',';
        $lines[] = "            'handwritten' => " . export($op->handwritten) . ',';
        $lines[] = "            'body' => " . export($op->body !== null ? 'json' : ($op->form ? 'form' : null)) . ',';
        $lines[] = "            'idempotency' => " . export($op->idempotency) . ',';
        if ($op->params === []) {
            $lines[] = "            'params' => [],";
        } else {
            $lines[] = "            'params' => [";
            foreach ($op->params as $p) {
                $lines[] = '                [' . export($p->api) . ', ' . export($p->in) . ', ' . export($p->required) . ', ' . export($p->kind) . '],';
            }
            $lines[] = '            ],';
        }
        $lines[] = "            'pagination' => " . export($op->pagination['style'] ?? null) . ',';
        $lines[] = "            'iterator' => " . export($op->pagination === null ? null : 'iter' . ucfirst($op->name)) . ',';
        $lines[] = '        ],';
    }
    $lines[] = '    ];';
    $lines[] = '';
    $lines[] = '    private function __construct()';
    $lines[] = '    {';
    $lines[] = '    }';
    $lines[] = '';
    $lines[] = '    /**';
    $lines[] = '     * @return Operation';
    $lines[] = '     */';
    $lines[] = '    public static function get(string $operationId): array';
    $lines[] = '    {';
    $lines[] = "        return self::ALL[\$operationId] ?? throw new \\LogicException('unknown operation ' . \$operationId);";
    $lines[] = '    }';
    $lines[] = '}';

    return sourceFile('Bizgo\\Internal', [], $lines);
}

/**
 * @param list<array{name: string, id: string, payload: string, ack: string, signed: bool, summary: string, description: string}> $hooks
 */
function webhookTrait(array $hooks): string
{
    $uses = ['Psr\\Http\\Message\\MessageInterface'];
    $lines = [
        '/**',
        ' * Webhook parsers generated from the `webhooks` of the spec (`x-sdk-webhook`). Used by {@see WebhookReceiver}, whose',
        ' * hand-written `report()`/`mo()` take precedence.',
        ' *',
        ' * @internal',
        ' */',
        'trait WebhookParsers',
        '{',
    ];
    $first = true;
    foreach ($hooks as $hook) {
        if (in_array($hook['name'], HANDWRITTEN_WEBHOOKS, true)) {
            continue;
        }
        $class = $hook['payload'];
        if (isset(Registry::$shared[$class])) {
            throw new RuntimeException("{$hook['id']}: a webhook payload shared with a request is not supported");
        }
        $uses[] = 'Bizgo\\Model\\' . $class;
        if (!$first) {
            $lines[] = '';
        }
        $first = false;
        $parse = 'parse' . ucfirst($hook['name']);
        $doc = wrap($hook['summary'], WIDTH);
        $doc[] = '';
        $doc = array_merge($doc, wrap(firstParagraph($hook['description']), WIDTH));
        $doc[] = '';
        if ($hook['signed']) {
            $doc[] = '서명 헤더(`X-IB-Timestamp`, `X-IB-Signature`)가 필수입니다. 검증에 실패하면 WebhookVerificationException.';
        } else {
            $doc = array_merge($doc, wrap('상담톡 웹훅에는 서명이 없습니다(서명은 리포트·MO 웹훅에만 적용). 서명을 요구하거나 검사하지 않고, '
                . '서명 헤더가 와도 무시합니다. 본문 크기(최대 1MB), JSON 중첩(최대 64단계), 필드 형식만 검사합니다. webhook secret 없이 쓰려면 '
                . "`WebhookReceiver::{$parse}()`를 쓰세요. 응답은 `WebhookReceiver::counselAck()`입니다.", WIDTH));
        }
        $doc[] = '';
        $doc[] = $hook['signed']
            ? '@param MessageInterface|array<array-key, mixed> $headers'
            : '@param MessageInterface|array<array-key, mixed> $headers request headers; not used (counsel webhooks are not signed)';
        $doc[] = '';
        $doc[] = $hook['signed'] ? '@throws WebhookVerificationException' : '@throws WebhookPayloadException the body is not usable';
        $lines = array_merge($lines, docBlock($doc, '    '));
        array_push($lines, ...openMethod("public", $hook["name"], ["        MessageInterface|array \$headers,", "        #[\SensitiveParameter]", "        string \$rawBody,"], $class));
        if ($hook['signed']) {
            $uses[] = 'Bizgo\\Exception\\WebhookVerificationException';
            $lines[] = '        $this->verify($headers);';
            $lines[] = '';
        } else {
            $uses[] = 'Bizgo\\Exception\\WebhookPayloadException';
        }
        $lines[] = "        return self::{$parse}(\$rawBody);";
        $lines[] = '    }';
        $lines[] = '';
        $lines[] = '    /**';
        if ($hook['signed']) {
            $lines[] = "     * Parses a `{$hook['name']}` webhook body without checking the signature; use";
            $lines[] = "     * {@see self::{$hook['name']}()} for that.";
        } else {
            $lines[] = "     * Parses a `{$hook['name']}` webhook body. Counsel webhooks are not signed, so this needs no webhook secret";
            $lines[] = "     * and applies the same checks as {@see self::{$hook['name']}()}.";
        }
        $lines[] = '     *';
        $lines[] = '     * @param string|array<array-key, mixed> $body raw JSON or an already decoded array';
        $lines[] = '     *';
        $lines[] = $hook['signed']
            ? '     * @throws WebhookVerificationException the body is not a JSON object with the required fields'
            : '     * @throws WebhookPayloadException the body is not a JSON object with the required fields';
        $lines[] = '     */';
        array_push($lines, ...openMethod("public static", $parse, ["        #[\SensitiveParameter]", "        string|array \$body,"], $class));
        $lines[] = "        return self::parse(\$body, {$class}::class, {$class}::REQUIRED);";
        $lines[] = '    }';
    }
    $lines[] = '}';

    return sourceFile('Bizgo\\Webhook', $uses, $lines);
}

/**
 * A call as one line, or one argument per line when it would be too long.
 *
 * @param list<string> $args
 *
 * @return list<string>
 */
function callLines(string $prefix, string $callee, array $args, string $indent): array
{
    $line = $prefix . $callee . '(' . implode(', ', $args) . ');';
    if (strlen($line) <= 120) {
        return [$line];
    }
    $out = [$prefix . $callee . '('];
    foreach ($args as $arg) {
        $out[] = $indent . '    ' . $arg . ',';
    }
    $out[] = $indent . ');';

    return $out;
}

/**
 * `public function name(...): type {` with one parameter per line, or `(): type` + `{` without parameters (PSR-12).
 *
 * @param list<string> $params
 *
 * @return list<string>
 */
function openMethod(string $visibility, string $name, array $params, string $type): array
{
    if ($params === []) {
        return ["    {$visibility} function {$name}(): {$type}", '    {'];
    }

    return array_merge(["    {$visibility} function {$name}("], $params, ["    ): {$type} {"]);
}

/**
 * Wraps a PHPDoc tag line (`@param type $name text`), continuing with an indent.
 *
 * @return list<string>
 */
function wrapTag(string $line): array
{
    if (mb_strlen($line) <= WIDTH) {
        return [$line];
    }
    $out = [];
    $current = '';
    foreach (explode(' ', $line) as $word) {
        $prefix = $out === [] ? '' : '    ';
        if ($current !== '' && mb_strlen($prefix . $current . ' ' . $word) > WIDTH) {
            $out[] = $prefix . $current;
            $current = $word;
            continue;
        }
        $current = $current === '' ? $word : $current . ' ' . $word;
    }
    $out[] = ($out === [] ? '' : '    ') . $current;

    return $out;
}
