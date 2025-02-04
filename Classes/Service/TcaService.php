<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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

        $this->addPalettes();
        $this->addToTypes();
    }

    protected function addDefaultFields(): void
    {
        $columns = [
            'tx_yoastseo_snippetpreview' => [
                'label' => self::LL_PREFIX_BACKEND . 'snippetPreview',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
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
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
                'config' => [
                    'type' => 'none',
                    'renderType' => 'readabilityAnalysis',
                ],
            ],
            'tx_yoastseo_focuskeyword' => [
                'label' => self::LL_PREFIX_BACKEND . 'seoFocusKeyword',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
                'config' => [
                    'type' => 'input',
                ],
            ],
            'tx_yoastseo_focuskeyword_analysis' => [
                'label' => self::LL_PREFIX_BACKEND . 'analysis',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
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
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tx_yoastseo_related_focuskeyword',
                    'foreign_field' => 'uid_foreign',
                    'foreign_table_field' => 'tablenames',
                    'maxitems' => 5,
                ],
            ],
            'tx_yoastseo_insights' => [
                'exclude' => true,
                'config' => [
                    'type' => 'none',
                    'renderType' => 'insights',
                ],
            ],
            'tx_yoastseo_focuskeyword_synonyms' => [
                'label' => self::LL_PREFIX_TCA . 'synonyms',
                'exclude' => true,
                'displayCond' => 'FIELD:tx_yoastseo_hide_snippet_preview:REQ:false',
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
        if ($this->table === 'pages') {
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
            '--palette--;' . self::LL_PREFIX_TCA . 'pages.palettes.advances;yoast-advanced,',
            $this->types,
            'after: sitemap_priority'
        );
    }

    /**
     * @return array<array<int|string, string|bool>>
     */
    protected function getInvertedCheckbox(): array
    {
        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= 12) {
            return [
                [
                    1 => '',
                    'invertStateDisplay' => true,
                    'label' => '',
                ],
            ];
        }
        return [
            [
                0 => '',
                1 => '',
                'invertStateDisplay' => true,
            ],
        ];
    }
}
