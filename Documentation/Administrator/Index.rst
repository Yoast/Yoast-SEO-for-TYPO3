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
There is no need for configuration although it is recommended to remove all other SEO related plugins creating metatags in frontend.

However, a few things can still be configured using an extension that overwrites the `EXTCONF` of `yoast_seo` or by TypoScript.

Open Graph / Twitter cards
~~~~~~~~~~~~~~~~~~~~~~~~~~
If you select an image for Open Graph or Twitter Cards <meta /> tags you can specify the dimensions of the
image shared. You can change the width and height by TypoScript.

.. code-block:: typoscript

    plugin.tx_yoastseo {
        settings {
            og.image.width = 640c
            og.image.height = 480c
            twitter.image.width = 640c
            twitter.image.height = 480c
        }
    }

Disable rendering Yoast SEO meta tags on specific page types
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
If you use a specific page type for something like a print-only template you can disable the rendering of additional markup.

.. code-block:: typoscript

    printPage.config.yoast_seo.enabled = 0

Enable snippet preview on specific page types
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
By default, the snippet preview is only shown on pages with doktype 1 (Standard page) and 6 (Backend user section). You can
add your own doktypes like the example below.

.. code-block:: typoscript

    module.tx_yoastseo {
        settings {
            allowedDoktypes {
                blog = 137
            }
        }
    }

Set fields to be used as title, description and canonical
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
By default the extension adds own fields for title, description and the canonical. In an installation where you already have
fields for this options and maybe even an extension providing data for these fields, you can now configure which fields to
use for title, description and canonical urls. You can do this in the constants part of TypoScript.

.. code-block:: typoscript

    plugin.tx_yoastseo {
        titleField = your_own_title_field
        descriptionField = your_own_description_field
        canonicalTagField = your_own_canonical_field
    }

Prepend and append text to the title of a page
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Sometimes you want the title of the page to be prepended or appended with a specific text. You can prepend and append the
title by using these constants in TypoScript:

.. code-block:: typoscript

    plugin.tx_yoastseo {
        titlePrepend = append example -
        titleAppend = - name of company
    }

Make your extension overwrite yoast_seo
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
For some settings you need to extend the `yoast_seo` extension with your own extension. To load your extension in the
right order, you need to set a dependency on `yoast_seo` in your extension. See the example `ext_emconf.php` below:

.. code-block:: php

    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '7.6.0-8.7.99',
                    'yoast_seo' => '*'
                ],
            'conflicts' => [],
            'suggests' => [],
        ],

Now, you can simply add the the `EXTCONF` settings to the `ext_localconf.php` of your own extension and change them
according to your needs.

Show / Hide tabs in backend module
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
You can completely hide certain tabs from the backend module. To do so, you have to override the `viewSettings`
array in the `ext_localconf.php` of your own extension. See the example below to hide the advanced tab.

.. note::
    **This is only a usability change and won't properly protect the access to the functionalities of the respective
    tabs in a secure way!**


.. code-block:: php
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['viewSettings'] = array (
        'showAnalysisTab' => true,
        'showSocialTab'   => true,
        'showAdvancedTab' => false  //Hide tab Advanced
    );

Show / Hide menu entries in backend module
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
You can completely hide certain entries from the top menu of the backend module. To do this, you can override the
`menuActions` array in your own extensions `ext_localconf.php`.

.. note::
    **This is only a usability change and won't properly protect the access to the functionalities of the respective
    tabs in a secure way!**


.. code-block:: php
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['menuActions'] = array (
         ['action' => 'edit', 'label' => 'edit'],
        //['action' => 'dashboard', 'label' => 'dashboard'],    This will hide the dashboard menu item
        ['action' => 'settings', 'label' => 'settings']
    );


Overwrite the PreviewDomain to a custom value
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
You can change the domain which is used for analysing the content. The default behaviour is, that `yoast_seo` will get
the domain from `sys_domain`, just like the normal preview functionality of TYPO3. If you want to alter the preview
domain, you can add this setting to your `ext_localconf.php`. See the example below.

.. code-block:: php
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['previewDomain'] = 'demo.typo3.local';


Change the PreviewURL Template
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
You can change the script-path of the preview, e.g. usable for SPAs.

.. code-block:: php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['previewUrlTemplate'] = '/#%d&type=%d&L=%d';
