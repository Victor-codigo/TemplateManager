<?php

declare(strict_types=1);

namespace Tests\Unit;

use Lib\Comun\Lang;
use Lib\Exception\ExceptionDataCargar;
use Lib\GestorPlantillas;
use Lib\Plantilla;
use Lib\PlantillaConfig;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\comun\PhpunitUtil;
use Tests\Unit\Fixtures\PlantillaDataMuestraGestor;
use Tests\Unit\Fixtures\PlantillaDataMuestraOtraGestor;

class GestorPlantillasTest extends TestCase
{
    use PhpunitUtil;

    /**
     * Clase plantilla.
     *
     * @var GestorPlantillas
     */
    protected $object;

    /**
     * Plantillas data que se cargan en el gestor de plantillas.
     *
     * @var PlantillaDataMuestraGestor[]
     */
    protected $plantillas_data = [];

    /**
     * Configuración de la plantilla.
     */
    private PlantillaConfig $plantilla_config;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new GestorPlantillas(null);
        $this->plantilla_config = new PlantillaConfig();
        $this->plantilla_config->path = PlantillaDataMuestraGestor::PATH;
        $this->plantilla_config->lang_raiz = 'raiz';

        $this->propertyEdit(
            $this->object,
            'plantillas',
            [
                new Plantilla($this->object, $this->plantilla_config),
                new Plantilla($this->object, $this->plantilla_config),
                new Plantilla($this->object, $this->plantilla_config),
                new Plantilla($this->object, $this->plantilla_config),
            ]
        );

        $this->plantillas_data = [
            'plantilla_data_0' => new PlantillaDataMuestraGestor(),
            'plantilla_data_1' => new PlantillaDataMuestraGestor(),
            'plantilla_data_2' => new PlantillaDataMuestraGestor(),
            'plantilla_data_3' => new PlantillaDataMuestraGestor(),
        ];

        $this->plantillas_data['plantilla_data_0']->propiedad_1 = 'plantillas_data_0';
        $this->plantillas_data['plantilla_data_1']->propiedad_1 = 'plantillas_data_1';
        $this->plantillas_data['plantilla_data_2']->propiedad_1 = 'plantillas_data_2';
        $this->plantillas_data['plantilla_data_3']->propiedad_1 = 'plantillas_data_3';

