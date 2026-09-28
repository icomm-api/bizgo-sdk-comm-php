<?php

declare(strict_types=1);

namespace Bizgo;

/** Bizgo API server. */
enum Environment: string
{
    /** 운영 서버. 실제로 발송됩니다. */
    case Production = 'https://mars.ibapi.kr';

    /** 테스트 서버. 운영과 같은 API Key를 쓰며 실제로 발송되지 않습니다. */
    case Sandbox = 'https://sandbox-mars.ibapi.kr';
}
