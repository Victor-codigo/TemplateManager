<?php

declare(strict_types=1);

namespace Lib;

use Lib\Comun\Call;
use Lib\Exception\ExceptionDataCargar;
use Lib\Exception\ExceptionPlantillaCargar;
use Lib\Exception\ExceptionPlantillaNoEncontrada;

class Plantilla
{
    use Call;

    /**
     * CONSTANTE
     * Bandera que indica que caracteres escapa la función htmlspecialchars.
     *
     * @var int
     */
    public const HTML_ESCAPE_ENT_QUOTES = ENT_QUOTES;

    /**
     * CONSTANTE
     * Codificación de la web.
     *
     * @var string
     */
    public const CODIFICACION = 'UTF-8';

    /**
     * CONSTANTE
     * Características separador de las URLs. Se utiliza para la función url.
     *
     * @var string
     */
    public const URL_SECCION_SEPARADOR = '/';

    /**
     * Path de la plantilla.
     *
     * @var string
     */
    private $path;

    /**
     * Obtiene el path de la plantilla.
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
     * Establece el path de la plantilla.
     *
     * @version 1.0
     *
     * @param string $path path de la plantilla
     *
     * @throws ExceptionPlantillaNoEncontrada si no se puede leer el path
     */
    public function setPath($path): void
    {
        if (is_readable($path)) {
            $this->path = $path;
        } else {
            throw new ExceptionPlantillaNoEncontrada($path);
        }
    }

    /**
     * Raíz del path de idioma.
     */
    private string $lang_raiz = '';

    /**
     * Establece la raíz del path de idioma.
     *
     * @version 1.0
     *
     * @param string $lang_raiz raíz del path de idioma
     */
    public function setLangRaiz(string $lang_raiz): void
    {
        $this->lang_raiz = $lang_raiz;
    }

    /**
     * Obtiene la raíz del path de idioma.
     *
     * @version 1.0
     */
    public function getLangRaiz(): string
    {
        return $this->lang_raiz;
    }

    /**
     * Callback de la plantilla
     * con el siguiente formato:
     * Parámetros:
     *  - PlantillaData, información que se l pasa a la estructura
     *  - Plantilla, plantilla.
     */
    private ?\Closure $callback;

    /**
     * Obtiene la función de la plantilla.
     *
     * @version 1.0
     *
     * @return callable callback con el siguiente formato:
     *                  Parámetros:
     *                  - PlantillaData, información que se le pasa a la estructura
     *                  - Plantilla, plantilla
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Constructor.
     *
     * @version 1.1
     *
     * @param GestorPlantillas $gestor gestor de plantillas
     * @param PlantillaConfig  $config Configuración de la plantilla
     */
    public function __construct(
        private ?GestorPlantillas $gestor,
        PlantillaConfig $config,
    ) {
        $this->setPath($config->path);
        $this->setLangRaiz($config->lang_raiz);
        $this->cargar($config->path);
    }

    /**
     * Destructor.
     *
     * @version 1.0
     */
    public function __destruct()
    {
        $this->gestor = null;
        $this->callback = null;
    }

    /**
     * Carga la plantilla.
     *
     * @version 1.0
     *
     * @param string $path path del archivo con la plantilla que se carga
     *
     * @throws ExceptionPlantillaCargar si no se puede leer o no devuelve una función
     */
    private function cargar(string $path): void
    {
        if (is_readable($path)) {
            $plantilla = require $path;

            $callable_name = '';
            if (is_callable($plantilla, false, $callable_name)) {
                $this->callback = $plantilla;
            } else {
                throw new ExceptionPlantillaCargar('El formato de la plantilla no es válido: '.$path);
            }
        } else {
            throw new ExceptionPlantillaCargar('No se puede cargar la plantilla: '.$path);
        }
    }

    /**
     * Renderiza la plantilla.
     *
     * @version 1.0
     *
     * @param string|PlantillaData $data   Identificador de la información guardada o
     *                                     Objeto con la información
     * @param bool                 $string TRUE si la plantilla se carga en una cadena,
     *                                     FALSE si se carga en el buffer de salida
     *
     * @return bool|string - Si $string es TRUE:
     *                     string con el código de la plantilla
     *                     - Si $string es FALSE:
     *                     TRUE si se ejecuta correctamente.
     *                     FALSE si se produce un error
     */
    public function render($data, $string = false): string|bool
    {
        ob_start();

        if (is_string($data)) {
            $data = $this->gestor->getData($data);

            if (null === $data) {
                ob_end_flush();
                throw new ExceptionDataCargar('No se encuentra la información de la plantilla: '.$data);
            }
        }

        // @phpstan-ignore method.notFound
        $this->callback($data, $this);

        return $string ? ob_get_clean() : ob_end_flush();
    }

