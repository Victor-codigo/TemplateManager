<?php

namespace Lib\Comun;

use Exception;
use Lib\Comun\Coleccion\ArrayPath;


/**
 * Gestor de idioma
 */
class Lang
{
    /**
     * Extensión de los archivos de idioma
     */
    const string ARCHIVO_EXTENSION = '.lang.php';

    /**
     * Carácter que separa los distintos niveles de el path de la variable
     * de idioma
     */
    const SEPARADOR = '.';


    /**
     * Path donde se guardan los idiomas
     * @var string
     */
    private $lang_path = '';

        /**
         * Establece el path donde se guardan los idiomas
         * @param type $path
         */
        public function setLangPath($path)
        {
            $this->lang_path = $path;
        }

        /**
         * Obtiene el path donde se guardan los idiomas
         *
         * @version 1.0
         *
         * @return string
         */
        public function getLangPath()
        {
            return $this->lang_path;
        }


    /**
     * Array con los idiomas válidos, con el siguiente formato
     * - arr[identificador del idioma] = string, path de la carpeta de idioma
     *                                          relativo al la carpeta donde se
     *                                          guardan los idiomas
     *
     * @var array
     */
    private $langs = array();

        /**
         * Establece los idiomas válidos
         *
         * @version 1.0
         *
         * @param array $langs con los idiomas validos, con el siguiente formato
         *                      - arr[identificador del idioma] = string, path de la carpeta de idioma
         *                                                          relativo al la carpeta donde se
         *                                                          guardan los idiomas
         */
        public function setLangs(array $langs)
        {
            $this->langs = $langs;
        }

        /**
         * Obtiene los idiomas válidos
         *
         * @version 1.0
         *
         * @return array con el siguiente formato:
         *                  - arr[identificador del idioma] = string, path de la carpeta de idioma
         *                                                          relativo al la carpeta donde se
         *                                                          guardan los idiomas
         */
        public function getLangs()
        {
            return $this->langs;
        }

    /**
     * Identificador del idioma que se carga
     * @var int|string
     */
    private $lang = null;

        /**
         * Establece el idioma a cargar
         *
         * @version 1.0
         *
         * @param int|string $lang identificador del idioma a cargar
         */
        public function setLang($lang)
        {
            if(array_key_exists($lang, $this->langs))
            {
                $this->lang = $lang;
            }
        }

        /**
         * Identificador del idioma que es cargado
         *
         * @version 1.0
         *
         * @return int|string
         */
        public function getLang()
        {
            return $this->lang;
        }


    /**
     * Identificador del idioma que se carga por defecto, si no se selecciona ninguno
     *
     * @version 1.0
     *
     * @var int|string
     */
    private $lang_default = null;

        /**
         * Establece el idioma por defecto
         *
         * @version 1.0
         *
         * @param int|string $lang
         */
        public function setLangDefault($lang)
        {
            $this->lang_default = $lang;
        }

        /**
         * Obtiene el idioma por defecto
         *
         * @version 1.0
         *
         * @return int|string
         */
        public function getLangDefault()
        {
            return $this->lang_default;
        }



    /**
     * Array con las variables de los idiomas ordenados por grupos. Con el siguiente
     * formato: arr[nombre del grupo] = array, variables del grupo
     *
     * @var ArrayPath
     */
    private $lang_vars = null;



    /**
     * Constructor
     *
     * @version 1.0
     */
    public function __construct()
    {
        $this->lang_vars = new ArrayPath();
    }

    /**
     * Destructor
     *
     * @version 1.0
     */
    public function __destruct()
    {
        $this->lang_vars = null;
    }


    /**
     * Carga un archivo de idioma
     *
     * @version 1.0
     *
     * @param string $path path al archivo
     * @param string $path_var path dentro de la clase lang en el que se colocan
     *                          las variables cargadas de idioma.
     *                          Si no se pasa se coloca en el path pasado en el
     *                          parámetro $path
     * @param string $separador carácter que separa el los elementos del
     *                          path de la variable
     * @throws Exception
     */
    public function cargar($path, $path_var = null, $separador = self::SEPARADOR)
    {
        $path_var = $path_var===null
            ? str_replace(array('\\', '/'), $separador, $path)
            : $path_var;

        $archivo = $this->lang_path . '/' . $this->langs[$this->lang]
                    . '/' . $path . self::ARCHIVO_EXTENSION;

        if(!is_readable($archivo))
        {
            throw new Exception('No se encuentra el archivo de idioma: ' . $archivo);
        }

        $lang_vars = include($archivo);

        if(!is_array($lang_vars))
        {
            throw new Exception('Formato del archivo erroneo: ' . $archivo);
        }

        $encontrado = true;
        $var =& $this->lang_vars->getItemsRef();

        if($path_var!='')
        {
            $var =& $this->lang_vars->getPointer($path_var, $encontrado, $separador);
        }

        if($encontrado && is_array($var))
        {
            $var = array_replace_recursive($var, $lang_vars);
        }
        else
        {
            $this->set($path_var, $lang_vars, $separador);
        }
    }


