<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Security\AdminUserProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminAccessTest extends WebTestCase
{
    public function testAdminDashboardRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseRedirects('/admin/login');
    }

    public function testSoftwareVersionListRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/software-version');

        $this->assertResponseRedirects('/admin/login');
    }

    public function testLoginPageIsPublic(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
        $this->assertSelectorExists('input[name="_csrf_token"]');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/login');

        $client->submitForm('Sign in', [
            '_username' => 'admin',
            '_password' => 'wrong_password',
        ]);

        $this->assertResponseRedirects('/admin/login');
        $client->followRedirect();
        $this->assertSelectorExists('.error-msg');
    }

    public function testDashboardAccessibleWhenAuthenticated(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.dash-stats');
        $this->assertSelectorExists('.dash-table');
        $this->assertSelectorTextContains('.dash-stat-label', 'Total versions');
    }

    public function testSoftwareVersionListAccessibleWhenAuthenticated(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/admin/software-version');

        $this->assertResponseIsSuccessful();
    }

    public function testDashboardHasLinkToCustomerPage(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('a.dash-link[href="/carplay/software-download"]');
    }

    public function testLogoutRedirectsToLogin(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/admin/logout');

        $this->assertResponseRedirects('/admin/login');
    }

    private function createAuthenticatedClient(): KernelBrowser
    {
        $client = static::createClient();

        $provider = static::getContainer()->get(AdminUserProvider::class);
        $user = $provider->loadUserByIdentifier('admin');
        $client->loginUser($user, 'admin');

        return $client;
    }
}