    /**
     * renderiza una plantilla.
     *
     * @version 1.0
     *
     * @param string|PlantillaData $data   Identificador de la información guardada o
     *                                     Objeto con la información
     * @param bool                 $string TRUE si la plantilla se carga en una cadena,
     *                                     FALSE si se carga en el buffer de salida
     */
    public function renderPlantilla($data, $string = false): string|bool
    {
        return $this->gestor->renderPlantilla($data, $string);
    }

    /**
     * Comprueba si existe una PlantillaData en el gestor de plantillas.
     *
     * @version 1.0
     *
     * @param string $id identificador de la información
     *
     * @return bool TRUE si existe, FALSE si no existe
     */
    public function existeData($id): bool
    {
        return null !== $this->gestor->getData($id);
    }

    /**
     * Carga en la plantilla una variable de idioma.
     *
     * @version 1.0
     *
     * @param string                    $path        path de la variable
     * @param array<string|int, string> $sustitucion con los place-holders a sustituir. Con el siguiente formato:
     *                                               - arr[place-holder sin marcador] = string, reemplazo
     * @param string                    $marca       caracteres que se utilizan para marcar el place-holder
     * @param bool                      $string      TRUE si devuelve un string
     *                                               FALSE si se muestra por pantalla
     */
    public function lang(string $path, array $sustitucion = [], $marca = ':', $string = false): ?string
    {
        $valor = (string) $this->gestor->getLang()->get(
            $this->getLangRaiz().'.'.$path,
            $sustitucion,
            '.',
            $marca,
            '\\'
        );

        return $this->data($valor, $string);
    }

