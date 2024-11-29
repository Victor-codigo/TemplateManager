<?php

namespace GT\Libs\Sistema\Plantilla;
use GT\Libs\Sistema\Tipos\Enum;
//******************************************************************************


/**
 * Tipos de datos de una plantilla
 */
final class TIPODATO extends Enum
{
    /**
     * Información escapapada del código HTML
     * @var int
     */
    const DATA = 1;

    /**
     * URL escapapada del código HTML
     * @var int
     */
    const URL = 2;

    /**
     * Código JSON escapapada del código HTML
     * @var int
     */
    const JSON = 4;
}