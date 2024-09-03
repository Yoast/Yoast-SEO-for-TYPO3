<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Widgets;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use YoastSeoForTypo3\YoastSeo\Widgets\Provider\PageProviderInterface;

if (interface_exists(RequestAwareWidgetInterface::class)) {
    abstract class AbstractPageOverviewWidget implements WidgetInterface, RequestAwareWidgetInterface
    {
        protected ?ServerRequestInterface $request = null;
        protected array $options;

        public function __construct(
            protected WidgetConfigurationInterface $configuration,
            protected PageProviderInterface $dataProvider,
            protected BackendViewFactory $view,
            array $options = []
        ) {
            $this->options = array_merge(
                [
                    'template' => 'Widget/ExtendedListWidget',
                ],
                $options
            );
        }

        public function setRequest(ServerRequestInterface $request): void
        {
            $this->request = $request;
        }

        public function renderWidgetContent(): string
        {
            $view = $this->view->create($this->request, ['typo3/cms-dashboard', 'yoast-seo-for-typo3/yoast_seo']);
            $this->assignToView($view);
            return $view->render($this->options['template']);
        }

        abstract protected function assignToView($view): void;
    }
} else {
    abstract class AbstractPageOverviewWidget implements WidgetInterface
    {
        protected array $options;

        public function __construct(
            protected WidgetConfigurationInterface $configuration,
            protected PageProviderInterface $dataProvider,
            protected StandaloneView $view,
            array $options = []
        ) {
            $this->options = array_merge(
                [
                    'template' => 'Widget/ExtendedListWidget',
                ],
                $options
            );
        }

        public function renderWidgetContent(): string
        {
            $this->view->setTemplate($this->options['template']);
            $this->assignToView($this->view);
            return $this->view->render();
        }

        abstract protected function assignToView($view): void;
    }
}
