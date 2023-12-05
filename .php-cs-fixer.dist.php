<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

return (new Config())
    ->setRules([
        '@Symfony' => true,
        'yoda_style' => [],
        'ordered_imports' => true,
    ])
    ->setFinder($finder);
