<?php

namespace JkOster\Pa11y;

use ArrayAccess;
use JsonSerializable;

class Pa11yCiResult implements ArrayAccess, JsonSerializable
{
    public function __construct(protected array $rawIssue = []) {}

    /**
     * @example
     * {
     *   "code": "WCAG2AA.Principle1.Guideline1_4.1_4_3.G18.Fail",
     *   "type": "error",
     *   "typeCode": 1,
     *   "message": "This element has insufficient contrast at this conformance level. Expected a contrast ratio of at least 4.5:1, but text in this element has a contrast ratio of 3.8:1. Recommendation:  change background to #e8211b.",
     *   "context": "<span class=\"w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-white text-center font-bold uppercase bg-red-500 ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-transla...",
     *   "selector": "html > body > div:nth-child(2) > section > div > div:nth-child(4) > div > a:nth-child(1) > span",
     *   "runner": "htmlcs",
     *   "runnerExtras": {},
     * }
     */
    public function json(): string
    {
        return json_encode($this->rawIssue ?? [], JSON_PRETTY_PRINT);
    }

    public function jsonSerialize(): mixed
    {
        return json_encode($this->rawIssue ?? []);
    }

    public function setRawIssue(array $rawIssue): self
    {
        $this->rawIssue = $rawIssue;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->rawIssue['code'];
    }

    public function getType(): ?string
    {
        return $this->rawIssue['type'] ?? null;
    }

    public function getTypeCode(): ?int
    {
        return $this->rawIssue['typeCode'] ?? null;
    }

    public function getMessage(): ?string
    {
        return $this->rawIssue['message'] ?? null;
    }

    public function getContext(): ?string
    {
        return $this->rawIssue['context'] ?? null;
    }

    public function getSelector(): ?string
    {
        return $this->rawIssue['selector'] ?? null;
    }

    public function getRunner(): ?string
    {
        return $this->rawIssue['runner'] ?? null;
    }

    public function getRunnerExtras(): ?array
    {
        return $this->rawIssue['runnerExtras'] ?? null;
    }

    // ArrayAccess methods
    public function offsetExists($offset): bool
    {
        return isset($this->rawIssue[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->rawIssue[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->rawIssue[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->rawIssue[$offset]);
    }
}
