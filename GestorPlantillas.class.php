<?php

namespace GT\Libs\Sistema\Plantilla;

use Exception;
use GT\Libs\Sistema\Lang;
//******************************************************************************


if(!class_exists(ExceptionDataCargar::class, false))
{
    /**
     * Excepción, no se ha podido cargar la información de la plantilla
     */
    class ExceptionDataCargar extends Exception {}
    //******************************************************************************
}


class GestorPlantillas
{

    /**
     * Plantillas cargadas
     * @var Plantilla[]
     */
    private $plantillas = array();

        /**
         * Obtiene una plantilla
         *
         * @version 1.0
         *
         * @param string $path path de la plantilla
         *
         * @return Plantilla|Plantilla[]|NULL plantilla con el path pasado
         *                                     Plantillas si no se pasa $path
         *                                     NULL si no se encuentra el path
         */
        public function getPlantilla($path)
        {
            $retorno = null;

            if($path!==null)
            {
                foreach($this->plantillas as $plantilla)
                {
                    if($plantilla->getPath()==$path)
                    {
                        $retorno = $plantilla;

                        break;
                    }
                }
            }
            else
            {
                $retorno = $this->plantillas;
            }

            return $retorno;
        }
//******************************************************************************


    /**
     * Información guardada para las plantillas, con el siguiente formato:
     *  - arr[identificador de la ingformación] = PlantillaData, información
     * @var PlantillaData[]
     */
    private $plantillas_data = array();

        /**
         * Obtiene la información cargada para una plantilla
         * a partir del identificador de información
         *
         * @param string $id identificdaor de la información
         *
         * @return PlantillaData|PlantillaData[]|NULL Si se pasa $id:
            *                                              - Si se encuentra PlantillaData
            *                                              - Si no se encuentra NULL
         *                                              Si no se pasa $id
         *                                                  - Todas las plantillasData
         */
        public function getData($id = null)
        {
            $retorno = null;

            if($id!==null)
            {
                if(isset($this->plantillas_data[$id]))
                {
                    $retorno = $this->plantillas_data[$id];
                }
            }
            else
            {
                $retorno = $this->plantillas_data;
            }

            return $retorno;
        }

        /**
         * Establece la información para una plantilla con el identificador de
         * información
         *
         * @param string $id identificdaor de la información
         * @param PlantillaData $data información de la plantilla
         */
        public function setData($id, PlantillaData $data)
        {
            $this->plantillas_data[$id] = $data;
        }
//******************************************************************************


    /**
     * idioma
     * @var Lang
     */
    private $lang = null;

        /**
         * Obitiene la clase de idioma
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
         * Establece la clase de idioma
         *
         * @version 1.0
         *
         * @param Lang $lang
         */
        public function setLang($lang)
        {
            $this->lang = $lang;
        }
//******************************************************************************


    /**
     * Constructor
     *
     * @version 1.0
     */
    public function __construct(Lang $lang = null)
    {
        $this->setLang($lang);
    }
//******************************************************************************

    /**
     * Destructor
     *
     * @version 1.0
     */
    public function __destruct()
    {
        $this->lang = null;

        foreach($this->plantillas as &$plantilla)
        {
            $plantilla = null;
        }

        foreach($this->plantillas_data as &$data)
        {
            $data = null;
        }
    }
//******************************************************************************


    /**
     * Carga una plantilla si existe o la crea si no existe
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

        if($plantilla===null)
        {
            $plantilla = new Plantilla($this, $config);
        }

        return $plantilla;
    }
//******************************************************************************


    /**
     * Renderiza una plantilla
     *
     * @version 1.1
     *
     * @throws ExceptionDataCargar Si no se encuentra el identificdaor de la información pasada
     *
     * @param string|PlantillaData $data Identificador de la información de la plantilla.
     *                                      Objeto con la Información de la plantilla
     * @param boolean $string TRUE si se carga la plantilla en un string,
     *                          FALSE si se muestra por pantalla
     * @return string|boolean si el parámetro $string es TRUE: un string con la plantilla,
     *                          o FALSE si se produce un error
     *                          Si el parámetro $string es FALSE: TRUE si se ejecutó correctamente
     *                                                              FALSE si se produjo un error
     */
    public function renderPlantilla($data, $string = false)
    {
        if(is_string($data))
        {
            $data = $this->getData($data);
        }

        if($data===null)
        {
            throw new ExceptionDataCargar('No se encuentra la información de la plantilla: NULL');
        }

        $plantilla_config = new PlantillaConfig();
        $plantilla_config->path = $data::PATH;
        $plantilla_config->lang_raiz = $data::LANG_RAIZ;

        return $this->cargarPlantilla($plantilla_config)
                    ->render($data, $string);
    }
//******************************************************************************
}