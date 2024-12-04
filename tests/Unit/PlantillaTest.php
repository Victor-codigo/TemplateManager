<?php

declare(strict_types=1);

namespace Tests\Unit;

use Lib\Comun\Lang;
use Lib\EACH;
use Lib\Exception\ExceptionDataCargar;
use Lib\Exception\ExceptionPlantillaCargar;
use Lib\Exception\ExceptionPlantillaNoEncontrada;
use Lib\GestorPlantillas;
use Lib\Plantilla;
use Lib\PlantillaConfig;
use Lib\TIPODATO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Fixtures\PlantillaDataMuestra;
use Tests\Unit\Fixtures\PlantillaFixture;
use Tests\Unit\Fixtures\PlantillaForTesting;
use Tests\comun\PhpunitUtil;

use const Tests\PATH_UNIT_FIXTURE;

require_once 'tests/Unit/Fixtures/PlantillaFixture.php';

class PlantillaTest extends TestCase
{
    use PhpunitUtil;

    /**
     * Clase plantilla.
     *
     * @var Plantilla
     */
    protected $object;

    /**
     * Mock de la clase GestorPlantillas.
     */
    protected MockObject&GestorPlantillas $gestor_mock;

    /**
     * Clase Lang.
     *
     */
    private MockObject&Lang $lang;

    /**
     * PlantillaData de muestra.
     */
    private PlantillaDataMuestra $plantilla_data;

