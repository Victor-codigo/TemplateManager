<?php

declare(strict_types=1);

namespace Tests\comun;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub\Stub;
use ReflectionMethod;
use ReflectionProperty;
use Tests\comun\Exceptions\GetMethodException;
use Tests\comun\Exceptions\GetPropertyException;

trait PhpunitUtil
{
    /**
     * @var \ReflectionClass
     */
    private $reflectClass = [];

    /**
     * Obtiene la clase que ha sido reflectada.
     *
     * @version 1.0
     *
     * @param string $class nombre de la clase
     *
     * @return \ReflectionClass clase reflectada
     */
    protected function getReflectedClass($class)
    {
        $retorno = null;

        foreach ($this->reflectClass as $classReflect) {
            if ($classReflect->name == $class) {
                $retorno = $classReflect;

                break;
            }
        }

        return $retorno;
    }

    /**
     * Crea la reflexión de la clase o devuelve la reflexión de la clase si ya existía.
     *
     * @version 1.0
     *
     * @param object $object objeto que se reflecta
     *
     */
    private function reflectClass($object):\ReflectionClass|null
    {
        $encontrado = false;
        $class_name = $object::class;
        $reflect_class = null;

        foreach ($this->reflectClass as $class_reflect) {
            if ($class_reflect->name == $class_name) {
                $encontrado = true;
                $reflect_class = $class_reflect;

                break;
            }
        }

        if (!$encontrado) {
            $reflect_class = new \ReflectionClass($class_name);
            $this->reflectClass[] = $reflect_class;
        }

        return $reflect_class;
    }

    /**
     * Obtiene las clases parientes de la clase pasada.
     *
     * @version 1.0
     *
     * @param \ReflectionClass $class clase para la que se buscan los parientes
     * @param string           $until nombre completo de la clase en la que se detiene,
     *                                la búsqueda de parientes
     *
     * @return \ReflectionClass[] clases parientes
     */
    private function getParents(\ReflectionClass $class, $until = null): array
    {
        $parents = [];

        while ($class = $class->getParentClass()) {
            $parents[] = $class;
            $class_name = $class->getNamespaceName().'\\'.$class->getName();

            if (null !== $until && $class_name === $until) {
                break;
            }
        }

        return $parents;
    }

    /**
     * Obtiene una propiedad de la clase.
     *
     * @version 1.0
     *
     * @param \ReflectionClass $class  clase en la que se busca la propiedad
     * @param string           $name   nombre de la propiedad
     * @param bool             $access TRUE si la propiedad se hace publica, FALSE si no
     *
     *
     * @throws GetPropertyException
     */
    private function getProperty(\ReflectionClass $class, string $name, $access): ReflectionProperty
    {
        $reflectedClases = $this->getParents($class);
        array_unshift($reflectedClases, $class);

        foreach ($reflectedClases as $class) {
            if ($class->hasProperty($name)) {
                $property = $class->getProperty($name);
                $property->setAccessible($access);

                break;
            }
        }

        if (!isset($property)) {
            throw new GetPropertyException('La propiedad: \''.$name.'\'. No existe.');
        }

        return $property;
    }

    /**
     * Obtiene un método de la clase.
     *
     * @version 1.0
     *
     * @param \ReflectionClass $class  clase en la que se busca el método
     * @param string           $name   nombre del método
     * @param bool             $access TRUE si la propiedad se hace publica, FALSE si no
     *
     *
     * @throws GetMethodException
     */
    private function getMethod(\ReflectionClass $class, string $name, $access):ReflectionMethod
    {
        $reflected_clases = $this->getParents($class);
        array_unshift($reflected_clases, $class);

        foreach ($reflected_clases as $class) {
            if ($class->hasMethod($name)) {
                $method = $class->getMethod($name);
                $method->setAccessible($access);

                break;
            }
        }

        if (!isset($method)) {
            throw new GetMethodException('El método: \''.$name.'\'. No existe.');
        }

        return $method;
    }

    /**
     * Establece una propiedad de la clase como publica y establece su valor.
     *
     * @version 1.0
     *
     * @param object $object        objeto que se reflecta
     * @param string $property_name nombre de la propiedad
     * @param mixed  $value         valor en que se establece la propiedad
     * @param bool   $access        TRUE si la propiedad se hace publica, FALSE si no
     *
     * @return ReflectionProperty Propiedad
     *
     */
    protected function propertyEdit($object, $property_name, mixed $value = null, $access = true):ReflectionProperty
    {
        $property = $this->getProperty(
            $this->reflectClass($object),
            $property_name,
            $access
        );

        if (func_num_args() >= 3) {
            $property->setValue($object, $value);
        }

        return $property;
    }

