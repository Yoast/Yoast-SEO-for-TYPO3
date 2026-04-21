<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

class TwitterPreview extends AbstractSocialPreview
{
    protected function getSocialType(): string
    {
        return 'twitter';
    }

    /**
     * @return array<string, string>
     */
    protected function getFieldSelectors(): array
    {
        return [
            'twitterTitle' => 'twitter_title',
            'twitterDescription' => 'twitter_description',
            'twitterImage' => 'twitter_image',
            'twitterCard' => 'twitter_card',
            'twitterImageContainer' => $this->getImageContainerSelector('twitter_image'),
        ];
    }

    protected function getImageField(): string
    {
        return 'twitter_image';
    }
}
