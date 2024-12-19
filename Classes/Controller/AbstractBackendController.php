<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use YoastSeoForTypo3\YoastSeo\Traits\BackendUserTrait;
use YoastSeoForTypo3\YoastSeo\Traits\LanguageServiceTrait;

abstract class AbstractBackendController extends ActionController
{
    use BackendUserTrait, LanguageServiceTrait;

    /**
     * @param array<string, mixed> $data
     */
    protected function returnResponse(string $template, array $data = [], ?ModuleTemplate $moduleTemplate = null): ResponseInterface
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
        return $moduleTemplate->renderResponse($template);
    }

    protected function getModuleTemplate(): ModuleTemplate
    {
        $moduleTemplateFactory = GeneralUtility::makeInstance(ModuleTemplateFactory::class);
        return $moduleTemplateFactory->create($this->request);
    }

    /**
     * @return array<string, string|int>
     */
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
}
