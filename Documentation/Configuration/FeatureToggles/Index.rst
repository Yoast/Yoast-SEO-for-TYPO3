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
