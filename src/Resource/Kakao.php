<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Http\Transport;
use Bizgo\Resource\Kakao\Categories;
use Bizgo\Resource\Kakao\Groups;
use Bizgo\Resource\Kakao\Sanctions;
use Bizgo\Resource\Kakao\Senders;

/**
 * `$client->kakao` (groups the resources below).
 */
final class Kakao
{
    /** `$client->kakao->senders`. */
    public readonly Senders $senders;

    /** `$client->kakao->categories`. */
    public readonly Categories $categories;

    /** `$client->kakao->groups`. */
    public readonly Groups $groups;

    /** `$client->kakao->sanctions`. */
    public readonly Sanctions $sanctions;

    /** @internal */
    public function __construct(Transport $transport)
    {
        $this->senders = new Senders($transport);
        $this->categories = new Categories($transport);
        $this->groups = new Groups($transport);
        $this->sanctions = new Sanctions($transport);
    }
}
