<?php

declare(strict_types=1);

namespace Lib\Comun;

/**
 * Añade la función __call a la clase, que permite llamar a funciones
 * que no pertenezcan a la clase y se guarden dentro de propiedades de la misma.
 */
trait Call
{
    /**
     * Llama funciones que no están dentro de la clase.
     *
     * @version 1.0
     *
     * @deprecated Solo para versiones de PHP inferiores a la 7.0
     *
     * @param array $argumentos argumentos que se le pasan a la función
     *
     * @return mixed valor devuelto por la función
     */
    public function __call(string $funcion, $argumentos)
    {
        $retorno = null;

        if (is_callable($this->$funcion)) {
            $retorno = call_user_func_array($this->$funcion, $argumentos);
        } else {
            throw new \Exception('Call::__call: No es una función: '.$funcion);
        }

        return $retorno;
    }
}
