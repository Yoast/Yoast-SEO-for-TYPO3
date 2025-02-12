<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

class TwitterPreview extends AbstractSocialPreview
{
    protected function getSocialType(): string
    {
        return 'twitter';
    }

    protected function getFieldSelectors(): array
    {
        return [
            'twitterTitle' => 'twitter_title',
            'twitterDescription' => 'twitter_description',
            'twitterImage' => 'twitter_image',
        ];
    }

    protected function getImageField(): string
    {
        return 'twitter_image';
    }
}