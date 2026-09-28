<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo;

use Bizgo\Http\Transport;
use Bizgo\Resource\Alimtalk;
use Bizgo\Resource\BrandMessage;
use Bizgo\Resource\Counsel;
use Bizgo\Resource\Insights;
use Bizgo\Resource\Kakao;
use Bizgo\Resource\Rcs;
use Bizgo\Resource\Reservations;

/**
 * The resources generated from the spec, as properties of {@see BizgoClient}.
 *
 * @internal
 */
trait GeneratedResources
{
    /** `$client->alimtalk`. */
    public readonly Alimtalk $alimtalk;

    /** `$client->brandMessage`. */
    public readonly BrandMessage $brandMessage;

    /** `$client->counsel`. */
    public readonly Counsel $counsel;

    /** `$client->insights`. */
    public readonly Insights $insights;

    /** `$client->kakao`. */
    public readonly Kakao $kakao;

    /** `$client->rcs`. */
    public readonly Rcs $rcs;

    /** `$client->reservations`. */
    public readonly Reservations $reservations;

    private function initGeneratedResources(Transport $transport): void
    {
        $this->alimtalk = new Alimtalk($transport);
        $this->brandMessage = new BrandMessage($transport);
        $this->counsel = new Counsel($transport);
        $this->insights = new Insights($transport);
        $this->kakao = new Kakao($transport);
        $this->rcs = new Rcs($transport);
        $this->reservations = new Reservations($transport);
    }
}
