<?php

namespace Lib\Comun\Coleccion;

use Countable;
use Iterator;
use JsonSerializable;

/**
 * Crea una clase base para extender la funcionalidad del array
 */
abstract class ArrayBase implements Iterator, Countable, JsonSerializable
{
    /**
     * Array
     * @var array
     */
    protected $array = array();


    /**
     * Constructor
     *
     * @version 1.0
     *
     * @param array $items elementos del array
     */
    public function __construct(array $items = array())
    {
        $this->array = $items;
    }

    /**
     * Destructor
     *
     * @version 1.0
     */
    public function __destruct()
    {
        $this->clear();
    }


    /**
     * Obtiene el número de elementos del array
     *
     * @version 1.0
     *
     * @return int
     */
    public function count():int
    {
        return count($this->array);
    }


    /**
     * Obtiene el item actual
     *
     * @version 1.0
     *
     * @return mixed|FALSE valor
     *                     FALSE si está vacío o ha alcanzado el final
     */
    public function current():mixed
    {
        return current($this->array);
    }


    /**
     * Obtiene el indice del item actual
     *
     * @version 1.0
     *
     * @return int|NULL indice actual
     *                  NULL si está vacío o ha alcanzado el final
     */
    public function key():mixed
    {
        return key($this->array);
    }


    /**
     * Avanza el una posición
     *
     * @version 1.0
     */
    public function next():void
    {
        next($this->array);
    }


    /**
     * Rebobina al primer elemento
     *
     * @version 1.0
     */
    public function rewind():void
    {
        reset($this->array);
    }


    /**
     * Comprueba si el item actual es válido
     *
     * @version 1.0
     *
     * @return boolean TRUE si el indice interno es válido
     *                  FALSE si no lo es
     */
    public function valid():bool
    {
        return current($this->array)===false ? false : true;
    }

    /**
     * Elimina todos los items
     *
     * @version 1.0
     */
    public function clear()
    {
        foreach($this->array as &$item)
        {
            $item = null;
        }

        $this->array = array();
    }

    /**
     * Comprueba si está vacío
     *
     * @version 1.0
     *
     * @return boolean TRUE si el está vacío
     *                  FALSE si no lo está
     */
    public function isEmpty()
    {
        return empty($this->array);
    }

    /**
     * Obtiene el array
     *
     * @version 1.0
     *
     * @return mixed[]
     */
    public function getItems()
    {
        return $this->array;
    }

    /**
     * Obtiene la referencia al array el array
     *
     * @version 1.0
     *
     * @return mixed[]
     */
    public function &getItemsRef()
    {
        return $this->array;
    }

    /**
     * Añade un elemento al final del array
     *
     * @version 1.0
     *
     * @param mixed $item elemento que se añade
     * @param int|string $id identificador del elemento
     */
    public function push($item, $id = null)
    {
        if(is_string($id) || is_numeric($id))
        {
            $this->array[$id] = $item;

            return;
        }

        $this->array[] = $item;
    }

    /**
     * Crea un clon de la clase
     *
     * @version 1.0
     *
     * @return ArrayBase
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * Carga los datos desde un array
     *
     * @version 1.0
     *
     * @param array $array desde el que se cargan los datos
     * @param boolean $set_keys TRUE si se añaden los identificadores de los items
     *                          FALSE no se añaden
     */
    public function fillFromArray(array $array, $set_keys = true)
    {
        $this->clear();

        foreach($array as $indice => $item)
        {
            $id = $set_keys ? $indice : null;
            $this->push($item, $id);
        }
    }


    /**
     * copia la referencia de un array
     *
     * @version 1.0
     *
     * @param array $array del que se copia la referencia
     */
    public function fillFromArrayRef(array &$array)
    {
        $this->array =& $array;
    }


    /**
     * Obtiene un item del array por su indice
     *
     * @version 1.0
     *
     * @param int $index indice
     *
     * @return mixed|NULL item
     *                   NULL si no se encuentra
     */
    public function get($index)
    {
        $retorno = null;

        if(isset($this->array[$index]))
        {
            $retorno = $this->array[$index];
        }

        return $retorno;
    }

    /**
     * Obtiene los elementos del array serializados en formato JSON
     *
     * @version 1.0
     *
     * @return string|FALSE array en JSON
     *                      FALSE Si se produce un error
     */
    public function jsonSerialize():mixed
    {
        return json_encode($this->array);
    }
}