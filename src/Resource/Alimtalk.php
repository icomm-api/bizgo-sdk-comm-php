<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Http\Transport;
use Bizgo\Resource\Alimtalk\PublicTemplates;
use Bizgo\Resource\Alimtalk\TemplateCategories;
use Bizgo\Resource\Alimtalk\Templates;

/**
 * `$client->alimtalk` (groups the resources below).
 */
final class Alimtalk
{
    /** `$client->alimtalk->templates`. */
    public readonly Templates $templates;

    /** `$client->alimtalk->templateCategories`. */
    public readonly TemplateCategories $templateCategories;

    /** `$client->alimtalk->publicTemplates`. */
    public readonly PublicTemplates $publicTemplates;

    /** @internal */
    public function __construct(Transport $transport)
    {
        $this->templates = new Templates($transport);
        $this->templateCategories = new TemplateCategories($transport);
        $this->publicTemplates = new PublicTemplates($transport);
    }
}
