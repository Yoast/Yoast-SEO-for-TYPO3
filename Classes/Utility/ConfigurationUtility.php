<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use YoastSeoForTypo3\YoastSeo\Form\Element;
use YoastSeoForTypo3\YoastSeo\DataProviders;
use YoastSeoForTypo3\YoastSeo\MetaTag\Generator;

class ConfigurationUtility
{
    public static function getFormEngineNodes(): array
    {
        return [
            1514550050 => ['snippetPreview', Element\SnippetPreview::class],
            1514728465 => ['readabilityAnalysis', Element\ReadabilityAnalysis::class],
            1514830899 => ['focusKeywordAnalysis', Element\FocusKeywordAnalysis::class],
            1519937113 => ['insights', Element\Insights::class],
            1552342645 => ['cornerstone', Element\Cornerstone::class],
            1552511464 => ['internalLinkingSuggestion', Element\InternalLinkingSuggestion::class],
        ];
    }

    public static function getDefaultConfiguration(): array
    {
        $llBackendOverview = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf';
        return [
            'allowedDoktypes' => [
                'page' => 1,
                'backend_section' => 5
            ],
            'translations' => [
                'availableLocales' => [
                    'ar',
                    'bg_BG',
                    'bs_BA',
                    'ca',
                    'da_DK',
                    'de_CH',
                    'de_DE',
                    'el',
                    'en_AU',
                    'en_CA',
                    'en_GB',
                    'en_NZ',
                    'en_ZA',
                    'es_AR',
                    'es_CL',
                    'es_CR',
                    'es_EC',
                    'es_ES',
                    'es_MX',
                    'fa_IR',
                    'fi',
                    'fr_BE',
                    'fr_CA',
                    'fr_FR',
                    'gl_ES',
                    'he_IL',
                    'hi_IN',
                    'hr',
                    'id_ID',
                    'it_IT',
                    'ja',
                    'ko_KR',
                    'it_LT',
                    'nb_NO',
                    'nl_BE',
                    'nl_NL',
                    'pl_PL',
                    'pl_AO',
                    'pt_BR',
                    'pt_PT',
                    'ro_RO',
                    'ru_RU',
                    'sk_SK',
                    'sq',
                    'sr_RS',
                    'sv_SE',
                    'tr_TR',
                    'uk',
                    'vi',
                    'zh_CN',
                    'zh_HK',
                    'zh_TW'
                ],
                'languageKeyToLocaleMapping' => [
                    'bg' => 'bg_BG',
                    'da' => 'da_DK',
                    'de' => 'de_DE',
                    'en' => 'en_GB',
                    'es' => 'es_ES',
                    'fa' => 'fa_IR',
                    'fr' => 'fr_FR',
                    'he' => 'he_IL',
                    'it' => 'it_IT',
                    'no' => 'nb_NO',
                    'nl' => 'nl_NL',
                    'pl' => 'pl_PL',
                    'pt' => 'pt_PT',
                    'ru' => 'ru_RU',
                    'sk' => 'sk_SK',
                    'sv' => 'sv_SE',
                    'tr' => 'tr_TR'
                ]
            ],
            'previewSettings' => [
                'basicAuth' => [
                    'username' => '',
                    'password' => '',
                ]
            ],
            'overview_filters' => [
                '10' => [
                    'key' => 'cornerstone',
                    'label' => $llBackendOverview . ':cornerstoneContent',
                    'description' => $llBackendOverview . ':cornerstoneContent.description',
                    'link' => 'https://yoa.st/typo3-cornerstone-content',
                    'dataProvider' => DataProviders\CornerstoneOverviewDataProvider::class . '->process',
                    'countProvider' => DataProviders\CornerstoneOverviewDataProvider::class . '->numberOfItems'
                ],
                '20' => [
                    'key' => 'withoutDescription',
                    'label' => $llBackendOverview . ':withoutDescription',
                    'description' => $llBackendOverview . ':withoutDescription.description',
                    'link' => 'https://yoa.st/typo3-meta-description',
                    'dataProvider' => DataProviders\PagesWithoutDescriptionOverviewDataProvider::class . '->process',
                    'countProvider' => DataProviders\PagesWithoutDescriptionOverviewDataProvider::class . '->numberOfItems'
                ],
                '30' => [
                    'key' => 'orphaned',
                    'label' => $llBackendOverview . ':orphanedContent',
                    'description' => $llBackendOverview . ':orphanedContent.description',
                    'link' => 'https://yoa.st/1ja',
                    'dataProvider' => DataProviders\OrphanedContentDataProvider::class . '->process',
                    'countProvider' => DataProviders\OrphanedContentDataProvider::class . '->numberOfItems'
                ]
            ],
            'recordMetaTags' => [
                'description' => Generator\DescriptionGenerator::class,
                'opengraph' => Generator\OpenGraphGenerator::class,
                'twitter' => Generator\TwitterGenerator::class,
                'robots' => Generator\RobotsGenerator::class,
            ]
        ];
    }
}
