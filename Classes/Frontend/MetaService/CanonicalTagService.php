<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend\MetaService;


class CanonicalTagService implements TagRendererServiceInterface
{

    /**
     * @return string
     */
    public function render()
    {
        return '<link rel="canonical">';
    }
}