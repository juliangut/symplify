<?php

declare(strict_types=1);

use Nette\Utils\Strings;

require __DIR__ . '/vendor/autoload.php';

$timestamp = (new DateTime('now'))->format('Ymd');

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'EasyCI' . $timestamp,
    'expose-classes' => [
        // part of public interface of configs.php
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
    ],
    'exclude-namespaces' => ['#^Symplify\EasyCI\*#'],
    'excluded-files' => [
        // do not prefix "trigger_deprecation" from symfony - https://github.com/symfony/symfony/commit/0032b2a2893d3be592d4312b7b098fb9d71aca03
        // these paths are relative to this file location, so it should be in the root directory
        'vendor/symfony/deprecation-contracts/function.php',
        'vendor/symfony/polyfill-intl-normalizer/bootstrap.php',
        'vendor/symfony/polyfill-intl-normalizer/bootstrap80.php',
        'vendor/symfony/polyfill-mbstring/bootstrap.php',
        'vendor/symfony/polyfill-mbstring/bootstrap80.php',
        'vendor/symfony/polyfill-php80/bootstrap.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/ValueError.php',
        'vendor/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
    ],
    'patchers' => [
        // scope symfony configs
        function (string $filePath, string $prefix, string $content): string {
            if (! Strings::match($filePath, '#(packages|config|services)\.php$#')) {
                return $content;
            }

            // fix symfony config load scoping, except EasyCI
            $content = Strings::replace(
                $content,
                '#load\(\'Symplify\\\\\\\\(?<package_name>[A-Za-z]+)#',
                function (array $match) use ($prefix) {
                    if (in_array($match['package_name'], ['EasyCI'], true)) {
                        // skip
                        return $match[0];
                    }

                    return 'load(\'' . $prefix . '\Symplify\\' . $match['package_name'];
                }
            );

            return $content;
        },

        // unprefix string class names to ignore, to keep original class names
        function (string $filePath, string $prefix, string $content): string {
            if (! str_ends_with($filePath, 'packages/ActiveClass/Filtering/PossiblyUnusedClassesFilter.php')) {
                return $content;
            }

            return Strings::replace($content, '#DEFAULT_TYPES_TO_SKIP = (?<content>.*?)\;#ms', function (array $match) use (
                $prefix
            ) {
                // remove prefix from there
                return 'DEFAULT_TYPES_TO_SKIP = ' .
                    Strings::replace($match['content'], '#' . $prefix . '\\\\#', '') . ';';
            });
        },
    ],
];
