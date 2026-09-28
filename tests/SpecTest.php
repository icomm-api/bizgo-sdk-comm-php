<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\ServiceCodes;
use Bizgo\Model\MoWebhookPayload;
use Bizgo\Model\ReportWebhookPayload;
use Bizgo\Model\SendOmniRequest;
use Bizgo\Model\SendOmniResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/** Consistency with the vendored OpenAPI spec (spec/openapi.yaml) and error code table. */
final class SpecTest extends TestCase
{
    /** @return array<array-key, mixed> */
    private static function spec(): array
    {
        $spec = Yaml::parseFile(__DIR__ . '/../spec/openapi.yaml');
        self::assertIsArray($spec);

        return $spec;
    }

    /** A value inside a nested array, by path segments. */
    private static function at(mixed $value, string ...$path): mixed
    {
        foreach ($path as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /** @return array<array-key, mixed> */
    private static function sendOperation(): array
    {
        $send = self::at(self::spec(), 'paths', '/api/comm/v1/send/omni', 'post');
        self::assertIsArray($send);

        return $send;
    }

    /** @return iterable<string, array{array<array-key, mixed>}> */
    public static function requestExamples(): iterable
    {
        $examples = self::at(self::sendOperation(), 'requestBody', 'content', 'application/json', 'examples');
        self::assertIsArray($examples);
        foreach ($examples as $name => $example) {
            self::assertIsArray($example);
            self::assertIsArray($example['value']);
            yield (string) $name => [$example['value']];
        }
    }

    /** @param array<array-key, mixed> $value */
    #[DataProvider('requestExamples')]
    public function testSpecRequestExamplesValidateAndRoundTrip(array $value): void
    {
        $request = SendOmniRequest::fromArray($value);
        // same keys and values (strict types); key order follows the spec, not the example
        self::assertSame(self::sorted($value), self::sorted($request->toArray()));
        self::assertSame(self::sorted($value), self::sorted(json_decode(json_encode($request, JSON_THROW_ON_ERROR), true)));
    }

    public function testSpecResponseExampleParses(): void
    {
        $example = self::at(self::sendOperation(), 'responses', '200', 'content', 'application/json', 'example');
        self::assertIsArray($example);
        $response = SendOmniResponse::fromArray($example);
        self::assertSame('Infobank-Tracking-Id', $response->common?->infobankTrId);
        self::assertSame('A000', $response->data?->data?->destinations[0]->code ?? null);
        self::assertSame(self::sorted($example), self::sorted($response->toArray()));
    }

    /** Recursively sorts string keys so arrays can be compared independent of key order. */
    private static function sorted(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }
        $out = array_map(self::sorted(...), $value);
        if (!array_is_list($out)) {
            ksort($out);
        }

        return $out;
    }

    /** Every operation of the spec is in the generated registry (and nothing else); tests/AllOperationsTest.php calls each one. */
    public function testEveryOperationIsCovered(): void
    {
        $ids = [];
        $paths = self::spec()['paths'];
        self::assertIsArray($paths);
        foreach ($paths as $item) {
            self::assertIsArray($item);
            foreach ($item as $key => $operation) {
                if ($key === 'parameters') {
                    continue; // path-level parameters
                }
                self::assertIsArray($operation);
                $ids[] = $operation['operationId'];
            }
        }
        sort($ids);
        $registered = array_keys(\Bizgo\Internal\Operations::ALL);
        sort($registered);
        self::assertCount(146, $ids);
        self::assertSame($ids, $registered, 'regenerate: php bin/generate-models.php');
    }

    public function testBrandMessageUploadKindsMatchTheSpecPaths(): void
    {
        $kinds = [];
        $paths = self::spec()['paths'];
        self::assertIsArray($paths);
        foreach (array_keys($paths) as $path) {
            if (str_starts_with((string) $path, '/api/comm/v1/file/brandmessage/')) {
                $kinds[] = substr((string) $path, strlen('/api/comm/v1/file/brandmessage/'));
            }
        }
        // uploadBrandMessage(kind) covers the P0 kinds of SDK-DESIGN.md §3; catalog images have generated methods
        foreach (\Bizgo\Resource\Files::BRAND_KINDS as $kind) {
            self::assertContains($kind, $kinds);
        }
        self::assertSame(['catalog', 'catalog/oddFirst'], array_values(array_diff($kinds, \Bizgo\Resource\Files::BRAND_KINDS)));
    }

    public function testWebhookModelsCarryTheRequiredFields(): void
    {
        self::assertSame(self::at(self::spec(), 'components', 'schemas', 'ReportWebhookPayload', 'required'), ReportWebhookPayload::REQUIRED);
        self::assertSame(self::at(self::spec(), 'components', 'schemas', 'MoWebhookPayload', 'required'), MoWebhookPayload::REQUIRED);
    }

    public function testServiceCodeTableMatchesTheErrorCodeFile(): void
    {
        $data = json_decode((string) file_get_contents(__DIR__ . '/../spec/error-codes.json'), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($data);
        self::assertIsArray($data['service']);
        self::assertCount(count($data['service']), ServiceCodes::CODES);
        self::assertSame(400, ServiceCodes::httpStatus('A306'));
        self::assertSame('요청 성공', ServiceCodes::describe('A000'));
        self::assertNull(ServiceCodes::describe('NOPE'));
    }

    public function testGeneratedModelsAreUpToDate(): void
    {
        $ini = php_ini_loaded_file();
        $command = escapeshellarg(PHP_BINARY) . (is_string($ini) ? ' -c ' . escapeshellarg($ini) : '')
            . ' ' . escapeshellarg(__DIR__ . '/../bin/generate-models.php') . ' --check 2>&1';
        exec($command, $output, $status);
        self::assertSame(0, $status, implode("\n", $output));
    }
}
