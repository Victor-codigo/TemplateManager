<?php

declare(strict_types=1);

namespace Tests\Unit\Fixtures;

class GestorPlantillasFixture
{
    /**
     * Callback del mock de la función is_readable.
     */
    public static \Closure $is_readable;

    public static function is_readable(...$args)
    {
        return !\is_callable(self::$is_readable) ?
                    \is_readable($args[0]) :
                    call_user_func_array(self::$is_readable, $args);
    }
}