    /**
     * Configuración de la plantilla.
     */
    private PlantillaConfig $plantilla_config;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->lang = $this
            ->getMockBuilder(Lang::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $this->gestor_mock = $this
            ->getMockBuilder(GestorPlantillas::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->gestor_mock->setLang($this->lang);

        $this->plantilla_config = new PlantillaConfig();
        $this->plantilla_config->path = PATH_UNIT_FIXTURE.'/plantillas/plantillaOk.html.php';
        $this->plantilla_config->lang_raiz = 'raiz';
        $this->object = new Plantilla($this->gestor_mock, $this->plantilla_config);

        $this->plantilla_data = new PlantillaDataMuestra();
        $this->plantilla_data->propiedad_6 = new PlantillaDataMuestra();
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();

        PlantillaFixture::$is_readable = null;
        PlantillaFixture::$is_callable = null;
    }

    #[Test]
    public function getPath(): void
    {
        $plantilla__path = $this->propertyEdit($this->object, 'path', 'path/de/la/plantilla');

        $retorno = $this->object->getPath();

        $this->assertEquals(
            $plantilla__path->getValue($this->object),
            $retorno,
            'ERROR: el valor devuelto no es el esperado'
        );
    }

    public static function providerSetPath(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'path' => 'path/de/la/plantilla',
                    ],
                    'mock' => [
                        'isReadable' => true,
                    ],
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'path' => 'path/de/la/plantilla/error',
                    ],
                    'mock' => [
                        'isReadable' => false,
                    ],
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('providerSetPath')]
    public function setPath(array $provider): void
    {
        $plantilla__path = $this->propertyEdit($this->object, 'path');

        PlantillaFixture::$is_readable = fn() => $provider['mock']['isReadable'];

        if ($provider['mock']['isReadable']) {
            PlantillaFixture::$is_readable = fn (...$args): bool => true;
        } else {
            $this->expectException(ExceptionPlantillaNoEncontrada::class);
        }

        $this->object->setPath($provider['params']['path']);

        $this->assertEquals(
            $provider['params']['path'],
            $plantilla__path->getValue($this->object),
            'ERROR: el valor de la propiedad no es el esperado'
        );
    }

    #[Test]
    public function setLangRaiz(): void
    {
        $lang_raiz = 'lang.raiz';

        $this->object->setLangRaiz($lang_raiz);

        $this->assertEquals(
            $lang_raiz,
            $this->propertyEdit($this->object, 'lang_raiz')->getValue($this->object),
            'ERROR: el valor de la propiedad lang_raiz no es el esperado'
        );
    }

    #[Test]
    public function getLangRaiz(): void
    {
        $lang_raiz = 'lang.raiz';
        $this->propertyEdit($this->object, 'lang_raiz', $lang_raiz);

        $resultado = $this->object->getLangRaiz();

        $this->assertEquals(
            $lang_raiz,
            $resultado,
            'ERROR: el valor devuelto no es el esperado'
        );
    }

    #[Test]
    public function getCallback(): void
    {
        $callback = function (): void {};
        $plantillaCallabck = $this->propertyEdit($this->object, 'callback', $callback);

        $retorno = $this->object->getCallback();

        $this->assertEquals(
            $plantillaCallabck->getValue($this->object),
            $retorno,
            'ERROR: el valor devuelto no es el esperado'
        );
    }

    public static function providerCargar(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'path' => '',
                    ],
                    'mock' => [
                        'isReadable' => false,
                        'isCallable' => false,
                    ],
                    'expect' => new ExceptionPlantillaCargar(),
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'path' => PATH_UNIT_FIXTURE.'/plantillas/plantillaOk.html.php',
                    ],
                    'mock' => [
                        'isReadable' => true,
                        'isCallable' => false,
                    ],
                    'expect' => new ExceptionPlantillaCargar(),
                ],
            ],

            // #2
            [
                [
                    'params' => [
                        'path' => PATH_UNIT_FIXTURE.'/plantillas/plantillaOk.html.php',
                    ],
                    'mock' => [
                        'isReadable' => true,
                        'isCallable' => true,
                    ],
                    'expect' => true,
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('providerCargar')]
    public function cargar(array $provider): void
    {
        $plantilla__callback = $this->propertyEdit($this->object, 'callback');

        PlantillaFixture::$is_readable = fn (): bool => $provider['mock']['isReadable'];
        PlantillaFixture::$is_callable = fn (): bool => $provider['mock']['isCallable'];

        if ($provider['expect'] instanceof \Exception) {
            $this->expectException($provider['expect']::class);
        } else {
            $provider['expect'] = require $provider['params']['path'];
        }

        $plantilla__cargar = $this->setMethodPublic($this->object, 'cargar');
        $plantilla__cargar->invoke(
            $this->object,
            $provider['params']['path']
        );

        $this->assertEquals(
            $provider['expect'],
            $plantilla__callback->getValue($this->object),
            'ERROR: El valor de la propiedad callback no es el esperado'
        );
    }

    public static function providerRender(): array
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
                        'getData' => null,
                        'callback' => '<div>OK</div>',
                    ],
                    'expect' => new ExceptionDataCargar(),
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'data' => 'prueba_ok',
                        'string' => true,
                    ],
                    'mock' => [
                        'getData' => new PlantillaDataMuestra(),
                        'callback' => '<div>OK</div>',
                    ],
                    'expect' => '<div>OK</div>',
                ],
            ],

            // #2
            [
                [
                    'params' => [
                        'data' => new PlantillaDataMuestra(),
                        'string' => true,
                    ],
                    'mock' => [
                        'getData' => null,
                        'callback' => '<div>OK</div>',
                    ],
                    'expect' => '<div>OK</div>',
                ],
            ],

            // #3
            [
                [
                    'params' => [
                        'data' => new PlantillaDataMuestra(),
                        'string' => false,
                    ],
                    'mock' => [
                        'getData' => null,
                        'callback' => '<div>OK</div>',
                    ],
                    'expect' => true,
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('providerRender')]
    public function render(array $provider): void
    {
        if ($provider['expect'] instanceof \Exception) {
            $this->expectException($provider['expect']::class);
        } elseif (!$provider['params']['string']) {
            $this->expectOutputString($provider['mock']['callback']);
        }

        $this->gestor_mock
            ->expects($this->any())
            ->method('getData')
            ->willReturn($provider['mock']['getData']);

        /** @var MockObject&Plantilla $plantilla_mock */
        $plantilla_mock = $this
            ->getMockBuilder(PlantillaForTesting::class)
            ->setConstructorArgs([$this->gestor_mock, $this->plantilla_config])
            ->onlyMethods(['cargar', 'callback'])
            ->getMock();

        $plantilla_mock
            ->expects($this->any())
            ->method('callback')
            ->willReturnCallback(function () use ($provider): void {
                echo $provider['mock']['callback'];
            });

        $resultado = $plantilla_mock->render(
            $provider['params']['data'],
            $provider['params']['string']
        );

        $this->assertEquals(
            $provider['expect'],
            $resultado,
            'ERROR: El valor devuelto no es el esperado'
        );
    }

    #[Test]
    public function renderPlantilla(): void
    {
        $expect = 'Hola';
        $this->gestor_mock->expects($this->once())
                            ->method('renderPlantilla')
                            ->willReturn($expect);

        $resultado = $this->object->renderPlantilla('id', true);

        $this->assertEquals(
            $expect,
            $resultado,
            'ERROR: El valor devuelto no es el esperado'
        );
    }

    public static function providerExistedata(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'id' => '',
                    ],
                    'mock' => [
                        'getData' => new PlantillaDataMuestra(),
                    ],
                    'expect' => true,
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'id' => '',
                    ],
                    'mock' => [
                        'getData' => null,
                    ],
                    'expect' => false,
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('providerExistedata')]
    public function existeData(array $provider): void
    {
        $this->gestor_mock
            ->expects($this->once())
            ->method('getData')
            ->with($provider['params']['id'])
            ->willReturn($provider['mock']['getData']);

        $resultado = $this->object->existeData($provider['params']['id']);

        $this->assertEquals(
            $provider['expect'],
            $resultado,
            'ERROR: El valor devuelto no es el esperado'
        );
    }

    public static function providerData(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'valores' => 'texto de prueba',
                    ],
                    'expect' => 'texto de prueba',
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'valores' => '<div class="css" id=\'id\'>texto de prueba</div>',
                    ],
                    'expect' => '&lt;div class=&quot;css&quot; id=&#039;id&#039;&gt;texto de prueba&lt;/div&gt;',
                ],
            ],

            // #2
            [
                [
                    'params' => [
                        'valores' => '<section class=\'css\' id="identificador">texto de prueba</section>',
                    ],
                    'expect' => '&lt;section class=&#039;css&#039; id=&quot;identificador&quot;&gt;texto de prueba&lt;/section&gt;',
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('providerData')]
    public function data__devuelve_string(array $provider): void
    {
        $resultado = $this->object->data($provider['params']['valores'], true);

        $this->assertEquals(
            $provider['expect'],
            $resultado,
            'ERROR: El valor devuelto no es el esperado'
        );
    }

    #[Test]
    #[DataProvider('providerData')]
    public function data__echo(array $provider): void
    {
        $this->expectOutputString($provider['expect']);

        $this->object->data($provider['params']['valores'], false);
    }

    public static function providerUrl(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'url' => 'url/de/prueba',
                        'sustitucion' => [' ' => '-'],
                        'seccion_separador' => Plantilla::URL_SECCION_SEPARADOR,
                    ],
                    'expect' => 'url/de/prueba',
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'url' => 'url nueva/de/prueba 2',
                        'sustitucion' => [' ' => '-'],
                        'seccion_separador' => Plantilla::URL_SECCION_SEPARADOR,
                    ],
                    'expect' => 'url-nueva/de/prueba-2',
                ],
            ],

            // #2
            [
                [
                    'params' => [
                        'url' => 'url nueva/de/prueba 2',
                        'sustitucion' => [' ' => '-',
                            'a' => '4'],
                        'seccion_separador' => Plantilla::URL_SECCION_SEPARADOR,
                    ],
                    'expect' => 'url-nuev4/de/prueb4-2',
                ],
            ],

            // #3
            [
                [
                    'params' => [
                        'url' => 'url nueva/de/prueba 2*',
                        'sustitucion' => [],
                        'seccion_separador' => Plantilla::URL_SECCION_SEPARADOR,
                    ],
                    'expect' => 'url+nueva/de/prueba+2%2A',
                ],
            ],

            // #4
            [
                [
                    'params' => [
                        'url' => 'url nueva<script>alert();</script>/de/prueba 2*',
                        'sustitucion' => [],
                        'seccion_separador' => Plantilla::URL_SECCION_SEPARADOR,
                    ],
                    'expect' => 'url+nueva%3Cscript%3Ealert%28%29%3B%3C/script%3E/de/prueba+2%2A',
                ],
            ],

            // #5
            [
                [
                    'params' => [
                        'url' => 'http://dominio.com/pagina',
                        'sustitucion' => [],
                        'seccion_separador' => Plantilla::URL_SECCION_SEPARADOR,
                    ],
                    'expect' => 'http://dominio.com/pagina',
                ],
            ],

            // #6
            [
                [
                    'params' => [
                        'url' => 'https://dominio.com/pagina',
                        'sustitucion' => [],
                        'seccion_separador' => Plantilla::URL_SECCION_SEPARADOR,
                    ],
                    'expect' => 'https://dominio.com/pagina',
                ],
            ],

            // #7
            [
                [
                    'params' => [
                        'url' => '#dominio.com/pagina',
                        'sustitucion' => [],
                        'seccion_separador' => Plantilla::URL_SECCION_SEPARADOR,
                    ],
                    'expect' => '#dominio.com/pagina',
                ],
            ],

            // #8
            [
                [
                    'params' => [
                        'url' => 'https://#dominio.com/pagina',
                        'sustitucion' => [],
                        'seccion_separador' => Plantilla::URL_SECCION_SEPARADOR,
                    ],
                    'expect' => 'https://%23dominio.com/pagina',
                ],
            ],
        ];
    }

    /**
     * @covers \GT\Libs\Sistema\Plantilla\Plantilla::url
     * @covers \GT\Libs\Sistema\Plantilla\Plantilla::urlEspaceHttpYSharp
     */
    #[DataProvider('providerUrl')]
    public function testUrlDevuelveString(array $provider): void
    {
        $resultado = $this->object->url(
            $provider['params']['url'],
            $provider['params']['sustitucion'],
            $provider['params']['seccion_separador'],
            true
        );

        $this->assertEquals(
            $provider['expect'],
            $resultado,
            'ERROR: El valor devuelto no es el esperado'
        );
    }

    #[Test]
    #[DataProvider('providerUrl')]
    public function url__echo(array $provider): void
    {
        $this->expectOutputString($provider['expect']);

        $this->object->url(
            $provider['params']['url'],
            $provider['params']['sustitucion'],
            $provider['params']['seccion_separador'],
            false
        );
    }

    public static function providerJson(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'json' => [1, 2, 3],
                        'opciones' => JSON_HEX_TAG | JSON_HEX_AMP,
                        'depth' => 512,
                    ],
                    'expect' => json_encode([1, 2, 3], JSON_HEX_TAG | JSON_HEX_AMP),
                ],
            ],

            // #0
            [
                [
                    'params' => [
                        'json' => "\xB1\x31",
                        'opciones' => JSON_HEX_TAG | JSON_HEX_AMP,
                        'depth' => 512,
                    ],
                    'expect' => '',
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('providerjson')]
    public function json__Devuelve_un_string(array $provider): void
    {
        $resultado = $this->object->json(
            $provider['params']['json'],
            $provider['params']['opciones'],
            $provider['params']['depth'],
            true
        );

        $this->assertEquals(
            $provider['expect'],
            $resultado,
            'ERROR: El valor devuelto no es el esperado'
        );
    }

    #[Test]
    #[DataProvider('providerjson')]
    public function json__echo(array $provider): void
    {
        $this->expectOutputString($provider['expect']);

        $this->object->json(
            $provider['params']['json'],
            $provider['params']['opciones'],
            $provider['params']['depth'],
            false
        );
    }

    public static function providerAtr(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'atr' => 'id',
                        'valor' => '<script>identificador</script>',
                        'tipo' => TIPODATO::DATA,
                    ],
                    'expect' => 'id="&lt;script&gt;identificador&lt;/script&gt;"',
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'atr' => 'src',
                        'valor' => 'path/to/the/im age',
                        'tipo' => TIPODATO::URL,
                    ],
                    'expect' => 'src="path/to/the/im-age"',
                ],
            ],

            // #2
            [
                [
                    'params' => [
                        'atr' => 'value',
                        'valor' => '0',
                        'tipo' => TIPODATO::URL,
                    ],
                    'expect' => 'value="0"',
                ],
            ],

            // #3
            [
                [
                    'params' => [
                        'atr' => 'value',
                        'valor' => 0,
                        'tipo' => 3333,
                    ],
                    'expect' => '',
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('providerAtr')]
    public function atr__Devuelve_string(array $provider): void
    {
        $resultado = $this->object->atr(
            $provider['params']['atr'],
            $provider['params']['valor'],
            $provider['params']['tipo'],
            true
        );

        $this->assertEquals(
            $provider['expect'],
            $resultado,
            'ERROR: El valor devuelto no es el esperado'
        );
    }

    #[Test]
    #[DataProvider('providerAtr')]
    public function atr__Echo(array $provider): void
    {
        $this->expectOutputString($provider['expect']);

        $this->object->atr(
            $provider['params']['atr'],
            $provider['params']['valor'],
            $provider['params']['tipo'],
            false
        );
    }

    public static function providerEach(): array
    {
        return [
            // #0
            [
                [
                    'params' => [
                        'array' => ['hola', 'adios', 'que<tal'],
                        'modo' => EACH::NORMAL,
                        'separador' => ' ',
                    ],
                    'expect' => ' hola adios que&lt;tal',
                ],
            ],

            // #1
            [
                [
                    'params' => [
                        'array' => [
                            'atributo_1' => '1',
                            'atributo_2>' => '2',
                            'atributo_3' => 'valor>',
                        ],
                        'modo' => EACH::ATRIBUTO,
                        'separador' => '->',
                    ],
                    'expect' => '->atributo_1="1"->atributo_2&gt;="2"->atributo_3="valor&gt;"',
                ],
            ],

            // #2
            [
                [
                    'params' => [
                        'array' => [
                            'atributo_1' => '1',
                            'atributo_2>' => '2',
                            'atributo_3' => 'valor>',
                        ],
                        'modo' => EACH::DATA,
                        'separador' => ' ',
                    ],
                    'expect' => ' data-atributo_1="1" data-atributo_2&gt;="2" data-atributo_3="valor&gt;"',
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('providerEach')]
    public function each(array $provider): void
    {
        $resultado = $this->object->each(
            $provider['params']['array'],
            $provider['params']['modo'],
            $provider['params']['separador'],
            true
        );

        $this->assertEquals(
            $provider['expect'],
            $resultado,
            'ERROR: El valor devuelto no es el esperado'
        );
    }

    #[Test]
    #[DataProvider('providerEach')]
    public function each__Echo(array $provider): void
    {
        $this->expectOutputString($provider['expect']);

        $this->object->each(
            $provider['params']['array'],
            $provider['params']['modo'],
            $provider['params']['separador'],
            false
        );
    }

    #[Test]
    public function lang__Devuelve_un_string(): void
    {
        $path = 'path.a.la.ruta.del.idioma';
        $sustitucion = ['sustituir'];
        $marca = ':';
        $expect = 'idioma';
        $lang_raiz = $this->plantilla_config->lang_raiz.'.';

        $this->gestor_mock
            ->expects($this->once())
            ->method('getLang')
            ->willReturn($this->lang);

        $this->lang
            ->expects($this->once())
            ->method('get')
            ->with($lang_raiz.$path, $sustitucion, '.', $marca, '\\')
            ->willReturn($expect);

        $resultado = $this->object->lang($path, $sustitucion, $marca, true);

        $this->assertEquals(
            $expect,
            $resultado,
            'ERROR: el valor devuelto no es el esperado'
        );
    }

    #[Test]
    public function lang__Echo(): void
    {
        $path = 'path.a.la.ruta.del.idioma';
        $sustitucion = ['sustituir'];
        $marca = ':';
        $expect = 'idioma';
        $lang_raiz = $this->plantilla_config->lang_raiz.'.';

        $this->gestor_mock
            ->expects($this->once())
            ->method('getLang')
            ->willReturn($this->lang);

        $this->lang
            ->expects($this->once())
            ->method('get')
            ->with($lang_raiz.$path, $sustitucion, '.', $marca, '\\')
            ->willReturn($expect);

        $this->expectOutputString($expect);
        $this->object->lang($path, $sustitucion, $marca, false);
    }

    #[Test]
    public function langHtml__No_escapa_los_valores_sustituidos_devuelve_un_string(): void
    {
        $path = 'path.a.la.ruta.del.idioma';
        $sustitucion = ['SUSTITUIR' => '<a href="#link">link</a>'];
        $marca = ':';
        $get_expect = 'idioma :SUSTITUIR';
        $expect = 'idioma <a href="#link">link</a>';
        $lang_raiz = $this->plantilla_config->lang_raiz.'.';

        $this->gestor_mock
            ->expects($this->once())
            ->method('getLang')
            ->willReturn($this->lang);

        $this->lang
            ->expects($this->once())
            ->method('get')
            ->with($lang_raiz.$path, [], '.', $marca, '\\')
            ->willReturn($get_expect);

        $resultado = $this->object->langHtml($path, $sustitucion, $marca, true);

        $this->assertEquals(
            $expect,
            $resultado,
            'ERROR: el valor devuelto no es el esperado'
        );
    }

    #[Test]
    public function langHtml__Escapata_el_texto_pero_no_escapa_los_valores_sustituidos_devuelve_un_string(): void
    {
        $path = 'path.a.la.ruta.del.idioma';
        $sustitucion = ['SUSTITUIR' => '<a href="#link">link</a>'];
        $marca = ':';
        $get_expect = 'idioma :SUSTITUIR <a href="#link">link</a>';
        $expect = 'idioma <a href="#link">link</a> &lt;a href=&quot;#link&quot;&gt;link&lt;/a&gt;';
        $lang_raiz = $this->plantilla_config->lang_raiz.'.';

        $this->gestor_mock
            ->expects($this->once())
            ->method('getLang')
            ->willReturn($this->lang);

        $this->lang
            ->expects($this->once())
            ->method('get')
            ->with($lang_raiz.$path, [], '.', $marca, '\\')
            ->willReturn($get_expect);

        $resultado = $this->object->langHtml($path, $sustitucion, $marca, true);

        $this->assertEquals(
            $expect,
            $resultado,
            'ERROR: el valor devuelto no es el esperado'
        );
    }

    #[Test]
    public function langHtml__Echo(): void
    {
        $path = 'path.a.la.ruta.del.idioma';
        $sustitucion = ['SUSTITUIR' => '<a href="#link">link</a>'];
        $marca = ':';
        $get_expect = 'idioma :SUSTITUIR';
        $expect = 'idioma <a href="#link">link</a>';
        $lang_raiz = $this->plantilla_config->lang_raiz.'.';

        $this->gestor_mock
            ->expects($this->once())
            ->method('getLang')
            ->willReturn($this->lang);

        $this->lang
            ->expects($this->once())
            ->method('get')
            ->with($lang_raiz.$path, [], '.', $marca, '\\')
            ->willReturn($get_expect);

        $this->expectOutputString($expect);
        $this->object->langHtml($path, $sustitucion, $marca, false);
    }
}
