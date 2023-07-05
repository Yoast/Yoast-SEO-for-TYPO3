<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Record\Builder;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\TcaService;

class TcaBuilder extends AbstractBuilder
{
    public function build(): void
    {
        if ($this->record->hasDefaultSeoFields()) {
            $this->addDefaultSeoFields();
        }

        if ($this->record->hasYoastSeoFields()) {
            $this->addYoastFields();
        }

        if ($this->record->hasSitemapFields()) {
            $this->addSitemapFields();
        }

        if ($this->record->shouldAddDescriptionField()) {
            $this->addDescriptionField();
        }

        $GLOBALS['TCA'][$this->record->getTableName()]['yoast_seo'] = [
            'defaultSeoFields' => $this->record->hasDefaultSeoFields(),
            'yoastFields' => $this->record->hasYoastSeoFields(),
            'sitemapFields' => $this->record->hasSitemapFields(),
            'titleField' => $this->record->getTitleField(),
            'descriptionField' => $this->record->getDescriptionField(),
            'addDescriptionField' => $this->record->shouldAddDescriptionField(),
            'getParameters' => $this->record->getGetParameters(),
            'generatePageTitle' => $this->record->shouldGeneratePageTitle(),
            'generateMetaTags' => $this->record->shouldGenerateMetaTags(),
            'generateRobotsTag' => $this->record->shouldGenerateRobotsTag(),
        ];

        if (!empty($this->record->getOverrideTca())) {
            $GLOBALS['TCA'][$this->record->getTableName()] = array_replace_recursive(
                $GLOBALS['TCA'][$this->record->getTableName()],
                $this->record->getOverrideTca()
            );
        }
    }

    protected function addDefaultSeoFields(): void
    {
        $tca = [
            'palettes' => [
                'seo' => $GLOBALS['TCA']['pages']['palettes']['seo'],
                'robots' => $GLOBALS['TCA']['pages']['palettes']['robots'],
                'canonical' => $GLOBALS['TCA']['pages']['palettes']['canonical'],
                'opengraph' => $GLOBALS['TCA']['pages']['palettes']['opengraph'],
                'twittercards' => $GLOBALS['TCA']['pages']['palettes']['twittercards'],
            ],
            'columns' => [
                'seo_title' => $GLOBALS['TCA']['pages']['columns']['seo_title'],
                'no_index' => $GLOBALS['TCA']['pages']['columns']['no_index'],
                'no_follow' => $GLOBALS['TCA']['pages']['columns']['no_follow'],
                'canonical_link' => $GLOBALS['TCA']['pages']['columns']['canonical_link'],
                'og_title' => $GLOBALS['TCA']['pages']['columns']['og_title'],
                'og_description' => $GLOBALS['TCA']['pages']['columns']['og_description'],
                'og_image' => $GLOBALS['TCA']['pages']['columns']['og_image'],
                'twitter_title' => $GLOBALS['TCA']['pages']['columns']['twitter_title'],
                'twitter_description' => $GLOBALS['TCA']['pages']['columns']['twitter_description'],
                'twitter_image' => $GLOBALS['TCA']['pages']['columns']['twitter_image'],
                'twitter_card' => $GLOBALS['TCA']['pages']['columns']['twitter_card'],
            ]
        ];
        $GLOBALS['TCA'][$this->record->getTableName()] = array_replace_recursive(
            $GLOBALS['TCA'][$this->record->getTableName()],
            $tca
        );

        ExtensionManagementUtility::addToAllTCAtypes(
            $this->record->getTableName(),
            '--div--;LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:pages.tabs.seo,
                --palette--;;seo,
                --palette--;;robots,
                --palette--;;canonical,
            --div--;LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:pages.tabs.socialmedia,
                --palette--;;opengraph,
                --palette--;;twittercards',
            $this->record->getTypes(),
            $this->record->getFieldsPosition()
        );
    }

    protected function addYoastFields(): void
    {
        GeneralUtility::makeInstance(TcaService::class)
            ->addYoastFields(
                $this->record->getTableName(),
                $this->record->getTypes(),
                $this->record->getDescriptionField()
            );
    }

    protected function addSitemapFields(): void
    {
        $tca = [
            'palettes' => [
                'sitemap' => $GLOBALS['TCA']['pages']['palettes']['sitemap'],
            ],
            'columns' => [
                'sitemap_changefreq' => $GLOBALS['TCA']['pages']['columns']['sitemap_changefreq'],
                'sitemap_priority' => $GLOBALS['TCA']['pages']['columns']['sitemap_priority'],
            ]
        ];
        $GLOBALS['TCA'][$this->record->getTableName()] = array_replace_recursive(
            $GLOBALS['TCA'][$this->record->getTableName()],
            $tca
        );
        ExtensionManagementUtility::addToAllTCAtypes(
            $this->record->getTableName(),
            '--palette--;;sitemap',
            $this->record->getTypes(),
            'after:--palette--;;canonical'
        );
    }

    protected function addDescriptionField(): void
    {
        ExtensionManagementUtility::addTCAcolumns($this->record->getTableName(), [
            $this->record->getDescriptionField() => $GLOBALS['TCA']['pages']['columns']['description']
        ]);
    }

    public function getResult(): array
    {
        return $GLOBALS['TCA'];
    }
}
