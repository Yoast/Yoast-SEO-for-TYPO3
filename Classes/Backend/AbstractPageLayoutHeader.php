<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Backend;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Service\PageHeaderService;
use YoastSeoForTypo3\YoastSeo\Service\UrlService;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

abstract class AbstractPageLayoutHeader
{
    /**
     * @var array
     */
    protected $configuration = [
        'translations' => [
            'availableLocales' => [],
            'languageKeyToLocaleMapping' => []
        ]
    ];

    /**
     * @var \YoastSeoForTypo3\YoastSeo\Service\LocaleService
     */
    protected $localeService;

    /**
     * @var \YoastSeoForTypo3\YoastSeo\Service\UrlService
     */
    protected $urlService;

    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var \YoastSeoForTypo3\YoastSeo\Service\PageHeaderService
     */
    protected $pageHeaderService;

    public function __construct()
    {
        if (array_key_exists('yoast_seo', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo'])
        ) {
            ArrayUtility::mergeRecursiveWithOverrule(
                $this->configuration,
                $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']
            );
        }

        $this->localeService = GeneralUtility::makeInstance(LocaleService::class, $this->configuration);
        $this->urlService = GeneralUtility::makeInstance(UrlService::class);
        $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $this->pageHeaderService = GeneralUtility::makeInstance(PageHeaderService::class);
    }

    /**
     * @param int $pageId
     * @param array $moduleData
     * @param object $parentObj
     * @return array|null
     */
    protected function getCurrentPage(int $pageId, array $moduleData, object $parentObj): ?array
    {
        $currentPage = null;

        if ($parentObj instanceof PageLayoutController && $pageId > 0) {
            if ((int)$moduleData['language'] === 0) {
                $currentPage = BackendUtility::getRecord(
                    'pages',
                    $pageId
                );
            } elseif ((int)$moduleData['language'] > 0) {
                $overlayRecords = BackendUtility::getRecordLocalization(
                    'pages',
                    $pageId,
                    (int)$moduleData['language']
                );

                if (is_array($overlayRecords) && array_key_exists(0, $overlayRecords) && is_array($overlayRecords[0])) {
                    $currentPage = $overlayRecords[0];
                }
            }
        }
        return $currentPage;
    }

    protected function getModuleData(): array
    {
        return (array)BackendUtility::getModuleData(['language'], [], 'web_layout');
    }

    protected function getPageId(): int
    {
        return (int)GeneralUtility::_GET('id');
    }

    protected function shouldShowPreview(int $pageId, array $pageRecord): bool
    {
        if (!YoastUtility::snippetPreviewEnabled($pageId, $pageRecord)) {
            return false;
        }

        $allowedDoktypes = YoastUtility::getAllowedDoktypes();
        return isset($pageRecord['doktype']) && in_array((int)$pageRecord['doktype'], $allowedDoktypes, true);
    }

    abstract public function render(array $params = null, $parentObj = null): string;
}
