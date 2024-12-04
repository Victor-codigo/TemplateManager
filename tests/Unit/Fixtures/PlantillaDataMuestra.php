<?php

declare(strict_types=1);

namespace Tests\Unit\Fixtures;

use Lib\PlantillaData;

use const Tests\PATH_UNIT_FIXTURE;

final class PlantillaDataMuestra extends PlantillaData
{
    public const string PATH = PATH_UNIT_FIXTURE.'/plantillas/plantillaOk.html.php';

    public $propiedad_1 = 1;
    public $propiedad_2 = 'hola';
    public $propiedad_3 = false;
    public $propiedad_4;
    public $propiedad_5 = [1, 2, 3];
    public ?PlantillaDataMuestra $propiedad_6;
}