    /**
     * Carga una variable de idioma de idioma.
     *
     * @version 1.0
     *
     * @param string $path path a la variable.
     * @param array $sustitucion con los place-holders a sustituir. Con el siguiente formato:
     *                          - arr[place-holder sin marcador] = string, reemplazo
     * @param string $separador carácter que se usa para las carpetas y los indices de los
     *                          array dentro del archivo
     * @param string $marca caracteres que se utilizan para marcar el place-holder
     * @param string $escape Carácter que se utiliza de escape para la marca place-holder
     *
     * @return string|string[]|NULL Devuelve el valor de la variable
     *                              NULL si no se encuentra devuelve
     */
    public function get($path, $sustitucion = array(), $separador = self::SEPARADOR, $marca = ':', $escape = '\\')
    {
        $retorno = $this->lang_vars->getPath($path, $encontrado, $separador);

        if($encontrado)
        {
            $retorno = $this->replace($retorno, $sustitucion, $marca, $escape);
        }

        return $retorno;
    }


    /**
     * Establece el path de una variable o grupo de variables de idioma
     *
     * @version 1.0
     *
     * @param string $path path en el que se guarda la variable
     * @param string|string[] $vars variables que se guardan
     * @param string $marca carácter que separa los niveles del path
     */
    public function set($path, $vars, $marca = self::SEPARADOR)
    {
        $this->lang_vars->setPath($path, $vars, $marca);
    }

    /**
     * Elimina una o un grupo de variables de idioma
     *
     * @version 1.0
     *
     * @param string $path path en el que se guarda la variable
     * @param string $marca carácter que separa los niveles del path
     */
    public function remove($path, $marca = self::SEPARADOR)
    {
        return $this->lang_vars->removePath($path, $marca);
    }



    /**
     * Copia el contenido de un path de origen a otro path de destino.
     * Si $path_destino no existe lo crea, si existe lo sobreescribe.
     * En el caso de que $path_origen y $path_destino sean ambos array los combina
     *
     * @version 1.0
     *
     * @param string $path_origen path de origen
     * @param string $path_destino path de destino
     * @param string $marca carácter que separa los niveles del path
     */
    public function copy($path_origen, $path_destino, $marca = self::SEPARADOR)
    {
        $vars_origen = $this->lang_vars->getPath($path_origen, $retorno, $marca);

        if($retorno)
        {
            $vars_destino = $this->lang_vars->getPath($path_destino, $encontrado, $marca);

             if($encontrado && is_array($vars_origen) && is_array($vars_destino))
             {
                 $vars_origen = array_replace_recursive($vars_destino, $vars_origen);
             }

             $this->set($path_destino, $vars_origen, $marca);
        }

        return $retorno;
    }


    /**
     * Mueve el contenido de un path de origen a otro path de destino
     * Si $path_destino no existe lo crea, si existe lo sobre escribe.
     * En el caso de que $path_origen y $path_destino sean ambos array los combina
     *
     * @version 1.0
     *
     * @param string $path_origen path de origen
     * @param string $path_destino path de destino
     * @param string $marca carácter que separa los niveles del path
     */
    public function move($path_origen, $path_destino, $marca = self::SEPARADOR)
    {
        if($this->copy($path_origen, $path_destino, $marca))
        {
            $this->remove($path_origen, $marca);
        }
    }


    /**
     * Reemplaza en un valor los place-holders por los valores pasados
     *
     * @version 1.0
     *
     * @param string $valor valor que se reemplaza
     * @param array $sustitucion con los place-holders a sustituir. Con el siguiente formato:
     *                          - arr[place-holder sin marcador] = string, reemplazo
     * @param string $marca caracteres que se utilizan para marcar el place-holder
     * @param string $escape Carácter que se utiliza de escape para la marca place-holder
     *
     * @return string string con los place-holders reemplazados
     */
    public function replace($valor, array $sustitucion = array(), $marca = ':', $escape = '\\')
    {
        if(!empty($sustitucion))
        {
            $patron = array();

            $marca = preg_quote($marca, '/');
            $escape = preg_quote($escape, '/');

            foreach($sustitucion as $sustitucionNombre => $_)
            {
                $patron[] = '/(?<![^' . $escape . ']' . $escape . ')' . $marca . preg_quote($sustitucionNombre) . '/uis';
            }

            $valor = preg_replace($patron, array_values($sustitucion), $valor);
        }

        if($valor===null)
        {
            $valor = '';
        }

        return $valor;
    }
}