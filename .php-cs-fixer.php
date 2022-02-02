<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('bootstrap/cache')
    ->notPath('storage')
    ->notPath('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        'ternary_operator_spaces' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'concat_space' => ['spacing' => 'one'],
        'single_trait_insert_per_statement' => true,
        'explicit_string_variable' => true,
        'single_line_throw' => false,
        'not_operator_with_successor_space' => false,
        'trailing_comma_in_multiline' => true,
        'phpdoc_scalar' => true,
        'unary_operator_spaces' => true,
        'binary_operator_spaces' => true,
        'logical_operators' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'method_chaining_indentation' => true,
        'array_indentation' => true,
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
            ],
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            // 'keep_multiple_spaces_after_comma' => true,
        ],
        'no_leading_import_slash' => true,
        'no_alternative_syntax' => true,
        'native_function_casing' => true,
        'native_function_type_declaration_casing' => true,
        'single_line_after_imports' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'single_blank_line_before_namespace' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'switch',
                'throw',
                'use',
                'use_trait',
            ],
        ],
        'native_constant_invocation' => [
            'include' => ['@compiler_optimized'],
            'strict' => true,
        ],
        'native_function_invocation' => [
            'scope' => 'namespaced',
            'strict' => true,
        ],
    ])
    ->setFinder($finder);
