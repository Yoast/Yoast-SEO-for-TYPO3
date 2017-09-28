<?php
namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class KeyValueViewHelper
 * @package YoastSeoForTypo3\YoastSeo\ViewHelpers
 */
class KeyValueViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('obj', 'mixed', '');
        $this->registerArgument('prop', 'mixed', '');
        $this->registerArgument('sep', 'mixed', '');
    }

    public function render()
    {
        $obj = $this->arguments['obj'];
        $prop = $this->arguments['prop'];
        $sep = $this->arguments['sep'];

        if (is_array($prop)) {
            $prop = implode($sep, $prop);
        }

        if (is_object($obj)) {
            return $obj->$prop;
        } elseif (is_array($obj)) {
            if (array_key_exists($prop, $obj)) {
                return $obj[$prop];
            }
        }
        return null;
    }
}
