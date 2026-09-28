<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\BizgoClient;
use Bizgo\Exception\InternalServerException;
use Bizgo\Internal\Operations;
use Bizgo\Model\SmsMessage;
use Bizgo\Testing\FakeTransport;
use Bizgo\Tests\Support\SpecSamples as S;
use Bizgo\Upload;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Every operation of the spec is reachable as `$client-><x-sdk-resource>-><x-sdk-method>()` with the documented HTTP
 * method, path template, retry policy, rate bucket and pagination (SDK-DESIGN.md §10). Offline (FakeTransport).
 */
final class AllOperationsTest extends TestCase
{
    /** @return iterable<string, array{string}> */
    public static function operations(): iterable
    {
        foreach (array_keys(Operations::ALL) as $id) {
            yield $id => [$id];
        }
    }

    #[DataProvider('operations')]
    public function testOperationIsReachableWithItsMethodPathAndRetry(string $id): void
    {
        $op = Operations::get($id);
        $spec = S::operation($id);
        self::assertSame(S::str($spec, 'x-sdk-resource') . '.' . S::str($spec, 'x-sdk-method'), $op['name']);
        self::assertSame(S::get($spec, 'x-sdk-retry'), $op['retry']);
        self::assertSame(S::get($spec, 'x-sdk-rate') === 'send' ? 'send' : 'other', $op['rate']);
        self::assertSame(S::get($spec, 'x-sdk-pagination', 'style'), $op['pagination']);

        $fake = new FakeTransport();
        $client = $fake->client(maxRetries: 1);
        self::invoke($client, $id);
        $request = $fake->lastRequest();
        self::assertSame($id, $request->operationId);
        self::assertSame($op['method'], $request->method);
        self::assertSame($op['path'], $request->pathTemplate);
        self::assertStringNotContainsString('{', $request->path, 'path values are filled in');

        // retry policy: safe operations retry a 503, the others give up (a second execution could duplicate)
        $fake->reset();
        $fake->on($id)->respond(503, 'unavailable')->default();
        $retried = true;
        try {
            self::invoke($client, $id);
        } catch (InternalServerException) {
            $retried = false;
        }
        $expectRetry = $op['retry'] === 'safe';
        self::assertSame($expectRetry, $retried, "{$id}: retry on 503");
        self::assertCount($expectRetry ? 2 : 1, $fake->requests());
    }

    /** @return iterable<string, array{string}> */
    public static function paginated(): iterable
    {
        foreach (Operations::ALL as $id => $op) {
            if ($op['pagination'] !== null) {
                yield $id => [$id];
            }
        }
    }

    #[DataProvider('paginated')]
    public function testIteratorWalksPagesAndStops(string $id): void
    {
        $op = Operations::get($id);
        $spec = S::operation($id);
        $pagination = S::arr($spec, 'x-sdk-pagination');
        $style = S::str($pagination, 'style');
        $cursorParam = S::str($pagination, 'request');
        $sizeName = S::get($pagination, 'size');
        $size = null;
        foreach (S::arr($spec, 'parameters') as $parameter) {
            if (S::get($parameter, 'name') === $sizeName) {
                $minimum = S::get($parameter, 'schema', 'minimum');
                $size = is_int($minimum) ? $minimum : 1;
            }
        }
        $count = $size ?? 1;
        $fake = new FakeTransport();
        $client = $fake->client();
        $fake->on($id)->respond(200, self::page($pagination, $count, true))->respond(200, self::page($pagination, 0, false));
        $iterator = $op['iterator'];
        self::assertIsString($iterator);
        $overrides = is_string($sizeName) && $size !== null ? [$sizeName => $size] : [];
        $result = self::invoke($client, $id, $iterator, $overrides);
        self::assertInstanceOf(\Generator::class, $result);
        self::assertCount($count, iterator_to_array($result), "{$id}: every item of the first page, none of the empty one");
        $requests = $fake->requests();
        self::assertCount(2, $requests, "{$id}: stops after the empty page");
        $expected = match ($style) {
            'cursor' => str_contains(S::str($pagination, 'response'), 'lastSeq') ? '5' : 'NEXT-1',
            'page' => '2',
            default => (string) $count,
        };
        self::assertSame($expected, $requests[1]->query[$cursorParam] ?? null, "{$id}: the next page is requested");
    }

    /**
     * A response body with `$count` items at the pagination paths.
     *
     * @param array<array-key, mixed> $pagination
     *
     * @return array<array-key, mixed>
     */
    private static function page(array $pagination, int $count, bool $more): array
    {
        $body = FakeTransport::success(null);
        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $items[] = ['requestId' => 'NEXT-' . ($i + 1)];
        }
        $body = self::set($body, S::str($pagination, 'items'), $items);
        $response = S::get($pagination, 'response');
        if (is_string($response) && !str_contains($response, '[-1]')) {
            $body = self::set($body, $response, $more ? 5 : null);
        }
        $hasNext = S::get($pagination, 'hasNext');
        if (is_string($hasNext)) {
            $body = self::set($body, $hasNext, $more);
        }
        $total = S::get($pagination, 'total');
        if (is_string($total)) {
            $body = self::set($body, $total, 1000);
        }

