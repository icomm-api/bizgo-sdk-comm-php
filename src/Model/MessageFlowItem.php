<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

use Bizgo\Exception\ValidationException;

/**
 * 발송할 채널 메시지 1개입니다. 객체에는 채널 키(`sms`, `mms`, `international`, `rcs`, `alimtalk`,
 * `brandmessage`, `navertalk`) 중 **정확히 하나**를 넣습니다.
 * `messageFlow` 배열의 앞 메시지가 실패하면 다음 메시지가 자동으로 대체발송(Fallback)됩니다.
 *
 * `messageFlow` 항목 1개입니다. 채널 메시지 하나를 `{"<채널 키>": {...}}` 형태로 감쌉니다.
 */
final readonly class MessageFlowItem implements RequestModel
{
    /** @var array<string, class-string<ChannelMessage>> channel key => message class */
    public const CHANNELS = [
        'sms' => SmsMessage::class,
        'mms' => MmsMessage::class,
        'international' => InternationalMessage::class,
        'rcs' => RcsMessage::class,
        'alimtalk' => AlimtalkMessage::class,
        'brandmessage' => BrandMessage::class,
        'navertalk' => NaverTalkMessage::class,
    ];

    public function __construct(
        /** The channel message. */
        public ChannelMessage $message,
    ) {
    }

    /** Wraps a channel message; a flow item is returned as is. */
    public static function of(ChannelMessage|self $item): self
    {
        return $item instanceof self ? $item : new self($item);
    }

    /** The channel key, for example `sms`. */
    public function channelKey(): string
    {
        return $this->message->channelKey();
    }

    /**
     * Builds the item from `['<channel key>' => [...]]`. Exactly one channel key (sms, mms, international, rcs, alimtalk, brandmessage, navertalk) is allowed.
     *
     * @param array<array-key, mixed> $data
     *
     * @throws ValidationException
     */
    public static function fromArray(#[\SensitiveParameter] array $data, string $path = ''): static
    {
        $keys = array_map('strval', array_keys($data));
        if (count($keys) !== 1 || !isset(self::CHANNELS[$keys[0]])) {
            throw ValidationException::of(
                $path,
                'messageFlow 항목에는 채널 키(sms, mms, international, rcs, alimtalk, brandmessage, navertalk) 중 정확히 하나가 있어야 합니다',
            );
        }
        $key = $keys[0];
        $value = $data[$key];
        if (!is_array($value)) {
            throw ValidationException::of(ValidationException::join($path, $key), '객체(키-값 배열)여야 합니다');
        }

        return new static(self::CHANNELS[$key]::fromArray($value, ValidationException::join($path, $key)));
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [$this->channelKey() => $this->message->toArray()];
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return [$this->channelKey() => $this->message];
    }

    /**
     * @param list<self|ChannelMessage>|null $items
     *
     * @return list<self>|null
     */
    public static function wrapAll(?array $items): ?array
    {
        return $items === null ? null : array_map(self::of(...), $items);
    }

    /**
     * @param list<self|ChannelMessage>|null $items
     *
     * @return list<array<string, mixed>>|null
     */
    public static function listToArray(?array $items): ?array
    {
        if ($items === null) {
            return null;
        }

        return array_map(static fn (self|ChannelMessage $i): array => self::of($i)->toArray(), $items);
    }
}
