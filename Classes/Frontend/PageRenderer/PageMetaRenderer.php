<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend\MetaService;


use TYPO3\CMS;

class PageMetaRenderer implements CMS\Core\SingletonInterface
{

    /**
     * @var array
     */
    protected $services = array();

    /**
     * @return string
     */
    public function render()
    {
        return '<link rel="canonical">';
    }

    /**
     * @param string $className
     *
     * @return void
     */
    public function registerService($className) {
        if (in_array(TagRendererServiceInterface::class, class_implements($className))) {
            $this->services[] = $className;
        }
    }
}