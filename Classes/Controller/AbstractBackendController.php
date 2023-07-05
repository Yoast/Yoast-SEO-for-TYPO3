<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

abstract class AbstractBackendController extends ActionController
{
    protected function returnResponse(array $data = [], ModuleTemplate $moduleTemplate = null): ResponseInterface
    {
        $this->view->assignMultiple($data);
        if ($moduleTemplate === null) {
            $moduleTemplate = $this->getModuleTemplate();
        }
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    protected function getModuleTemplate(): ModuleTemplate
    {
        $moduleTemplateFactory = GeneralUtility::makeInstance(ModuleTemplateFactory::class);
        return $moduleTemplateFactory->create($this->request);
    }
}
