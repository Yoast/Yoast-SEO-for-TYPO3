<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;

class TcaService
{
    protected const LL_PREFIX_TCA = 'LLL:EXT:yoast_seo/Resources/Private/Language/TCA.xlf:';
    protected const LL_PREFIX_BACKEND = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

    protected string $table = '';
    protected string $types = '';
    protected string $descriptionField = '';

    public function addYoastFields(string $table, string $types, string $descriptionField = 'description'): void
    {
        $this->table = $table;
        $this->types = $types;
        $this->descriptionField = $descriptionField;

        $this->addDefaultFields();
        $this->addSocialPreviewFields();

        $this->addPalettes();
        $this->addToTypes();
    }

    protected function addDefaultFields(): void
    {
        $columns = [
            'tx_yoastseo_snippetpreview' => [
                'label' => self::LL_PREFIX_BACKEND . 'snippetPreview',
                'exclude' => true,
                'displayCond' => [
                    'OR' => [
                        'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
                        'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                    ],
                ],
                'config' => [
                    'type' => 'none',
                    'renderType' => 'snippetPreview',
                    'settings' => [
                        'titleField' => 'seo_title',
                        'descriptionField' => $this->descriptionField,
                    ],
                ],
            ],
            'tx_yoastseo_readability_analysis' => [
                'label' => self::LL_PREFIX_BACKEND . 'analysis',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                'config' => [
                    'type' => 'none',
                    'renderType' => 'readabilityAnalysis',
                ],
            ],
            'tx_yoastseo_focuskeyword' => [
                'label' => self::LL_PREFIX_BACKEND . 'seoFocusKeyword',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                'config' => [
                    'type' => 'input',
                ],
            ],
            'tx_yoastseo_focuskeyword_analysis' => [
                'label' => self::LL_PREFIX_BACKEND . 'analysis',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                'config' => [
                    'type' => 'none',
                    'renderType' => 'focusKeywordAnalysis',
                    'settings' => [
                        'focusKeywordField' => 'tx_yoastseo_focuskeyword',
                    ],
                ],
            ],
            'tx_yoastseo_cornerstone' => [
                'label' => '',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                'config' => [
                    'type' => 'input',
                    'default' => 0,
                    'renderType' => 'cornerstone',
                ],
            ],
            'tx_yoastseo_score_readability' => [
                'label' => '',
                'exclude' => false,
                'config' => [
                    'type' => 'passthrough',
                ],
            ],
            'tx_yoastseo_score_seo' => [
                'label' => '',
                'exclude' => false,
                'config' => [
                    'type' => 'passthrough',
                ],
            ],
            'tx_yoastseo_focuskeyword_related' => [
                'label' => self::LL_PREFIX_TCA . 'pages.fields.tx_yoastseo_focuskeyword_related',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => TableNames::RELATED_FOCUSKEYWORD,
                    'foreign_field' => 'uid_foreign',
                    'foreign_table_field' => 'tablenames',
                    'maxitems' => 5,
                ],
            ],
            'tx_yoastseo_insights' => [
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                'config' => [
                    'type' => 'none',
                    'renderType' => 'insights',
                ],
            ],
            'tx_yoastseo_focuskeyword_synonyms' => [
                'label' => self::LL_PREFIX_TCA . 'synonyms',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                'config' => [
                    'type' => 'input',
                ],
            ],
            'tx_yoastseo_robots_noimageindex' => [
                'label' => self::LL_PREFIX_TCA . 'pages.fields.robots.noimageindex',
                'exclude' => true,
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'items' => $this->getInvertedCheckbox(),
                ],
            ],
            'tx_yoastseo_robots_noarchive' => [
                'label' => self::LL_PREFIX_TCA . 'pages.fields.robots.noarchive',
                'exclude' => true,
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'items' => $this->getInvertedCheckbox(),
                ],
            ],
            'tx_yoastseo_robots_nosnippet' => [
                'label' => self::LL_PREFIX_TCA . 'pages.fields.robots.nosnippet',
                'exclude' => true,
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'items' => $this->getInvertedCheckbox(),
                ],
            ],
        ];
        if ($this->table === TableNames::PAGES) {
            $columns['tx_yoastseo_disable_analysis'] = [
                'label' => self::LL_PREFIX_BACKEND . 'disableAnalysis',
                'exclude' => true,
                'onChange' => 'reload',
                'config' => [
                    'type' => 'check',
                ],
            ];
            $columns['tx_yoastseo_hide_snippet_preview'] = [
                'label' => self::LL_PREFIX_BACKEND . 'hideSnippetPreview',
                'exclude' => true,
                'config' => [
                    'type' => 'check',
                ],
            ];
        }
        ExtensionManagementUtility::addTCAcolumns(
            $this->table,
            $columns
        );
    }

    protected function addSocialPreviewFields(): void
    {
        ExtensionManagementUtility::addTCAcolumns(
            $this->table,
            [
                'tx_yoastseo_facebook_preview' => [
                    'label' => self::LL_PREFIX_TCA . 'pages.fields.tx_yoastseo_facebook_preview',
                    'exclude' => true,
                    'displayCond' => 'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                    'config' => [
                        'type' => 'none',
                        'renderType' => 'facebookPreview',
                    ],
                ],
                'tx_yoastseo_twitter_preview' => [
                    'label' => self::LL_PREFIX_TCA . 'pages.fields.tx_yoastseo_twitter_preview',
                    'exclude' => true,
                    'displayCond' => 'FIELD:tx_yoastseo_disable_analysis:REQ:false',
                    'config' => [
                        'type' => 'none',
                        'renderType' => 'twitterPreview',
                    ],
                ],
            ]
        );

        ExtensionManagementUtility::addFieldsToPalette(
            TableNames::PAGES,
            'opengraph',
            'tx_yoastseo_facebook_preview,--linebreak--',
            'before:og_title'
        );

        ExtensionManagementUtility::addFieldsToPalette(
            TableNames::PAGES,
            'twittercards',
            'tx_yoastseo_twitter_preview,--linebreak--',
            'before:twitter_title'
        );
    }

    protected function addPalettes(): void
    {
        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'seo',
            '--linebreak--, tx_yoastseo_snippetpreview, --linebreak--',
            'before: seo_title'
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'seo',
            '--linebreak--, ' . $this->descriptionField . ', --linebreak--, tx_yoastseo_cornerstone',
            'after: seo_title'
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'yoast-readability',
            '--linebreak--, tx_yoastseo_readability_analysis'
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'yoast-focuskeyword',
            '--linebreak--, tx_yoastseo_focuskeyword,
            --linebreak--, tx_yoastseo_focuskeyword_synonyms,
            --linebreak--, tx_yoastseo_focuskeyword_analysis'
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'yoast-relatedkeywords',
            '--linebreak--, tx_yoastseo_focuskeyword_related '
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'yoast-insights',
            '--linebreak--, tx_yoastseo_insights'
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'yoast-advanced-robots',
            '--linebreak--, tx_yoastseo_robots_noimageindex,tx_yoastseo_robots_noarchive,tx_yoastseo_robots_nosnippet'
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'yoast-advanced',
            '--linebreak--, tx_yoastseo_disable_analysis, tx_yoastseo_hide_snippet_preview'
        );
        $GLOBALS['TCA'][$this->table]['palettes']['yoast-advanced']['description']
            = self::LL_PREFIX_TCA . 'pages.palettes.advanced.description';
    }

    protected function addToTypes(): void
    {
        ExtensionManagementUtility::addToAllTCAtypes(
            $this->table,
            '--palette--;Label;yoast-snippetpreview,',
            $this->types,
            'before:seo_title'
        );

        ExtensionManagementUtility::addToAllTCAtypes(
            $this->table,
            '--palette--;' . self::LL_PREFIX_TCA . 'pages.palettes.readability;yoast-readability,
            --palette--;' . self::LL_PREFIX_TCA . 'pages.palettes.focusKeyphrase;yoast-focuskeyword,
            --palette--;' . self::LL_PREFIX_TCA . 'pages.palettes.focusRelatedKeyphrases;yoast-relatedkeywords,
            --palette--;' . self::LL_PREFIX_TCA . 'pages.palettes.insights;yoast-insights,',
            $this->types,
            'after: tx_yoastseo_cornerstone'
        );

        ExtensionManagementUtility::addToAllTCAtypes(
            $this->table,
            '--palette--;' . self::LL_PREFIX_TCA . 'pages.palettes.robots;yoast-advanced-robots,',
            $this->types,
            'after:no_follow'
        );

        ExtensionManagementUtility::addToAllTCAtypes(
            $this->table,
            '--palette--;' . self::LL_PREFIX_TCA . 'pages.palettes.advanced;yoast-advanced,',
            $this->types,
            'after: sitemap_priority'
        );
    }

    /**
     * @return array<array<int|string, string|bool>>
     */
    protected function getInvertedCheckbox(): array
    {
        return [
            [
                1 => '',
                'invertStateDisplay' => true,
                'label' => '',
            ],
        ];
    }
}
