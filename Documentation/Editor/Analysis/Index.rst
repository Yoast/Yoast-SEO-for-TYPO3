.. include:: /Includes.rst.txt


.. _user-analysis:

Content Analysis
================

Where can I find the analysis?
------------------------------

Within the page module
~~~~~~~~~~~~~~~~~~~~~~
On top of the page module you will find the "Readability" and "SEO" analysis.
When the "Inclusive Language" analysis is enabled, it will also be shown here.

..  figure:: /Images/analysis-page.png
    :alt: Analysis status on top of the page

        Analysis status on top of the page

You can click on a particular analysis to see more information about problems, improvements and which tests have passed:

..  figure:: /Images/analysis-readability.png
    :alt: Example of the readability analysis

        Example of the readability analysis

..  figure:: /Images/analysis-seo.png
    :alt: Example of the SEO analysis

        Example of the SEO analysis

Within the page properties
~~~~~~~~~~~~~~~~~~~~~~~~~~
Within the page properties the analyses are not clickable, but the results are shown at the appropriate place.
Both results are shown under the SEO tab.

The Readability analysis can be beneath the **Description** field

The SEO analysis can be found beneath the **Focus keyphrase** field.

Inclusive Language analysis
--------------------------

The Inclusive Language analysis checks your content for non-inclusive language and suggests
more inclusive alternatives. This analysis is not enabled by default.

To enable the Inclusive Language analysis, you need to activate the TYPO3 Feature Toggle
``yoastSeoInclusiveLanguage``. You can do this by adding the following to your
:file:`config/system/additional.php` (or :file:`typo3conf/system/additional.php` for
non-Composer setups):

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['yoastSeoInclusiveLanguage'] = true;

Once enabled, the Inclusive Language analysis will appear alongside the Readability and SEO
analysis in the page module and page properties.

Disabling the analysis
----------------------

You can disable the analysis for specific pages. When analysis is disabled, the readability and
SEO score indicators are hidden, no analysis is calculated, and existing scores and prominent
words are removed. The snippet preview can remain visible independently.

To disable the analysis on a specific page, check "Disable analysis" in the Advanced settings
of the SEO tab in the page properties.

Insights
--------

| Within the SEO tab you'll find the "Insights" information.
| This is a list of the most prominent words on your page.

An example of a prominent words list:

..  figure:: /Images/insights.png
    :alt: Example of Insights

        Example of Insights

