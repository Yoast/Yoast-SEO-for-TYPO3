<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Functional\Backend;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Backend\Controller\PageLayoutController;
use YoastSeoForTypo3\YoastSeo\Backend\PageLayoutHeader;
use YoastSeoForTypo3\YoastSeo\Tests\Functional\AbstractFunctionalTestCase;

#[CoversClass(PageLayoutHeader::class)]
class PageLayoutHeaderTest extends AbstractFunctionalTestCase
{
    protected PageLayoutHeader $pageLayoutHeader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->setUpBackendUser(1);
        $this->setUpBackendRequest();

        $this->pageLayoutHeader = $this->getContainer()->get(PageLayoutHeader::class);
    }

    #[Test]
    public function testRenderReturnsEmptyStringWhenNoPageExists(): void
    {
        $_GET['id'] = '99999'; // page does not exist in the fixture
        $result = $this->pageLayoutHeader->render(null, $this->getContainer()->get(PageLayoutController::class));
        $this->assertSame('', $result);
    }

    #[Test]
    public function testRenderReturnsOutputForExistingPage(): void
    {
        $_GET['id'] = '1'; // page exists in the fixture
        $result = $this->pageLayoutHeader->render(null, $this->getContainer()->get(PageLayoutController::class));
        $this->assertNotEmpty($result);
    }
}
