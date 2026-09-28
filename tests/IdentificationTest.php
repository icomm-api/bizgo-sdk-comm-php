<?php

declare(strict_types=1);

namespace Bizgo\Tests;

use Bizgo\AppInfo;
use Bizgo\Exception\ConfigurationException;
use Bizgo\Http\Identification;
use Bizgo\Testing\FakeTransport;
use Bizgo\Version;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\DataProvider;

/** SDK identification headers (SDK-DESIGN.md §2.1). */
final class IdentificationTest extends TestCase
{
    public function testHeadersOnEveryRequest(): void
    {
        $this->http->always(self::reply(self::envelope(['report' => []])));
        $this->client->reports->inquiry('KEY001');
        $request = $this->http->last();
        self::assertMatchesRegularExpression(
            '~^bizgo-sdk-comm-php/' . preg_quote(Version::SDK, '~') . ' php/\d+\.\d+\.\d+ \((linux|windows|darwin|freebsd|other); (x64|arm64|x86|arm|other)\)$~',
            $request->getHeaderLine('User-Agent'),
        );
        self::assertSame('bizgo-sdk-comm-php/' . Version::SDK, $request->getHeaderLine('X-Bizgo-Client'));
        self::assertSame('bizgo-sdk-comm-php/1.2.0', $request->getHeaderLine('X-Bizgo-Client'));
    }

    public function testAppInfoIsAppended(): void
    {
        $factory = new Psr17Factory();
        $client = new \Bizgo\BizgoClient(
            apiKey: self::API_KEY,
            httpClient: $this->http,
            trustHttpClient: true,
            requestFactory: $factory,
            streamFactory: $factory,
            appInfo: new AppInfo('myshop', '1.4.2+build.7'),
            rateLimit: null,
        );
        $this->http->always(self::reply(self::envelope(['report' => []])));
        $client->reports->inquiry('KEY001');
        self::assertStringEndsWith(') app/myshop-1.4.2+build.7', $this->http->last()->getHeaderLine('User-Agent'));
        self::assertSame($this->http->last()->getHeaderLine('User-Agent'), $client->userAgent());
    }

    /** @return iterable<array{string, string}> */
    public static function badAppInfo(): iterable
    {
        yield 'empty name' => ['', '1.0'];
        yield 'space' => ['my shop', '1.0'];
        yield 'header injection' => ["shop\r\nX-Evil: 1", '1.0'];
        yield 'e-mail' => ['user@example.com', '1.0'];
        yield 'long name' => [str_repeat('a', 51), '1.0'];
        yield 'bad version' => ['shop', '1.0 beta'];
        yield 'long version' => ['shop', str_repeat('1', 31)];
        yield 'non-ascii' => ['상점', '1.0'];
    }

    #[DataProvider('badAppInfo')]
    public function testAppInfoIsValidated(string $name, string $version): void
    {
        $this->expectException(ConfigurationException::class);
        new AppInfo($name, $version);
    }

    public function testCoarseOsAndArchitecture(): void
    {
        self::assertSame(['linux', 'windows', 'darwin', 'freebsd', 'other', 'other'], [
            Identification::os('Linux'),
            Identification::os('Windows'),
            Identification::os('Darwin'),
            Identification::os('BSD', 'FreeBSD'),
            Identification::os('BSD', 'OpenBSD'),
            Identification::os('Solaris'),
        ]);
        self::assertSame(['x64', 'x64', 'arm64', 'arm64', 'x86', 'arm', 'other'], array_map(Identification::arch(...), ['x86_64', 'AMD64', 'aarch64', 'arm64', 'i686', 'armv7l', 'riscv64']));
        self::assertStringNotContainsString((string) gethostname(), Identification::userAgent());
    }

    /** A header parameter can never replace the key or the identification headers. */
    public function testHeaderParametersCannotReplaceSdkHeaders(): void
    {
        $fake = new FakeTransport();
        $fake->client()->kakao->senders->create(
            body: ['yellowId' => '@KAKAO_CHANNEL_ID_EXAMPLE', 'categoryCode' => '00100010001'],
            token: '123456',
            phoneNumber: '01000000000',
        );
        self::assertContains('token', $fake->lastRequest()->headerNames);
        $this->expectException(\Bizgo\Exception\ValidationException::class);
        $fake->client()->kakao->senders->create(body: ['yellowId' => '@KAKAO_CHANNEL_ID_EXAMPLE', 'categoryCode' => '00100010001'], token: "1\r\nAuthorization: x", phoneNumber: '01000000000');
    }
}
