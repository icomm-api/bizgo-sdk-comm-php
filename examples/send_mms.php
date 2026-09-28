<?php

declare(strict_types=1);

// Upload an image, then send an MMS with it.

namespace Bizgo\Examples\SendMms;

use Bizgo\BizgoClient;
use Bizgo\Environment;
use Bizgo\Upload;

use function Bizgo\Examples\env;
use function Bizgo\Examples\isMain;

require_once __DIR__ . '/_bootstrap.php';

/** @param string|Upload $image a local jpg path (max 300KB) or an Upload */
function run(BizgoClient $client, string $from, string $to, string|Upload $image): void
{
    $uploaded = $client->files->uploadMms($image, filename: 'banner.jpg');
    if ($uploaded->fileKey === null) {
        throw new \RuntimeException('업로드 응답에 fileKey가 없습니다');
    }
    $result = $client->send->mms(
        to: $to,
        from: $from,
        text: '첨부 이미지를 확인해 주세요.',
        fileKeys: [$uploaded->fileKey],
        title: '이벤트 안내',
    );
    echo '접수: ', implode(', ', $result->msgKeys()), ' / 파일 키 만료: ', $uploaded->expired ?? '-', PHP_EOL;
}

if (isMain(__FILE__)) {
    run(new BizgoClient(environment: Environment::Sandbox), env('BIZGO_FROM'), env('BIZGO_TO'), env('BIZGO_IMAGE'));
}
