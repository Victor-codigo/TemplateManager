<?php

use Lib\Plantilla;
use Lib\PlantillaData;

return function (PlantillaData $data, Plantilla $plt) { ?>

<div>
    Plantilla de prueba

    <ul>
        <li>
            elemento 1
        </li>
        <li>
            elemento 2
        </li>
        <li>
            elemento 3
        </li>
    </ul>
</div>

<?php } ?>