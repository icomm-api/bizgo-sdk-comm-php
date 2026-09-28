<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\WebhookPayloadException;
use Bizgo\Exception\WebhookVerificationException;
use Bizgo\Testing\FakeTransport;
use Bizgo\Tests\Support\SpecSamples;
use Bizgo\Webhook\WebhookReceiver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Counsel (상담톡) webhooks are not signed (signatures apply only to report and MO webhooks): the parsers do not
 * require or check a signature, still check the body, need no secret in their static form, and the answer is
 * `{"code": "A000", "result": "Success"}` (SDK-DESIGN.md §11.5).
 */
final class CounselWebhookTest extends TestCase
{
    private const SECRET = 'test-webhook-secret';

    /** @return iterable<string, array{string, class-string}> every counsel webhook of the spec */
    public static function counselWebhooks(): iterable
    {
        foreach (SpecSamples::arr(SpecSamples::spec(), 'webhooks') as $item) {
            $name = SpecSamples::str($item, 'post', 'x-sdk-webhook');
            if (str_starts_with($name, 'counsel')) {
                $ref = SpecSamples::str($item, 'post', 'requestBody', 'content', 'application/json', 'schema', '$ref');
                /** @var class-string $class */
                $class = 'Bizgo\\Model\\' . substr($ref, strlen('#/components/schemas/'));
                yield $name => [$name, $class];
            }
        }
    }

    /** @return array<array-key, mixed> the spec example of a webhook */
    private static function example(string $name): array
    {
        foreach (SpecSamples::arr(SpecSamples::spec(), 'webhooks') as $item) {
            if (SpecSamples::get($item, 'post', 'x-sdk-webhook') === $name) {
                return SpecSamples::arr($item, 'post', 'requestBody', 'content', 'application/json', 'example');
            }
        }
        self::fail("no webhook {$name}");
    }

    /** @param class-string $class */
    #[DataProvider('counselWebhooks')]
    public function testCounselWebhookParsesWithoutSignatureHeaders(string $name, string $class): void
    {
        $body = json_encode(self::example($name), JSON_THROW_ON_ERROR);
        $parse = [new WebhookReceiver(self::SECRET), $name];
        self::assertIsCallable($parse);
        self::assertInstanceOf($class, $parse(['Content-Type' => 'application/json'], $body));
        self::assertInstanceOf($class, $parse([], $body));
    }

    /**
     * The documented path for counsel-only endpoints: the static parsers need no webhook secret.
     *
     * @param class-string $class
     */
    #[DataProvider('counselWebhooks')]
    public function testCounselWebhookIsUsableWithoutSecret(string $name, string $class): void
    {
        $static = [WebhookReceiver::class, 'parse' . ucfirst($name)];
        self::assertIsCallable($static);
        self::assertInstanceOf($class, $static(json_encode(self::example($name), JSON_THROW_ON_ERROR)));
        self::assertInstanceOf($class, $static(self::example($name)));
    }

    public function testCounselWebhookIgnoresSignatureHeaders(): void
    {
        $receiver = new WebhookReceiver(self::SECRET);
        $body = json_encode(self::example('counselMessage'), JSON_THROW_ON_ERROR);
        $wrong = FakeTransport::signWebhook('another-secret', $body);
        $cases = [
            'valid signature' => FakeTransport::signWebhook(self::SECRET, $body)->headers,
            'wrong secret' => $wrong->headers,
            'timestamp only' => ['X-IB-Timestamp' => $wrong->headers['X-IB-Timestamp']],
            'signature only' => ['X-IB-Signature' => $wrong->headers['X-IB-Signature']],
            'old timestamp' => FakeTransport::signWebhook(self::SECRET, $body, timestamp: '1000000000000')->headers,
            'garbage' => ['X-IB-Timestamp' => 'not-a-number', 'X-IB-Signature' => 'bogus'],
        ];
        foreach ($cases as $case => $headers) {
            self::assertSame('USER_KEY_EXAMPLE', $receiver->counselMessage($headers, $body)->userKey, $case);
        }
        self::assertSame('SESSION', $receiver->counselExpiredSession($wrong->headers, json_encode(self::example('counselExpiredSession'), JSON_THROW_ON_ERROR))->msgType);
    }

    public function testReceiverHasNoCounselSignatureOption(): void
    {
        $constructor = new \ReflectionMethod(WebhookReceiver::class, '__construct');
        self::assertSame(['secret', 'tolerance'], array_map(static fn (\ReflectionParameter $p): string => $p->getName(), $constructor->getParameters()));
        self::assertFalse((new \ReflectionClass(WebhookReceiver::class))->hasMethod('verifyOptional'));
        self::assertStringNotContainsString('allowUnsigned', print_r(new WebhookReceiver(self::SECRET), true));
    }

    /** @return iterable<string, array{string}> bodies the counsel parsers must reject */
    public static function invalidBodies(): iterable
    {
        $nested = str_repeat('[', WebhookReceiver::MAX_JSON_DEPTH + 1) . str_repeat(']', WebhookReceiver::MAX_JSON_DEPTH + 1);
        yield 'too large' => ['{"msgKey":"' . str_repeat('a', WebhookReceiver::MAX_BODY_BYTES) . '"}'];
        yield 'too deep' => ['{"msgKey":"K","userKey":"U","extra":' . $nested . '}'];
        yield 'not json' => ['not json'];
        yield 'not an object' => ['[1,2]'];
        yield 'wrong type' => ['{"msgKey":["K"],"userKey":{"a":1},"senderKey":1,"contents":"text"}'];
    }

    #[DataProvider('invalidBodies')]
    public function testCounselWebhookStillChecksTheBody(string $body): void
    {
        $receiver = new WebhookReceiver(self::SECRET);
        foreach ([static fn () => $receiver->counselMessage([], $body), static fn () => WebhookReceiver::parseCounselMessage($body)] as $parse) {
            try {
                $parse();
                self::fail('expected WebhookPayloadException');
            } catch (WebhookPayloadException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function testReportAndMoWebhooksAlwaysNeedASignature(): void
    {
        $receiver = new WebhookReceiver(self::SECRET);
        $wrong = FakeTransport::signWebhook('another-secret', '{"msgKey":"KEY001"}')->headers;
        foreach (['report', 'mo'] as $method) {
            foreach (['unsigned' => [], 'wrong secret' => $wrong] as $case => $headers) {
                $call = [$receiver, $method];
                self::assertIsCallable($call);
                try {
                    $call($headers, '{"msgKey":"KEY001"}');
                    self::fail("expected WebhookVerificationException: {$method} {$case}");
                } catch (WebhookVerificationException $e) {
                    self::assertNotInstanceOf(WebhookPayloadException::class, $e, "{$method} {$case}");
                }
            }
        }
    }

    public function testCounselAckDiffersFromReportAck(): void
    {
        self::assertSame(['code' => 'A000', 'result' => 'Success'], WebhookReceiver::counselAck());
        self::assertSame(['msgKey' => 'KEY001'], WebhookReceiver::ack('KEY001'));
    }

    public function testCounselPayloadMasksPersonalData(): void
    {
        $payload = WebhookReceiver::parseCounselPersonalInfo(self::example('counselPersonalInfo'));
        $text = (string) $payload . print_r($payload, true);
        self::assertStringNotContainsString('01000000000', $text);
        self::assertStringNotContainsString('NICKNAME_EXAMPLE', $text);
    }
}
