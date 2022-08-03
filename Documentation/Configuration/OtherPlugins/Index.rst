.. include:: /Includes.rst.txt


.. _otherplugins:

Other Plugins
===================

By default the SEO analysis is only done on pages.
If you want more type of records to use the SEO functions, these are two ways
of accomplishing this:

.. toctree::
   :maxdepth: 5
   :titlesonly:

   Automatic/Index
   Manual/Index

Required: adding PageTsconfig preview configuration
---------------------------------------------------
One important thing to know is that the snippet preview of records other than
pages, only works when you have set a proper preview link. This is needed for
both of the solutions above. The only thing you need to do is set some
PageTsconfig. More information about the configuration of the preview links
can be found in the :ref:`documentation <t3tsconfig:pagetcemain-preview>`.

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
