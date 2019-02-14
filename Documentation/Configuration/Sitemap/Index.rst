.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _sitemap:

XML Sitemap
===========

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

Use your own url builder (detailPid not exists)
-----------------------------
If you would like to build your own urls (e.g. sitemap for downloads/files), you have the possibility to switch off the check for empty url generation by the SitemapXml class with the config flag ignoreEmptyLinks:

.. code-block:: typoscript

    plugin.tx_yoastseo {
        sitemap {
            config {
                your_sitemap_type {
                    ...
                    ignoreEmptyLinks = 1
                    ...
                }
            }
        }
    }


How to configure EXT:realurl
----------------------------
If you are using the auto-configuration option on EXT:realurl, you are all set. You can find your sitemap by browsing
to https://domain.com/sitemap.xml. It will show you an index of sitemaps, containing 1 sitemap for pages. Only pages
where indexing is enabled in the page properties are mentioned here.

When you have a manual configuration for EXT:realurl, you add to set the follow to your configuration array:

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
