.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _rendering:

Disable rendering Yoast SEO meta tags
=====================================

If you use a specific page type for something like a print-only template you can disable the rendering of additional markup.

.. code-block:: typoscript

    printPage.config.yoast_seo.enabled = 0