<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use YoastSeoForTypo3\YoastSeo\Traits\BackendUserTrait;
use YoastSeoForTypo3\YoastSeo\Traits\LanguageServiceTrait;

abstract class AbstractBackendController extends ActionController
{
    use BackendUserTrait;
    use LanguageServiceTrait;

    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    protected function returnResponse(
        array $data = [],
        ?ModuleTemplate $moduleTemplate = null
    ): ResponseInterface {
        $this->view->assignMultiple($data);

        if ($moduleTemplate === null) {
            $moduleTemplate = $this->getModuleTemplate();
        }

        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    protected function getModuleTemplate(): ModuleTemplate
    {
        return $this->moduleTemplateFactory->create($this->request);
    }
}
