<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class TypoScriptStructuredDataProvider implements StructuredDataProviderInterface
{
    public function __construct(
        protected SiteFinder $siteFinder,
        protected PageRepository $pageRepository,
        protected TypoScriptService $typoScriptService,
    ) {}

    /**
     * @return array<int, array<int|string, mixed>>
     */
    public function getData(): array
    {
        $data = [];

        foreach (
            $this->getTypoScriptFrontendController()->config['config']['structuredData.']['data.'] ?? [] as $dataConfig
        ) {
            if (!isset($dataConfig['type'], $dataConfig['context'])) {
                continue;
            }

            $item = [];
            $config = $this->typoScriptService->convertTypoScriptArrayToPlainArray($dataConfig);

            foreach ($config as $key => $value) {
                $cObject = $key . '.';
                if (isset($dataConfig[$cObject])) {
                    $value = $this->getTypoScriptFrontendController()->cObj->stdWrap((string)$key, $dataConfig[$cObject]);
                }
                $key = in_array($key, ['type', 'context']) ? '@' . $key : $key;

                $item[$key] = $value;
            }
            $data[] = $item;
        }

        return $data;
    }

    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
