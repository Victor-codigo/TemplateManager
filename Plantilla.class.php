<?php

namespace GT\Libs\Sistema\Plantilla;
use GT\Libs\Sistema\Call;
//******************************************************************************

/**
 * Excepción, no se ha encontrado la plantilla
 */
class ExceptionPlantillaNoEncontrada extends \Exception {}
//******************************************************************************

/**
 * Excepción, no se ha podido cargar la plantilla
 */
class ExceptionPlantillaCargar extends \Exception {}
//******************************************************************************

if(!class_exists(ExceptionDataCargar::class, false))
{
    /**
     * Excepción, no se ha podido cargar la información de la plantilla
     */
    class ExceptionDataCargar extends \Exception {}
    //******************************************************************************
}


class Plantilla
{
    use Call;
//******************************************************************************


    /**
     * CONSTANTE
     * Bandera que indica que caracteres escapa la función htmlspecialchars
     * @var int
     */
    const HTML_ESCAPE_ENT_QUOTES = ENT_QUOTES;

    /**
     * CONSTANTE
     * Codificación de la web
     * @var string
     */
    const CODIFICACION = CODIFICACION_TEXTO_WEB;

    /**
     * CONSTANTE
     * Caractesr separador de las URLs. Se utiliza para la furnción url
     * @var string
     */
    const URL_SECCION_SEPARADOR = '/';
//******************************************************************************



    /**
     * Gestor al que pertenece la plantilla
     * @var GestorPlantillas
     */
    private $gestor;

    /**
     * Path de la plantilla
     * @var string
     */
    private $path;


        /**
         * Obtiene el path de la plantilla
         *
         * @version 1.0
         *
         * @return string
         */
        public function getPath()
        {
            return $this->path;
        }



        /**
         * Establece el path de la plantilla
         *
         * @version 1.0
         *
         * @throws ExceptionPlantillaNoEncontrada si no se puede leer el path
         *
         * @param string $path path de la plantilla
         */
        public function setPath($path)
        {
            if(is_readable($path))
            {
                $this->path = $path;
            }
            else
            {
                throw new ExceptionPlantillaNoEncontrada($path);
            }
        }
//******************************************************************************

    /**
     * Raíz del path de idioma
     * @var string
     */
    private $lang_raiz = '';

        /**
         * Establece la raiz del path de idioma
         *
         * @version 1.0
         *
         * @param string $lang_raiz raiz del path de idioma
         */
        public function setLangRaiz($lang_raiz)
        {
            $this->lang_raiz = $lang_raiz;
        }
//******************************************************************************

        /**
         * Obtiene la raiz del path de idioma
         *
         * @version 1.0
         */
        public function getLangRaiz()
        {
            return $this->lang_raiz;
        }
//******************************************************************************

    /**
     * Callback de la plantilla
     * con el siguiente formato:
     * Parámetros:
     *  - PlantillaData, información que se lepasa a la estructura
     *  - Plantilla, plantilla
     *
     * @var callable
     */
    private $callback = null;

        /**
         * Obtiene la función de la plantilla
         *
         * @version 1.0
         *
         * @return callable callback con el siguiente formato:
         *                  Parámetros:
         *                      - PlantillaData, información que se lepasa a la estructura
         *                      - Plantilla, plantilla
         */
        public function getCallback()
        {
            return $this->callback;
        }
//******************************************************************************





    /**
     * Constructor
     *
     * @version 1.1
     *
     * @param GestorPlantillas $gestor gestor de plantillas
     * @param PlantillaConfig $config Configuración de la plantilla
     */
    public function __construct(GestorPlantillas $gestor, PlantillaConfig $config)
    {
        $this->gestor = $gestor;
        $this->setPath($config->path);
        $this->setLangRaiz($config->lang_raiz);
        $this->cargar($config->path);
    }
//******************************************************************************


    /**
     * Destructor
     *
     * @version 1.0
     */
    public function __destruct()
    {
        $this->gestor = null;
        $this->callback = null;
    }
//******************************************************************************



    /**
     * Carga la plantilla
     *
     * @version 1.0
     *
     * @throws ExceptionPlantillaCargar si no se puede leer o no devuelve una función
     *
     * @param string $path path del archivo con la plantilla que se carga
     */
    private function cargar($path)
    {
        if(is_readable($path))
        {
            $plantilla = require $path;

            if(is_callable($plantilla))
            {
                $this->callback = $plantilla;
            }
            else
            {
                throw new ExceptionPlantillaCargar('El formato de la plantilla no es válido: ' . $path);
            }
        }
        else
        {
            throw new ExceptionPlantillaCargar('No se puede cargar la plantilla: ' . $path);
        }
    }
//******************************************************************************


