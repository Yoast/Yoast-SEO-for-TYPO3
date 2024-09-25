<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Traits;

trait LanguageServiceTrait
{
    public function getLanguageService(): \TYPO3\CMS\Core\Localization\LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
