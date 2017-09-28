<?php
namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RemoveEntersViewHelper
 * @package YoastSeoForTypo3\YoastSeo\ViewHelpers
 */
class RemoveEntersViewHelper extends AbstractViewHelper
{
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @param string $string
     * @return null
     */
    public function render($string = '')
    {
        if (!$string) {
            $string = $this->renderChildren();
        }

        $patterns = [
            '/\n+/',
            '/\s+/'
        ];
        $replacements = [
            '',
            ' '
        ];

        return trim(preg_replace($patterns, $replacements, $string));
    }
}
