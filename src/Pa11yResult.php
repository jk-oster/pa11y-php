<?php

namespace JkOster\Pa11y;

use ArrayAccess;
use Illuminate\Support\Arr;
use JsonSerializable;

class Pa11yResult implements ArrayAccess, JsonSerializable
{
    public function __construct(protected array $rawResults = []) {}

    public function setRawResults(array $rawResults): self
    {
        $this->rawResults = $rawResults;

        return $this;
    }

    public function getRawResults(): array
    {
        return $this->rawResults;
    }

    /**
     * @example
     * [
     *   {
     *     "code": "WCAG2AA.Principle1.Guideline1_4.1_4_3.G18.Fail",
     *     "type": "error",
     *     "typeCode": 1,
     *     "message": "This element has insufficient contrast at this conformance level. Expected a contrast ratio of at least 4.5:1, but text in this element has a contrast ratio of 3.8:1. Recommendation:  change background to #e8211b.",
     *     "context": "<span class=\"w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-white text-center font-bold uppercase bg-red-500 ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-transla...",
     *     "selector": "html > body > div:nth-child(2) > section > div > div:nth-child(4) > div > a:nth-child(1) > span",
     *     "runner": "htmlcs",
     *     "runnerExtras": {},
     *   }
     * ]
     */
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

    public function getRunner(): string
    {
        return isset($this->rawResults[0]['runner']) ? $this->rawResults[0]['runner'] : '';
    }

    public function getTotalIssueCount(): int
    {
        return count($this->rawResults ?? []);
    }

    /**
     * @return array<int, Pa11yIssue>
     */
    public function getErrors(): array
    {
        return Arr::map(Arr::where($this->rawResults ?? [], fn ($issue) => $issue['type'] === 'error'), fn ($issue) => new Pa11yIssue($issue));
    }

    /**
     * @return array<int, Pa11yIssue>
     */
    public function getWarnings(): array
    {
        return Arr::map(Arr::where($this->rawResults ?? [], fn ($issue) => $issue['type'] === 'warning'), fn ($issue) => new Pa11yIssue($issue));
    }

    /**
     * @return array<int, Pa11yIssue>
     */
    public function getNotices(): array
    {
        return Arr::map(Arr::where($this->rawResults ?? [], fn ($issue) => $issue['type'] === 'notice'), fn ($issue) => new Pa11yIssue($issue));
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

    /**
     * @return array<int, Pa11yIssue>
     */
    public function getIssues(?string $code = null): ?array
    {
        return Arr::map($code === null ? $this->rawResults : Arr::where($this->rawResults ?? [], fn ($issue) => $issue['code'] === $code), fn ($issue) => new Pa11yIssue($issue));
    }

    public function getIssueCount(?string $code): int
    {
        return count($this->getIssues($code));
    }

    /**
     * @return array<string, array<int, Pa11yIssue>>
     */
    public function getGroupedIssues(): array
    {
        $result = [];

        foreach (($this->rawResults ?? []) as $issue) {
            $result[$issue['code']][] = new Pa11yIssue($issue);
        }

        return $result;
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
