<?php
namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Controller\ModuleController;

class ModuleControllerTest extends UnitTestCase
{
    public function testGetLanguageServiceReturnsValueThatIsSet()
    {
        $moduleController = new ModuleController();

        $value = '1517350343';
        $GLOBALS['LANG'] = $value;

        $this->assertEquals($value, $moduleController->getLanguageService());
    }

    public function testBackendUserReturnsValueThatIsSet()
    {
        $moduleController = new ModuleController();

        $value = '1517350444';
        $GLOBALS['BE_USER'] = $value;

        $this->assertEquals($value, $moduleController->getBackendUser());
    }
}
