<?php
namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Controller;

/*
 * This file is part of the "Yoast SEO for TYPO3" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use YoastSeoForTypo3\YoastSeo\Controller\OverviewController;

class OverviewControllerTest extends UnitTestCase
{
    public function testBackendUserReturnsValueThatIsSet(): void
    {
        $value = new BackendUserAuthentication();
        $GLOBALS['BE_USER'] = $value;

        $this->assertEquals($value, $this->callInaccessibleMethod(new OverviewController(), 'getBackendUser'));
    }
}
