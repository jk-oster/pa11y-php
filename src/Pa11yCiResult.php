<?php

namespace JkOster\Pa11y;

use ArrayAccess;
use Illuminate\Support\Arr;
use JsonSerializable;

class Pa11yCiResult implements ArrayAccess, JsonSerializable
{
    public function __construct(
        protected array $rawResults,
        protected array $config = [],
        protected string|null $outputFilePath = null
    ) {}

    public function setRawResults(array $rawResults): self
    {
        $this->rawResults = $rawResults;

        return $this;
    }

    public function getRawResults(): array
    {
        return $this->rawResults;
    }

    public function json(): string
    {
        return json_encode($this->rawResults ?? [], JSON_PRETTY_PRINT);
    }

    public function jsonSerialize(): mixed
    {
        return json_encode($this->rawResults ?? []);
    }

    public function saveJson(string $path): self
    {
        file_put_contents($path, $this->json());

        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getOutputFilePath(): string|null
    {
        return $this->outputFilePath;
    }

    public function getUrlsCount(): int
    {
        return isset($this->rawResults['total']) ? $this->rawResults['total'] : 0;
    }

    public function getPassesCount(): int
    {
        return isset($this->rawResults['passes']) ? $this->rawResults['passes'] : 0;
    }

    public function getFailuresCount(): int
    {
        return $this->getUrlsCount() - $this->getPassesCount();
    }

    public function getTotalIssuesCount(): int
    {
        return isset($this->rawResults['errors']) ? $this->rawResults['errors'] : 0;
    }

    public function getTotalErrorsCount(): int
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => $acc + $result->getErrorsCount(), 0);
    }

    public function getTotalWarningsCount(): int
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => $acc + $result->getWarningsCount(), 0);
    }

    public function getTotalNoticesCount(): int
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => $acc + $result->getNoticesCount(), 0);
    }

    /**
     * @return array<string, Pa11yResult>
     */
    public function getResultsGroupedByUrl(): array
    {
        return Arr::map(isset($this->rawResults['results']) ? $this->rawResults['results'] : [], fn ($result) => new Pa11yResult($result));
    }

    /**
     * @return array<int, Pa11yIssue>
     */
    public function getAllErrors(): array
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => array_merge($acc, $result->getErrors()), []);
    }

    /**
     * @return array<int, Pa11yIssue>
     */
    public function getAllWarnings(): array
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => array_merge($acc, $result->getWarnings()), []);
    }

    /**
     * @return array<int, Pa11yIssue>
     */
    public function getAllNotices(): array
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => array_merge($acc, $result->getNotices()), []);
    }

    /**
     * @return array<int, Pa11yIssue>
     */
    public function getAllIssues(): array
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => array_merge($acc, $result->getIssues()), []);
    }

    public function getResultsOfUrl(string $url): Pa11yResult
    {
        return new Pa11yResult(isset($this->rawResults['results'][$url]) ? $this->rawResults['results'][$url] : []);
    }

    /**
     * @return array<int, string>
     */
    public function getUrls(): array
    {
        return array_keys(isset($this->rawResults['results']) ? $this->rawResults['results'] : []);
    }

    // ArrayAccess methods
    public function offsetExists($offset): bool
    {
        return isset($this->rawResults[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->rawResults[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->rawResults[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->rawResults[$offset]);
    }
}
