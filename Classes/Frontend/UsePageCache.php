<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Frontend;

use YoastSeoForTypo3\YoastSeo\Utility\YoastRequestHash;

/**
 * Class UsePageCache
 * @package YoastSeoForTypo3\YoastSeo\Frontend
 */
class UsePageCache
{
    /**
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $pObj
     * @param bool $usePageCache
     * @return bool
     */
    public function usePageCache($pObj, $usePageCache): bool
    {
        $serverParams = $GLOBALS['TYPO3_REQUEST'] ? $GLOBALS['TYPO3_REQUEST']->getServerParams() : $_SERVER;
        if (YoastRequestHash::isValid($serverParams)) {
            return false;
        }
        return $usePageCache;
    }
}
