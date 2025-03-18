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
        protected ModuleTemplateFactory $moduleTemplateFactory
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    protected function returnResponse(
        string $template = '',
        array $data = [],
        ?ModuleTemplate $moduleTemplate = null
    ): ResponseInterface {
        if ($moduleTemplate === null) {
            $moduleTemplate = $this->getModuleTemplate();
        }

        $moduleTemplate->assignMultiple($data);
        return $moduleTemplate->renderResponse($template);
    }

    protected function getModuleTemplate(): ModuleTemplate
    {
        return $this->moduleTemplateFactory->create($this->request);
    }
}
