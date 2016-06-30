<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend\PageRenderer;


use TYPO3\CMS;
use YoastSeoForTypo3\YoastSeo;

class PageMetaRenderer implements CMS\Core\SingletonInterface
{

    /**
     * @var array
     */
    protected $services = array();

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters)
    {
        $typoScriptFrontendController = $GLOBALS['TSFE'];

        if ($typoScriptFrontendController instanceof CMS\Frontend\Controller\TypoScriptFrontendController) {
            $lineBuffer = array_map(function ($serviceClassName) use ($typoScriptFrontendController) {
                $serviceInstance = CMS\Core\Utility\GeneralUtility::makeInstance($serviceClassName);

                return $serviceInstance instanceof YoastSeo\Frontend\MetaService\TagRendererServiceInterface ? $serviceInstance->render($typoScriptFrontendController) : '';
        }, $this->services);

            $parameters['headerData'][] = implode(PHP_EOL, $lineBuffer);
        }
    }

    /**
     * @param string $className
     *
     * @return void
     */
    public function registerService($className) {
        if (in_array(YoastSeo\Frontend\MetaService\TagRendererServiceInterface::class, class_implements($className))) {
            $this->services[] = $className;
        }
    }
}