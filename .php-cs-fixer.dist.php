<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude([
        'vendor',
    ]);

return (new Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'ordered_imports' => true,
        'yoda_style' => false,
        'global_namespace_import' => [
            'import_classes' => true,
        ],
    ])
    ->setFinder($finder);
