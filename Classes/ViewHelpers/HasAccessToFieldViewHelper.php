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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class HasAccessToFieldViewHelper
 * @package YoastSeoForTypo3\YoastSeo\ViewHelpers
 */
class HasAccessToFieldViewHelper extends AbstractViewHelper
{

    /**
     * @param string $field
     * @param string $table
     * @return bool
     */
    public function render($field, $table = 'pages')
    {
        return (bool)$GLOBALS['BE_USER']->check('non_exclude_fields', $table . ':' . $field);
    }
}
