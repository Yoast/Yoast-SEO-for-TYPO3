.. include:: /Includes.rst.txt


.. _user-twitterpreview:

X/Twitter preview
=================

Yoast SEO for TYPO3 provides a live X/Twitter Card preview in the backend.
This preview shows you how your page will appear when shared on X (formerly Twitter).

Where can I find the X/Twitter preview?
---------------------------------------
The X/Twitter preview is located in the **Social media** tab of the page properties, inside the **Twitter Cards** palette.
It is rendered directly above the Twitter Card fields.

|img-twitter-preview|

What fields are used for the X/Twitter preview?
------------------------------------------------

Title
~~~~~
| The title shown in the preview uses the **Twitter title** field (``twitter_title``).
| If the Twitter title is empty, it falls back to the **Title for search engines** (``title``), followed by the **Page title**.

Description
~~~~~~~~~~~
| The description uses the **Twitter description** field (``twitter_description``).
| If the Twitter description is empty, it falls back to the **Meta description** from the SEO tab.
| When no description is available at all, a placeholder is shown.

Image
~~~~~
| The image uses the **Twitter image** field (``twitter_image``).
| You can click the image area in the preview to open the file browser and select an image.
| The preview updates automatically when an image is added or changed.

Card type
~~~~~~~~~
| The **Card type** field (``twitter_card``) controls the layout of the preview.
| Two options are available:

- **Summary** -- A compact card with a small square image on the left.
- **Summary with large image** -- A card with a large image displayed above the title and description.

| The preview layout updates immediately when you change the card type.

Site URL
~~~~~~~~
| The site URL shown in the preview is derived from your TYPO3 site configuration.
| It reflects the base URL of the current site language.

How does the live preview work?
-------------------------------
The preview updates in real-time as you type in the Twitter Card fields.
Any changes to the title, description, image, or card type are reflected immediately in the preview, so you can see exactly how your page will look when shared on X before saving.
