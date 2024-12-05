<?php

declare(strict_types=1);

namespace Lib;

use Lib\Comun\Lang;
use Lib\Exception\ExceptionDataCargar;

class GestorPlantillas
{
    /**
     * Plantillas cargadas.
     *
     * @var Plantilla[]
     */
    private array $plantillas = [];

    /**
     * Obtiene una plantilla.
     *
     * @version 1.0
     *
     * @param string $path path de la plantilla
     *
     * @return Plantilla|Plantilla[]|null plantilla con el path pasado
     *                                    Plantillas si no se pasa $path
     *                                    NULL si no se encuentra el path
     */
    public function getPlantilla($path)
    {
        $retorno = null;

        if (null !== $path) {
            foreach ($this->plantillas as $plantilla) {
                if ($plantilla->getPath() == $path) {
                    $retorno = $plantilla;

                    break;
                }
            }
        } else {
            $retorno = $this->plantillas;
        }

        return $retorno;
    }

    /**
     * Información guardada para las plantillas, con el siguiente formato:
     *  - arr[identificador de la información] = PlantillaData, información
     *
     * @var PlantillaData[]
     */
    private array $plantillas_data = [];

    /**
     * Obtiene la información cargada para una plantilla
     * a partir del identificador de información.
     *
     * @param string $id identificador de la información
     *
     * @return PlantillaData|PlantillaData[]|null Si se pasa $id:
     *                                            - Si se encuentra PlantillaData
     *                                            - Si no se encuentra NULL
     *                                            Si no se pasa $id
     *                                            - Todas las plantillasData
     */
    public function getData($id = null)
    {
        $retorno = null;

        if (null !== $id) {
            if (isset($this->plantillas_data[$id])) {
                $retorno = $this->plantillas_data[$id];
            }
        } else {
            $retorno = $this->plantillas_data;
        }

        return $retorno;
    }

    /**
     * Establece la información para una plantilla con el identificador de
     * información.
     *
     * @param string        $id   identificador de la información
     * @param PlantillaData $data información de la plantilla
     */
    public function setData($id, PlantillaData $data): void
    {
        $this->plantillas_data[$id] = $data;
    }

    /**
     * idioma.
     *
     */
    private ?Lang $lang;

    /**
     * Obtiene la clase de idioma.
     *
     * @version 1.0
     *
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Establece la clase de idioma.
     *
     * @version 1.0
     *
     * @param Lang $lang
     */
    public function setLang($lang): void
    {
        $this->lang = $lang;
    }

    /**
     * Constructor.
     *
     * @version 1.0
     */
    public function __construct(?Lang $lang)
    {
        $this->setLang($lang);
    }

    /**
     * Destructor.
     *
     * @version 1.0
     */
    public function __destruct()
    {
        $this->lang = null;

        foreach ($this->plantillas as &$plantilla) {
            $plantilla = null;
        }

        foreach ($this->plantillas_data as &$data) {
            $data = null;
        }
    }

    /**
     * Carga una plantilla si existe o la crea si no existe.
     *
     * @version 1.1
     *
     * @param PlantillaConfig $config Configuración de la plantilla
     *
     * @return Plantilla plantilla cargada
     */
    public function cargarPlantilla(PlantillaConfig $config)
    {
        $plantilla = $this->getPlantilla($config->path);

        if (null === $plantilla) {
            $plantilla = new Plantilla($this, $config);
        }

        return $plantilla;
    }

    /**
     * Renderiza una plantilla.
     *
     * @version 1.1
     *
     * @param string|PlantillaData $data   Identificador de la información de la plantilla.
     *                                     Objeto con la Información de la plantilla
     * @param bool                 $string TRUE si se carga la plantilla en un string,
     *                                     FALSE si se muestra por pantalla
     *
     * @return string|bool si el parámetro $string es TRUE: un string con la plantilla,
     *                     o FALSE si se produce un error
     *                     Si el parámetro $string es FALSE: TRUE si se ejecutó correctamente
     *                     FALSE si se produjo un error
     *
     * @throws ExceptionDataCargar Si no se encuentra el identificador de la información pasada
     */
    public function renderPlantilla($data, $string = false): string|bool
    {
        if (is_string($data)) {
            $data = $this->getData($data);
        }

        if (null === $data) {
            throw new ExceptionDataCargar('No se encuentra la información de la plantilla: NULL');
        }

        $plantilla_config = new PlantillaConfig();
        $plantilla_config->path = $data::PATH;
        $plantilla_config->lang_raiz = $data::LANG_RAIZ;

        return $this->cargarPlantilla($plantilla_config)
                    ->render($data, $string);
    }
}
