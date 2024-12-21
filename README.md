# TemplateManager
Module that allows to manage templates in php

# Prerequisites
PHP 7.0

# Stack
- [PHP 7.0](https://www.php.net/)
- [PHPUnit 11](https://phpunit.de/index.html)
- [PHPStan](https://phpstan.org)
- [Rector](https://getrector.com)
- [Composer](https://getcomposer.org/)

# Usage

1. Create language class to set supported languages.
```php
use Lib\Comun\IDIOMA;
use Lib\Comun\Lang;
use Lib\GestorPlantillas;
use Lib\PlantillaConfig;

$lang = new Lang();
$lang->setLangPath('path to language files');   // Path to lang files where language files are saved
$lang->setLangs([IDIOMA::ING, IDIOMA::ESP]); // Available languages
$lang->setLangDefault(IDIOMA::ING); // Set default language
```

2. Create the template.
```php
$plantilla_config = new PlantillaConfig();
$plantilla_config->path ='path to template file';
$plantilla_config->lang_raiz ='path to template language folder';
```

3. Add template and language to Template manager
```php
$gestor_platillas = new GestorPlantillas($lang);
$plantilla = $gestor_platillas->cargarPlantilla($plantilla_config);
```

4. Render template
```php
$plantilla_data = []; // Data to pass to the template
$plantilla_renderizada = $plantilla->render($plantilla_data); // Render the template
```
