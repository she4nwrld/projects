<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminPagesTest extends WebTestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function adminPages(): iterable
    {
        yield 'bookings' => ['/admin/dashboard', 'Every reservation'];
        yield 'fleet' => ['/admin/products', 'Vehicles'];
        yield 'maintenance' => ['/admin/maintenance', 'Repairs and damage'];
        yield 'promos' => ['/admin/promos', 'Deals and codes'];
        yield 'customers' => ['/admin/customers', 'Registered customers'];
        yield 'reviews' => ['/admin/reviews', 'Customer reviews'];
        yield 'inbox' => ['/admin/messages', 'Contact messages'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminPages')]
    public function testPageRendersWithItsHeading(string $url, string $heading): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        self::assertResponseIsSuccessful();
        self::assertSame($heading, $crawler->filter('.list-head h1')->text());

        // The shared chrome is present on every admin page.
        self::assertCount(1, $crawler->filter('aside.adm-side'));
        self::assertCount(1, $crawler->filter('header.adm-top'));
    }

    public function testFilterTabMarksTheActiveView(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/products?view=hidden');

        self::assertResponseIsSuccessful();
        self::assertSame('Hidden', $crawler->filter('.admin-filters .filter-tab.is-active')->text());
        self::assertCount(1, $crawler->filter('article.ad-card'));
    }

    public function testUnknownFilterFallsBackToAll(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/dashboard?status=bogus');

        self::assertResponseIsSuccessful();
        self::assertSame('All', $crawler->filter('.admin-filters .filter-tab.is-active')->text());
    }

    public function testSidebarMarksTheCurrentSection(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/reviews');

        self::assertResponseIsSuccessful();
        self::assertSame('Reviews', $crawler->filter('.adm-nav .adm-link.is-on')->text());
    }
}
