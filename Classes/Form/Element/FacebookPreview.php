<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Form\Element;

class FacebookPreview extends AbstractSocialPreview
{
    protected function getSocialType(): string
    {
        return 'facebook';
    }

    /**
     * @return array<string, string>
     */
    protected function getFieldSelectors(): array
    {
        return [
            'ogTitle' => 'og_title',
            'ogDescription' => 'og_description',
            'ogImage' => 'og_image',
            'ogImageContainer' => $this->getImageContainerSelector('og_image'),
        ];
    }

    protected function getImageField(): string
    {
        return 'og_image';
    }
}
