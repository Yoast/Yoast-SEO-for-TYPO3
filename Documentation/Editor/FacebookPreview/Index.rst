.. include:: /Includes.rst.txt


.. _user-facebookpreview:

Facebook / OpenGraph preview
============================

Yoast SEO for TYPO3 provides a live Facebook / OpenGraph preview in the backend.
This preview shows you how your page will appear when shared on Facebook and other platforms that support OpenGraph meta tags (LinkedIn, WhatsApp, Slack, etc.).

Where can I find the Facebook preview?
--------------------------------------
The Facebook preview is located in the **Social media** tab of the page properties, inside the **Open Graph** palette.
It is rendered directly above the OpenGraph fields.

..  figure:: /Images/facebook-preview.png
    :alt: Facebook / OpenGraph preview

        Facebook / OpenGraph preview

What fields are used for the Facebook preview?
----------------------------------------------

Title
~~~~~
| The title shown in the preview uses the **OpenGraph title** field (``og_title``).
| If the OpenGraph title is empty, it falls back to the **Title for search engines** (``title``), followed by the **Page title**.

Description
~~~~~~~~~~~
| The description uses the **OpenGraph description** field (``og_description``).
| If the OpenGraph description is empty, it falls back to the **Meta description** from the SEO tab.
| When no description is available at all, a placeholder is shown.

Image
~~~~~
| The image uses the **OpenGraph image** field (``og_image``).
| You can click the image area in the preview to open the file browser and select an image.
| The preview updates automatically when an image is added or changed.

Site URL
~~~~~~~~
| The site URL shown in the preview is derived from your TYPO3 site configuration.
| It reflects the base URL of the current site language.

How does the live preview work?
-------------------------------
The preview updates in real-time as you type in the OpenGraph fields.
Any changes to the title, description, or image are reflected immediately in the preview, so you can see exactly how your page will look when shared on social media before saving.
