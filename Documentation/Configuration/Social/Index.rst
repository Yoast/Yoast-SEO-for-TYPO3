.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _social:

Social media settings
=====================

Open Graph / Twitter cards
--------------------------

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

Set different fallback images for each site
-------------------------------------------
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