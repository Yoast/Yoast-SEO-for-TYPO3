<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Traits;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

trait BackendUserTrait
{
    public function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
