.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _sitemap:

XML Sitemap
===========

.. warning::
    You should only use the sitemap functionality if you have a TYPO3 8.7 installation.

    If you have TYPO3 9.5 or higher, the sitemap functionality is shipped with core in EXT:seo:

    https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/XmlSitemap/Index.html

From version 3.0 of EXT:yoast_seo, we ship a extendable sitemap functionality. It is enabled by default and showing all
normal pages. But there are some configuration options.

Add doktypes to pages sitemap
-----------------------------
The configuration of sitemaps is done within TypoScript. If you want to change the doktypes that are in the pages sitemap,
by default only normal pages, you can use the following TypoScript setup.

.. code-block:: typoscript

    plugin.tx_yoastseo {
        sitemap {
            config {
                pages {
                    additionalWhere = AND doktype IN (1, 137)
                }
            }
        }
    }

With this TypoScript you can also add more constraints to which pages you want to show.


How to configure EXT:realurl
----------------------------
You have to do a manual configuration for EXT:realurl. You can add the following code to your configuration array of EXT:realurl:

.. code-block:: php

    'fileName' => [
        'index' => [
            'sitemap-pages.xml' => [
                'keyValues' => [
                    'type' => 1522073831,
                    'tx_yoastseo_sitemap' => 'pages'
                ]
            ],
            'sitemap.xml' => [
                'keyValues' => [
                    'type' => 1522073831
                ]
            ]
        ]
    ]
