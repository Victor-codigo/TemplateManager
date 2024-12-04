<?php

namespace Lib\Comun\Coleccion;

class Struct implements \Serializable
{
    /**
     * Destructor.
     *
     * @version 1.0
     */
    public function __destruct()
    {
        foreach (array_keys(get_object_vars($this)) as $propiedad) {
            if (!is_scalar($this->$propiedad)) {
                $this->$propiedad = null;
            }
        }
    }

    /**
     * Carga la estructura desde un array.
     *
     * @version 1.1
     *
     * @param array $array con los datos de la estructura , con el siguiente formato:
     *                     - arr[propiedad] = mixed, valor de la propiedad
     * @param bool  $crear TRUE se crea una propiedad para cada indice del array,
     *                     FALSE se omiten los elementos del array cuyo indice no coincida
     *                     con el de una propiedad de la estructura
     */
    public function fromArray(array $array, $crear = false)
    {
        $propiedades = get_object_vars($this);

        foreach ($array as $propiedad => $valor) {
            if ($crear || array_key_exists($propiedad, $propiedades)) {
                $this->$propiedad = $valor;
            }
        }
    }

    /**
     * Carga la estructura desde otra estructura.
     *
     * @version 1.0
     *
     * @param Struct $struct estructura desde la que se carga la información
     * @param bool   $crear  TRUE se crea una propiedad para cada propiedad de la estructura,
     *                       FALSE se omiten las propiedades de la estructura
     *                       que no pertenecen a la estructura
     *
     * @return Struct
     */
    public function fromStruct(Struct $struct, $crear = false)
    {
        return $this->fromArray($struct->toArray(0));
    }

    /**
     * Guarda las propiedades en un array.
     *
     * @version 1.0
     *
     * @param int $niveles Número de niveles de anidación que alcanza la función
     *                     a la hora de convertir en array.
     *                     0 solo el actual
     *
     * @return array con las propiedades de la estructura, con el siguiente formato:
     *               - arr[nombre de la propiedad] = mixed, valor de la propiedad
     */
    public function toArray($niveles = 10)
    {
        $propiedades = [];

        foreach (get_object_vars($this) as $propiedad => $valor) {
            if ($niveles > 0 && $valor instanceof Struct) {
                $propiedades[$propiedad] = $valor->toArray($niveles - 1);
            } else {
                $propiedades[$propiedad] = $valor;
            }
        }

        return $propiedades;
    }

    /**
     * Establece un valor para todas las propiedades.
     *
     * @version 1.0
     *
     * @param mixed $valor valor que se establece para todos las propiedades
     */
    public function setPropiedadesValor(mixed $valor)
    {
        foreach (array_keys(get_object_vars($this)) as $propiedad) {
            $this->$propiedad = $valor;
        }
    }

    /**
     * Obtiene el número de propiedades de la estructura.
     *
     * @version 1.0
     *
     * @return int número de propiedades
     */
    public function length()
    {
        return count(get_object_vars($this));
    }

    /**
     * Devuelve todas las propiedades de la estructura, que no contengan el valor
     * pasado.
     *
     * @version 1.0
     *
     * @param mixed $valor  no devover propiedades con el valor pasado. Si son objetos se
     *                      compara el tipo de objeto
     * @param bool  $strict TRUE si la comparación es estricta, FALSE no
     *
     * @return array con las propiedades de la estructura que no contengan el valor pasado,
     *               con el siguiente formato:
     *               - arr[propiedad] = mixed, valor de la propiedad
     */
    public function getPropiedadesNoValor(mixed $valor, $strict = false)
    {
        $retorno = [];
        $valor_type = gettype($valor);

        foreach (get_object_vars($this) as $propiedad => $prop_valor) {
            if ($strict) {
                if ($prop_valor !== $valor) {
                    $retorno[$propiedad] = $prop_valor;
                }
            } elseif ($valor_type === gettype($prop_valor)) {
                if ($prop_valor != $valor) {
                    $retorno[$propiedad] = $prop_valor;
                }
            } elseif ((is_numeric($prop_valor) && is_string($valor))
            || (is_numeric($valor) && is_string($prop_valor))) {
                if ($prop_valor !== $valor) {
                    $retorno[$propiedad] = $prop_valor;
                }
            } else {
                $retorno[$propiedad] = $prop_valor;
            }
        }

        return $retorno;
    }

    /**
     * Devuelve todas las propiedades de la estructura.
     *
     * @version 1.0
     *
     * @return string[] Nombre de las propiedades
     */
    public function getPropiedades()
    {
        return array_keys(get_object_vars($this));
    }

    /**
     * Devuelve todos los valores de la estructura.
     *
     * @version 1.0
     *
     * @return mixed[] valores de las propiedades
     */
    public function getValores()
    {
        return get_object_vars($this);
    }

    /**
     * Devuelve el valor de una propiedad.
     *
     * @version 1.0
     *
     * @param string $propiedad nombre de la propiedad para la que se devuelve el
     *                          valor
     *
     * @return mixed|null valor de la propiedad
     *                    NULL si no existe
     */
    public function getValor($propiedad)
    {
        $retorno = null;

        foreach (get_object_vars($this) as $prop => $valor) {
            if ($propiedad === $prop) {
                $retorno = $valor;

                break;
            }
        }

        return $retorno;
    }

    /**
     * Obtiene el valor de una constante.
     *
     * @version 1.0
     *
     * @param string $constante nombre de la constate
     *
     * @return mixed valor de la constante
     */
    public function getConst($constante)
    {
        return 'class' === $constante ? static::class : constant(static::class.'::'.$constante);
    }

    /**
     * obtiene el número de propiedades que contiene la estructura.
     *
     * @version 1.0
     *
     * @return int
     */
    public function count()
    {
        return count(get_object_vars($this));
    }

    /**
     * Serializa la estructura
     * Solo se serializa las propiedades escalares, loa arrays y los valores null.
     * Serializa objetos, si estos implementan la interface \Serializable o
     * los métodos __sleep y __wakeup.
     *
     * @version 1.0
     */
    #[\Override]
    public function serialize()
    {
        return serialize($this);
    }

    public function __serialize(): array
    {
        $serializar = [];

        foreach (get_object_vars($this) as $propiedad => $valor) {
            if (is_object($valor)) {
                if ($valor instanceof \Serializable || method_exists($valor, '__sleep')) {
                    $serializar[$propiedad] = serialize($valor);
                }
            } elseif (is_scalar($valor) || is_array($valor) || is_null($valor)) {
                $serializar[$propiedad] = $valor;
            }
        }

        return $serializar;
    }

    /**
     * Desserializa la estructura
     * Solo se desserializa las propiedades escalares, loa arrays y los valores null.
     * Desserializa objetos, si estos implementan la interface \Serializable o
     * los métodos __sleep y __wakeup.
     *
     * @version 1.0
     *
     * @param string $serialized estructura serializada
     */
    #[\Override]
    public function unserialize($serialized): bool
    {
        return unserialize($serialized);
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $propiedad => $valor) {
            if (is_string($valor)) {
                $serializado = @unserialize($valor);

                $this->$propiedad = false === $serializado ? $valor : $serializado;
            } else {
                $this->$propiedad = $valor;
            }
        }
    }
}
