<?php

namespace JkOster\Pa11y;

use Illuminate\Support\Arr;
use JsonSerializable;

class Pa11yCiResult implements JsonSerializable
{
    public function __construct(protected array $rawResults = []) {}

    public function setRawResults(array $rawResults): self
    {
        $this->rawResults = $rawResults;

        return $this;
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

    public function getResultsGroupedByUrl(): array
    {

        return Arr::map(isset($this->rawResults['results']) ? $this->rawResults['results'] : [], fn ($result) => new Pa11yResult($result));
    }

    public function getAllErrors(): array
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => array_merge($acc, $result->getErrors()), []);
    }

    public function getAllWarnings(): array
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => array_merge($acc, $result->getWarnings()), []);
    }

    public function getAllNotices(): array
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn ($acc, $result) => array_merge($acc, $result->getNotices()), []);
    }

    public function getResultsOfUrl(string $url): Pa11yResult
    {
        return new Pa11yResult(isset($this->rawResults['results'][$url]) ? $this->rawResults['results'][$url] : []);
    }

    public function getUrls(): array
    {
        return array_keys(isset($this->rawResults['results']) ? $this->rawResults['results'] : []);
    }
}
