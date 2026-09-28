<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Http\Transport;
use Bizgo\Resource\Insights\Alimtalk;
use Bizgo\Resource\Insights\BrandMessage;
use Bizgo\Resource\Insights\Rcs;

/**
 * `$client->insights` (groups the resources below).
 */
final class Insights
{
    /** `$client->insights->alimtalk`. */
    public readonly Alimtalk $alimtalk;

    /** `$client->insights->brandMessage`. */
    public readonly BrandMessage $brandMessage;

    /** `$client->insights->rcs`. */
    public readonly Rcs $rcs;

    /** @internal */
    public function __construct(Transport $transport)
    {
        $this->alimtalk = new Alimtalk($transport);
        $this->brandMessage = new BrandMessage($transport);
        $this->rcs = new Rcs($transport);
    }
}
