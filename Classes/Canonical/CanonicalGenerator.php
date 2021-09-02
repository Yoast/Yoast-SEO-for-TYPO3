<?php
declare(strict_types = 1);

namespace YoastSeoForTypo3\YoastSeo\Canonical;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;
use YoastSeoForTypo3\YoastSeo\Utility\CanonicalizationUtility;

/**
 * Class to add the canonical tag to the page
 */
class CanonicalGenerator
{
    /**
     * @var TypoScriptFrontendController
     */
    protected $typoScriptFrontendController;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * CanonicalGenerator constructor
     *
     * @param TypoScriptFrontendController $typoScriptFrontendController
     * @param Dispatcher $signalSlotDispatcher
     */
    public function __construct(TypoScriptFrontendController $typoScriptFrontendController = null, Dispatcher $signalSlotDispatcher = null)
    {
        if ($typoScriptFrontendController === null) {
            $typoScriptFrontendController = $this->getTypoScriptFrontendController();
        }
        if ($signalSlotDispatcher === null) {
            $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        }
        $this->typoScriptFrontendController = $typoScriptFrontendController;
        $this->signalSlotDispatcher = $signalSlotDispatcher;
        $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function generate(&$params, $pObj): string
    {
        if (!$this->typoScriptFrontendController instanceof TypoScriptFrontendController) {
            return '';
        }
        $href = '';
        $this->signalSlotDispatcher->dispatch(self::class, 'beforeGeneratingCanonical', [&$href]);

        if (empty($href) && (int)$this->typoScriptFrontendController->page['no_index'] === 1) {
            return '';
        }

        if (empty($href)) {
            // 1) Check if page show content from other page
            $href = $this->checkContentFromPid();
        }
        if (empty($href)) {
            // 2) Check if page has canonical URL set
            $href = $this->checkForCanonicalLink();
        }
        if (empty($href)) {
            // 3) Fallback, create canonical URL
            $href = $this->checkDefaultCanonical();
        }

        if (!empty($href)) {
            $canonical = '<link ' . GeneralUtility::implodeAttributes([
                    'rel' => 'canonical',
                    'href' => $href
                ], true) . '/>' . LF;
            $params['headerData']['Canonical'] = $canonical;
            return $canonical;
        }
        return '';
    }

    /**
     * @return string
     */
    protected function checkForCanonicalLink(): string
    {
        if (!empty($this->typoScriptFrontendController->page['canonical_link'])) {
            return $this->typoScriptFrontendController->cObj->typoLink_URL([
                'parameter' => $this->typoScriptFrontendController->page['canonical_link'],
                'forceAbsoluteUrl' => true,
            ]);
        }
        return '';
    }

    /**
     * @return string
     */
    protected function checkContentFromPid(): string
    {
        if (!empty($this->typoScriptFrontendController->page['content_from_pid'])) {
            $parameter = (int)$this->typoScriptFrontendController->page['content_from_pid'];
            if ($parameter > 0) {
                $targetPage = $this->pageRepository->getPage($parameter, true);
                if (!empty($targetPage['canonical_link'])) {
                    $parameter = $targetPage['canonical_link'];
                }
                return $this->typoScriptFrontendController->cObj->typoLink_URL([
                    'parameter' => $parameter,
                    'forceAbsoluteUrl' => true,
                ]);
            }
        }
        return '';
    }

    /**
     * @return string
     */
    protected function checkDefaultCanonical(): string
    {
        if (!empty($this->typoScriptFrontendController->cObj)) {
            return $this->typoScriptFrontendController->cObj->typoLink_URL([
                'parameter' => $this->typoScriptFrontendController->id . ',' . $this->typoScriptFrontendController->type,
                'forceAbsoluteUrl' => true,
                'addQueryString' => true,
                'addQueryString.' => [
                    'method' => 'GET',
                    'exclude' => implode(
                        ',',
                        CanonicalizationUtility::getParamsToExcludeForCanonicalizedUrl(
                            (int)$this->typoScriptFrontendController->id,
                            (array)$GLOBALS['TYPO3_CONF_VARS']['FE']['additionalCanonicalizedUrlParameters']
                            )
                            )
                        ]
                    ]);
        } else {
            return '';
        }
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
