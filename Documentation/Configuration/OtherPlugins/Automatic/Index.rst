.. include:: /Includes.rst.txt


.. _otherplugins_automatic:

Automatic functionality for records
===================================

.. warning::

    This functionality is only available for TYPO3 10 and higher. This is due
    to the usage of some hooks and Event Listeners which are not available in
    version 9.

What does the automatic functionality do?
-----------------------------------------
By using the automatic functionality the following will be added to your
records:

* Backend:
   * Fields of the core SEO extension:
      * SEO title
      * Robots: Index this page, Follow this page
      * Opengraph fields: Title, Description, Image
      * Twitter fields: Title, Description, Image, Card
      * Canonical link
      * Sitemap: Change frequency, Priority
   * Functionality of Yoast SEO:
      * Snippet preview
      * Title and Description progress bar
      * Readability Analysis
      * SEO Analysis
      * Focus keyword
* Frontend:
   * Metatag tags:
      * Description
      * Opengraph
      * Twitter
      * Robots
   * Canonical generation
   * Page title based on SEO Title and Title field

Configuration for a record
--------------------------

To get all the functionality mentioned above you only have to set the table
name and the GET parameters on which it responds to. This should be placed
in a file within ``Configuration/TCA/Overrides`` of your extension
(f.e. sitepackage):

.. code-block:: php

   <?php

   \YoastSeoForTypo3\YoastSeo\Utility\RecordUtility::configureForRecord('tx_extension_record')
        ->setGetParameters([
            ['tx_extension_pi1', 'record']
        ]);

This will automatically set the TCA, Database fields and activate the frontend
functionality.

Configuration options
---------------------
The method ``configureRecord`` returns an object which has the following
methods:

setGetParameters
~~~~~~~~~~~~~~~~
:aspect:`Datatype`
    array

:aspect:`Default`
    none

:aspect:`Description`
   The GET parameters on where the frontend functionality should be activated.
   This should be a multidimensional array, which gives you the possibility to
   react to multiple situations.

:aspect:`Example`

.. code-block:: php

   ->setGetParameters([
      ['tx_extension_pi1', 'record'],
      ['tx_extension_pi1', 'record_preview']
   ])


setDefaultSeoFields
~~~~~~~~~~~~~~~~~~~
:aspect:`Datatype`
    boolean

:aspect:`Default`
    true

:aspect:`Description`
   This will define if the default fields from EXT:seo should be added.

setYoastSeoFields
~~~~~~~~~~~~~~~~~
:aspect:`Datatype`
    boolean

:aspect:`Default`
    true

:aspect:`Description`
   This will define if the fields from Yoast SEO should be added.

setSitemapFields
~~~~~~~~~~~~~~~~~
:aspect:`Datatype`
    boolean

:aspect:`Default`
    true

:aspect:`Description`
   This will define if the fields "Change frequency" and "Priority" for Sitemap
   should be added.

setTypes
~~~~~~~~
:aspect:`Datatype`
    string

:aspect:`Default`
    empty (all types)

:aspect:`Description`
   Defines on which types of the record the fields should be added.

setTitleField
~~~~~~~~~~~~~
:aspect:`Datatype`
    string

:aspect:`Default`
    title

:aspect:`Description`
   Sets the title field to another column.

setDescriptionField
~~~~~~~~~~~~~~~~~~~
:aspect:`Datatype`
    string

:aspect:`Default`
    description

:aspect:`Description`
   Sets the description field to another column.

setAddDescriptionField
~~~~~~~~~~~~~~~~~~~~~~
:aspect:`Datatype`
    boolean

:aspect:`Default`
    false

:aspect:`Description`
   Adds the description column to both TCA and database (in case there's none
   already).

setFieldsPosition
~~~~~~~~~~~~~~~~~
:aspect:`Datatype`
    string

:aspect:`Default`
    after:title

:aspect:`Description`
   Sets the position of the TCA fields.

setOverrideTca
~~~~~~~~~~~~~~
:aspect:`Datatype`
    array

:aspect:`Default`
    none

:aspect:`Description`
   Override TCA of the complete table. This can be useful if you want to change
   something of the TCA of the EXT:seo or Yoast SEO fields. This is needed
   because the automatic TCA generation is executed after all generated TCA, so
   trying to add this to one of the ``Overrides`` files will not take effect.

:aspect:`Example`

.. code-block:: php

   ->setOverrideTca([
      'columns' => [
         'seo_title' => [
            'config' => [
               'max' => 100
            ]
         ]
      ]
   ])

setGeneratePageTitle
~~~~~~~~~~~~~~~~~~~~
:aspect:`Datatype`
    boolean

:aspect:`Default`
    true

:aspect:`Description`
   This will enable/disable the functionality of the Page Title Provider
   in the frontend.


setGenerateMetaTags
~~~~~~~~~~~~~~~~~~~
:aspect:`Datatype`
    boolean

:aspect:`Default`
    true

:aspect:`Description`
   This will enable/disable the rendering of the metatags in the frontend (in
   case you want to this yourself). This will not deactivate the canonical tag.


Example with EXT:news
---------------------
EXT:news has his own sitemap fields and has multiple GET parameters to respond
to. The basic configuration can be:

.. code-block:: php

   \YoastSeoForTypo3\YoastSeo\Utility\RecordUtility::configureForRecord('tx_news_domain_model_news')
        ->setGetParameters([
            ['tx_news_pi1', 'news'],
            ['tx_news_pi1', 'news_preview']
        ])
        ->setSitemapFields(false)
        ->setFieldsPosition('after:bodytext');

.. note:: EXT:news provides an own Page Title Provider, if you want to use the
          Page Title Provider of Yoast SEO you can unset the one from EXT:news
          with typoscript: ``config.pageTitleProviders.news >``

Adding PageTsconfig
-------------------
Make sure to add the needed PageTsconfig mentioned in :ref:`otherplugins`
