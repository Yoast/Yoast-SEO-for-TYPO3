<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class TcaService
{
    protected const LL_PREFIX = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';

    protected string $table = '';
    protected string $types = '';
    protected string $descriptionField = '';

    public function addYoastFields(string $table, string $types, string $descriptionField = 'description'): void
    {
        $this->table = $table;
        $this->types = $types;
        $this->descriptionField = $descriptionField;

        $this->addDefaultFields();

        if (!YoastUtility::isPremiumInstalled()) {
            $this->addPremiumFields();
        }

        $this->addPalettes();
        $this->addToTypes();
    }

    protected function addDefaultFields(): void
    {
        $columns = [
            'tx_yoastseo_snippetpreview' => [
                'label' => self::LL_PREFIX . 'snippetPreview',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
                'config' => [
                    'type' => 'none',
                    'renderType' => 'snippetPreview',
                    'settings' => [
                        'titleField' => 'seo_title',
                        'descriptionField' => $this->descriptionField
                    ]
                ]
            ],
            'tx_yoastseo_readability_analysis' => [
                'label' => self::LL_PREFIX . 'analysis',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
                'config' => [
                    'type' => 'none',
                    'renderType' => 'readabilityAnalysis'
                ]
            ],
            'tx_yoastseo_focuskeyword' => [
                'label' => self::LL_PREFIX . 'seoFocusKeyword',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
                'config' => [
                    'type' => 'input',
                ]
            ],
            'tx_yoastseo_focuskeyword_analysis' => [
                'label' => self::LL_PREFIX . 'analysis',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
                'config' => [
                    'type' => 'none',
                    'renderType' => 'focusKeywordAnalysis',
                    'settings' => [
                        'focusKeywordField' => 'tx_yoastseo_focuskeyword',
                    ]
                ]
            ],
            'tx_yoastseo_cornerstone' => [
                'label' => '',
                'exclude' => true,
                'config' => [
                    'type' => 'input',
                    'default' => 0,
                    'renderType' => 'cornerstone'
                ]
            ],
            'tx_yoastseo_score_readability' => [
                'label' => '',
                'exclude' => false,
                'config' => [
                    'type' => 'passthrough'
                ]
            ],
            'tx_yoastseo_score_seo' => [
                'label' => '',
                'exclude' => false,
                'config' => [
                    'type' => 'passthrough'
                ]
            ],
        ];
        if ($this->table === 'pages') {
            $columns['tx_yoastseo_hide_snippet_preview'] = [
                'label' => self::LL_PREFIX . 'hideSnippetPreview',
                'exclude' => true,
                'config' => [
                    'type' => 'check'
                ]
            ];
        }
        ExtensionManagementUtility::addTCAcolumns(
            $this->table,
            $columns
        );
    }

    protected function addPremiumFields(): void
    {
        ExtensionManagementUtility::addTCAcolumns(
            $this->table,
            [
                'tx_yoastseo_focuskeyword_synonyms' => [
                    'label' => self::LL_PREFIX . 'synonyms',
                    'exclude' => false,
                    'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
                    'config' => [
                        'type' => 'none',
                        'renderType' => 'synonyms',
                    ]
                ],
                'tx_yoastseo_focuskeyword_premium' => [
                    'exclude' => true,
                    'config' => [
                        'type' => 'none',
                        'renderType' => 'relatedKeyphrases'
                    ]
                ],
                'tx_yoastseo_insights' => [
                    'exclude' => true,
                    'config' => [
                        'type' => 'none',
                        'renderType' => 'insights'
                    ]
                ],
                'tx_yoastseo_robots_noimageindex' => [
                    'exclude' => true,
                    'config' => [
                        'type' => 'none',
                        'renderType' => 'advancedRobots'
                    ]
                ]
            ]
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
            '--linebreak--, tx_yoastseo_focuskeyword_premium '
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'yoast-insights',
            '--linebreak--, tx_yoastseo_insights'
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'yoast-advanced-robots',
            '--linebreak--, tx_yoastseo_robots_noimageindex'
        );

        ExtensionManagementUtility::addFieldsToPalette(
            $this->table,
            'yoast-advanced',
            '--linebreak--, tx_yoastseo_hide_snippet_preview'
        );
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
            '--palette--;' . self::LL_PREFIX . 'pages.palettes.readability;yoast-readability,
            --palette--;' . self::LL_PREFIX . 'pages.palettes.focusKeyphrase;yoast-focuskeyword,
            --palette--;' . self::LL_PREFIX . 'pages.palettes.focusRelatedKeyphrases;yoast-relatedkeywords,
            --palette--;' . self::LL_PREFIX . 'pages.palettes.insights;yoast-insights,',
            $this->types,
            'after: tx_yoastseo_cornerstone'
        );

        ExtensionManagementUtility::addToAllTCAtypes(
            $this->table,
            '--palette--;' . self::LL_PREFIX . 'pages.palettes.robots;yoast-advanced-robots,',
            $this->types,
            'after:no_follow'
        );

        ExtensionManagementUtility::addToAllTCAtypes(
            $this->table,
            '--palette--;' . self::LL_PREFIX . 'pages.palettes.advances;yoast-advanced,',
            $this->types,
            'after: twitter_image'
        );
    }
}
