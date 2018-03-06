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

Disable snippet preview with PageTs
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Sometimes only a check on doktype isn't enough for disabling the snippet preview. For example if you want to hide the
snippet preview on detail pages of for example a news item, you need more than a check on a doktype. That is why you can
also disable the snippet preview based on PageTs. Below an example to hide page if it is a subpage of page with id 4.

.. code-block:: typoscript

    [PIDupinRootline = 4]
        mod.web_SeoPlugin {
            disableSnippetPreview = 1
        }
    [global]

Set fields to be used as title, description and canonical
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
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
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Sometimes you want the title of the page to be prepended or appended with a specific text. You can prepend and append the
title by using these constants in TypoScript:

.. code-block:: typoscript

    plugin.tx_yoastseo {
        titlePrepend = prepend title -
        titleAppend = - append title
    }

Set different fallback images for each site
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
There is a settings panel which you can use to set fallback images for opengraph and Twitter. These will then be used for
your site. If you have multiple sites however, there is a need to set different fallback images for each site. This is
possible by using the opengraph and Twitter image fields on the root page of each respective site. You can then use the
following TypoScript to use those images as fallback images:

.. code-block:: typoscript

    lib.yoastSEO {
        og {
            fallBackImages {
                references {
                    fieldName = og_image
                }
            }
        }
        twitter {
            fallBackImages {
                references {
                    fieldName = twitter_image
                }
            }
        }
    }

Access rights
~~~~~~~~~~~~~
Since version 2 of Yoast SEO for TYPO3, you can set permissions by setting the permissions to fields and backend modules
in the backend group permissions. All fields are exclude fields and all modules can be turned on or of separately.
You don't need specific configuration anymore.
