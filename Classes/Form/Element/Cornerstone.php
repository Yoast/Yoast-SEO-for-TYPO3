<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\Element\CheckboxElement;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * TODO: This should be handled differently in the future, for example by overriding appendValueToLabelInDebugMode
 * but due to the differences between 11, 12 and 13 there's currently no way to add the html to the label in a clean way
 * the old way was to provide a custom html template but that caused problems with the inline javascript (CSP)
 * This way the core method is used and thus works for every version, but it's not clean
 */
class Cornerstone extends CheckboxElement
{
    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $checkboxResultArray = parent::render();

        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() < 12) {
            $checkboxResultArray['html'] = str_replace(
                '<span class="form-check-label-text">',
                '<span class="form-check-label-text">' . $this->getLabelWithYoastLink(),
                $checkboxResultArray['html']
            );
        } else {
            $checkboxResultArray['html'] = preg_replace(
                '/(<label class="form-check-label"[^>]*>)(.*?)(<\/label>)/is',
                '$1$2' . $this->getLabelWithYoastLink() . '$3',
                $checkboxResultArray['html']
            );
        }

        return $checkboxResultArray;
    }

    protected function getLabelWithYoastLink(): string
    {
        return LocalizationUtility::translate(
            'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:thisPageIsCornerstoneContent',
            'yoast_seo',
            ['https://yoa.st/metabox-help-cornerstone']
        ) ?? '';
    }
}
