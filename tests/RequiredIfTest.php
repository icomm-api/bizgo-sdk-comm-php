<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\Exception\ValidationException;
use Bizgo\Internal\RequiredIfRules;
use Bizgo\Model\AlimtalkMessage;
use Bizgo\Model\BrandMessageButton;
use Bizgo\Model\Destination;
use Bizgo\Model\RcsBody;
use Bizgo\Model\RcsMessage;
use Bizgo\Model\SendOmniRequest;
use Symfony\Component\Yaml\Yaml;

/**
 * `x-sdk-required-if` (SDK-DESIGN.md §4): conditionally required fields are checked before anything is sent,
 * for typed models and raw arrays, with `$.` paths checked against the whole request body.
 */
final class RequiredIfTest extends TestCase
{
    private const TEXT = '#{name}님 주문(ORDER-SECRET-TEXT) 완료';

    /**
     * @return list<array{path: string, reason: string}>
     */
    private function rejected(\Closure $call): array
    {
        try {
            $call();
        } catch (ValidationException $e) {
            self::assertSame(0, $this->http->count(), 'nothing may be sent');
            self::assertStringNotContainsString('01000001234', $e->getMessage());
            self::assertStringNotContainsString('ORDER-SECRET-TEXT', $e->getMessage());
            self::assertStringNotContainsString('SENDER_KEY_EXAMPLE', $e->getMessage());
            self::assertStringNotContainsString('홍길동', $e->getMessage());

            return $e->errors;
        }
        self::fail('expected a ValidationException');
    }

    public function testAlimtalkFullSendWithoutMsgTypeIsRejectedBeforeSending(): void
    {
        // the sandbox rejects this with A523; the SDK stops it first
        $errors = $this->rejected(fn () => $this->client->send->omni(
            to: '01000001234',
            messages: [new AlimtalkMessage(senderKey: 'SENDER_KEY_EXAMPLE', templateCode: 'T', text: self::TEXT)],
        ));
        self::assertSame([['path' => 'msgType', 'reason' => '필수 필드입니다 (sendType != template일 때)']], $errors);

        $errors = $this->rejected(fn () => $this->client->send->request([
            'destinations' => [['to' => '01000001234']],
            'messageFlow' => [['alimtalk' => ['senderKey' => 'SENDER_KEY_EXAMPLE', 'templateCode' => 'T']]],
        ]));
        self::assertSame([
            ['path' => 'messageFlow[0].alimtalk.msgType', 'reason' => '필수 필드입니다 (sendType != template일 때)'],
            ['path' => 'messageFlow[0].alimtalk.text', 'reason' => '필수 필드입니다 (sendType != template일 때)'],
        ], $errors);
    }

    public function testAlimtalkFullSendWithMsgTypeAndText(): void
    {
        $this->http->push(self::reply(self::accepted('A000')));
        $this->client->send->omni(
            to: '01000001234',
            messages: [new AlimtalkMessage(senderKey: 'SENDER_KEY_EXAMPLE', templateCode: 'T', msgType: 'AT', text: self::TEXT)],
        );
        self::assertSame(1, $this->http->count());
        self::assertSame('AT', $this->http->lastAt('messageFlow.0.alimtalk.msgType'));
    }

    public function testAlimtalkTemplateSendNeedsReplaceWordsOfEveryDestination(): void
    {
        $template = new AlimtalkMessage(senderKey: 'SENDER_KEY_EXAMPLE', templateCode: 'T', sendType: 'template');
        $reason = '필수 필드입니다 (messageFlow[0].alimtalk.sendType == template일 때)';

        $errors = $this->rejected(fn () => $this->client->send->omni(
            to: [new Destination(to: '01000000000', replaceWords: ['name' => '홍길동']), new Destination(to: '01000001234')],
            messages: [$template],
        ));
        self::assertSame([['path' => 'destinations[1].replaceWords', 'reason' => $reason]], $errors);

        $errors = $this->rejected(fn () => $this->client->send->request([
            'destinations' => [['to' => '01000001234']],
            'messageFlow' => [['alimtalk' => ['senderKey' => 'SENDER_KEY_EXAMPLE', 'templateCode' => 'T', 'sendType' => 'template']]],
        ]));
        self::assertSame([['path' => 'destinations[0].replaceWords', 'reason' => $reason]], $errors);

        // reservations have the same root rule
        $errors = $this->rejected(fn () => $this->client->reservations->create([
            'destinations' => [['to' => '01000001234']],
            'messageFlow' => [['sms' => ['from' => '01000000000', 'text' => 'x']], ['alimtalk' => $template->toArray()]],
            'resvSendTime' => '2026-10-01T09:00:00+09:00',
        ]));
        self::assertSame([['path' => 'destinations[0].replaceWords', 'reason' => '필수 필드입니다 (messageFlow[1].alimtalk.sendType == template일 때)']], $errors);
    }

