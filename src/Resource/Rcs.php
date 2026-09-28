<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Http\Transport;
use Bizgo\Resource\Rcs\Brands;
use Bizgo\Resource\Rcs\Chatbots;
use Bizgo\Resource\Rcs\CommonFormats;
use Bizgo\Resource\Rcs\TemplateForms;
use Bizgo\Resource\Rcs\TemplateImages;
use Bizgo\Resource\Rcs\Templates;

/**
 * `$client->rcs` (groups the resources below).
 */
final class Rcs
{
    /** `$client->rcs->brands`. */
    public readonly Brands $brands;

    /** `$client->rcs->chatbots`. */
    public readonly Chatbots $chatbots;

    /** `$client->rcs->commonFormats`. */
    public readonly CommonFormats $commonFormats;

    /** `$client->rcs->templateForms`. */
    public readonly TemplateForms $templateForms;

    /** `$client->rcs->templates`. */
    public readonly Templates $templates;

    /** `$client->rcs->templateImages`. */
    public readonly TemplateImages $templateImages;

    /** @internal */
    public function __construct(Transport $transport)
    {
        $this->brands = new Brands($transport);
        $this->chatbots = new Chatbots($transport);
        $this->commonFormats = new CommonFormats($transport);
        $this->templateForms = new TemplateForms($transport);
        $this->templates = new Templates($transport);
        $this->templateImages = new TemplateImages($transport);
    }
}
