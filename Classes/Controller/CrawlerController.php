<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\SiteFinder;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerJavascriptConfigService;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerService;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JsonTranslationsService;

class CrawlerController extends AbstractBackendController
{
    public function __construct(
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly CrawlerService $crawlerService,
        protected readonly CrawlerJavascriptConfigService $crawlerJavascriptConfigService,
        protected readonly JsonTranslationsService $jsonTranslationsService,
        protected readonly SiteFinder $siteFinder,
        protected readonly PageRenderer $pageRenderer,
    ) {
        parent::__construct($this->moduleTemplateFactory);
    }

    public function indexAction(): ResponseInterface
    {
        $this->pageRenderer->getJavaScriptRenderer()->addJavaScriptModuleInstruction(
            JavaScriptModuleInstruction::create('@yoast/yoast-seo-for-typo3/crawler.js')->invoke(
                'initialize',
                $this->crawlerJavascriptConfigService->getJavascriptConfig()
            )
        );
        $this->jsonTranslationsService->addTranslations();
        return $this->returnResponse('Crawler/Index', ['sites' => $this->siteFinder->getAllSites()]);
    }

    public function resetProgressAction(int $site, int $language): ?ResponseInterface
    {
        $this->crawlerService->resetProgressInformation($site, $language);
        return $this->redirect('index');
    }
}
