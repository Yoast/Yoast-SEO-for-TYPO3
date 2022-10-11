<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use YoastSeoForTypo3\YoastSeo\Utility\PathUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class ModuleController extends ActionController
{
    protected $defaultViewObjectName = BackendTemplateView::class;

    protected function initializeAction(): void
    {
        GeneralUtility::makeInstance(PageRenderer::class)->addCssFile(
            PathUtility::getPublicPathToResources() . '/CSS/yoast-seo-backend.min.css'
        );
    }

    public function dashboardAction(): void
    {
    }

    public function premiumAction(): void
    {
        $this->view->assignMultiple([
            'features' => [
                ['name' => 'keywords', 'free' => true],
                ['name' => 'preview', 'free' => true],
                ['name' => 'readability', 'free' => true],
                ['name' => 'algorithm', 'free' => true],
                ['name' => 'linking_suggestions', 'free' => false],
                ['name' => 'insights', 'free' => false],
                ['name' => 'advanced_robots', 'free' => false],
                ['name' => 'orphanedcontent', 'free' => false],
                ['name' => 'ad_free', 'free' => false],
                ['name' => 'support', 'free' => false],
            ],
            'upcomingFeatures' => [
                ['name' => 'word_forms', 'free' => false],
                ['name' => 'social_previews', 'free' => false],
            ],
            'premiumInstalled' => YoastUtility::isPremiumInstalled()
        ]);
    }
}
