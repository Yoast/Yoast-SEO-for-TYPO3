<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\StringUtility;

abstract class AbstractLabeledFormElement extends AbstractFormElement
{
    protected function getLabel(): string
    {
        if ((new Typo3Version())->getMajorVersion() === 12) {
            return '';
        }
        $fieldId = StringUtility::getUniqueId('formengine-input-');
        return $this->renderLabel($fieldId);
    }
}
