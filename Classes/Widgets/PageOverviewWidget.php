<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Widgets;

use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use YoastSeoForTypo3\YoastSeo\Traits\BackendUserTrait;

class PageOverviewWidget extends AbstractPageOverviewWidget
{
    use BackendUserTrait;

    /**
     * @return array|string[]
     */
    public function getOptions(): array
    {
        return [];
    }

    protected function assignToView(ViewInterface|StandaloneView $view): void
    {
        $view->assignMultiple([
            'pages' => $this->dataProvider->getPages(),
            'options' => $this->options,
            'button' => null,
            'configuration' => $this->configuration,
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
        ]);
    }
}
