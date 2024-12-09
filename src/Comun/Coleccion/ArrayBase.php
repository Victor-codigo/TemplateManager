<?php

declare(strict_types=1);

namespace Lib\Comun\Coleccion;

/**
 * Crea una clase base para extender la funcionalidad del array.
 *
 * @implements \Iterator<object>
 */
abstract class ArrayBase implements \Iterator, \Countable, \JsonSerializable
{
    /**
     * Constructor.
     *
     * @version 1.0
     *
     * @param object[] $items elementos del array
     */
    public function __construct(protected array $items = [])
    {
    }

    /**
     * Destructor.
     *
     * @version 1.0
     */
    public function __destruct()
    {
        $this->clear();
    }

    /**
     * Obtiene el número de elementos del array.
     *
     * @version 1.0
     */
    #[\Override]
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Obtiene el item actual.
     *
     * @version 1.0
     *
     * @return mixed|false valor
     *                     FALSE si está vacío o ha alcanzado el final
     */
    #[\Override]
    public function current(): mixed
    {
        return current($this->items);
    }

    /**
     * Obtiene el indice del item actual.
     *
     * @version 1.0
     *
     * @return string|int|null indice actual
     *                         NULL si está vacío o ha alcanzado el final
     */
    #[\Override]
    public function key(): string|int|null
    {
        return key($this->items);
    }

    /**
     * Avanza el una posición.
     *
     * @version 1.0
     */
    #[\Override]
    public function next(): void
    {
        next($this->items);
    }

    /**
     * Rebobina al primer elemento.
     *
     * @version 1.0
     */
    #[\Override]
    public function rewind(): void
    {
        reset($this->items);
    }

    /**
     * Comprueba si el item actual es válido.
     *
     * @version 1.0
     *
     * @return bool TRUE si el indice interno es válido
     *              FALSE si no lo es
     */
    #[\Override]
    public function valid(): bool
    {
        return false !== current($this->items);
    }

    /**
     * Elimina todos los items.
     *
     * @version 1.0
     */
    public function clear(): void
    {
        foreach ($this->items as &$item) {
            $item = null;
        }

        $this->items = [];
    }

    /**
     * Comprueba si está vacío.
     *
     * @version 1.0
     *
     * @return bool TRUE si el está vacío
     *              FALSE si no lo está
     */
    public function isEmpty()
    {
        return [] === $this->items;
    }

    /**
     * Obtiene el array.
     *
     * @version 1.0
     *
     * @return mixed[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Obtiene la referencia al array el array.
     *
     * @version 1.0
     *
     * @return mixed[]
     */
    public function &getItemsRef()
    {
        return $this->items;
    }

    /**
     * Añade un elemento al final del array.
     *
     * @version 1.0
     *
     * @param mixed           $item elemento que se añade
     * @param int|string|null $id   identificador del elemento
     */
    public function push(mixed $item, $id = null): void
    {
        if (is_string($id) || is_numeric($id)) {
            $this->items[$id] = $item;

            return;
        }

        $this->items[] = $item;
    }

    /**
     * Crea un clon de la clase.
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
     * Carga los datos desde un array.
     *
     * @version 1.0
     *
     * @param object[] $array    desde el que se cargan los datos
     * @param bool     $set_keys TRUE si se añaden los identificadores de los items
     *                           FALSE no se añaden
     */
    public function fillFromArray(array $array, $set_keys = true): void
    {
        $this->clear();

        foreach ($array as $indice => $item) {
            $id = $set_keys ? $indice : null;
            $this->push($item, $id);
        }
    }

    /**
     * copia la referencia de un array.
     *
     * @version 1.0
     *
     * @param object[] $array del que se copia la referencia
     */
    public function fillFromArrayRef(array &$array): void
    {
        $this->items = &$array;
    }

    /**
     * Obtiene un item del array por su indice.
     *
     * @version 1.0
     *
     * @param int $index indice
     *
     * @return mixed|null item
     *                    NULL si no se encuentra
     */
    public function get($index)
    {
        $retorno = null;

        if (isset($this->items[$index])) {
            $retorno = $this->items[$index];
        }

        return $retorno;
    }

    /**
     * Obtiene los elementos del array serializados en formato JSON.
     *
     * @version 1.0
     *
     * @return string|false array en JSON
     *                      FALSE Si se produce un error
     */
    #[\Override]
    public function jsonSerialize(): mixed
    {
        return json_encode($this->items);
    }
}