    /**
     * Cambia el ámbito de un método privado o protegido a público.
     *
     * @version 1.0
     *
     * @param string $object objeto que se reflecta
     * @param string $method nombre del método
     *
     */
    protected function setMethodPublic($object, $method):ReflectionMethod
    {
        return $this->getMethod(
            $this->reflectClass($object),
            $method,
            true
        );
    }









    /**
     * Testea el destructor de la clase.
     *
     * @version 1.0
     *
     * @param object $object objeto que se testea
     * @param bool   $static TRUE si se comprueban las propiedades estáticas
     */
    protected function destructorTest($object, $static = true)
    {
        $reflection = new \ReflectionClass($object);
        $parents = $this->getParents($reflection);
        array_unshift($parents, $reflection);

        $object->__destruct();

        foreach ($parents as $class) {
            /* @var $property ReflectionProperty */
            foreach ($class->getProperties() as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($class);

                if (\is_callable($value)
                || (!\is_scalar($value) && !\is_null($value) && !\is_array($value))) {
                    $this->assertNull(
                        $value,
                        'ERROR Destructor:  la propiedad: '.$property->getName().'. No ha sido destruida'
                    );
                }
            }

            if ($static) {
                /* @var $property ReflectionProperty */
                foreach ($class->getStaticProperties() as $property => $value) {
                    if (\is_callable($value)
                    || (!\is_scalar($value) && !\is_null($value) && !\is_array($value))) {
                        $this->assertNull(
                            $value,
                            'ERROR Destructor:  la propiedad: '.$property.'. No ha sido destruida'
                        );
                    }
                }
            }
        }
    }

    /**
     * Realiza un trazado de las llamadas a funciones.
     *
     * @version 1.0
     *
     * @param int $indice indice del trazado que se devuelve. NULL se devuelve todo
     *
     * @return array todo el trazado o parte del trazado
     */
    public static function getCallerInfo($indice = null): array
    {
        $trazado = debug_backtrace();

        return null === $indice ? $trazado : $trazado[$indice];
    }

    /**
     * Afirma que para un array de objectos todos los objetos tienen la propiedad
     * pasada.
     *
     * @version 1.0
     *
     * @param string $atributo nombre de la propiedad
     * @param array  $array    array de objetos
     * @param string $mensaje  mensaje de error
     */
    public function assertArrayObjectHasAttribute($atributo, array $array, string $mensaje = ''): void
    {
        foreach ($array as $indice => $objeto) {
            // @phpstan-ignore method.notFound
            $this->assertObjectHasAttribute(
                $atributo,
                $objeto,
                'INDICE =>'.$indice.'. |=| '.$mensaje
            );
        }
    }

    /**
     * Afirma que el objeto pasado tiene una propiedad y que esta tiene el valor
     * pasado.
     *
     * @version 1.0
     *
     * @param string $atributo nombre de la propiedad
     * @param mixed  $valor    valor de la propiedad
     * @param object $objeto   objeto que se comprueba
     * @param string $mensaje  mensaje de error
     */
    public function assertObjectAttributeValue($atributo, mixed $valor, $objeto, string $mensaje = ''): void
    {
        // @phpstan-ignore method.notFound
        $this->assertObjectHasAttribute(
            $atributo,
            $objeto,
            'ERROR: assertObjectHasAttribute |=| '.$mensaje
        );

        $this->assertEquals(
            $valor,
            $objeto->$atributo,
            'ERROR: Propiedad valor |=| '.$mensaje
        );
    }

    /**
     * Afirma que todos los elementos del array son instancias de la clase pasada.
     *
     * @version 1.0
     *
     * @param string $expected nombre completo de la clase
     * @param array  $array    array a comprobar
     * @param string $mensaje  mensaje de error
     */
    public function assertArrayInstancesOf($expected, array $array, string $mensaje = ''): void
    {
        foreach ($array as $indice => $objeto) {
            $this->assertInstanceOf(
                $expected,
                $objeto,
                'INDICE =>'.$indice.' |=| '.$mensaje
            );
        }
    }

    /**
     * Afirma que el objeto pasado tiene uno de los tipos esperados.
     *
     * @version 1.0
     *
     * @param array  $expected tipos de objetos válidos
     * @param mixed  $object   objeto que se comprueba
     * @param string $mensaje  mensaje de error
     */
    public function assertArrayHasInstanceOf(array $expected, mixed $object, $mensaje = ''): void
    {
        $assert_ok = false;
        foreach ($expected as $instancia) {
            if ($object instanceof $instancia) {
                $assert_ok = true;

                break;
            }
        }

        $this->assertTrue($assert_ok, $mensaje);
    }
}
