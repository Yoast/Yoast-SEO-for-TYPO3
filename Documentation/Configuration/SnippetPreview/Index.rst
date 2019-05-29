.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _snippetpreview:

Snippet Preview
===============

Enable snippet preview on specific page types
---------------------------------------------
By default, the snippet preview is only shown on pages with doktype 1 (Standard page) and 6 (Backend user section). You can
add your own doktypes like the example below to your TypoScript setup.

.. code-block:: typoscript

    module.tx_yoastseo {
        settings {
            allowedDoktypes {
                blog = 137
            }
        }
    }

Disable snippet preview with PageTs
-----------------------------------
Sometimes only a check on doktype isn't enough for disabling the snippet preview. For example if you want to hide the
snippet preview on detail pages of for example a news item, you need more than a check on a doktype. That is why you can
also disable the snippet preview based on PageTs. Below an example to hide page if it is a subpage of page with id 4.

.. code-block:: typoscript

    [PIDupinRootline = 4]
        mod.web_SeoPlugin {
            disableSnippetPreview = 1
        }
    [global]
