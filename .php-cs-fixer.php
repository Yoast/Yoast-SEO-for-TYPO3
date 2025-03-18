<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use TYPO3\CodingStandards\CsFixerConfig;

$config = CsFixerConfig::create();
$config->setParallelConfig(ParallelConfigFactory::detect());

$config->getFinder()->in('Classes')->in('Configuration')->in('Tests');
return $config;
