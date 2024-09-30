<?php

namespace JkOster\Pa11y;

use Illuminate\Support\Arr;
use JsonSerializable;

class Pa11yResult implements JsonSerializable {
    public function __construct(protected array $rawResults = []) {
    }

    // rawResults JSON Output Example
    // [
    //   {
    //     "code": "WCAG2AA.Principle1.Guideline1_4.1_4_3.G18.Fail",
    //     "type": "error",
    //     "typeCode": 1,
    //     "message": "This element has insufficient contrast at this conformance level. Expected a contrast ratio of at least 4.5:1, but text in this element has a contrast ratio of 3.8:1. Recommendation:  change background to #e8211b.",
    //     "context": "<span class=\"w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-white text-center font-bold uppercase bg-red-500 ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-transla...",
    //     "selector": "html > body > div:nth-child(2) > section > div > div:nth-child(4) > div > a:nth-child(1) > span",
    //     "runner": "htmlcs",
    //     "runnerExtras": {}
    //   }
    // ]

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

    public function getRunner(): string
    {
        return $this->rawResults[0]['runner'];
    }

    public function getTotalIssueCount(): int
    {
        return count($this->rawResults);
    }

    public function getErrors(): array
    {
        return Arr::where($this->rawResults, fn($issue) => $issue['type'] === 'error');
    }

    public function getWarnings(): array
    {
        return Arr::where($this->rawResults, fn($issue) => $issue['type'] === 'warning');
    }

    public function getNotices(): array
    {
        return Arr::where($this->rawResults, fn($issue) => $issue['type'] === 'notice');
    }

    public function getErrorsCount(): int
    {
        return count($this->getErrors());
    }

    public function getWarningsCount(): int
    {
        return count($this->getWarnings());
    }

    public function getNoticesCount(): int
    {
        return count($this->getNotices());
    }

    public function getIssues(?string $code): ?array
    {
        return $code === null ? $this->rawResults : Arr::where($this->rawResults, fn($issue) => $issue['code'] === $code);
    }

    public function getIssueCount(?string $code): int
    {
        return count($this->getIssues($code));
    }

    public function getGroupedIssues(): array
    {
        $result = [];

        foreach ($this->rawResults as $issue) {
            $result[$issue['code']][] = $issue;
        }

        return $result;
    }
}