    public function testAlimtalkTemplateSendWithReplaceWordsNeedsNoMsgType(): void
    {
        $this->http->push(self::reply(self::accepted('A000')));
        $this->client->send->omni(
            to: [new Destination(to: '01000001234', replaceWords: ['name' => '홍길동'])],
            messages: [new AlimtalkMessage(senderKey: 'SENDER_KEY_EXAMPLE', templateCode: 'T', sendType: 'template')],
        );
        self::assertSame(1, $this->http->count());
        self::assertSame(
            ['senderKey' => 'SENDER_KEY_EXAMPLE', 'templateCode' => 'T', 'sendType' => 'template'],
            $this->http->lastAt('messageFlow.0.alimtalk'),
        );

        // a nested model alone cannot see the destinations: the root request checks them
        $request = SendOmniRequest::fromArray([
            'destinations' => [['to' => '01000001234', 'replaceWords' => ['name' => '홍길동']]],
            'messageFlow' => [['alimtalk' => ['senderKey' => 'S', 'templateCode' => 'T', 'sendType' => 'template']]],
        ]);
        self::assertCount(1, $request->destinations);
    }

    public function testBrandMessageButtonUrls(): void
    {
        $errors = $this->rejected(static fn () => new BrandMessageButton(type: 'WL', name: '보기', urlMobile: 'https://example.com/m'));
        self::assertSame([['path' => 'urlPc', 'reason' => '필수 필드입니다 (type == WL일 때)']], $errors);

        $errors = $this->rejected(static fn () => BrandMessageButton::fromArray(['type' => 'AL', 'name' => '앱'], 'buttons[2]'));
        self::assertSame([['path' => 'buttons[2].urlMobile', 'reason' => '필수 필드입니다 (type == AL일 때)']], $errors);

        $button = new BrandMessageButton(type: 'WL', name: '보기', urlPc: 'https://example.com', urlMobile: 'https://example.com/m');
        self::assertSame('WL', $button->type);
        self::assertSame('BK', (new BrandMessageButton(type: 'BK', name: '봇'))->type); // no rule for other types
    }

    public function testRcsHeaderNeedsFooter(): void
    {
        $body = new RcsBody(title: 't', description: self::TEXT);
        $errors = $this->rejected(static fn () => new RcsMessage(from: '01000001234', formatId: 'F', brandKey: 'B', body: $body, header: '1'));
        self::assertSame([['path' => 'footer', 'reason' => '필수 필드입니다 (header == 1일 때)']], $errors);

        $ok = new RcsMessage(from: '01000001234', formatId: 'F', brandKey: 'B', body: $body, header: '1', footer: '080-000-0000');
        self::assertSame('1', $ok->header);
        self::assertNull((new RcsMessage(from: '01000001234', formatId: 'F', brandKey: 'B', body: $body, header: '0'))->footer);
    }

    public function testRelativePathsThroughMissingObjects(): void
    {
        $reason = '필수 필드입니다 (msgType == FILE일 때)';
        $errors = $this->rejected(fn () => $this->client->counsel->messages->sendPlain([
            'userKey' => 'U', 'senderKey' => 'SENDER_KEY_EXAMPLE', 'msgType' => 'FILE', 'message' => self::TEXT,
        ]));
        self::assertSame([
            ['path' => 'attachment.file.fileName', 'reason' => $reason],
            ['path' => 'attachment.file.fileSize', 'reason' => $reason],
        ], $errors);

        $errors = $this->rejected(fn () => $this->client->counsel->messages->sendPlain([
            'userKey' => 'U', 'senderKey' => 'SENDER_KEY_EXAMPLE', 'msgType' => 'FILE', 'message' => self::TEXT,
            'attachment' => ['file' => ['fileUrl' => 'https://example.com/f', 'fileName' => 'a.pdf']],
        ]));
        self::assertSame([['path' => 'attachment.file.fileSize', 'reason' => $reason]], $errors);
    }

    public function testEveryRuleOfTheSpecIsInTheTable(): void
    {
        $spec = Yaml::parseFile(__DIR__ . '/../spec/openapi.yaml');
        self::assertIsArray($spec);
        $components = $spec['components'] ?? null;
        self::assertIsArray($components);
        $schemas = $components['schemas'] ?? null;
        self::assertIsArray($schemas);
        $local = [];
        $rootRules = 0;
        foreach ($schemas as $name => $schema) {
            $rules = is_array($schema) ? ($schema['x-sdk-required-if'] ?? []) : [];
            self::assertIsArray($rules);
            foreach ($rules as $rule) {
                self::assertIsArray($rule);
                $paths = is_array($rule['requiredPaths'] ?? null) ? $rule['requiredPaths'] : [];
                $relative = array_filter($paths, static fn (mixed $p): bool => is_string($p) && !str_starts_with($p, '$.'));
                if (isset($rule['required']) || $relative !== []) {
                    $local[(string) $name] = ($local[(string) $name] ?? 0) + 1;
                }
                if (count($relative) !== count($paths)) {
                    $rootRules++;
                }
            }
        }
        self::assertNotSame([], $local);
        ksort($local);
        self::assertSame($local, array_map('count', RequiredIfRules::MODELS));
        self::assertGreaterThan(0, $rootRules);
        self::assertCount($rootRules, RequiredIfRules::ROOTS['SendOmniRequest']); // every `$.` rule is reachable from a send
    }
}
