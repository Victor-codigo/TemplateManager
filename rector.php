 <?php

use Rector\CodeQuality\Rector\Include_\AbsolutizeRequireAndIncludePathRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    // here we can define, what prepared sets of rules will be applied
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    )
    ->withPaths([
        'src',
        'tests'
    ])
    ->withTypeCoverageLevel(36)
    ->withPhpSets(php84: true)
    ->withSets([
        PHPUnitSetList::PHPUNIT_110,
    ])
    ->withPhpVersion(PhpVersion::PHP_84)
    ->withImportNames(
        removeUnusedImports: true,
        importShortClasses: false
    )
    ->withSkipPath('vendor')
    ->withSkip([
        AbsolutizeRequireAndIncludePathRector::class
    ]);
