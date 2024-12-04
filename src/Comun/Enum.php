<?php

namespace Lib\Comun;

/**
 * Permite crear enumeraciones.
 */
abstract class Enum
{
    /**
     * Crea la reflexión de la enumeración.
     *
     * @version 1.0
     *
     * @return \ReflectionClass
     */
    private static function getReflexion()
    {
        return new \ReflectionClass(get_called_class());
    }

    /**
     * Obtiene las constantes de la emumeración.
     *
     * @version 1.0
     *
     * @return string[] con los nombres y valores de las constantes,
     *                  con el  siguiente formato:
     *                  - arr[nombre de la constante] = mixed, valor de la constante
     */
    public static function getConstants()
    {
        return self::getReflexion()->getConstants();
    }

    /**
     * Obtiene el nombre de las constantes.
     *
     * @version 1.0
     *
     * @return string[] nombre de las constantes
     */
    public static function getConstantsNames()
    {
        return array_keys(self::getConstants());
    }

    /**
     * Comprueba si la enumeración tiene una constante con el nombre pasado.
     *
     * @version 1.0
     *
     * @param string $constant nombre de la constante
     *
     * @return bool TRUE si la constante existe en al numeración, FALSE si no
     */
    public static function hasConstantName($constant)
    {
        $retorno = self::getReflexion()->getConstant($constant);

        if (false !== $retorno) {
            $retorno = true;
        }

        return $retorno;
    }

    /**
     * Comprueba si la enumeración tiene una constante con el valor pasado.
     *
     * @version 1.0
     *
     * @param string $value  valor de la constante
     * @param bool   $strict TRUE si la comprobación se hace de forma estricta
     *                       FALSE no
     *
     * @return bool TRUE si la constante existe en al numeración, FALSE si no
     */
    public static function hasConstant($value, $strict = false)
    {
        foreach (static::getConstants() as $const => $const_value) {
            if ($strict && $const_value === $value) {
                return true;
            } elseif (!$strict && $const_value == $value) {
                return true;
            }
        }

        return false;
    }
}
