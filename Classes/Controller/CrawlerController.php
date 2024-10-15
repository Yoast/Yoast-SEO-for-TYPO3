<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JavascriptUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;
use YoastSeoForTypo3\YoastSeo\Service\CrawlerService;

class CrawlerController extends AbstractBackendController
{
    public function __construct(
        protected CrawlerService $crawlerService,
        protected SiteFinder $siteFinder,
    ) {}

    public function indexAction(): ResponseInterface
    {
        $this->addYoastJavascriptConfig();
        return $this->returnResponse('Crawler/Index', ['sites' => $this->siteFinder->getAllSites()]);
    }

    public function resetProgressAction(int $site, int $language):? ResponseInterface
    {
        $this->crawlerService->resetProgressInformation($site, $language);

        return $this->redirect('index');
    }

    protected function addYoastJavascriptConfig(): void
    {
        JavascriptUtility::loadJavascript();

        $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $jsonConfigUtility->addConfig([
            'urls' => [
                'workerUrl' => PathUtility::getPublicPathToResources() . '/JavaScript/dist/worker.js',
                'preview' => (string)$uriBuilder->buildUriFromRoute('ajax_yoast_preview'),
                'determinePages' => (string)$uriBuilder->buildUriFromRoute('ajax_yoast_crawler_determine_pages'),
                'indexPages' => (string)$uriBuilder->buildUriFromRoute('ajax_yoast_crawler_index_pages'),
                'prominentWords' => (string)$uriBuilder->buildUriFromRoute('ajax_yoast_prominent_words'),
            ],
        ]);
    }
}
