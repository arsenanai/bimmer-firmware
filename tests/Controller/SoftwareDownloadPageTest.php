<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SoftwareDownloadPageTest extends WebTestCase
{
    public function testHomeRedirectsToDownloadPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/carplay/software-download');
    }

    public function testDownloadPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/carplay/software-download');

        $this->assertResponseIsSuccessful();
    }

    public function testPageHasTitle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/carplay/software-download');

        $this->assertSelectorTextContains('.title', 'Update the software for your');
        $this->assertSelectorTextContains('.title', 'CarPlay / Android Auto MMI');
    }

    public function testPageHasForm(): void
    {
        $client = static::createClient();
        $client->request('GET', '/carplay/software-download');

        $this->assertSelectorExists('form#softwareForm');
        $this->assertSelectorExists('input#version');
        $this->assertSelectorExists('input#hwVersion');
        $this->assertSelectorExists('button[type="submit"]');
    }

    public function testMcuFieldIsNotAnInput(): void
    {
        $client = static::createClient();
        $client->request('GET', '/carplay/software-download');

        $this->assertSelectorNotExists('input#mcuVersion');
        $this->assertSelectorExists('.mcu-note');
        $this->assertSelectorTextContains('.mcu-text', 'not required');
    }

    public function testPageHasWarningBox(): void
    {
        $client = static::createClient();
        $client->request('GET', '/carplay/software-download');

        $this->assertSelectorExists('.alert-warning');
        $this->assertSelectorTextContains('.alert-warning', 'Warning!!!');
        $this->assertSelectorTextContains('.alert-warning', 'brick the MMI');
    }

    public function testPageHasShopLink(): void
    {
        $client = static::createClient();
        $client->request('GET', '/carplay/software-download');

        $this->assertSelectorExists('a.shop-link');
    }

    public function testPageHasHelpModal(): void
    {
        $client = static::createClient();
        $client->request('GET', '/carplay/software-download');

        $this->assertSelectorExists('#openModalLink');
        $this->assertSelectorExists('#softwareModal');
        $this->assertSelectorTextContains('.help-link', 'What is my current software?');
    }

    public function testPageHasDisclaimer(): void
    {
        $client = static::createClient();
        $client->request('GET', '/carplay/software-download');

        $this->assertSelectorTextContains('.disclaimer', 'BMW and MINI are registered trademarks');
    }

    public function testPageLoadsJavascript(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/carplay/software-download');

        $scripts = $crawler->filter('script[src*="software-download.js"]');
        $this->assertCount(1, $scripts);
    }

    public function testPageLoadsCss(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/carplay/software-download');

        $styles = $crawler->filter('link[href*="software-download.css"]');
        $this->assertCount(1, $styles);
    }

    public function testPageIsPublicNoAuthRequired(): void
    {
        $client = static::createClient();
        $client->request('GET', '/carplay/software-download');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }
}
