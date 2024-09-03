<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Widgets;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class PageOverviewWidget extends AbstractPageOverviewWidget
{
    public function getOptions(): array
    {
        return [];
    }

    protected function assignToView($view): void
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

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
