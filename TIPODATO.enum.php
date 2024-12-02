<?php

namespace Lib;

use Lib\Comun\Enum;

/**
 * Tipos de datos de una plantilla
 */
final class TIPODATO extends Enum
{
    /**
     * Información escapada del código HTML
     */
    const int DATA = 1;

    /**
     * URL escapada del código HTML
     */
    const int URL = 2;

    /**
     * Código JSON escapada del código HTML
     */
    const int JSON = 4;
}