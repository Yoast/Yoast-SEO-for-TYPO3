<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

class FacebookPreview extends AbstractSocialPreview
{
    protected function getSocialType(): string
    {
        return 'facebook';
    }

    protected function getFieldSelectors(): array
    {
        return [
            'ogTitle' => 'og_title',
            'ogDescription' => 'og_description',
            'ogImage' => 'og_image',
        ];
    }

    protected function getImageField(): string
    {
        return 'og_image';
    }
}