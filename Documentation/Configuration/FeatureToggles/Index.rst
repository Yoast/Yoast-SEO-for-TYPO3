.. include:: /Includes.rst.txt


.. _feature-toggles:

Feature Toggles
===============

Yoast SEO provides optional features that can be enabled using the
`TYPO3 Feature Toggle system <https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/Configuration/FeatureToggles.html>`__.

Inclusive Language analysis
---------------------------

:Feature Toggle:
   ``yoastSeoInclusiveLanguage``

:Default:
   ``false``

:Description:
   Enables the Inclusive Language analysis, which checks your content for
   non-inclusive language and suggests more inclusive alternatives. When enabled,
   the analysis appears alongside the Readability and SEO analysis in the page
   module and page properties.

To enable, add the following to your :file:`config/system/additional.php`
(or :file:`typo3conf/system/additional.php` for non-Composer setups):

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['yoastSeoInclusiveLanguage'] = true;

Disable all caches on Yoast preview requests
--------------------------------------------

:Feature Toggle:
   ``yoastSeoDisableAllCachesOnPreviewRequest``

:Default:
   ``false``

:Description:
   Disables all TYPO3 caches for frontend requests that originate from Yoast.
   When enabled, the ``noCache`` request attribute is set on every validated
   Yoast request. This should only be necessary for edge cases where hidden
   pages could end up in certain caches (f.e. a custom menu cache).

To enable, add the following to your :file:`config/system/additional.php`
(or :file:`typo3conf/system/additional.php` for non-Composer setups):

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['yoastSeoDisableAllCachesOnPreviewRequest'] = true;