        return $body;
    }

    /**
     * @param array<array-key, mixed> $body
     *
     * @return array<array-key, mixed>
     */
    private static function set(array $body, string $path, mixed $value): array
    {
        $keys = explode('.', $path);
        $key = array_shift($keys);
        if ($keys === []) {
            $body[$key] = $value;

            return $body;
        }
        $child = is_array($body[$key] ?? null) ? $body[$key] : [];
        $body[$key] = self::set($child, implode('.', $keys), $value);

        return $body;
    }

    /**
     * Calls the SDK method of an operation with minimal valid arguments.
     *
     * @param array<string, mixed> $overrides
     */
    private static function invoke(BizgoClient $client, string $id, ?string $method = null, array $overrides = []): mixed
    {
        $op = Operations::get($id);
        if ($op['handwritten']) {
            return self::handwritten($client, $id, $method);
        }
        $parts = explode('.', $op['name']);
        $name = array_pop($parts);
        $name = $method ?? $name;
        $target = $client;
        foreach ($parts as $part) {
            $target = (new \ReflectionProperty($target, $part))->getValue($target);
            self::assertIsObject($target);
        }
        $reflection = new \ReflectionMethod($target, $name);
        self::assertTrue($reflection->isPublic(), "{$op['name']} is public");
        $spec = S::operation($id);
        $args = [];
        foreach ($reflection->getParameters() as $parameter) {
            $pname = $parameter->getName();
            if (array_key_exists($pname, $overrides)) {
                $args[$pname] = $overrides[$pname];
            } elseif (!$parameter->isOptional()) {
                $args[$pname] = self::argument($spec, $pname);
            }
        }

        return $reflection->invokeArgs($target, $args);
    }

    /** @param array<array-key, mixed> $spec */
    private static function argument(array $spec, string $name): mixed
    {
        if ($name === 'body') {
            return S::value(S::arr($spec, 'requestBody', 'content', 'application/json', 'schema'));
        }
        foreach (S::arr($spec, 'parameters') as $parameter) {
            if (S::get($parameter, 'name') !== $name) {
                continue;
            }
            $schema = S::arr($parameter, 'schema');
            $type = S::get($schema, 'type');
            if ($type === 'array') {
                return ['A'];
            }
            if ($type === 'integer') {
                $minimum = S::get($schema, 'minimum');

                return is_int($minimum) ? $minimum : 1;
            }
            if (in_array(S::get($schema, 'x-format'), ['yyyyMMdd', 'YYYYMMDD'], true)) {
                return new \DateTimeImmutable('2026-01-01 10:00:00', new \DateTimeZone('Asia/Seoul'));
            }

            return S::get($parameter, 'in') === 'path' ? 'VALUE/1' : S::string($schema);
        }
        $form = S::str($spec, 'requestBody', 'content', 'multipart/form-data', 'schema', '$ref');
        $property = S::arr(S::schema(substr($form, strlen('#/components/schemas/'))), 'properties', $name);
        if (S::get($property, 'format') === 'binary') {
            return Upload::fromContents('fake-bytes', 'a.jpg');
        }
        if (S::get($property, 'type') === 'array') {
            return [Upload::fromContents('fake-bytes', 'a.pdf')];
        }
        if (isset($property['allOf']) || isset($property['$ref'])) {
            return S::value($property);
        }

        return S::string($property);
    }

    /** The hand-written convenience methods, called with placeholder values. */
    private static function handwritten(BizgoClient $client, string $id, ?string $iterator): mixed
    {
        $jpeg = Upload::fromContents("\xff\xd8fake", 'a.jpg');
        if ($iterator === 'iterHistory') {
            return $client->messages->iterHistory('2026-01-01T10:00:00', limit: 1);
        }
        if ($iterator === 'iterMoHistory') {
            return $client->messages->iterMoHistory('2026-01-01T10:00:00+09:00', limit: 1);
        }

        return match ($id) {
            'sendOmni' => $client->send->omni(to: '01000000000', messages: [new SmsMessage(from: '01000000000', text: 'hi')]),
            'uploadMmsFile' => $client->files->uploadMms($jpeg),
            'uploadRcsFile' => $client->files->uploadRcs($jpeg),
            'uploadBrandMessageDefaultImage' => $client->files->uploadBrandMessage($jpeg, 'default'),
            'uploadBrandMessageWideImage' => $client->files->uploadBrandMessage($jpeg, 'wide'),
            'uploadBrandMessageWideItemListFirstImage' => $client->files->uploadBrandMessage($jpeg, 'wideItemList/first'),
            'uploadBrandMessageWideItemListImage' => $client->files->uploadBrandMessage($jpeg, 'wideItemList'),
            'uploadBrandMessageCarouselFeedImage' => $client->files->uploadBrandMessage($jpeg, 'carouselFeed'),
            'uploadBrandMessageCarouselCommerceImage' => $client->files->uploadBrandMessage($jpeg, 'carouselCommerce'),
            'getReportPolling' => $client->reports->poll(),
            'ackReportPolling' => $client->reports->ack('REPORT/1'),
            'getReportInquiry' => $client->reports->inquiry('KEY/1'),
            'getMessageStatistics' => $client->messages->statistics('20260101'),
            'getMessageHistory' => $client->messages->history('2026-01-01T10:00:00'),
            'getMoHistory' => $client->messages->moHistory('2026-01-01T10:00:00+09:00'),
            'getMessageStatusByMsgKey' => $client->messages->status('KEY/1'),
            'getMessageStatusByRequestId' => $client->messages->statusByRequestId('REQ/1'),
            'getMoByMsgKey' => $client->messages->mo('KEY/1'),
            default => self::fail("no hand-written call for {$id}"),
        };
    }
}
