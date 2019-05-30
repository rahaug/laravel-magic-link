<?php

use PhpCsFixer\Config;
use Symfony\Component\Finder\Finder;

$excludeDirs = [
    'vendor/',
];

$excludeFiles = [
    '.php_cs.dist',
    '.phpstorm.meta.php',
    '_ide_helper.php',
    '_ide_helper_models.php',
    'artisan',
    'server.php',
];

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->exclude($excludeDirs)
    ->notPath($excludeFiles)
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return Config::create()
    ->setRules([
        '@PSR2' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => true,
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_return' => true,
        'braces' => true,
        'class_definition' => true,
        'method_chaining_indentation' => true,
        'no_extra_consecutive_blank_lines' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_short_echo_tag' => true,
        'no_spaces_around_offset' => true,
        'no_unused_imports' => true,
        'no_whitespace_before_comma_in_array' => true,
        'not_operator_with_successor_space' => true,
        'ordered_imports' => ['sortAlgorithm' => 'length'],
        'trailing_comma_in_multiline_array' => true,
        'trim_array_spaces' => true,
        'single_quote' => true,
    ])
    ->setFinder($finder);