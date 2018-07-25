.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _tca:

TCA
===

Set fields to be used as title, description and canonical
---------------------------------------------------------
By default the extension adds own fields for the SEO fields. In an installation where you already have
fields for this options and maybe even an extension providing data for these fields, you can now configure which fields to
use for the SEO analysis. You can do this in the constants part of TypoScript.

.. code-block:: typoscript

    plugin.tx_yoastseo {
        titleField = your_own_title_field
        descriptionField = your_own_description_field
        canonicalTagField = your_own_canonical_field
        noIndexField = your_own_no_index_field
        noFollowField = your_own_no_follow_field
    }

Prepend and append text to the title of a page
----------------------------------------------
Sometimes you want the title of the page to be prepended or appended with a specific text. You can prepend and append the
title by using these constants in TypoScript:

.. code-block:: typoscript

    plugin.tx_yoastseo {
        titlePrepend = prepend title -
        titleAppend = - append title
    }
