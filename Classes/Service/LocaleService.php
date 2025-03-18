<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Traits\BackendUserTrait;
use YoastSeoForTypo3\YoastSeo\Traits\LanguageServiceTrait;

class LocaleService
{
    use BackendUserTrait;
    use LanguageServiceTrait;

    protected const APP_TRANSLATION_FILE_PATTERN = 'EXT:yoast_seo/Resources/Private/Language/wordpress-seo-%s.json';

    public function __construct(
        protected Locales $locales,
        protected SiteFinder $siteFinder
    ) {}

    /**
     * @return array<string, array<string, string>>
     */
    public function getTranslations(): array
    {
        $interfaceLocale = $this->getInterfaceLocale();

        if ($interfaceLocale === null) {
            // Fall back to English if no suitable locale could be resolved to prevent missing translations
            $interfaceLocale = 'en_GB';
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

        $translationConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['translations'] ?? [
            'availableLocales' => [],
            'languageKeyToLocaleMapping' => [],
        ];

        $userLanguage = $this->getBackendUser()->user['lang'] ?? '';
        if (empty($userLanguage) || $userLanguage === 'default') {
            $userLanguage = 'en';
        }

        $languageChain = $this->locales->getLocaleDependencies($userLanguage);
        array_unshift($languageChain, $userLanguage);

        // try to find a matching locale available for this plugins UI
        // take configured locale dependencies into account
        if ($languageChain !== null) {
            $suitableLocales = array_intersect(
                $languageChain,
                $translationConfiguration['availableLocales']
            );
            if (count($suitableLocales) > 0) {
                $locale = array_shift($suitableLocales);
            }
        }

        // if a locale couldn't be resolved try if an entry of the
        // language dependency chain matches legacy mapping
        if ($locale === null && $languageChain !== null) {
            $suitableLanguageKeys = array_intersect(
                $languageChain,
                array_flip(
                    $translationConfiguration['languageKeyToLocaleMapping']
                )
            );
            if (count($suitableLanguageKeys) > 0) {
                $locale = $translationConfiguration['languageKeyToLocaleMapping'][array_shift($suitableLanguageKeys)];
            }
        }

        return $locale;
    }

    public function getLocale(int $pageId, int &$languageId): ?string
    {
        try {
            $site = $this->siteFinder->getSiteByPageId($pageId);
            if ($languageId === -1) {
                $languageId = $site->getDefaultLanguage()->getLanguageId();
                return $this->getLanguageCode($site->getDefaultLanguage());
            }
            return $this->getLanguageCode($site->getLanguageById($languageId));
        } catch (SiteNotFoundException|\InvalidArgumentException) {
            return null;
        }
    }

    protected function getLanguageCode(SiteLanguage $siteLanguage): string
    {
        // Support for v11
        if (method_exists($siteLanguage, 'getTwoLetterIsoCode')) {
            return $siteLanguage->getTwoLetterIsoCode();
        }
        return $siteLanguage->getLocale()->getLanguageCode();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function getLanguageIdFromData(array $data): int
    {
        if (!isset($data['databaseRow']['sys_language_uid'])) {
            return 0;
        }

        if (is_array($data['databaseRow']['sys_language_uid']) && count($data['databaseRow']['sys_language_uid']) > 0) {
            return (int)current($data['databaseRow']['sys_language_uid']);
        }
        return (int)$data['databaseRow']['sys_language_uid'];
    }

    /**
     * @return array<int, string>
     */
    public function getSupportedLanguages(): array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['supportedLanguages'] ?? [];
    }
}
