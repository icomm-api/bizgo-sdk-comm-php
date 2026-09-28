<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Model;

/**
 * A channel message you can put in `$client->send->omni(messages: [...])`.
 *
 * | messageFlow key | class |
 * |---|---|
 * | `sms` | {@see SmsMessage} |
 * | `mms` | {@see MmsMessage} |
 * | `international` | {@see InternationalMessage} |
 * | `rcs` | {@see RcsMessage} |
 * | `alimtalk` | {@see AlimtalkMessage} |
 * | `brandmessage` | {@see BrandMessage} |
 * | `navertalk` | {@see NaverTalkMessage} |
 */
interface ChannelMessage extends RequestModel
{
    /** The `messageFlow` channel key, for example `sms` or `alimtalk`. */
    public function channelKey(): string;
}
