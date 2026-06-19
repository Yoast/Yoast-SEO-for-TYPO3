<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';
ExtensionManagementUtility::addTCAcolumns(
    'tx_sitepackage_domain_model_manual',
    [
        'tx_yoastseo_snippetpreview' => [
            'label' => $llPrefix . 'snippetPreview',
            'exclude' => true,
            'displayCond' => 'REC:NEW:false',
            'config' => [
                'type' => 'none',
                'renderType' => 'snippetPreview',
                'settings' => [
                    'titleField' => 'seo_title',
                    'descriptionField' => 'seo_description',
                ],
            ],
        ],
        'tx_yoastseo_readability_analysis' => [
            'label' => $llPrefix . 'analysis',
            'exclude' => true,
            'config' => [
                'type' => 'none',
                'renderType' => 'readabilityAnalysis',
            ],
        ],
        'tx_yoastseo_focuskeyword' => [
            'label' => $llPrefix . 'seoFocusKeyword',
            'exclude' => true,
            'config' => [
                'type' => 'input',
            ],
        ],
        'tx_yoastseo_focuskeyword_analysis' => [
            'label' => $llPrefix . 'analysis',
            'exclude' => true,
            'config' => [
                'type' => 'none',
                'renderType' => 'focusKeywordAnalysis',
                'settings' => [
                    'focusKeywordField' => 'tx_yoastseo_focuskeyword',
                ],
            ],
        ],
    ]
);

ExtensionManagementUtility::addFieldsToPalette(
    'tx_sitepackage_domain_model_manual',
    'yoast-metadata',
    '
--linebreak--, tx_yoastseo_snippetpreview,
--linebreak--, seo_title,
--linebreak--, seo_description,
'
);

ExtensionManagementUtility::addFieldsToPalette(
    'tx_sitepackage_domain_model_manual',
    'yoast-focuskeyword',
    '
--linebreak--, tx_yoastseo_focuskeyword,
--linebreak--, tx_yoastseo_focuskeyword_analysis
'
);

ExtensionManagementUtility::addFieldsToPalette(
    'tx_sitepackage_domain_model_manual',
    'yoast-readability',
    '
--linebreak--, tx_yoastseo_readability_analysis
'
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tx_sitepackage_domain_model_manual',
    '
--div--;LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:pages.tabs.seo,
--palette--;;yoast-metadata,
--palette--;;yoast-readability,
--palette--;;yoast-focuskeyword,
',
    '',
    'after:text'
);
