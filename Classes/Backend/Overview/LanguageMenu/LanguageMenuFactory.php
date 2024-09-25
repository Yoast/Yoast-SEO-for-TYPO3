<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend\Overview\LanguageMenu;

use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use YoastSeoForTypo3\YoastSeo\Traits\BackendUserTrait;
use YoastSeoForTypo3\YoastSeo\Traits\LanguageServiceTrait;

class LanguageMenuFactory
{
    use BackendUserTrait, LanguageServiceTrait;

    protected RequestInterface $request;
    protected ModuleTemplate $moduleTemplate;
    protected int $pageUid;

    public function __construct(
        protected SiteFinder $siteFinder,
        protected UriBuilder $uriBuilder
    ) {
    }

    public function create(
        RequestInterface $request,
        ModuleTemplate $moduleTemplate,
        int $pageUid
    ): ?Menu {
        try {
            $site = $this->siteFinder->getSiteByPageId($pageUid);
        } catch (SiteNotFoundException) {
            return null;
        }

        $languages = $site->getAvailableLanguages($this->getBackendUser());
        if (empty($languages)) {
            return null;
        }

        $this->request = $request;
        $this->moduleTemplate = $moduleTemplate;
        $this->pageUid = $pageUid;

        return $this->buildLanguageMenu($languages);
    }

    /**
     * @param SiteLanguage[] $languages
     */
    protected function buildLanguageMenu(
        array $languages
    ): Menu {
        $languageMenu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $languageMenu->setIdentifier('languageMenu');
        $languageMenu->setLabel(
            $this->getLanguageService()->sL(
                'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language'
            )
        );
        foreach ($this->getLanguageMenuItems($languages) as $languageMenuItem) {
            $menuItem = $languageMenu
                ->makeMenuItem()
                ->setTitle($languageMenuItem->getTitle())
                ->setHref($languageMenuItem->getHref())
                ->setActive($languageMenuItem->isActive());
            $languageMenu->addMenuItem($menuItem);
        }
        return $languageMenu;
    }

    /**
     * @param SiteLanguage[] $languages
     * @return LanguageMenuItem[]
     */
    protected function getLanguageMenuItems(
        array $languages
    ): array {
        $arguments = $this->getArguments();

        $filter = $arguments['filter'] ?? '';
        $returnUrl = $arguments['returnUrl'] ?? '';
        $items = [];
        foreach ($languages as $language) {
            $url = $this->uriBuilder
                ->reset()
                ->setRequest($this->request)
                ->setTargetPageUid($this->pageUid)
                ->setArguments([
                    'tx_yoastseo_yoast_yoastseooverview' => [
                        'filter' => $filter,
                        'language' => $language->getLanguageId(),
                        'returnUrl' => $returnUrl,
                        'controller' => 'Overview',
                    ],
                ])
                ->build();
            $items[] = new LanguageMenuItem(
                $language->getTitle(),
                $url,
                (int)($arguments['language'] ?? 0) === $language->getLanguageId()
            );
        }
        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getArguments(): array
    {
        return $this->request->getArguments()['tx_yoastseo_yoast_yoastseooverview'] ?? $this->request->getArguments();
    }
}
