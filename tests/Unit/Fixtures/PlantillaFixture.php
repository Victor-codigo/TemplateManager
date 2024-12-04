<?php

declare(strict_types=1);

namespace Lib;

use Tests\Unit\Fixtures\PlantillaFixture;

function is_readable(string $filename)
{
    return \is_callable(PlantillaFixture::$is_readable)
        ? (PlantillaFixture::$is_readable)($filename)
        : \is_readable($filename);
}

function is_callable(mixed $value, bool $syntax_only, &$callable_name): bool
{
    return \is_callable(PlantillaFixture::$is_callable)
        ? (PlantillaFixture::$is_callable)($value, $syntax_only, $callable_name)
        : \is_callable($value, $syntax_only, $callable_name);
}

function defined(string $constant_name): bool
{
    return \is_callable(PlantillaFixture::$defined)
        ? (PlantillaFixture::$defined)($constant_name)
        : \defined($constant_name);
}

namespace Tests\Unit\Fixtures;

class PlantillaFixture
{
    /**
     * callBack del mock de la función is_callable.
     *
     * @var callable
     */
    public static $is_callable;

    /**
     * Callback del mock de la función defined.
     *
     * @var callable
     */
    public static $defined;

    /**
     * Callback del mock de la función is_readable.
     *
     * @var callable
     */
    public static $is_readable;
}
