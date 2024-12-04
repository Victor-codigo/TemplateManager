<?php

declare(strict_types=1);

namespace Lib;

use Lib\Comun\Coleccion\Struct;

/**
 * Información que se pasa para cargar una plantilla.
 */
final class PlantillaConfig extends Struct
{
    /**
     * Path de la plantilla HTML.
     */
    public string $path = '';

    /**
     * Raíz del path de idioma.
     */
    public string $lang_raiz = '';
}
