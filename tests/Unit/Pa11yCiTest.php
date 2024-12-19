<?php

namespace JkOster\Pa11y\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use JkOster\Pa11y\Pa11yCi;
use JkOster\Pa11y\Pa11yCiResult;
use JkOster\Pa11y\Tests\TestCase;

class Pa11yCiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_pa11y_ci_intallation()
    {
        $result = shell_exec('pa11y-ci --version');
        $this->assertNotEmpty($result);
    }

    public function test_pa11y_ci()
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

    public function test_pa11y_ci_with_sitemap()
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

    public function test_pa11y_ci_with_json_config()
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

    public function test_pa11y_ci_with_json_config_and_file_output()
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
