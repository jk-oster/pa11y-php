<?php

namespace JkOster\Pa11y\Exceptions;

use Exception;

class CouldNotReadOutputJson extends Exception
{
    public static function from(string $command = 'pa11y', string $output = ''): self
    {
        $text = substr(trim($output), 0, 100);

        return new self("Could not read command output from '{$command}': {$text}... ");
    }
}