    /**
     * Carga en la plantilla una variable de idioma.
     * No escapa el texto que es sustituido.
     *
     * @version 1.0
     *
     * @param string                $path        path de la variable
     * @param array<string, string> $sustitucion con los place-holders a sustituir. Con el siguiente formato:
     *                                           - arr[place-holder sin marcador] = string, reemplazo
     * @param string                $marca       caracteres que se utilizan para marcar el place-holder
     *
     * @return string con los valores escapados
     */
    public function langHtml(string $path, array $sustitucion = [], $marca = ':'): string
    {
        $lang = $this->gestor->getLang();
        $valor = $lang->get(
            $this->getLangRaiz().'.'.$path,
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

        return $texto_escapado;
    }

    /**
     * Escapa los valores pasados para ser mostrados en HTML.
     *
     * @version 1.1
     *
     * @param string $valor  cadena a escapar
     * @param bool   $string TRUE si devuelve un string
     *                       FALSE si se muestra por pantalla
     *
     * @return string con los valores escapados
     */
    public function data(string $valor, bool $string = false): ?string
    {
        $data = htmlspecialchars(
            $valor,
            self::HTML_ESCAPE_ENT_QUOTES,
            self::CODIFICACION
        );

        if ($string) {
            return $data;
        }

        echo $data;

        return null;
    }

    /**
     * Devuelve una URL válida a partir de la URL pasada.
     *
     * @version 1.1
     *
     * @param string                $url               URL a escapar
     * @param array<string, string> $sustitucion       caracteres que se sustituyen en cada una de las partes
     *                                                 de la URL, con el siguiente formato:
     *                                                 - arr[carácter a sustituir] = string, carácter sustituido
     * @param string                $seccion_separador carácter separador de las secciones de la URL
     * @param bool                  $string            TRUE si devuelve un string
     *                                                 FALSE si se muestra por pantalla
     *
     * @return string URL saneada
     */
    public function url($url, array $sustitucion = [' ' => '-'], string $seccion_separador = self::URL_SECCION_SEPARADOR, bool $string = false): ?string
    {
        // @phpstan-ignore argument.type
        $url_trozos = explode($seccion_separador, trim($url));
        $sustituido = array_keys($sustitucion);

        for ($i = 0, $length = count($url_trozos); $i < $length; ++$i) {
            $url_trozos[$i] = urlencode(str_replace(
                $sustituido,
                $sustitucion,
                mb_strtolower($url_trozos[$i])
            ));
        }

        return $this->data(
            // @phpstan-ignore argument.type
            $this->urlEspaceHttpYSharp(implode($seccion_separador, $url_trozos)),
            $string
        );
    }

    /**
     * Corrige el error de escape del protocolo HTTP|HTTPS y del #.
     *
     * @version 1.0
     *
     * @param string $url URL escapada
     *
     * @return string|string[]|null url corregida
     */
    // @phpstan-ignore return.unusedType
    private function urlEspaceHttpYSharp(string $url): string|array|null
    {
        $patron = [];
        $reemplazo = [];

        if (false !== mb_strpos($url, '%3A')) {
            $patron[] = '/^(https?)%3A\/\//';
            $reemplazo[] = '$1://';
        }

        if (false !== mb_strpos($url, '%23')) {
            $patron[] = '/^%23/';
            $reemplazo[] = '#';
        }

        if ([] !== $patron) {
            return preg_replace($patron, $reemplazo, $url, 1);
        }

        return $url;
    }

    /**
     * Convierte un valor a formato JSON.
     *
     * @version 1.0
     *
     * @param mixed $json     json que se escapa
     * @param int   $opciones una de las constantes JSON_*
     * @param int   $depth    profundidad máxima de la variable
     * @param bool  $string   TRUE si devuelve un string
     *                        FALSE si se muestra por pantalla
     *
     * @return string Si existe la variable, devuelve la variable convertida a JSON
     */
    public function json(mixed $json, int $opciones = JSON_HEX_TAG | JSON_HEX_AMP, int $depth = 512, bool $string = false): ?string
    {
        // @phpstan-ignore argument.type
        $retorno = json_encode($json, $opciones, $depth);

        if (false === $retorno) {
            $retorno = '';
        }

        if ($string) {
            return $retorno;
        }

        echo $retorno;

        return null;
    }

    /**
     * Devuelve un atributo de una etiqueta HTML.
     *
     * @param string $atr    nombre del atributo
     * @param string $valor  valor del atributo
     * @param int    $tipo   tipo de valor. Una de las constates TIPODATO::DATA o TIPODATO::URL
     * @param bool   $string TRUE si devuelve un string
     *                       FALSE si se muestra por pantalla
     *
     * @return string con el atributo y su valor escapado
     */
    public function atr($atr, $valor, $tipo = TIPODATO::DATA, $string = false): ?string
    {
        $retorno = '';

        if ('' !== $valor) {
            $valor = match ($tipo) {
                TIPODATO::DATA => $this->data($valor, true),
                TIPODATO::URL => $this->url($valor, [' ' => '-'], self::URL_SECCION_SEPARADOR, true),
                default => '',
            };
            $retorno = '' == $valor
                ? ''
                : $this->data($atr, true).'="'.trim($valor).'"';
        }

        if ($string) {
            return $retorno;
        }

        echo $retorno;

        return null;
    }

    /**
     * Ejecuta una sentencia foreach para un array pasado. Concatenando los
     * elementos del array separados por un espacio.
     *
     * @version 1.0
     *
     * @param string[] $array     con los elementos
     * @param int      $modo      establece el modo en el que se concatenen los elementos
     * @param string   $separador string con el que se separan los elementos
     * @param bool     $string    TRUE si devuelve un string
     *                            FALSE si se muestra por pantalla
     *
     * @return string con los elementos concatenados
     */
    public function each(array $array, int $modo = EACH::NORMAL, string $separador = ' ', bool $string = false): ?string
    {
        $retorno = '';

        foreach ($array as $indice => $valor) {
            switch ($modo) {
                case EACH::ATRIBUTO:
                    $retorno .= $separador.$this->data($indice, true).'="'.$this->data($valor, true).'"';

                    break;

                case EACH::DATA:
                    $retorno .= $separador.'data-'.$this->data($indice, true).'="'.$this->data($valor, true).'"';

                    break;

                case EACH::NORMAL:
                    $retorno .= $separador.$this->data($valor, true);

                    break;
            }
        }

        if ($string) {
            return $retorno;
        }

        echo $retorno;

        return null;
    }
}
