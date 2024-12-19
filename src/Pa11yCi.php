<?php

namespace JkOster\Pa11y;

use Illuminate\Support\Arr;
use JkOster\Pa11y\Exceptions\CouldNotReadOutputJson;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Pa11yCi
{
    public function __construct(protected array $urls)
    {
        $this->urls($this->urls);
    }

    protected array $options = [];

    protected bool $deleteTempFiles = true;

    public static function fromUrls(array $urls): Pa11yCi
    {
        return new Pa11yCi($urls);
    }

    public static function fromSitemap(string $url): Pa11yCi
    {
        return (new Pa11yCi([]))->sitemap($url);
    }

    protected array $defaultConfig = [
        'defaults' => [
            // pa11y options
            'timeout' => 10000,
            'chromeLaunchConfig' => [
                'args' => [
                    '--no-sandbox',
                ],
                'headless' => true,
                'ignoreHTTPSErrors' => true,
            ],
            'viewport' => [
                'width' => 1280,
                'height' => 1024,
                'deviceScaleFactor' => 1,
                'isMobile' => false,
            ],
            'level' => 'none',
            'includeWarnings' => true,
            'includeNotices' => true,

            // 'runners' => ['htmlcs'],
            // 'ignore' => [],
            // 'screenCapture' => null,
            // 'debug' => false,
            // 'actions' => [],
            // 'headers' => [],
            // 'hideElements' => null,
            // 'method' => 'GET',
            // 'postData' => [],
            // 'rules' => [],
            // 'rootElement' => null,
            // 'screenCapture' => null,
            // 'standard' => 'WCAG2A',
            // 'userAgent' => 'A11Y TESTS',
            // 'wait' => 0,

            // pa11y-ci specific options
            'reporters' => ['json'],
        ],
        'threshold' => 99999999,
        'concurrency' => 1,
        // 'sitemap' => null,
        // 'sitemapReplace' => null,
        // 'sitemapFind' => null,
        // 'sitemapExclude' => null,
        'urls' => [],
    ];

    protected int $timeoutInSeconds = 600;

    protected bool $debug = false;

    public function setOption(string $optionPath, $value = null): self
    {
        Arr::set($this->options, $optionPath, $value);

        return $this;
    }

    public function urls(array $urls): self
    {
        return $this->setOption('urls', $urls);
    }

    public function sitemap(string $sitemapUrl): self
    {
        return $this->setOption('sitemap', $sitemapUrl);
    }

    public function sitemapReplace(string $string): self
    {
        return $this->setOption('sitemapReplace', $string);
    }

    public function sitemapFind(string $pattern): self
    {
        return $this->setOption('sitemapFind', $pattern);
    }

    public function sitemapExclude(string $pattern): self
    {
        return $this->setOption('sitemapExclude', $pattern);
    }

    public function includeNotices(bool $include = true): self
    {
        return $this->setOption('defaults.includeNotices', $include);
    }

    public function includeWarnings(bool $include = true): self
    {
        return $this->setOption('defaults.includeWarnings', $include);
    }

    public function timeout(int $ms): self
    {
        return $this->setOption('defaults.timeout', $ms);
    }

    public function wait(int $ms): self
    {
        return $this->setOption('defaults.wait', $ms);
    }

    // public function level(string $level): self
    // {
    //     return $this->setOption('defaults.level', $level);
    // }

    public function standard(string $standard): self
    {
        return $this->setOption('defaults.standard', $standard);
    }

    // public function threshold(int $threshold): self
    // {
    //     $this->setOption('threshold', $threshold);
    //     return $this->setOption('defaults.threshold', $threshold);
    // }

    public function runners(array $runners = ['htmlcs']): self
    {
        return $this->setOption('defaults.runners', $runners);
    }

    public function actions(array $actions): self
    {
        return $this->setOption('defaults.actions', $actions);
    }

    public function headers(array $headers): self
    {
        return $this->setOption('defaults.headers', $headers);
    }

    public function screenCapture(string $path): self
    {
        return $this->setOption('defaults.screenCapture', $path);
    }

    public function rules(array $rules): self
    {
        return $this->setOption('defaults.rules', $rules);
    }

    public function ignore(array $ignore): self
    {
        return $this->setOption('defaults.ignore', $ignore);
    }

    public function viewport(int $width, int $height, int $deviceScaleFactor = 1, bool $isMobile = false): self
    {
        return $this->setOption('defaults.viewport', [
            'width' => $width,
            'height' => $height,
            'deviceScaleFactor' => $deviceScaleFactor,
            'isMobile' => $isMobile,
        ]);
    }

    public function hideElements(array $elements): self
    {
        return $this->setOption('defaults.hideElements', $elements);
    }

    public function userAgent(string $userAgent): self
    {
        return $this->setOption('defaults.userAgent', $userAgent);
    }

    public function method(string $method): self
    {
        return $this->setOption('defaults.method', $method);
    }

    public function postData(array $postData): self
    {
        return $this->setOption('defaults.postData', $postData);
    }

    public function rootElement(string $rootElement): self
    {
        return $this->setOption('defaults.rootElement', $rootElement);
    }

    public function saveToFile(?string $path = null): self
    {
        if ($path === null) {
            $path = tempnam(sys_get_temp_dir(), 'pa11y_');
        }

        return $this->setOption('defaults.reporters', [
            ['json', ['fileName' => $path]],
        ]);
    }

    public function concurrency(int $concurrency): self
    {
        return $this->setOption('concurrency', $concurrency);
    }

    public function deleteTempFiles(bool $delete = true): self
    {
        $this->deleteTempFiles = $delete;

        return $this;
    }

    public function processTimeout(int $sec): self
    {
        $this->timeoutInSeconds = $sec;

        return $this;
    }

    public function config(array $config): self
    {
        $this->options = array_merge_recursive($this->options, $config);

        return $this;
    }

    public function debug(bool $debug = true): self
    {
        $this->debug = $debug;

        return $this;
    }

    public function run(): Pa11yCiResult
    {
        $config = json_encode(array_merge_recursive($this->defaultConfig, $this->options));

        $command = [
            (new ExecutableFinder)->find('pa11y-ci', 'pa11y-ci', [
                __DIR__.'/../node_modules/bin',
                __DIR__.'/../../node_modules/bin',
                __DIR__.'/../../../node_modules/bin',
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            '--config',
            $config,
            '--json',
        ];

        if ($this->debug) {
            echo '[DEBUG] running command: "'.implode(' ', $command).'"'.PHP_EOL;
        }

        $process = new Process(
            command: $command,
            cwd: __DIR__.'/..',
            timeout: $this->timeoutInSeconds,
        );

        $process->run();
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $result = json_decode($output, true);

        if (empty($output)) {
            throw CouldNotReadOutputJson::from(implode(' ', $command), '[empty output]');
        }

        if (is_string($result) && file_exists($result)) {
            $output = file_get_contents($result);
            $result = json_decode($output, true);

            if ($this->deleteTempFiles) {
                unlink($result);
            }
        }

        if (! is_array($result)) {
            throw CouldNotReadOutputJson::from(implode(' ', $command), $output);
        }

        return new Pa11yCiResult($result);
    }
}
