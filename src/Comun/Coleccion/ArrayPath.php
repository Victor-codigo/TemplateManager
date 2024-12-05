<?php

declare(strict_types=1);

namespace Lib\Comun\Coleccion;

/**
 * Array al que se le pueden modificar los items a través de un path.
 *
 */
class ArrayPath extends ArrayBase
{
    /**
     * Obtiene el valor para el path pasado.
     *
     * @version 1.0
     *
     * @param string $path       path de la ruta
     * @param bool   $encontrado [OUT] TRUE si se encuentra, FALSE si no se encuentra
     * @param string $separator  separador que se usa para crear la ruta
     *
     * @return mixed|null valor
     *                    NULL si no se encuentra
     */
    public function getPath($path, &$encontrado = false, $separator = '.')
    {
        return $this->getPointer($path, $encontrado, $separator);
    }

    /**
     * Crea una ruta. Si la ruta ya existe la sobre escribe.
     *
     * @version 1.0
     *
     * @param string $path      path de la ruta
     * @param mixed  $value     valor que se guarda en la ruta
     * @param string $separator separador que se usa para crear la ruta
     *
     * @return bool TRUE si se creó con éxito,
     *              FALSE si no se creó
     */
    public function setPath($path, mixed $value, $separator = '.'): bool
    {
        if ('' === $path) {
            $this->items = [$value];

            return true;
        }

        $path_indices = explode($separator, $path);
        $path_indices_count = count($path_indices);
        $item_actual = &$this->items;

        foreach ($path_indices as $count => $indice) {
            if (!isset($item_actual[$indice])) {
                $item_actual[$indice] = [];
            }

            if ($count == $path_indices_count - 1) {
                $item_actual[$indice] = $value;
            }

            $item_actual = &$item_actual[$indice];
        }

        return true;
    }

    /**
     * Comprueba si un path existe.
     *
     * @version 1.0
     *
     * @param string $path      path que se comprueba
     * @param string $separator separador usado en el path
     *
     * @return bool TRUE si el path existe
     *              FALSE si no existe
     */
    public function pathExists($path, $separator = '.')
    {
        $encontrado = false;
        $this->getPointer($path, $encontrado, $separator);

        return $encontrado;
    }

    /**
     * Elimina un path del array.
     *
     * @version 1.0
     *
     * @param string $path      path que se elimina
     * @param string $separator separador usado en el path
     *
     * @return bool TRUE si se borró correctamente,
     *              FALSE si no se borró
     */
    public function removePath($path, $separator = '.'): bool
    {
        if ('' == $path) {
            $this->clear();

            return true;
        }

        $contenedor = &$this->items;
        $path_ultimo = $path;
        $encontrado = true;
        $path_array = explode($separator, $path);

        if (count($path_array) > 1) {
            $path_ultimo = array_pop($path_array);
            $path_contenedor = implode($separator, $path_array);
            $contenedor = &$this->getPointer($path_contenedor, $encontrado, $separator);
        }

        if (!$encontrado || !isset($contenedor[$path_ultimo])) {
            return false;
        }

        unset($contenedor[$path_ultimo]);

        return true;
    }

    /**
     * Busca un path y devuelve su una referencia al valor.
     *
     * @version 1.0
     *
     * @param string $path
     * @param bool   $encontrado [OUT] TRUE si se encuentra, FALSE si no se encuentra
     *
     * @return resource|null NULL si no se encuentra
     */
    public function &getPointer(string $path, bool &$encontrado = false, string $separator = '.')
    {
        $path_array = explode($separator, $path);
        $item_actual = &$this->items;
        $encontrado = true;
        $retorno_null = null;

        foreach ($path_array as $indice) {
            if (!isset($item_actual[$indice])) {
                $encontrado = false;

                break;
            }

            $item_actual = &$item_actual[$indice];
        }

        if ($encontrado) {
            return $item_actual;
        }

        return $retorno_null;
    }
}
