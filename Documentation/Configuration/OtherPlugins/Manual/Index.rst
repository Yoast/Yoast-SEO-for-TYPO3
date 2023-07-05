.. include:: /Includes.rst.txt


.. _otherplugins_manual:

Manual configuration for records
================================

How to integrate in other plugins
---------------------------------

By default the SEO analysis is only done on pages.
If you want more type of records to use the SEO functions from Yoast SEO you
have to add some fields to the TCA.

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
                    'type' => 'none',
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
                    'type' => 'none',
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
                    'type' => 'none',
                    'renderType' => 'focusKeywordAnalysis',
                    'settings' => [
                        'focusKeywordField' => 'tx_yoastseo_focuskeyword',
                    ]
                ]
            ],

        ]
    );

In the example above, you see we define four fields for in this case the
EXT:news records.

The first field with renderType snippetPreview, is the field for the snippet
preview. In the settings section, you have to define which fields contains the
SEO title and the description. The snippet preview will use these fields to show
you your search result will look like. Make sure the fields that are set, do
exist. This field doesn't need a column in the database.

The second field doesn't need much configuration. Just add a field with
renderType readabilityAnalysis to have the readabilityAnalysis in your record.
This field also doesn't need a column in the database.

To have your content checked for SEO, you have to set a focus keyword, so we
need to add that field as well. This can be a simple text field. This is the
only field that needs a column in the database. Make sure you add this field to
the database table for your records. For the news table this would be the
following within the :file:`ext_tables.sql` of your extension:

.. code-block:: sql

    CREATE TABLE tx_news_domain_model_news (
        tx_yoastseo_focuskeyword varchar(100) DEFAULT '' NOT NULL,
    );

To show the focus keyword analysis, we need to add a field of renderType
focusKeywordAnalysis. In the settings section you have to define the field you
have created with the focus keyword. This field also doesn't need a column in
the database.

Adding PageTsconfig
-------------------
Make sure to add the needed PageTsconfig mentioned in :ref:`otherplugins`
