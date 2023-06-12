<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\ViewHelpers\ModuleLayout;

use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\ViewHelpers\ModuleLayoutViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class MetaInformationViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments()
    {
        $this->registerArgument('pageInformation', 'array', 'Page information', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $viewHelperVariableContainer = $renderingContext->getViewHelperVariableContainer();

        /** @var \TYPO3\CMS\Backend\Template\ModuleTemplate $moduleTemplate */
        $moduleTemplate = $viewHelperVariableContainer->get(ModuleLayoutViewHelper::class, ModuleTemplate::class);
        $moduleTemplate->getDocHeaderComponent()->setMetaInformation($arguments['pageInformation']);
    }
}
