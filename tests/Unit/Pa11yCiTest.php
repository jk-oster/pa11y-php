<?php

namespace JkOster\Pa11y\Tests\Unit;

use JkOster\Pa11y\Pa11yCi;
use JkOster\Pa11y\Pa11yCiResult;
use JkOster\Pa11y\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Pa11yCiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testPa11yCiIntallation()
    {
        $result = shell_exec('pa11y-ci --version');
        $this->assertNotEmpty($result);
    }

    public function testPa11yCi()
    {
        $results = Pa11yCi::fromUrls(['https://laravel.com'])->run();
        $this->assertInstanceOf(Pa11yCiResult::class, $results);

        $errorsCount = $results->getTotalErrorsCount();
        $this->assertGreaterThan(0, $errorsCount);

        $issuesCount = $results->getUrlsCount();
        $this->assertGreaterThan(0, $issuesCount);

        $urls = $results->getUrls();
        $this->assertGreaterThan(0, count($urls));
        $this->assertTrue(in_array('https://laravel.com/', $urls));

        $json = $results->json();
        $this->assertNotEmpty($json);
    }

    public function testPa11yCiWithSitemap()
    {
        $command = Pa11yCi::fromSitemap('https://laravel.com/sitemap_pages.xml');
        $results = $command->run();
        $this->assertInstanceOf(Pa11yCiResult::class, $results);

        $errorsCount = $results->getTotalErrorsCount();
        $this->assertGreaterThan(0, $errorsCount);

        $issuesCount = $results->getUrlsCount();
        $this->assertGreaterThan(0, $issuesCount);

        $urls = $results->getUrls();
        $this->assertGreaterThan(0, count($urls));
        $this->assertTrue(in_array('https://laravel.com/', $urls));

        $json = $results->json();
        $this->assertNotEmpty($json);
    }

    public function testPa11yCiWithJsonConfig()
    {
        $command = Pa11yCi::fromUrls(['https://laravel.com']);
            // ->config('{"threshold": 99999999, "urls": ["https://studiomitte.com"],"defaults":{"chromeLaunchConfig": {"args": ["--no-sandbox"]},"level": "none","includeWarnings": true,"includeNotices": true, "reporters": ["json"]}}');

        $results = $command->run();
        $this->assertInstanceOf(Pa11yCiResult::class, $results);

        $errorsCount = $results->getTotalErrorsCount();
        $this->assertGreaterThan(0, $errorsCount);

        $issuesCount = $results->getUrlsCount();
        $this->assertGreaterThan(0, $issuesCount);

        $urls = $results->getUrls();
        $this->assertGreaterThan(0, count($urls));
        $this->assertTrue(in_array('https://laravel.com/', $urls));

        $json = $results->json();
        $this->assertNotEmpty($json);
    }

    public function testPa11yCiWithJsonConfigAndFileOutput()
    {
        $command = Pa11yCi::fromUrls(['https://laravel.com']);
            // ->config('{"threshold": 99999999,"urls": ["https://studiomitte.com"],"defaults":{"chromeLaunchConfig": {"args": ["--no-sandbox"]},"level": "none", "includeWarnings": true,"includeNotices": true, "reporters": [["json", {"fileName": "./results.json"}]]}}');

        $results = $command->run();
        $this->assertInstanceOf(Pa11yCiResult::class, $results);

        $errorsCount = $results->getTotalErrorsCount();
        $this->assertGreaterThan(0, $errorsCount);

        $issuesCount = $results->getUrlsCount();
        $this->assertGreaterThan(0, $issuesCount);

        $urls = $results->getUrls();
        $this->assertGreaterThan(0, count($urls));
        $this->assertTrue(in_array('https://laravel.com/', $urls));

        $json = $results->json();
        $this->assertNotEmpty($json);
    }
}