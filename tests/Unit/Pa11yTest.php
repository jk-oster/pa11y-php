<?php

namespace JkOster\Pa11y\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use JkOster\Pa11y\Pa11y;
use JkOster\Pa11y\Pa11yResult;
use JkOster\Pa11y\Tests\TestCase;

class Pa11yTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_pa11y_intallation()
    {
        $result = shell_exec('pa11y --version');
        $this->assertNotEmpty($result);
    }

    public function test_pa11y()
    {
        $results = Pa11y::fromUrl('https://laravel.com')->run();
        $this->assertInstanceOf(Pa11yResult::class, $results);

        $errorsCount = $results->getErrorsCount();
        $this->assertGreaterThan(0, $errorsCount);

        $groupedIssues = $results->getGroupedIssues();
        $this->assertGreaterThan(0, count($groupedIssues));

        $issuesCount = $results->getTotalIssueCount();
        $this->assertGreaterThan(0, $issuesCount);

        $json = $results->json();
        $this->assertNotEmpty($json);
    }

    public function test_pa11y_multiple_urls()
    {
        $results = [
            Pa11y::fromUrl('https://laravel.com')->run(),
            Pa11y::fromUrl('https://studiomitte.com')->run(),
            Pa11y::fromUrl('https://google.com')->run(),
            Pa11y::fromUrl('https://isocell.com')->run(),
            Pa11y::fromUrl('https://kwb.net')->run(),
        ];
        $this->assertInstanceOf(Pa11yResult::class, $results[0]);
        $this->assertInstanceOf(Pa11yResult::class, $results[4]);
    }
}
