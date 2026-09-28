<?php

// Generated from spec/openapi.yaml by bin/generate-models.php. Do not edit.

declare(strict_types=1);

namespace Bizgo\Resource;

use Bizgo\Http\Transport;
use Bizgo\Resource\Counsel\Certs;
use Bizgo\Resource\Counsel\Channels;
use Bizgo\Resource\Counsel\ConsultTime;
use Bizgo\Resource\Counsel\Files;
use Bizgo\Resource\Counsel\Messages;
use Bizgo\Resource\Counsel\Sessions;
use Bizgo\Resource\Counsel\SystemMessages;
use Bizgo\Resource\Counsel\Users;

/**
 * `$client->counsel` (groups the resources below).
 */
final class Counsel
{
    /** `$client->counsel->messages`. */
    public readonly Messages $messages;

    /** `$client->counsel->sessions`. */
    public readonly Sessions $sessions;

    /** `$client->counsel->users`. */
    public readonly Users $users;

    /** `$client->counsel->certs`. */
    public readonly Certs $certs;

    /** `$client->counsel->files`. */
    public readonly Files $files;

    /** `$client->counsel->channels`. */
    public readonly Channels $channels;

    /** `$client->counsel->consultTime`. */
    public readonly ConsultTime $consultTime;

    /** `$client->counsel->systemMessages`. */
    public readonly SystemMessages $systemMessages;

    /** @internal */
    public function __construct(Transport $transport)
    {
        $this->messages = new Messages($transport);
        $this->sessions = new Sessions($transport);
        $this->users = new Users($transport);
        $this->certs = new Certs($transport);
        $this->files = new Files($transport);
        $this->channels = new Channels($transport);
        $this->consultTime = new ConsultTime($transport);
        $this->systemMessages = new SystemMessages($transport);
    }
}
