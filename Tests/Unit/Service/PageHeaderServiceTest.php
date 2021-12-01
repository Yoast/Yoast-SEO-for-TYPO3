<?php
namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Service;

/*
 * This file is part of the "Yoast SEO for TYPO3" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Service\PageHeaderService;

class PageHeaderServiceTest extends UnitTestCase
{
    /**
     * Check if the PageHeaderService sets the snippetPreviewEnabled correctly
     *
     * @dataProvider isSnippetPreviewEnabledSetCorrectlyDataProvider
     * @test
     */
    public function isSnippetPreviewEnabledSetCorrectly(bool $enabled, bool $expected): void
    {
        $pageHeaderService = new PageHeaderService();
        $pageHeaderService->setSnippetPreviewEnabled($enabled);
        $this->assertEquals($expected, $pageHeaderService->isSnippetPreviewEnabled());
    }

    /**
     * Check if the PageHeaderService sets the moduleData correctly
     *
     * @test
     */
    public function isModuleDataSetCorrectly(): void
    {
        $moduleData = [
            'test' => 1
        ];

        $pageHeaderService = new PageHeaderService();
        $pageHeaderService->setModuleData($moduleData);
        $this->assertEquals($moduleData, $pageHeaderService->getModuleData());
    }

    /**
     * Check if the PageHeaderService sets the pageId correctly
     *
     * @dataProvider isPageIdSetCorrectlyDataProvider
     * @test
     */
    public function isPageIdSetCorrectly(int $pageId): void
    {
        $pageHeaderService = new PageHeaderService();
        $pageHeaderService->setPageId($pageId);
        $this->assertEquals($pageId, $pageHeaderService->getPageId());
    }

    /**
     * @return array
     */
    public function isSnippetPreviewEnabledSetCorrectlyDataProvider(): array
    {
        return [
            [
                true,
                true,
            ],
            [
                false,
                false
            ]
        ];
    }

    /**
     * @return array
     */
    public function isPageIdSetCorrectlyDataProvider(): array
    {
        return [
            [1, 1],
            [2, 2]
        ];
    }
}
