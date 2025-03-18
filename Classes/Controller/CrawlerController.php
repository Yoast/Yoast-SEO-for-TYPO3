<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Site\SiteFinder;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerJavascriptConfigService;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerService;

class CrawlerController extends AbstractBackendController
{
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly CrawlerService $crawlerService,
        protected readonly CrawlerJavascriptConfigService $crawlerJavascriptConfigService,
        protected readonly SiteFinder $siteFinder,
    ) {
        parent::__construct($this->moduleTemplateFactory);
    }

    public function indexAction(): ResponseInterface
    {
        $this->crawlerJavascriptConfigService->addJavascriptConfig();
        return $this->returnResponse(['sites' => $this->siteFinder->getAllSites()]);
    }

    public function resetProgressAction(int $site, int $language): ?ResponseInterface
    {
        $this->crawlerService->resetProgressInformation($site, $language);

        return $this->redirect('index');
    }
}
