<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api;

use App\Entity\SoftwareVersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SoftwareControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
    }

    private function createSoftwareVersion(
        string $name,
        string $systemVersion,
        string $systemVersionAlt,
        string $link,
        string $st,
        string $gd,
        bool $latest = false,
        ?string $latestDisplayVersion = null,
    ): SoftwareVersion {
        $software = new SoftwareVersion();
        $software->setName($name);
        $software->setSystemVersion($systemVersion);
        $software->setSystemVersionAlt($systemVersionAlt);
        $software->setLink($link);
        $software->setSt($st);
        $software->setGd($gd);
        $software->setIsLatest($latest);
        if (null !== $latestDisplayVersion) {
            $software->setLatestDisplayVersion($latestDisplayVersion);
        }

        $this->entityManager->persist($software);
        $this->entityManager->flush();

        return $software;
    }

    private function clearSoftwareVersions(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\SoftwareVersion')->execute();
    }

    /**
     * @return array<string, mixed>
     */
    private function postVersion(string $version, string $hwVersion): array
    {
        $this->client->request(
            'POST',
            '/api/carplay/software/version',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['version' => $version, 'hwVersion' => $hwVersion])
        );

        return json_decode($this->client->getResponse()->getContent(), true);
    }

    public function testVersionIsRequired(): void
    {
        $this->client->request(
            'POST',
            '/api/carplay/software/version',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['version' => '', 'hwVersion' => 'CPAA_2024.05.25'])
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Version is required', $response['msg']);
    }

    public function testHwVersionIsRequired(): void
    {
        $this->client->request(
            'POST',
            '/api/carplay/software/version',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['version' => '3.3.6.mmipri.c', 'hwVersion' => ''])
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('HW Version is required', $response['msg']);
    }

    public function testInvalidHwVersionReturnsError(): void
    {
        $this->client->request(
            'POST',
            '/api/carplay/software/version',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['version' => '3.3.6.mmipri.c', 'hwVersion' => 'INVALID'])
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('There was a problem identifying your software. Contact us for help.', $response['msg']);
        $this->assertFalse(isset($response['versionExist']));
    }

    public function testStandardSTReturnsUpdateAvailable(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.6.mmipri.c',
            '3.3.6.mmipri.c',
            'https://example.com/standard-3.3.6',
            'https://example.com/st',
            'https://example.com/gd',
            false,
            'v3.3.7',
        );
        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.7.mmipri.c',
            '3.3.7.mmipri.c',
            'https://example.com/standard',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.3.7',
        );

        $response = $this->postVersion('3.3.6.mmipri.c', 'CPAA_2024.05.25');

        $this->assertTrue($response['versionExist']);
        $this->assertStringContainsString('The latest version of software is v3.3.7', $response['msg']);
        $this->assertEquals('https://example.com/standard-3.3.6', $response['link']);
        $this->assertEquals('https://example.com/st', $response['st']);
        $this->assertEquals('', $response['gd']);
    }

    public function testStandardGDReturnsUpdateAvailable(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.6.mmipri.c',
            '3.3.6.mmipri.c',
            'https://example.com/standard-3.3.6',
            'https://example.com/st',
            'https://example.com/gd',
            false,
            'v3.3.7',
        );
        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.7.mmipri.c',
            '3.3.7.mmipri.c',
            'https://example.com/standard',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.3.7',
        );

        $response = $this->postVersion('3.3.6.mmipri.c', 'CPAA_G_2024.05.25');

        $this->assertTrue($response['versionExist']);
        $this->assertStringContainsString('The latest version of software is v3.3.7', $response['msg']);
        $this->assertEquals('https://example.com/standard-3.3.6', $response['link']);
        $this->assertEquals('', $response['st']);
        $this->assertEquals('https://example.com/gd', $response['gd']);
    }

    public function testSystemIsUpToDate(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.7.mmipri.c',
            '3.3.7.mmipri.c',
            'https://example.com/standard',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.3.7',
        );

        $response = $this->postVersion('3.3.7.mmipri.c', 'CPAA_2024.05.25');

        $this->assertTrue($response['versionExist']);
        $this->assertEquals('Your system is upto date!', $response['msg']);
        $this->assertEquals('', $response['link']);
    }

    public function testLCICICReturnsUpdateAvailable(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'LCI MMI PRO CIC',
            'v3.4.3.mmipro.c',
            '3.4.3.mmipro.c',
            'https://example.com/lci-cic-3.4.3',
            'https://example.com/st',
            'https://example.com/gd',
            false,
            'v3.4.4',
        );
        $this->createSoftwareVersion(
            'LCI MMI PRO CIC',
            'v3.4.4.mmipro.c',
            '3.4.4.mmipro.c',
            'https://example.com/lci-cic',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.4.4',
        );

        $response = $this->postVersion('3.4.3.mmipro.c', 'B_C_2024.05.25');

        $this->assertTrue($response['versionExist']);
        $this->assertStringContainsString('The latest version of software is v3.4.4', $response['msg']);
        $this->assertEquals('https://example.com/lci-cic-3.4.3', $response['link']);
        $this->assertEquals('https://example.com/st', $response['st']);
    }

    public function testLCINBTReturnsUpdateAvailable(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'LCI MMI PRO NBT',
            'v3.4.3.mmipro.nbt',
            '3.4.3.mmipro.nbt',
            'https://example.com/lci-nbt-3.4.3',
            'https://example.com/st',
            'https://example.com/gd',
            false,
            'v3.4.4',
        );
        $this->createSoftwareVersion(
            'LCI MMI PRO NBT',
            'v3.4.4.mmipro.nbt',
            '3.4.4.mmipro.nbt',
            'https://example.com/lci-nbt',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.4.4',
        );

        $response = $this->postVersion('3.4.3.mmipro.nbt', 'B_N_G_2024.05.25');

        $this->assertTrue($response['versionExist']);
        $this->assertStringContainsString('The latest version of software is v3.4.4', $response['msg']);
        $this->assertEquals('https://example.com/lci-nbt-3.4.3', $response['link']);
        $this->assertEquals('https://example.com/gd', $response['gd']);
    }

    public function testLCIEVOReturnsUpdateAvailable(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'LCI MMI PRO EVO',
            'v3.4.3.mmipro.evo',
            '3.4.3.mmipro.evo',
            'https://example.com/lci-evo-3.4.3',
            'https://example.com/st',
            'https://example.com/gd',
            false,
            'v3.4.4',
        );
        $this->createSoftwareVersion(
            'LCI MMI PRO EVO',
            'v3.4.4.mmipro.evo',
            '3.4.4.mmipro.evo',
            'https://example.com/lci-evo',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.4.4',
        );

        $response = $this->postVersion('3.4.3.mmipro.evo', 'B_E_G_2024.05.25');

        $this->assertTrue($response['versionExist']);
        $this->assertStringContainsString('The latest version of software is v3.4.4', $response['msg']);
        $this->assertEquals('https://example.com/lci-evo-3.4.3', $response['link']);
        $this->assertEquals('https://example.com/gd', $response['gd']);
    }

    public function testUnknownVersionReturnsNotFound(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.7.mmipri.c',
            '3.3.7.mmipri.c',
            'https://example.com/standard',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.3.7',
        );

        $response = $this->postVersion('2.0.0.mmipri.c', 'CPAA_2024.05.25');

        $this->assertFalse($response['versionExist']);
        $this->assertEquals('There was a problem identifying your software. Contact us for help.', $response['msg']);
    }

    public function testVersionWithVPrefixIsStripped(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.6.mmipri.c',
            '3.3.6.mmipri.c',
            'https://example.com/standard-3.3.6',
            'https://example.com/st',
            'https://example.com/gd',
            false,
            'v3.3.7',
        );
        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.7.mmipri.c',
            '3.3.7.mmipri.c',
            'https://example.com/standard',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.3.7',
        );

        $response = $this->postVersion('v3.3.6.mmipri.c', 'CPAA_2024.05.25');

        $this->assertTrue($response['versionExist']);
        $this->assertStringContainsString('The latest version of software is v3.3.7', $response['msg']);
    }

    public function testVersionWithUppercaseVPrefixIsStripped(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.6.mmipri.c',
            '3.3.6.mmipri.c',
            'https://example.com/standard-3.3.6',
            'https://example.com/st',
            'https://example.com/gd',
            false,
            'v3.3.7',
        );
        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.7.mmipri.c',
            '3.3.7.mmipri.c',
            'https://example.com/standard',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.3.7',
        );

        $response = $this->postVersion('V3.3.6.mmipri.c', 'CPAA_2024.05.25');

        $this->assertTrue($response['versionExist']);
        $this->assertStringContainsString('The latest version of software is v3.3.7', $response['msg']);
    }

    public function testStandardHardwareDoesNotMatchLCIEntries(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'LCI MMI PRO CIC',
            'v3.4.4.mmipro.c',
            '3.4.4.mmipro.c',
            'https://example.com/lci-cic',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.4.4',
        );

        $response = $this->postVersion('3.4.3.mmipro.c', 'CPAA_2024.05.25');

        $this->assertFalse($response['versionExist']);
        $this->assertEquals('There was a problem identifying your software. Contact us for help.', $response['msg']);
    }

    public function testLCIHardwareDoesNotMatchStandardEntries(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.7.mmipri.c',
            '3.3.7.mmipri.c',
            'https://example.com/standard',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.3.7',
        );

        $response = $this->postVersion('3.3.6.mmipri.c', 'B_C_2024.05.25');

        $this->assertFalse($response['versionExist']);
        $this->assertEquals('There was a problem identifying your software. Contact us for help.', $response['msg']);
    }

    public function testHWVersionWithSuffixMatchesPattern(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.6.mmipri.c',
            '3.3.6.mmipri.c',
            'https://example.com/standard-3.3.6',
            'https://example.com/st',
            'https://example.com/gd',
            false,
            'v3.3.7',
        );
        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.7.mmipri.c',
            '3.3.7.mmipri.c',
            'https://example.com/standard',
            'https://example.com/st',
            'https://example.com/gd',
            true,
            'v3.3.7',
        );

        $response = $this->postVersion('3.3.6.mmipri.c', 'CPAA_2024.05.25_DEBUG');

        $this->assertTrue($response['versionExist']);
    }

    public function testLatestDisplayVersionIsReadFromDatabase(): void
    {
        $this->clearSoftwareVersions();

        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.3.6.mmipri.c',
            '3.3.6.mmipri.c',
            'https://example.com/link',
            'https://example.com/st',
            '',
            false,
        );
        $this->createSoftwareVersion(
            'MMI Prime CIC',
            'v3.5.0.mmipri.c',
            '3.5.0.mmipri.c',
            '',
            '',
            '',
            true,
            'v3.5.0',
        );

        $response = $this->postVersion('3.3.6.mmipri.c', 'CPAA_2024.01.15');

        $this->assertTrue($response['versionExist']);
        $this->assertStringContainsString('v3.5.0', $response['msg']);
    }
}
