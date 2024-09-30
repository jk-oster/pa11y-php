<?php

namespace JkOster\Pa11y;

use Illuminate\Support\Arr;
use JsonSerializable;

class Pa11yCiResult implements JsonSerializable {
    public function __construct(protected array $rawResults = []) {
    }

    public function setRawResults(array $rawResults): self
    {
        $this->rawResults = $rawResults;

        return $this;
    }

    public function json(): string
    {
        return json_encode($this->rawResults, JSON_PRETTY_PRINT);
    }

    public function jsonSerialize(): mixed
    {
        return json_encode($this->rawResults);
    }

    public function saveJson(string $path): self
    {
        file_put_contents($path, $this->json());

        return $this;
    }

    public function getUrlsCount(): int
    {
        return $this->rawResults['total'];
    }

    public function getPassesCount(): int
    {
        return $this->rawResults['passes'];
    }

    public function getFailuresCount(): int
    {
        return $this->getUrlsCount() - $this->getPassesCount();
    }

    public function getTotalIssuesCount(): int
    {
        return $this->rawResults['errors'];
    }

    public function getTotalErrorsCount(): int
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn($acc, $result) => $acc + $result->getErrorsCount(), 0);
    }

    public function getTotalWarningsCount(): int
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn($acc, $result) => $acc + $result->getWarningsCount(), 0);
    }

    public function getTotalNoticesCount(): int
    {
        return array_reduce($this->getResultsGroupedByUrl(), fn($acc, $result) => $acc + $result->getNoticesCount(), 0);
    }

    public function getResultsGroupedByUrl(): array
    {
        return Arr::map($this->rawResults['results'], fn($result) => new Pa11yResult($result));
    }

    public function getResultsOfUrl(string $url): Pa11yResult
    {
        return new Pa11yResult($this->rawResults['results'][$url]);
    }

    public function getUrls(): array
    {
        return array_keys($this->rawResults['results']);
    }
}