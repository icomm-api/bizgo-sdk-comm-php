<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\WebhookVerificationException;
use Bizgo\Webhook\WebhookReceiver;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class WebhookTest extends TestCase
{
    private const SECRET = 'test-webhook-secret';
    private const NOW = 1743381600.0;
    private const TIMESTAMP = '1743381600000'; // epoch ms, as in the API reference example
    private const BODY = '{"msgKey":"KEY001","serviceType":"SMS","reportTime":"t","reportType":"0","reportCode":"10000"}';

    private static function sign(string $timestamp = self::TIMESTAMP, string $secret = self::SECRET): string
    {
        return hash_hmac('sha256', $timestamp, $secret, true);
    }

    /** @return iterable<string, array{string}> */
    public static function encodings(): iterable
    {
        yield 'hex' => [bin2hex(self::sign())];
        yield 'upper-case hex' => [strtoupper(bin2hex(self::sign()))];
        yield 'base64' => [base64_encode(self::sign())];
        yield 'surrounding spaces' => [' ' . bin2hex(self::sign()) . ' '];
    }

    #[DataProvider('encodings')]
    public function testValidSignatureEncodings(string $signature): void
    {
        WebhookReceiver::verifySignature(self::SECRET, self::TIMESTAMP, $signature, now: self::NOW);
        $this->addToAssertionCount(1);
    }

    /** @return iterable<string, array{string}> */
    public static function prefixedSignatures(): iterable
    {
        yield 'sha256= prefix' => ['sha256=' . bin2hex(self::sign())];
        yield 'SHA256= prefix with base64' => ['SHA256=' . base64_encode(self::sign())];
    }

    #[DataProvider('prefixedSignatures')]
    public function testPrefixedSignatureIsRejected(string $signature): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('일치하지');
        WebhookReceiver::verifySignature(self::SECRET, self::TIMESTAMP, $signature, now: self::NOW);
    }

    public function testWrongSecret(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('일치하지');
        WebhookReceiver::verifySignature(self::SECRET, self::TIMESTAMP, bin2hex(self::sign(secret: 'other')), now: self::NOW);
    }

    public function testSignatureOfAnotherTimestamp(): void
    {
        $this->expectException(WebhookVerificationException::class);
        WebhookReceiver::verifySignature(self::SECRET, self::TIMESTAMP, bin2hex(self::sign('1743381600001')), now: self::NOW);
    }

    public function testOldTimestampIsRejectedToLimitReplay(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('허용 범위');
        WebhookReceiver::verifySignature(self::SECRET, self::TIMESTAMP, bin2hex(self::sign()), now: self::NOW + 301);
    }

    public function testFutureTimestampIsRejected(): void
    {
        $this->expectException(WebhookVerificationException::class);
        WebhookReceiver::verifySignature(self::SECRET, self::TIMESTAMP, bin2hex(self::sign()), now: self::NOW - 301);
    }

    public function testToleranceCanBeDisabled(): void
    {
        WebhookReceiver::verifySignature(self::SECRET, self::TIMESTAMP, bin2hex(self::sign()), tolerance: null, now: self::NOW + 86400);
        $this->addToAssertionCount(1);
    }

    public function testSecondsTimestampIsAccepted(): void
    {
        WebhookReceiver::verifySignature(self::SECRET, '1743381600', bin2hex(self::sign('1743381600')), now: self::NOW);
        $this->addToAssertionCount(1);
    }

    /** @return iterable<string, array{string, string}> */
    public static function malformed(): iterable
    {
        yield 'empty timestamp' => ['', 'x'];
        yield 'not a number' => ['abc', 'x'];
        yield 'negative' => ['-1743381600', 'x'];
        yield 'too long' => [str_repeat('9', 30), 'x'];
        yield 'empty signature' => [self::TIMESTAMP, ''];
    }

    #[DataProvider('malformed')]
    public function testMissingOrMalformedHeaders(string $timestamp, string $signature): void
    {
        $this->expectException(WebhookVerificationException::class);
        WebhookReceiver::verifySignature(self::SECRET, $timestamp, $signature, now: self::NOW);
    }

    public function testReceiverVerifiesThenParses(): void
    {
        $receiver = new WebhookReceiver(self::SECRET, tolerance: null);
        $headers = ['x-ib-timestamp' => self::TIMESTAMP, 'X-IB-SIGNATURE' => bin2hex(self::sign())]; // case-insensitive
        $report = $receiver->report($headers, self::BODY);
        self::assertSame('KEY001', $report->msgKey);
        self::assertSame('10000', $report->reportCode);
        self::assertSame(['msgKey' => 'KEY001'], WebhookReceiver::ack($report->msgKey));
        self::assertStringNotContainsString(self::SECRET, print_r($receiver, true));
    }

    public function testReceiverAcceptsFrameworkServerAndPsr7Headers(): void
    {
        $receiver = new WebhookReceiver(self::SECRET, tolerance: null);
        $signature = bin2hex(self::sign());
        // Symfony/Laravel style: lists of values
        $receiver->verify(['x-ib-timestamp' => [self::TIMESTAMP], 'x-ib-signature' => [$signature]]);
        // $_SERVER style
        $receiver->verify(['HTTP_X_IB_TIMESTAMP' => self::TIMESTAMP, 'HTTP_X_IB_SIGNATURE' => $signature, 'REQUEST_METHOD' => 'POST']);
        // PSR-7
        $request = new ServerRequest('POST', '/webhook', ['X-IB-Timestamp' => self::TIMESTAMP, 'X-IB-Signature' => $signature], self::BODY);
        self::assertSame('KEY001', $receiver->report($request, (string) $request->getBody())->msgKey);
    }

    public function testReceiverRejectsUnsignedRequest(): void
    {
        $this->expectException(WebhookVerificationException::class);
        (new WebhookReceiver(self::SECRET))->report([], self::BODY);
    }

    public function testEmptySecretIsRejected(): void
    {
        $this->expectException(WebhookVerificationException::class);
        new WebhookReceiver('');
    }

    public function testReceiverCannotBeSerialized(): void
    {
        $this->expectException(\LogicException::class);
        serialize(new WebhookReceiver(self::SECRET));
    }

    public function testParseMoAndInvalidJson(): void
    {
        $mo = WebhookReceiver::parseMo([
            'msgKey' => 'K',
            'serviceType' => 'MO',
            'msgType' => 'SM',
            'to' => '#000000',
            'from' => '01000000000',
            'carrier' => '10001',
            'originator' => '01000000000',
            'content' => '투표 1',
            'occurredTime' => 't',
        ]);
        self::assertSame('01000000000', $mo->from);
        self::assertSame('투표 1', $mo->content);
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('JSON');
        WebhookReceiver::parseMo('not json');
    }

    public function testMoWebhookEndToEnd(): void
    {
        $receiver = new WebhookReceiver(self::SECRET, tolerance: null);
        $body = '{"msgKey":"K","serviceType":"MO","msgType":"SM","to":"#000000","from":"01000000000","carrier":"10001","originator":"01000000000","content":"1","occurredTime":"t","newField":1}';
        $mo = $receiver->mo(['X-IB-Timestamp' => self::TIMESTAMP, 'X-IB-Signature' => base64_encode(self::sign())], $body);
        self::assertSame(['newField' => 1], $mo->extra);
    }

    /** @return iterable<string, array{string, string}> */
    public static function badBodies(): iterable
    {
        yield 'missing required field' => ['{"msgKey":"K"}', '필수 필드'];
        yield 'JSON list' => ['[1,2]', 'JSON 객체'];
        yield 'too large' => [str_repeat(' ', 1024 * 1024 + 1), '너무 큽니다'];
        yield 'wrong type' => ['{"msgKey":{"a":1},"serviceType":"SMS","reportTime":"t","reportType":"0","reportCode":"1"}', '형식'];
    }

    #[DataProvider('badBodies')]
    public function testBadBodiesAreRejected(string $body, string $message): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage($message);
        WebhookReceiver::parseReport($body);
    }
}
