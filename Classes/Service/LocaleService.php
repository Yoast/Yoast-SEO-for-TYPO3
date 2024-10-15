<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Traits\LanguageServiceTrait;

class LocaleService
{
    use LanguageServiceTrait;

    protected const APP_TRANSLATION_FILE_PATTERN = 'EXT:yoast_seo/Resources/Private/Language/wordpress-seo-%s.json';

    public function __construct(
        protected Locales $locales
    ) {
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getTranslations(): array
    {
        $interfaceLocale = $this->getInterfaceLocale();

        if ($interfaceLocale === null) {
            return [];
        }

        $translationFilePath = GeneralUtility::getFileAbsFileName(
            sprintf(static::APP_TRANSLATION_FILE_PATTERN, $interfaceLocale)
        );

        if ($translationFilePath === '' || !file_exists($translationFilePath)) {
            return [];
        }

        if ($jsonContents = file_get_contents($translationFilePath)) {
            return json_decode($jsonContents, true);
        }

        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getLabels(): array
    {
        $llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:label';
        return [
            'readability' => $this->getLanguageService()->sL($llPrefix . 'Readability'),
            'seo' => $this->getLanguageService()->sL($llPrefix . 'Seo'),
            'bad' => $this->getLanguageService()->sL($llPrefix . 'Bad'),
            'ok' => $this->getLanguageService()->sL($llPrefix . 'Ok'),
            'good' => $this->getLanguageService()->sL($llPrefix . 'Good')
        ];
    }

    /**
     * Try to resolve a supported locale based on the user settings
     * take the configured locale dependencies into account
     * so if the TYPO3 interface is tailored for a specific dialect
     * the local of a parent language might be used
     *
     * @return string|null
     */
    protected function getInterfaceLocale(): ?string
    {
        $locale = null;
        $languageChain = null;

        $translationConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['translations'] ?? [
            'availableLocales' => [],
            'languageKeyToLocaleMapping' => []
        ];

        if ($GLOBALS['BE_USER'] instanceof BackendUserAuthentication
            && is_array($GLOBALS['BE_USER']->uc)
            && array_key_exists('lang', $GLOBALS['BE_USER']->uc)
            && !empty($GLOBALS['BE_USER']->uc['lang'])
        ) {
            $languageChain = $this->locales->getLocaleDependencies(
                $GLOBALS['BE_USER']->uc['lang']
            );

            array_unshift($languageChain, $GLOBALS['BE_USER']->uc['lang']);
        }

        // try to find a matching locale available for this plugins UI
        // take configured locale dependencies into account
        if ($languageChain !== null
            && ($suitableLocales = array_intersect(
                $languageChain,
                $translationConfiguration['availableLocales']
            )) !== false
            && count($suitableLocales) > 0
        ) {
            $locale = array_shift($suitableLocales);
        }

        // if a locale couldn't be resolved try if an entry of the
        // language dependency chain matches legacy mapping
        if ($locale === null && $languageChain !== null
            && ($suitableLanguageKeys = array_intersect(
                $languageChain,
                array_flip(
                    $translationConfiguration['languageKeyToLocaleMapping']
                )
            )) !== false
            && count($suitableLanguageKeys) > 0
        ) {
            $locale =
                $translationConfiguration['languageKeyToLocaleMapping'][array_shift($suitableLanguageKeys)];
        }

        return $locale;
    }
}
