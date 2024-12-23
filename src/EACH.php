<?php

declare(strict_types=1);

namespace Lib;

use Lib\Comun\Enum;

/**
 * Tipos de each que se pueden usar para la función Plantilla::each.
 */
final class EACH extends Enum
{
    /**
     * Concatena los elementos del array uno detrás de otro separados por un
     * espacio.
     */
    public const int NORMAL = 0;

    /**
     * Concatena los elementos del array uno detrás de otro separados por un
     * espacio. Colocando con el formato de un atributo HTML. Siendo el indice
     * del array el nombre del atributo y el valor el valor del atributo.
     */
    public const int ATRIBUTO = 1;

    /**
     * Concatena los elementos del array uno detrás de otro separados por un
     * espacio. Colocando con el formato de un atributo HTML. Siendo el indice
     * del array el nombre del atributo (al que se le añade el prefijo "data-")
     * y el valor el valor del atributo.
     */
    public const int DATA = 2;
}
