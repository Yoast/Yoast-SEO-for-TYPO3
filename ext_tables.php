<?php

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'YoastSeoForTypo3.' . $_EXTKEY,
        'web',
        'seo_plugin',
        '',
        array(
            'Module' => 'edit, dashboard, save, settings, saveSettings',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Images/Yoast-module.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/BackendModule.xlf',
        )
    );
}