    /**
     * Renderiza la plantilla
     *
     * @version 1.0
     *
     * @param string|PlantillaData $data Identificador de la información guardada o
     *                              Objeto con la información
     * @param boolean $string TRUE si la plantilla se carga en una cadena,
     *                          FALSE si se carga en el buffer de salida
     * @return boolean|string - Si $string es TRUE:
     *                              string con el código de la plantilla
     *                          - Si $string es FALSE:
     *                              TRUE si se ejecura correctamente.
     *                              FALSE si se produce un error
     */
    public function render($data, $string = false)
    {
        ob_start();

        if(is_string($data))
        {
            $data = $this->gestor->getData($data);

            if($data===null)
            {
                ob_end_flush();
                throw new ExceptionDataCargar('No se encuentra la información de la plantilla: ' . $data);
            }
        }

        $this->callback($data, $this);

        return $string ? ob_get_clean() : ob_end_flush();
    }
//******************************************************************************


    /**
     * renderiza una plantilla
     *
     * @version 1.0
     *
     * @param string|PlantillaData $data Identificador de la información guardada o
     *                              Objeto con la información
     * @param boolean $string TRUE si la plantilla se carga en una cadena,
     *                          FALSE si se carga en el buffer de salida
     *
     * @return Plantilla plantilla creada
     */
    public function renderPlantilla($data, $string = false)
    {
        return $this->gestor->renderPlantilla($data, $string);
    }
//******************************************************************************

    /**
     * Comprueba si existe una PlantillaData en el gestor de plantillas
     *
     * @version 1.0
     *
     * @param string $id identificador de la información
     *
     * @return boolean TRUE si existe, FALSE si no existe
     */
    public function existeData($id)
    {
        return $this->gestor->getData($id)===null ? false : true;
    }
//******************************************************************************




    /**
     * Carga en la plantilla una variable de idioma
     *
     * @version 1.0
     *
     * @param stirng $path path de la variable
     * @param type $sustitucion con los place-holders a sustituir. Con el siguiente formato:     
     *                          - arr[place-holder sin marcador] = string, reemplazo
     * @param string $marca caracteres que se utilizan para marcar el place-holder
     * @param boolean $string TRUE si devuelve un string
     *                        FALSE si se muestra por pantalla
     *
     * @return string con los valores escapados
     */
    public function lang($path, $sustitucion = [], $marca = ':', $string = false)
    {
        $valor = $this->gestor->getLang()->get(

            $this->getLangRaiz() . '.' . $path, $sustitucion, '.',
            $marca,
            '\\'
        );

        return $this->data($valor, $string);
    }
//******************************************************************************

    /**
     * Carga en la plantilla una variable de idioma.
     * No escapa el texto que es sustituido
     *
     * @version 1.0
     *
     * @param stirng $path path de la variable
     * @param type $sustitucion con los place-holders a sustituir. Con el siguiente formato:     
     *                          - arr[place-holder sin marcador] = string, reemplazo
     * @param string $marca caracteres que se utilizan para marcar el place-holder
     * @param boolean $string TRUE si devuelve un string
     *                        FALSE si se muestra por pantalla
     *
     * @return string con los valores escapados
     */
    public function langHtml($path, $sustitucion = [], $marca = ':', $string = false)
    {
        $lang = $this->gestor->getLang();
        $valor = $lang->get(

            $this->getLangRaiz() . '.' . $path,
            [],
            '.',
            $marca,
            '\\'
        );

        $texto_escapado = $lang->replace(

            $this->data($valor, true),
            $sustitucion,
            $marca,
            '\\'
        );

        if($string)
        {
            return $texto_escapado;
        }

        echo $texto_escapado;
    }
//******************************************************************************




    /**
     * Escapa los valores pasados para ser mostrados en HTML.
     *
     * @version 1.1
     *
     * @param string $valor cadena a escapar
     * @param boolean $string TRUE si devuelve un string
     *                        FALSE si se muestra por pantalla
     *
     * @return string con los valores escapados
     */
    public function data($valor, $string = false)
    {
        $data = htmlspecialchars(

            $valor,
            self::HTML_ESCAPE_ENT_QUOTES,
            self::CODIFICACION
        );

        if($string)
        {
            return $data;
        }

        echo $data;
    }
//******************************************************************************



