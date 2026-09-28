<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Http\Transport;
use Bizgo\Resource\BrandMessage\Audience;
use Bizgo\Resource\BrandMessage\FriendGroups;
use Bizgo\Resource\BrandMessage\GroupSends;
use Bizgo\Resource\BrandMessage\GroupTags;
use Bizgo\Resource\BrandMessage\MarketingAgreements;
use Bizgo\Resource\BrandMessage\Permissions;
use Bizgo\Resource\BrandMessage\Templates;
use Bizgo\Resource\BrandMessage\UnsubscribeContents;
use Bizgo\Resource\BrandMessage\Videos;

/**
 * `$client->brandMessage` (groups the resources below).
 */
final class BrandMessage
{
    /** `$client->brandMessage->groupSends`. */
    public readonly GroupSends $groupSends;

    /** `$client->brandMessage->audience`. */
    public readonly Audience $audience;

    /** `$client->brandMessage->permissions`. */
    public readonly Permissions $permissions;

    /** `$client->brandMessage->videos`. */
    public readonly Videos $videos;

    /** `$client->brandMessage->templates`. */
    public readonly Templates $templates;

    /** `$client->brandMessage->groupTags`. */
    public readonly GroupTags $groupTags;

    /** `$client->brandMessage->friendGroups`. */
    public readonly FriendGroups $friendGroups;

    /** `$client->brandMessage->marketingAgreements`. */
    public readonly MarketingAgreements $marketingAgreements;

    /** `$client->brandMessage->unsubscribeContents`. */
    public readonly UnsubscribeContents $unsubscribeContents;

    /** @internal */
    public function __construct(Transport $transport)
    {
        $this->groupSends = new GroupSends($transport);
        $this->audience = new Audience($transport);
        $this->permissions = new Permissions($transport);
        $this->videos = new Videos($transport);
        $this->templates = new Templates($transport);
        $this->groupTags = new GroupTags($transport);
        $this->friendGroups = new FriendGroups($transport);
        $this->marketingAgreements = new MarketingAgreements($transport);
        $this->unsubscribeContents = new UnsubscribeContents($transport);
    }
}
