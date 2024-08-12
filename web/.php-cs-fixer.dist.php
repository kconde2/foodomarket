<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->ignoreVCSIgnored(true)
    ->exclude('var')
    ->exclude('vendor')
;
// https://cs.symfony.com/doc/ruleSets/Symfony.html
return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'trailing_comma_in_multiline' => true,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'declare_strict_types' => true,
        'void_return' => true,
        'php_unit_method_casing' => [
            'case' => 'snake_case',
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
