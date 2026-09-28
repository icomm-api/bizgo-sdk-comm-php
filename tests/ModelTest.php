<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\InvalidResponseException;
use Bizgo\Exception\ValidationException;
use Bizgo\Model\AlimtalkButton;
use Bizgo\Model\AlimtalkMessage;
use Bizgo\Model\BrandMessage;
use Bizgo\Model\ChannelMessage;
use Bizgo\Model\Destination;
use Bizgo\Model\InternationalMessage;
use Bizgo\Model\MessageFlowItem;
use Bizgo\Model\MessageStatusListResponse;
use Bizgo\Model\MmsMessage;
use Bizgo\Model\NaverTalkMessage;
use Bizgo\Model\RcsMessage;
use Bizgo\Model\SendOmniRequest;
use Bizgo\Model\SmsMessage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    /** @return iterable<string, array{\Closure(): mixed, string, string}> */
    public static function invalidModels(): iterable
    {
        yield 'enum' => [static fn () => new BrandMessage(sendType: 'bogus', senderKey: 'SENDER_KEY_EXAMPLE'), 'sendType', '허용되는 값이 아닙니다 (basic, template, free)'];
        yield 'pattern' => [static fn () => new SmsMessage(from: '01000000000', text: 'x', ttl: '1h'), 'ttl', '형식이 맞지 않습니다 (^[0-9]+$)'];
        yield 'maxLength counts characters' => [static fn () => new InternationalMessage(from: '01000000000', text: str_repeat('가', 1001)), 'text', '최대 1000자인데 1001자입니다'];
        yield 'integer range' => [
            static fn () => new SendOmniRequest(destinations: [new Destination(to: '01000000000')], messageFlow: [new SmsMessage(from: '01000000000', text: 'x')], idempotencyTtl: 86401),
            'idempotencyTtl',
            '86400 이하여야 합니다',
        ];
        yield 'minItems' => [static fn () => new SendOmniRequest(destinations: [], messageFlow: [new SmsMessage(from: '01000000000', text: 'x')]), 'destinations', '최소 1개'];
        yield 'maxItems' => [static fn () => new MmsMessage(from: '01000000000', text: 'x', fileKey: ['a', 'b', 'c', 'd']), 'fileKey', '최대 3개'];
        yield 'list item type' => [static fn () => self::make(MmsMessage::class, ['from' => '01000000000', 'text' => 'x', 'fileKey' => ['a', 1]]), 'fileKey[1]', '문자열'];
        yield 'model list item type' => [
            static fn () => self::make(SendOmniRequest::class, ['destinations' => ['01000000000'], 'messageFlow' => [new SmsMessage(from: '01000000000', text: 'x')]]),
            'destinations[0]',
            'Destination 객체',
        ];
        yield 'map value type' => [static fn () => self::make(Destination::class, ['to' => '01000000000', 'replaceWords' => ['name' => 1]]), 'replaceWords.name', '문자열'];
        yield 'invalid UTF-8' => [static fn () => new AlimtalkMessage(senderKey: 'S', templateCode: 'T', text: "\xff"), 'text', 'UTF-8'];
        yield 'rcs expiry option' => [
            static fn () => new RcsMessage(from: '01000000000', formatId: 'F', brandKey: 'B', body: new \Bizgo\Model\RcsBody(title: 't'), expiryOption: '5'),
            'expiryOption',
            '허용되는 값',
        ];
    }

    /**
     * Calls a constructor with arguments static analysis would reject, to test the runtime checks.
     *
     * @param class-string $class
     * @param array<string, mixed> $args
     */
    private static function make(string $class, array $args): object
    {
        return (new \ReflectionClass($class))->newInstanceArgs($args);
    }

    /** @param \Closure(): mixed $create */
    #[DataProvider('invalidModels')]
    public function testConstructorValidation(\Closure $create, string $path, string $reason): void
    {
        try {
            $create();
            self::fail('expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertSame($path, $e->errors[0]['path']);
            self::assertStringContainsString($reason, $e->errors[0]['reason']);
        }
    }

    public function testFromArrayReportsEveryProblemWithItsPath(): void
    {
        try {
            SendOmniRequest::fromArray([
                'destinations' => [['to' => 1000], ['phone' => '01000000000']],
                'messageFlow' => [['alimtalk' => ['senderKey' => 'S', 'text' => 'x', 'button' => 'no']]],
                'idempotencyTtl' => '60',
            ]);
            self::fail('expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertSame([
                ['path' => 'destinations[0].to', 'reason' => '문자열이어야 합니다'],
                ['path' => 'destinations[1].phone', 'reason' => '알 수 없는 필드입니다'],
                ['path' => 'destinations[1].to', 'reason' => '필수 필드입니다'],
                ['path' => 'messageFlow[0].alimtalk.button', 'reason' => '알 수 없는 필드입니다'],
                ['path' => 'messageFlow[0].alimtalk.templateCode', 'reason' => '필수 필드입니다'],
                ['path' => 'idempotencyTtl', 'reason' => '정수여야 합니다'],
            ], $e->errors);
        }
    }

    public function testNestedModelsFromArray(): void
    {
        $message = AlimtalkMessage::fromArray([
            'senderKey' => 'SENDER_KEY_EXAMPLE',
            'templateCode' => 'TEMPLATE_CODE_EXAMPLE',
            'msgType' => 'AT',
            'text' => 'x',
            'attachment' => ['button' => [['type' => 'WL', 'name' => '보기', 'urlMobile' => 'https://example.com']]],
        ]);
        $button = $message->attachment?->button[0] ?? null;
        self::assertInstanceOf(AlimtalkButton::class, $button);
        self::assertSame('보기', $button->name);
        self::assertSame(['button' => [['type' => 'WL', 'name' => '보기', 'urlMobile' => 'https://example.com']]], $message->toArray()['attachment']);

        $naver = NaverTalkMessage::fromArray(['partnerKey' => 'P', 'templateCode' => 'T', 'productCode' => 'INFORMATION', 'templateParams' => ['1' => 'a']]);
        self::assertSame('{"partnerKey":"P","templateCode":"T","productCode":"INFORMATION","templateParams":{"1":"a"}}', json_encode($naver));
    }

    public function testChannelMessagesKnowTheirKey(): void
    {
        $messages = [
            'sms' => new SmsMessage(from: '01000000000', text: 'x'),
            'mms' => new MmsMessage(from: '01000000000', text: 'x'),
            'international' => new InternationalMessage(from: '01000000000', text: 'x'),
            'alimtalk' => new AlimtalkMessage(senderKey: 'S', templateCode: 'T', msgType: 'AT', text: 'x'),
            'brandmessage' => new BrandMessage(sendType: 'template', senderKey: 'S', templateCode: 'T', targeting: 'I'),
            'navertalk' => new NaverTalkMessage(partnerKey: 'P', templateCode: 'T', productCode: 'INFORMATION'),
        ];
        foreach ($messages as $key => $message) {
            self::assertSame($key, $message->channelKey());
            self::assertSame([$key], array_keys(MessageFlowItem::of($message)->toArray()));
        }
        self::assertSame(array_keys(MessageFlowItem::CHANNELS), ['sms', 'mms', 'international', 'rcs', 'alimtalk', 'brandmessage', 'navertalk']);
    }

    public function testResponseParsingIsLenientButTyped(): void
    {
        $response = MessageStatusListResponse::fromArray([
            'common' => ['authCode' => 'A000'],
            'data' => ['code' => 'A000', 'data' => ['messages' => [['msgKey' => 123, 'to' => '01000000000']], 'lastSeq' => '42', 'hasNext' => 'true']],
        ]);
        $inner = $response->data->data ?? null;
        self::assertNotNull($inner);
        self::assertSame('123', $inner->messages[0]->msgKey ?? null);
        self::assertSame(42, $inner->lastSeq);
        self::assertTrue($inner->hasNext);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('data.data.messages[0].to');
        MessageStatusListResponse::fromArray(['data' => ['data' => ['messages' => [['to' => ['01000001234']]]]]]);
    }

    public function testInvalidResponseMessagesDoNotContainValues(): void
    {
        try {
            MessageStatusListResponse::fromArray(['data' => ['data' => ['messages' => [['to' => ['01000001234']]]]]]);
            self::fail('expected an InvalidResponseException');
        } catch (InvalidResponseException $e) {
            self::assertStringNotContainsString('01000001234', $e->getMessage());
        }
    }
}
