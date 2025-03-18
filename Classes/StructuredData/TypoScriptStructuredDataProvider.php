<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use YoastSeoForTypo3\YoastSeo\Service\Frontend\FrontendServiceInterface;

class TypoScriptStructuredDataProvider implements StructuredDataProviderInterface
{
    public function __construct(
        protected SiteFinder $siteFinder,
        protected PageRepository $pageRepository,
        protected TypoScriptService $typoScriptService,
        protected FrontendServiceInterface $frontendService,
    ) {}

    /**
     * @return array<int, array<int|string, mixed>>
     */
    public function getData(): array
    {
        $data = [];
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        foreach (
            $this->frontendService->getTyposcriptConfiguration()['structuredData.']['data.'] ?? [] as $dataConfig
        ) {
            if (!isset($dataConfig['type'], $dataConfig['context'])) {
                continue;
            }

            $item = [];
            $config = $this->typoScriptService->convertTypoScriptArrayToPlainArray($dataConfig);

            foreach ($config as $key => $value) {
                $cObject = $key . '.';
                if (isset($dataConfig[$cObject])) {
                    $value = $contentObjectRenderer->stdWrap((string)$key, $dataConfig[$cObject]);
                }
                $key = in_array($key, ['type', 'context']) ? '@' . $key : $key;

                $item[$key] = $value;
            }
            $data[] = $item;
        }

        return $data;
    }
}
