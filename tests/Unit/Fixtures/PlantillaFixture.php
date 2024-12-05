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

use Closure;

class PlantillaFixture
{
    /**
     * callBack del mock de la función is_callable.
     *
     */
    public static ?Closure $is_callable=null;

    /**
     * Callback del mock de la función defined.
     *
     */
    public static ?Closure $defined=null;

    /**
     * Callback del mock de la función is_readable.
     *
     */
    public static ?Closure $is_readable=null;
}