    /**
     * Devuelve una URL válida a partir de la URL pasada
     *
     * @version 1.1
     *
     * @param string $url URL a escapar
     * @param array $sustitucion caracteres que se sustituyen en cada una de las partes
     *                              de la URL, con el siguiente formato:
     *                                  - arr[caracter a sustituir] = string, caracter sustituido
     * @param string $seccion_separador caracter separador de las secciones de la URL
     * @param boolean $string TRUE si devuelve un string
     *                        FALSE si se muestra por pantalla
     *
     * @return string URL saneada
     */
    public function url($url, array $sustitucion = [' ' => '-'], $seccion_separador = self::URL_SECCION_SEPARADOR, $string = false)
    {
        $url_trozos = explode($seccion_separador, trim($url));
        $sustituido = array_keys($sustitucion);

        for($i = 0, $length = count($url_trozos); $i<$length; $i++)
        {
            $url_trozos[$i] = urlencode(str_replace(

                $sustituido,
                $sustitucion,
                mb_strtolower($url_trozos[$i])
            ));
        }

        return $this->data(

            $this->urlEspaceHttpYSharp(implode($seccion_separador, $url_trozos)),
            $string
        );
    }
//******************************************************************************

    /**
     * Corrige el error de escape del prodocolo HTTP|HTTPS y del #
     *
     * @version 1.0
     *
     * @param string $url URL escapada
     *
     * @return string url corregida
     */
    private function urlEspaceHttpYSharp($url)
    {
        $patron = array();
        $reemplazo = array();

        if(mb_strpos($url, '%3A')!==false)
        {
            $patron[] = '/^(https?)%3A\/\//';
            $reemplazo[] = '$1://';
        }

        if(mb_strpos($url, '%23')!==false)
        {
            $patron[] = '/^%23/';
            $reemplazo[] = '#';
        }

        if(!empty($patron))
        {
            return preg_replace($patron, $reemplazo, $url, 1);
        }

        return $url;
    }
//******************************************************************************




    /**
     * Convierte un valor a formato JSON
     *
     * @version 1.0
     *
     * @param sting $json json que se escapa
     * @param int $opciones una de las constantes JSON_*
     * @param int $depth profundidad máxima de la variable
     * @param boolean $string TRUE si devuelve un string
     *                        FALSE si se muestra por pantalla
     *
     * @return string Si existe la variable, devuelve la variable convertida a JSON
     */
    public function json($json, $opciones = JSON_HEX_TAG | JSON_HEX_AMP, $depth = 512, $string = false)
    {
        $retorno = json_encode($json, $opciones, $depth);

        if($retorno===false)
        {
            $retorno = '';
        }

        if($string)
        {
            return $retorno;
        }

        echo $retorno;
    }
//******************************************************************************


    /**
     * Devuelve un atributo de una etiqueta HTML
     *
     * @param string $atr nombre del atributo
     * @param string $valor valor del atributo
     * @param int $tipo tipo de valor. Una de las constates TIPODATO::DATA o TIPODATO::URL
     * @param boolean $string TRUE si devuelve un string
     *                        FALSE si se muestra por pantalla
     *
     * @return string con el atributo y su valor escapado
     */
    public function atr($atr, $valor, $tipo = TIPODATO::DATA, $string = false)
    {
        $retorno = '';

        if($valor!=='')
        {
            switch($tipo)
            {
                case TIPODATO::DATA:

                    $valor = $this->data($valor, true);

                break;

                case TIPODATO::URL:

                    $valor = $this->url($valor, [' ' => '-'], self::URL_SECCION_SEPARADOR, true);

                break;

                default :

                    $valor = '';
            }

            $retorno = $valor==''
                ? ''
                : $this->data($atr, true) .'="' . trim($valor) . '"';
        }

        if($string)
        {
            return $retorno;
        }

        echo $retorno;
    }
//******************************************************************************



    /**
     * Ejecuta una sentencia foreach para un array pasado. Concatenando los
     * elementos del array separados por un espacio
     *
     * @version 1.0
     *
     * @param array $array con los elemnetos
     * @param EACH $modo establece el modo en el que se concatenen los elementos
     * @param string $separador strig con el que se separan los ementos
     * @param boolean $string TRUE si devuelve un string
     *                        FALSE si se muestra por pantalla
     *
     * @return string con los elementos concatenados
     */
    public function each(array $array, $modo = EACH::NORMAL, $separador = ' ', $string = false)
    {
        $retorno = '';

        foreach($array as $indice => $valor)
        {
            switch($modo)
            {
                case EACH::ATRIBUTO:

                    $retorno .= $separador . $this->data($indice, true) . '="' . $this->data($valor, true) . '"';

                break;

                case EACH::DATA:

                    $retorno .= $separador . 'data-' . $this->data($indice, true) . '="' . $this->data($valor, true) . '"';

                break;

                case EACH::NORMAL:

                    $retorno .= $separador . $this->data($valor, true);

                break;
            }
        }

        if($string)
        {
            return $retorno;
        }


        echo $retorno;
    }
//******************************************************************************
}