<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use TYPO3\CodingStandards\CsFixerConfig;

$config = CsFixerConfig::create();
/// TODO: This construction can be removed when 11 support is dropped
if (method_exists($config, 'setParallelConfig')) {
    $config->setParallelConfig(ParallelConfigFactory::detect());
} else {
    // Old TYPO3 config standards so manually add some rules
    $config->addRules([
        'single_line_empty_body' => true
    ]);
}

// TODO: This construction can be removed when 11 support is dropped
$config->addRules([
    'php_unit_test_case_static_method_calls' => false,
]);

$config->getFinder()->in('Classes')->in('Configuration')->in('Tests');
return $config;
