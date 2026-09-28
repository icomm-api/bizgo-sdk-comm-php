<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Http\Transport;
use Bizgo\Internal\Operations;
use Bizgo\Model\Destination;
use Bizgo\Model\SendOmniRequest;
use Bizgo\Model\SendOmniResponse;
use Bizgo\Model\SmsMessage;
use Bizgo\Resource\Send;
use Bizgo\Testing\FakeTransport;
use Symfony\Component\Yaml\Yaml;

/**
 * SDK-DESIGN.md §12.20: an `idempotencyKey` without `idempotencyTtl` is rejected with A309, so the SDK sends
 * `idempotencyTtl` = Send::DEFAULT_IDEMPOTENCY_TTL (86400) with a key that has none.
 */
final class IdempotencyTtlTest extends TestCase
{
    public function testKeyWithoutTtlSendsTheDefault(): void
    {
        $fake = new FakeTransport();
        $send = $fake->client()->send;
        $send->sms(to: '01000000000', from: '01000000000', text: 'x', idempotencyKey: 'order-1');
        $send->lms(to: '01000000000', from: '01000000000', text: 'x', idempotencyKey: 'order-2');
        $send->mms(to: '01000000000', from: '01000000000', text: 'x', fileKeys: ['FILE_KEY'], idempotencyKey: 'order-3');
        $send->omni(to: '01000000000', messages: [self::sms()], idempotencyKey: 'order-4');
        foreach ($fake->requests() as $request) {
            self::assertIsArray($request->json);
            self::assertSame(86400, $request->json['idempotencyTtl'] ?? null);
        }
        self::assertCount(4, $fake->requests());
    }

    public function testExplicitTtlIsKeptIncludingZero(): void
    {
        $fake = new FakeTransport();
        $send = $fake->client()->send;
        $send->omni(to: '01000000000', messages: [self::sms()], idempotencyKey: 'order-1', idempotencyTtl: 0);
        $send->omni(to: '01000000000', messages: [self::sms()], idempotencyKey: 'order-2', idempotencyTtl: 600);
        $ttls = array_map(static fn ($r): mixed => is_array($r->json) ? $r->json['idempotencyTtl'] ?? 'missing' : null, $fake->requests());
        self::assertSame([0, 600], $ttls);
    }

    public function testNoKeyAddsNoTtl(): void
    {
        $fake = new FakeTransport();
        $send = $fake->client()->send;
        $send->sms(to: '01000000000', from: '01000000000', text: 'x');
        $send->omni(to: '01000000000', messages: [self::sms()], idempotencyTtl: 600); // a TTL alone is sent as given
        [$first, $second] = $fake->requests();
        self::assertIsArray($first->json);
        self::assertArrayNotHasKey('idempotencyTtl', $first->json);
        self::assertArrayNotHasKey('idempotencyKey', $first->json);
        self::assertIsArray($second->json);
        self::assertSame(600, $second->json['idempotencyTtl'] ?? null);
        self::assertArrayNotHasKey('idempotencyKey', $second->json);
    }

    public function testBulkChunks(): void
    {
        $fake = new FakeTransport();
        $send = $fake->client()->send;
        $numbers = ['01000000000', '01000000001', '01000000002'];
        $send->bulk(to: $numbers, messages: [self::sms()], chunkSize: 2, idempotencyKeyPrefix: 'camp');
        $send->bulk(to: $numbers, messages: [self::sms()], chunkSize: 2, idempotencyKeyPrefix: 'camp', idempotencyTtl: 0);
        $send->bulk(to: $numbers, messages: [self::sms()], chunkSize: 2);
        $ttls = array_map(static fn ($r): mixed => is_array($r->json) ? $r->json['idempotencyTtl'] ?? 'missing' : null, $fake->requests());
        self::assertSame([86400, 86400, 0, 0, 'missing', 'missing'], $ttls);
    }

    public function testModelsAndArraysAreNotChanged(): void
    {
        $fake = new FakeTransport();
        $model = new SendOmniRequest(destinations: [new Destination(to: '01000000000')], messageFlow: [self::sms()], idempotencyKey: 'order-1');
        $array = ['destinations' => [['to' => '01000000000']], 'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => 'x']]], 'idempotencyKey' => 'order-2'];
        $fake->client()->send->request($model);
        $fake->client()->send->request($array);
        self::assertNull($model->idempotencyTtl);
        self::assertArrayNotHasKey('idempotencyTtl', $model->toArray());
        self::assertArrayNotHasKey('idempotencyTtl', $array);
        $ttls = array_map(static fn ($r): mixed => is_array($r->json) ? $r->json['idempotencyTtl'] ?? null : null, $fake->requests());
        self::assertSame([Send::DEFAULT_IDEMPOTENCY_TTL, 86400], $ttls);
    }

    /** A raw array body given to the registry path (as generated operations do) gets the TTL only when absent. */
    public function testRawArrayBodyOfARegistryOperation(): void
    {
        $transport = (new \ReflectionProperty($this->client, 'transport'))->getValue($this->client);
        self::assertInstanceOf(Transport::class, $transport);
        $body = ['destinations' => [['to' => '01000000000']], 'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => 'x']]]];
        $cases = [
            [$body + ['idempotencyKey' => 'k-1'], 86400],
            [$body + ['idempotencyKey' => 'k-2', 'idempotencyTtl' => 0], 0],
            [$body + ['idempotencyKey' => 'k-3', 'idempotencyTtl' => 60], 60],
            [$body, null],
        ];
        foreach ($cases as [$json, $expected]) {
            $this->http->push(self::reply(self::envelope(['destinations' => []])));
            $transport->call('sendOmni', SendOmniResponse::class, json: $json);
            $sent = $this->http->lastJson();
            self::assertSame($expected, $sent['idempotencyTtl'] ?? null);
            self::assertSame($json['idempotencyKey'] ?? null, $sent['idempotencyKey'] ?? null);
        }
        self::assertArrayNotHasKey('idempotencyTtl', $cases[0][0]); // the caller's array is a value, not changed
    }

    /** The generator flags exactly the operations whose JSON body schema has both fields. */
    public function testRegistryFlagsMatchTheSpec(): void
    {
        $spec = Yaml::parseFile(__DIR__ . '/../spec/openapi.yaml');
        $paths = self::at($spec, 'paths');
        self::assertIsArray($paths);
        $expected = [];
        foreach ($paths as $item) {
            self::assertIsArray($item);
            foreach ($item as $key => $operation) {
                if ($key === 'parameters') {
                    continue;
                }
                $ref = self::at($operation, 'requestBody', 'content', 'application/json', 'schema', '$ref');
                $name = is_string($ref) ? substr($ref, strlen('#/components/schemas/')) : '';
                $props = self::at($spec, 'components', 'schemas', $name, 'properties');
                $id = self::at($operation, 'operationId');
                self::assertIsString($id);
                $expected[$id] = is_array($props) && isset($props['idempotencyKey'], $props['idempotencyTtl']);
            }
        }
        $actual = array_map(static fn (array $op): bool => $op['idempotency'], Operations::ALL);
        ksort($expected);
        ksort($actual);
        self::assertSame($expected, $actual, 'regenerate: php bin/generate-models.php');
        self::assertTrue($actual['sendOmni']);
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

    private static function sms(): SmsMessage
    {
        return new SmsMessage(from: '01000000000', text: 'x');
    }
}
