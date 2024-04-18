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
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'phpdoc_to_comment' => false,
        'declare_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => true,
        ],
        'phpdoc_separation' => false,
        'phpdoc_align' => false,
        'multiline_whitespace_before_semicolons' => true,
        'trailing_comma_in_multiline' => [
            'elements' => [
                'arrays',
                'parameters',
                'match',
            ],
        ],
        'no_unused_imports' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'case',
                'property',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public',
                'method_protected',
                'method_private',
            ],
        ],
        'ordered_imports' => true,
        'yoda_style' => false,
        'nullable_type_declaration_for_default_null_value' => false,
    ])
    ->setFinder($finder);
