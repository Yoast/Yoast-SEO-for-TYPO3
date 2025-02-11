<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use YoastSeoForTypo3\YoastSeo\Service\Overview\LanguageMenu\LanguageMenuFactory;
use YoastSeoForTypo3\YoastSeo\Service\Overview\OverviewService;

class OverviewController extends AbstractBackendController
{
    public const REQUEST_ARGUMENT = 'tx_yoastseo_yoast_yoastseooverview';

    public function __construct(
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected LanguageMenuFactory $languageMenuFactory,
        protected OverviewService $overviewService,
    ) {
        parent::__construct($this->moduleTemplateFactory);
    }

    public function listAction(int $currentPage = 1): ResponseInterface
    {
        $overviewData = $this->overviewService->getOverviewData($this->request, $currentPage, (int)$this->settings['itemsPerPage']);
        $moduleTemplate = $this->getModuleTemplate();

        $moduleTemplate->getDocHeaderComponent()->setMetaInformation($overviewData->getPageInformation());
        $languageMenu = $this->languageMenuFactory->create(
            $this->request,
            $moduleTemplate,
            (int)($overviewData->getPageInformation()['uid'] ?? 0)
        );
        if ($languageMenu !== null) {
            $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($languageMenu);
        }

        return $this->returnResponse('Overview/List', $overviewData->toArray(), $moduleTemplate);
    }
}
