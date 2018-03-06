<?php
namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

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

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RecordIconViewHelper
 * @package YoastSeoForTypo3\YoastSeo\ViewHelpers
 */
class RecordIconViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * @param string $table
     * @param array $row
     * @param string $size
     *
     * @return string
     */
    public function render($table, $row, $size = Icon::SIZE_DEFAULT)
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $icon = $iconFactory->getIconForRecord($table, $row, $size);

        return $icon->render();
    }
}
