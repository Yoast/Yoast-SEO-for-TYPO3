.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================

Installation
------------

Install the extension using your preferred method, TypoScript include is handled automatically.

Head over to your CLI and run the following command

.. code-block:: bash

	composer require yoast-seo-for-typo3/yoast_seo

After installing it's necessary to flush the system caches and possibly the "Compare Database" section of the install tool.

Configuration
-------------

If you select an image for the render Open Graph or Twitter Cards <meta /> tags you can specify the dimensions of the
image shared.

.. code-block:: typoscript

    plugin.tx_yoastseo {
        settings {
            og.image.width = 640c
            og.image.height = 480c
            twitter.image.width = 640c
            twitter.image.height = 480c
        }
    }

If you use a specific page type for something like a print-only template you can disable the rendering of additional markup.

.. code-block:: typoscript

    printPage.config.yoast_seo.enabled = 0
