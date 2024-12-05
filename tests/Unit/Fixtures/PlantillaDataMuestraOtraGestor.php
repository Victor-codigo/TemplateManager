<?php

declare(strict_types=1);

namespace Tests\Unit\Fixtures;

use Lib\PlantillaData;

use const Tests\PATH_UNIT_FIXTURE;

final class PlantillaDataMuestraOtraGestor extends PlantillaData
{
    public const string PATH = PATH_UNIT_FIXTURE.'/plantillas/plantillaOtra.html.php';

    public int|string $propiedad_1 = 1;
    public string $propiedad_2 = 'hola';
    public bool $propiedad_3 = false;
    public mixed $propiedad_4;
    /**
     *
     * @var int[]|null
     */
    public array|null $propiedad_5 = [1, 2, 3];
}
