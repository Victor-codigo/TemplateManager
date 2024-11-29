<?php

namespace GT\Libs\Sistema\Plantilla;

use GT\Libs\Sistema\Tipos\Struct;
//******************************************************************************


/**
 * Información que se pasa para cargar una plantilla
 */
final class PlantillaConfig extends Struct
{
    /**
     * Path de la plantilla HTML
     * @var string
     */
    public $path = '';

    /**
     * Raiz del path de idioma
     * @var string
     */
    public $lang_raiz = '';
}
//******************************************************************************