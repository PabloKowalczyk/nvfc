<?php

declare(strict_types=1);

$dir = __DIR__;

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@Symfony' => true,
            '@PSR2' => true,
            'array_syntax' => ['syntax' => 'short'],
            'protected_to_private' => false,
            'combine_consecutive_unsets' => true,
            'combine_consecutive_issets' => true,
            'compact_nullable_typehint' => true,
            'declare_strict_types' => true,
            'dir_constant' => true,
            'ereg_to_preg' => true,
            'explicit_indirect_variable' => true,
            'explicit_string_variable' => true,
            'function_to_constant' => true,
            'is_null' => true,
            'modernize_types_casting' => true,
            'linebreak_after_opening_tag' => true,
            'list_syntax' => ['syntax' => 'short'],
            'mb_str_functions' => true,
            'native_function_invocation' => [
                'scope' => 'all',
                'include' => ['@all'],
                'strict' => true,
            ],
            'no_alias_functions' => true,
            'no_homoglyph_names' => true,
            'no_php4_constructor' => true,
            'no_useless_else' => true,
            'no_useless_return' => true,
            'ordered_class_elements' => true,
            'ordered_imports' => true,
            'php_unit_construct' => true,
            'php_unit_dedicate_assert' => true,
            'php_unit_expectation' => true,
            'php_unit_mock' => true,
            'php_unit_namespaced' => true,
            'random_api_migration' => true,
            'strict_comparison' => true,
            'strict_param' => true,
            'ternary_to_null_coalescing' => true,
            'void_return' => true,
            'concat_space' => [
                'spacing' => 'one',
            ],
            'php_unit_method_casing' => ['case' => 'snake_case'],
        ]
    )
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in("{$dir}/src")
            ->in("{$dir}/tests")
            ->append([__FILE__, "{$dir}/bin/nvfc"])
    )
;
