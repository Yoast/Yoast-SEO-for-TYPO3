<?php
/**
 * Created by PhpStorm.
 * User: arnos
 * Date: 6/30/2016
 * Time: 11:14 AM
 */

namespace YoastSeoForTypo3\YoastSeo\Frontend\MetaService;


use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

interface TagRendererServiceInterface
{

    /**
     * @param TypoScriptFrontendController $typoScriptFrontendController
     *
     * @return string
     */
    public function render(TypoScriptFrontendController $typoScriptFrontendController);
}