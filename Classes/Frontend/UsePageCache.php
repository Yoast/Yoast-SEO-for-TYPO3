<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Frontend;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use YoastSeoForTypo3\YoastSeo\Service\YoastRequestService;

class UsePageCache
{
    public function __construct(
        protected YoastRequestService $yoastRequestService
    ) {}

    public function usePageCache(TypoScriptFrontendController $pObj, bool $usePageCache): bool
    {
        $serverParams = $GLOBALS['TYPO3_REQUEST'] ? $GLOBALS['TYPO3_REQUEST']->getServerParams() : $_SERVER;
        if ($this->yoastRequestService->isValidRequest($serverParams)) {
            return false;
        }
        return $usePageCache;
    }
}
