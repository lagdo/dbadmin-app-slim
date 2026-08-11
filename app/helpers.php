<?php

use Lagdo\DbAdmin\Support\Facade\Translator;

/**
 * @param string $idf
 *
 * @return string
 */
function __(string $idf, ...$params): string
{
    return Translator::lang($idf, ...$params);
}

/**
 * @param string $name
 * @param mixed $default
 *
 * @return mixed
 */
function env(string $name, mixed $default = null): mixed
{
    return $_ENV[$name] ?? $default;
}