        $this->propertyEdit($this->object, 'plantillas_data', $this->plantillas_data);
    }

    /**
     * @return array<int,
     *  array<int,
     *      array{
     *          params: array{
     *              path: string|null,
     *          },
     *          expect: string|null,
     *      }
     * >>
     */
    public static function providerGetPlantilla(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'path' => null,
                    ],
                    'expect' => null,
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'path' => PlantillaDataMuestraGestor::PATH,
                    ],
                    'expect' => PlantillaDataMuestraGestor::PATH,
                ],
            ],

            // #2
            [
                [
                    'params' => [
                        'path' => 'no existe',
                    ],
                    'expect' => null,
                ],
            ],
        ];
    }

    /**
     * @param array{
     *  params: array{
     *      path: string|null,
     *  },
     *  expect: string|null,
     * } $provider
     */
    #[Test]
    #[DataProvider('providerGetPlantilla')]
    public function getPlantilla(array $provider): void
    {
        $g_plantillas__plantilla = $this->propertyEdit($this->object, 'plantillas');

        /** @var Plantilla $resultado */
        $resultado = $this->object->getPlantilla($provider['params']['path']);

        if (null !== $provider['params']['path']) {
            if (null !== $provider['expect']) {
                $this->assertEquals(
                    $provider['params']['path'],
                    $resultado->getPath(),
                    'ERROR: el valor devuelto no es el esperado'
                );
            } else {
                // @phpstan-ignore method.impossibleType
                $this->assertNull(
                    $resultado,
                    'ERROR: se esperaba que el resultado fuera NULL'
                );
            }
        } else {
            $this->assertEquals(
                $g_plantillas__plantilla->getValue($this->object),
                $resultado,
                'ERROR: el valor devuelto no es el esperado'
            );
        }
    }

    /**
     * @return array<int,
     *  array<int,
     *      array{
     *          params: array{
     *              id: string|null,
     *          },
     *          expect: string|null,
     *      }
     * >>
     */
    public static function providerGetData(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'id' => null,
                    ],
                    'expect' => null,
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'id' => 'plantilla_data_2',
                    ],
                    'expect' => 'plantilla_data_2',
                ],
            ],

            // #2
            [
                [
                    'params' => [
                        'id' => 'no existe',
                    ],
                    'expect' => null,
                ],
            ],
        ];
    }

    /**
     * @param array{
     *  params: array{
     *      id: string|null,
     *  },
     *  expect: string|null,
     * } $provider
     */
    #[Test]
    #[DataProvider('providerGetData')]
    public function getData(array $provider): void
    {
        if (isset($this->plantillas_data[$provider['expect']])) {
            $provider['expect'] = $this->plantillas_data[$provider['expect']];
        }

        $g_plantillas__plantillas_data = $this->propertyEdit($this->object, 'plantillas_data');

        $resultado = $this->object->getData($provider['params']['id']);

        if (null !== $provider['params']['id']) {
            if (null !== $provider['expect']) {
                $this->assertEquals(
                    $provider['expect'],
                    $resultado,
                    'ERROR: el valor devuelto no es el esperado'
                );
            } else {
                $this->assertNull(
                    $resultado,
                    'ERROR: se esperaba que el resultado fuera NULL'
                );
            }
        } else {
            $this->assertEquals(
                $g_plantillas__plantillas_data->getValue($this->object),
                $resultado,
                'ERROR: el valor devuelto no es el esperado'
            );
        }
    }

    #[Test]
    public function setData__GestorPlantillas(): void
    {
        $g_plantillas__plantillas_data = $this->propertyEdit($this->object, 'plantillas_data');

        $num_plantillas_data = count($g_plantillas__plantillas_data->getValue($this->object));
        $this->object->setData('nuevo', new PlantillaDataMuestraGestor());

        $this->assertCount(
            $num_plantillas_data + 1,
            $g_plantillas__plantillas_data->getValue($this->object),
            'ERROR: el número de plantillas_data no es el esperado'
        );

        $this->plantillas_data['nuevo'] = new PlantillaDataMuestraGestor();

        $this->assertEquals(
            $this->plantillas_data,
            $g_plantillas__plantillas_data->getValue($this->object),
            'ERROR: no se ha añadido correctamente la plantilla_data'
        );
    }

    #[Test]
    public function getLang(): void
    {
        $lang = new Lang();
        $lang->setLangs(['lang' => 'lang']);
        $lang->setLang('lang');
        $this->propertyEdit($this->object, 'lang', $lang);
        $resultado = $this->object->getLang();

        $this->assertEquals(
            'lang',
            $resultado->getLang(),
            'ERROR:el valor devuelto no es el esperado'
        );
    }

    #[Test]
    public function setLang(): void
    {
        $lang_mock = $this
            ->getMockBuilder(Lang::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object->setLang($lang_mock);

        $this->assertEquals(
            $lang_mock,
            $this->propertyEdit($this->object, 'lang')->getValue($this->object),
            'ERROR:el valor devuelto no es el esperado'
        );
    }

    #[Test]
    public function cargarPlantilla__La_plantilla_no_esta_cargada(): void
    {
        $plantilla_config = new PlantillaConfig();
        $plantilla_config->path = PlantillaDataMuestraOtraGestor::PATH;
        $plantilla_config->lang_raiz = PlantillaDataMuestraOtraGestor::LANG_RAIZ;

        $resultado = $this->object->cargarPlantilla($plantilla_config);

        $this->assertInstanceOf(
            Plantilla::class,
            $resultado,
            'ERROR: el valor devuelto no es del tipo'.Plantilla::class
        );

        $this->assertNotInstanceOf(
            PlantillaDataMuestraGestor::class,
            $resultado,
            'ERROR: el valor devuelto no es del tipo'.PlantillaDataMuestraGestor::class
        );
    }

    #[Test]
    public function cargarPlantilla__La_plantilla_ya_esta_cargada(): void
    {
        $plantilla_config = new PlantillaConfig();
        $plantilla_config->path = PlantillaDataMuestraGestor::PATH;
        $plantilla_config->lang_raiz = PlantillaDataMuestraGestor::LANG_RAIZ;

        $plantillas = $this
            ->propertyEdit($this->object, 'plantillas')
            ->getValue($this->object);

        $resultado = $this->object->cargarPlantilla($plantilla_config);

        $this->assertInstanceOf(
            Plantilla::class,
            $resultado,
            'ERROR: el valor devuelto no es del tipo'.Plantilla::class
        );

        $this->assertEquals(
            $plantillas[0],
            $resultado,
            'ERROR: La plantilla devuelta no es la esperada'
        );
    }

    /**
     * @return array<int,
     *  array<int,
     *      array{
     *          params: array{
     *              data: string|PlantillaDataMuestraGestor,
     *              string: bool,
     *          },
     *          mock: array{
     *              render: string,
     *          },
     *          expect: \Exception|string|bool,
     *      }
     * >>
     */
    public static function providerRenderPlantilla(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'data' => 'no existe',
                        'string' => true,
                    ],
                    'mock' => [
                        'render' => '<div>OK</div>',
                    ],
                    'expect' => new \Exception(),
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'data' => 'plantilla_data_2',
                        'string' => true,
                    ],
                    'mock' => [
                        'render' => '<div>OK</div>',
                    ],
                    'expect' => '<div>OK</div>',
                ],
            ],

            // #2
            [
                [
                    'params' => [
                        'data' => 'plantilla_data_2',
                        'string' => false,
                    ],
                    'mock' => [
                        'render' => '<div>OK</div>',
                    ],
                    'expect' => true,
                ],
            ],

            // #3
            [
                [
                    'params' => [
                        'data' => new PlantillaDataMuestraGestor(),
                        'string' => false,
                    ],
                    'mock' => [
                        'render' => '<div>OK</div>',
                    ],
                    'expect' => true,
                ],
            ],
        ];
    }

    /**
     * @param array{
     *  params: array{
     *      data: string,
     *      string: bool,
     *  },
     *  mock: array{
     *      render: string,
     *  },
     *  expect: \Exception|string|bool,
     * } $provider
     */
    #[Test]
    #[DataProvider('providerRenderPlantilla')]
    public function testRenderPlantilla(array $provider): void
    {
        if ($provider['expect'] instanceof \Exception) {
            $this->expectException(ExceptionDataCargar::class);
        } elseif (!$provider['params']['string']) {
            $this->expectOutputString($provider['mock']['render']);
        }

        $plantilla_mock = $this
            ->getMockBuilder(Plantilla::class)
            ->setConstructorArgs([$this->object, $this->plantilla_config])
            ->onlyMethods(['render'])
            ->getMock();

        $plantilla_mock
            ->expects($this->any())
            ->method('render')
            ->willReturnCallback(function () use ($provider) {
                if ($provider['params']['string']) {
                    return $provider['mock']['render'];
                } else {
                    echo $provider['mock']['render'];

                    return true;
                }
            });

        $this->propertyEdit($this->object, 'plantillas', [$plantilla_mock]);

        $resultado = $this->object->renderPlantilla(
            $provider['params']['data'],
            $provider['params']['string']
        );

        $this->assertEquals(
            $provider['expect'],
            $resultado,
            'ERROR: El valor devuelto no es el esperado'
        );
    }
}
