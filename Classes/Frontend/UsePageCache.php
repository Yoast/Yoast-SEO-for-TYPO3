<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Frontend;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use YoastSeoForTypo3\YoastSeo\Utility\YoastRequestHash;

class UsePageCache
{
    public function usePageCache(TypoScriptFrontendController $pObj, bool $usePageCache): bool
    {
        $serverParams = $GLOBALS['TYPO3_REQUEST'] ? $GLOBALS['TYPO3_REQUEST']->getServerParams() : $_SERVER;
        if (YoastRequestHash::isValid($serverParams)) {
            return false;
        }
        return $usePageCache;
    }
}
