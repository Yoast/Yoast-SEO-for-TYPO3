<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

abstract class AbstractBackendController extends ActionController
{
    protected function returnResponse(array $data = [], ModuleTemplate $moduleTemplate = null): ResponseInterface
    {
        $data['layout'] = GeneralUtility::makeInstance(Typo3Version::class)
            ->getMajorVersion() < 13 ? 'Default' : 'Module';
        $this->view->assignMultiple($data);

        if ($moduleTemplate === null) {
            $moduleTemplate = $this->getModuleTemplate();
        }

        if (method_exists($moduleTemplate, 'setContent')) {
            $moduleTemplate->setContent($this->view->render());
            return $this->htmlResponse($moduleTemplate->renderContent());
        }
        $moduleTemplate->getDocHeaderComponent()->setMetaInformation($this->getPageInformation());

        $moduleTemplate->assignMultiple($data);
        return $moduleTemplate->renderResponse();
    }

    protected function getModuleTemplate(): ModuleTemplate
    {
        $moduleTemplateFactory = GeneralUtility::makeInstance(ModuleTemplateFactory::class);
        return $moduleTemplateFactory->create($this->request);
    }

    protected function getPageInformation(): array
    {
        $id = (int)($this->request->getQueryParams()['id'] ?? 0);
        if ($id === 0) {
            return [];
        }
        $pageInformation = BackendUtility::readPageAccess(
            $id,
            $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW)
        );
        return is_array($pageInformation) ? $pageInformation : [];
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
