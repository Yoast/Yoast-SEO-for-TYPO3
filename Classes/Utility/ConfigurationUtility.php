<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use YoastSeoForTypo3\YoastSeo\Form\Element;
use YoastSeoForTypo3\YoastSeo\MetaTag\Generator;

class ConfigurationUtility
{
    /**
     * @return array<int, array<int, string>>
     */
    public static function getFormEngineNodes(): array
    {
        return [
            1514550050 => ['snippetPreview', Element\SnippetPreview::class],
            1514728465 => ['readabilityAnalysis', Element\ReadabilityAnalysis::class],
            1514830899 => ['focusKeywordAnalysis', Element\FocusKeywordAnalysis::class],
            1519937113 => ['insights', Element\Insights::class],
            1552342645 => ['cornerstone', Element\Cornerstone::class],
            1552511464 => ['internalLinkingSuggestion', Element\InternalLinkingSuggestion::class],
            1739390685 => ['facebookPreview', Element\FacebookPreview::class],
            1739390690 => ['twitterPreview', Element\TwitterPreview::class],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getDefaultConfiguration(): array
    {
        return [
            'allowedDoktypes' => [
                'page' => 1,
                'backend_section' => 6,
            ],
            // Translations for the backend
            'translations' => [
                'availableLocales' => [
                    'ar',
                    'bg_BG',
                    'bs_BA',
                    'ca',
                    'ca_valencia',
                    'da_DK',
                    'de_AT',
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
                    'es_CO',
                    'es_CR',
                    'es_EC',
                    'es_ES',
                    'es_MX',
                    'es_PE',
                    'es_VE',
                    'fa_IR',
                    'fi',
                    'fr_BE',
                    'fr_CA',
                    'fr_FR',
                    'fy',
                    'gl_ES',
                    'he_IL',
                    'hi_IN',
                    'hr',
                    'hu_HU',
                    'id_ID',
                    'it_IT',
                    'ja',
                    'ko_KR',
                    'it_LT',
                    'nb_NO',
                    'nl_BE',
                    'nl_NL',
                    'pl_PL',
                    'pt_AO',
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
                    'zh_TW',
                ],
                'languageKeyToLocaleMapping' => [
                    'bg' => 'bg_BG',
                    'cs' => 'cs_CZ',
                    'da' => 'da_DK',
                    'de' => 'de_DE',
                    'en' => 'en_GB',
                    'es' => 'es_ES',
                    'fa' => 'fa_IR',
                    'fr' => 'fr_FR',
                    'he' => 'he_IL',
                    'hu' => 'hu_HU',
                    'id' => 'id_ID',
                    'it' => 'it_IT',
                    'ko' => 'ko_KR',
                    'no' => 'nb_NO',
                    'nl' => 'nl_NL',
                    'pl' => 'pl_PL',
                    'pt' => 'pt_PT',
                    'ro' => 'ro_RO',
                    'ru' => 'ru_RU',
                    'sk' => 'sk_SK',
                    'sr' => 'sr_RS',
                    'sv' => 'sv_SE',
                    'tr' => 'tr_TR',
                    'zh' => 'zh_CN',
                ],
            ],
            // Supported languages for content analysis
            'supportedLanguages' => [
                'ar',
                'ca',
                'cs',
                'de',
                'el',
                'en',
                'es',
                'fa',
                'fr',
                'he',
                'hu',
                'id',
                'it',
                'ja',
                'nb',
                'nl',
                'pl',
                'pt',
                'ru',
                'sk',
                'sv',
                'tr',
            ],
            'previewSettings' => [
                'basicAuth' => [
                    'username' => '',
                    'password' => '',
                ],
            ],
            'recordMetaTags' => [
                'description' => Generator\DescriptionGenerator::class,
                'opengraph' => Generator\OpenGraphGenerator::class,
                'twitter' => Generator\TwitterGenerator::class,
            ],
        ];
    }
}
