.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _otherplugins:

Other plugins
=============

How to integrate in other plugins
---------------------------------
From version 2.0 of Yoast SEO for TYPO3, the snippet preview and content- and SEO-analysis are TCA fields. By default the
SEO analysis is only done on pages. If you want more type of records to use the SEO functions from Yoast SEO you have to add some fields to the TCA.

.. code-block:: php

    $llPrefix = 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'tx_news_domain_model_news',
        [
            'tx_yoastseo_snippetpreview' => [
                'label' => $llPrefix . 'snippetPreview',
                'exclude' => true,
                'displayCond' => 'REC:NEW:false',
                'config' => [
                    'type' => 'text',
                    'renderType' => 'snippetPreview',
                    'settings' => [
                        'titleField' => 'alternative_title',
                        'descriptionField' => 'description'
                    ]
                ]
            ],
            'tx_yoastseo_readability_analysis' => [
                'label' => $llPrefix . 'analysis',
                'exclude' => true,
                'config' => [
                    'type' => 'text',
                    'renderType' => 'readabilityAnalysis'
                ]
            ],
            'tx_yoastseo_focuskeyword' => [
                'label' => $llPrefix . 'seoFocusKeyword',
                'exclude' => true,
                'config' => [
                    'type' => 'input',
                ]
            ],
            'tx_yoastseo_focuskeyword_analysis' => [
                'label' => $llPrefix . 'analysis',
                'exclude' => true,
                'config' => [
                    'type' => 'input',
                    'renderType' => 'focusKeywordAnalysis',
                    'settings' => [
                        'focusKeywordField' => 'tx_yoastseo_focuskeyword',
                    ]
                ]
            ],

        ]
    );

In the example above, you see we define four fields for in this case the EXT:news records.

The first field with renderType snippetPreview, is the field for the snippet preview. In the settings section, you have to
define which fields contains the SEO title and the description. The snippet preview will use these fields to show you your
search result will look like. Make sure the fields that are set, do exist. This field doesn't need a column in the database.

The second field doesn't need much configuration. Just add a field with renderType readabilityAnalysis to have the
readabilityAnalysis in your record. This field also doesn't need a column in the database.

To have your content checked for SEO, you have to set a focus keyword, so we need to add that field as well. This can
be a simple text field. This is the only field that needs a column in the database. Make sure you add this field to
the database table for your records.

To show the focus keyword analysis, we need to add a field of renderType focusKeywordAnalysis. In the settings section
you have to define the field you have created with the focus keyword. This field also doesn't need a column in the database.

How to use the snippet preview in other plugins
-----------------------------------------------
One important thing to know is that the snippet preview of records other than pages, only works when you have set a
proper preview link. The only thing you need to do is set some PageTsconfig. More information about the configuration of
the preview links can be found in the `documentation <https://docs.typo3.org/typo3cms/TSconfigReference/PageTsconfig/TceMain.html#preview>`__.

An example configuration of the preview links for EXT:news records is:

.. code-block:: typoscript

    TCEMAIN.preview {
        tx_news_domain_model_news {
                previewPageId = <YOUR_DETAIL_PAGE_ID_HERE>
                useCacheHash = 1
                useDefaultLanguageRecord = 0
                fieldToParameterMap {
                        uid = tx_news_pi1[news_preview]
                }
                additionalGetParameters {
                        tx_news_pi1.controller = News
                        tx_news_pi1.action = detail
                }
        }
    }
