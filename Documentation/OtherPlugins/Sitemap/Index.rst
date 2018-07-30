.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _otherplugins-sitemap:

Add extra sitemaps
==================

How to create a sitemap of specific records
-------------------------------------------
By default only normal pages are having an own XML sitemap. It is quite easy to add a sitemap for other records as well.

First we start with some TypoScript configuration to add the sitemap.

.. code-block:: typoscript

    plugin.tx_yoastseo {
        sitemap {
            config {
                news {
                    table = tx_news_domain_model_news
                    sortField = datetime DESC
                    detailPid = 12
                    useCacheHash = 1
                    additionalParams = tx_news_pi1[controller]=News&tx_news_pi1[action]=detail&tx_news_pi1[news]
                }
            }
        }
    }

In the example above, you see we define a new sitemap for in this case the EXT:news records. Within the config element,
you can create as many sitemaps you want. Remember to use unique names. In this case we named in news.

Within the sitemap configuration you can set some fields:

tables
~~~~~~

:aspect:`Datatype`
    string

:aspect:`Description`
    The name of the table you want your records from


sortField
~~~~~~~~~~

:aspect:`Datatype`
    string

:aspect:`Default`
    tstamp DESC

:aspect:`Description`
    The field which should be used to sort. You can also specify the direction.


detailPid
~~~~~~~~~

:aspect:`Datatype`
    int

:aspect:`Description`
    On which page should the content be shown?


useCacheHash
~~~~~~~~~~~~

:aspect:`Datatype`
    int

:aspect:`Description`
    When you want to add the cHash to your URL's, set this to 1. Please be careful with this. You don't want google to
    index your page with a specific cHash. Make sure your URL configuration is set correctly to do this.


additionalParams
~~~~~~~~~~~~~~~~

:aspect:`Datatype`
    string

:aspect:`Description`
    This string will be uses as extra parameters when building the link.


Adding EXT:realurl configuration for a new sitemap
--------------------------------------------------

To make sure you have a readable URL, you can add the following code in your EXT:realurl configuration.

.. code-block:: php

    'fileName' => [
        'index' => [
            'sitemap-news.xml' => [
                'keyValues' => [
                    'type' => 1522073831,
                    'tx_yoastseo_sitemap' => 'news'
                ]
            ],
        ]
    ]

On the third line you define the filename. The value of the tx_yoastseo_sitemap element should be the same as the key
you defined in the TypoScript configuration for what is in the sitemap. In this example we used news again.