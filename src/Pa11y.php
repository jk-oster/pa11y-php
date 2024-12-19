<?php

namespace JkOster\Pa11y;

use JkOster\Pa11y\Exceptions\CouldNotReadOutputJson;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Pa11y
{
    public function __construct(protected string $url) {}

    protected array $options = [];

    public static function fromUrl(string $url): Pa11y
    {
        return new Pa11y($url);
    }

    const REQUIRED_OPTIONS = [
        'reporter' => 'json',
        'config' => './pa11y.config.json',
        'level' => 'none',
    ];

    protected int $timeoutInSeconds = 300;

    public function processTimeout(int $sec): self
    {
        $this->timeoutInSeconds = $sec;

        return $this;
    }

    public function setOption(string $option, $value = null): self
    {
        $this->options[$option] = $value;

        return $this;
    }

    public function standard(string $name): self
    {
        return $this->setOption('standard', $name);
    }

    public function runner(string $runner): self
    {
        return $this->setOption('runner', $runner);
    }

    public function ignore(string $ignore): self
    {
        return $this->setOption('ignore', $ignore);
    }

    public function includeNotices(): self
    {
        return $this->setOption('include-notices');
    }

    public function includeWarnings(): self
    {
        return $this->setOption('include-warnings');
    }

    public function rootElement(string $selector): self
    {
        return $this->setOption('root-element', $selector);
    }

    public function hideElements(string $selectors): self
    {
        return $this->setOption('hide-elements', $selectors);
    }

    public function config(string $path): self
    {
        return $this->setOption('config', $path);
    }

    public function timeout(int $ms): self
    {
        return $this->setOption('timeout', $ms);
    }

    public function wait(int $ms): self
    {
        return $this->setOption('wait', $ms);
    }

    public function debug(): self
    {
        return $this->setOption('debug');
    }

    public function screenCapture(string $path): self
    {
        return $this->setOption('screen-capture', $path);
    }

    public function addRule(string $rule): self
    {
        return $this->setOption('add-rule', $rule);
    }

    /**
     * Generates an array of arguments for the Pa11y CLI command
     */
    public function commandArgs(): array
    {
        $options = array_merge($this->options, self::REQUIRED_OPTIONS);

        foreach ($options as $option => $value) {
            $command[] = '--'.$option;
            if (! is_null($value)) {
                $command[] = $value;
            }
        }

        return [$this->url, ...$command];
    }

    public function run(): Pa11yResult
    {
        $arguments = $this->commandArgs();

        $command = [
            (new ExecutableFinder)->find('pa11y', 'pa11y', [
                __DIR__.'/../node_modules/bin',
                __DIR__.'/../../node_modules/bin',
                __DIR__.'/../../../node_modules/bin',
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            ...$arguments,
        ];

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

        if (! is_array($result)) {
            throw CouldNotReadOutputJson::from(implode(' ', $command), $output);
        }

        return new Pa11yResult($result);
    }
}
