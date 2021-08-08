<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Form\Element;

class FacebookPreview extends AbstractSocialPreview
{
    protected function getSocialType(): string
    {
        return 'facebook';
    }
}
