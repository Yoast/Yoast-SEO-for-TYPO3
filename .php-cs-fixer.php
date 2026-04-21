<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use TYPO3\CodingStandards\CsFixerConfig;

$header = <<<HEADER
This file is part of the "yoast_seo" extension for TYPO3 CMS.

For the full copyright and license information, please read the
LICENSE.txt file that was distributed with this source code.
HEADER;

$config = CsFixerConfig::create();
$config->setParallelConfig(ParallelConfigFactory::detect());
$config->addRules(
    [
        'header_comment' => [
            'header' => $header,
            'location' => 'after_open',
            'separate' => 'both',
            'comment_type' => 'PHPDoc',
        ],
    ]
);

$config->getFinder()->in('Classes')->in('Configuration')->in('Tests');
return $config;
