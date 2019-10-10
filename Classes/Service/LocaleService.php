<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class LocaleService
 * @package YoastSeoForTypo3\YoastSeo\Service
 */
class LocaleService
{
    /**
     * @var string
     */
    const APP_TRANSLATION_FILE_PATTERN = 'EXT:yoast_seo/Resources/Private/Language/wordpress-seo-%s.json';

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var \TYPO3\CMS\Core\Localization\Locales
     */
    protected $locales;

    /**
     * LocaleService constructor.
     * @param $configuration
     */
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
        $this->locales = GeneralUtility::makeInstance(Locales::class);
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        $interfaceLocale = $this->getInterfaceLocale();

        if ($interfaceLocale !== null
            && ($translationFilePath = sprintf(
                static::APP_TRANSLATION_FILE_PATTERN,
                $interfaceLocale
            )) !== false
            && ($translationFilePath = GeneralUtility::getFileAbsFileName(
                $translationFilePath
            )) !== false
            && file_exists($translationFilePath)
        ) {
            return json_decode(file_get_contents($translationFilePath));
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
    protected function getInterfaceLocale()
    {
        $locale = null;
        $languageChain = null;

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
                $this->configuration['translations']['availableLocales']
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
                    $this->configuration['translations']['languageKeyToLocaleMapping']
                )
            )) !== false
            && count($suitableLanguageKeys) > 0
        ) {
            $locale =
                $this->configuration['translations']['languageKeyToLocaleMapping'][array_shift($suitableLanguageKeys)];
        }

        return $locale;
    }
}
