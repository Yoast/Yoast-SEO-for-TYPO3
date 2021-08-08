<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

class TwitterPreview extends AbstractSocialPreview
{
    protected function getSocialType(): string
    {
        return 'twitter';
    }
}
