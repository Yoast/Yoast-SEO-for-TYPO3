.. include:: /Includes.rst.txt


.. _user-snippetpreview:

Snippet preview
===============

Where can I find the snippet preview?
-------------------------------------
The snippet preview is located in 2 places.

By default it is rendered within the page module above your content:

..  figure:: /Images/snippetpreview-page.png
    :alt: Snippetpreview within the Page module

        Snippetpreview within the Page module

You can also find the snippet preview within the SEO tab of the page properties:

..  figure:: /Images/snippetpreview-properties.png
    :alt: Snippetpreview within the page properties

        Snippetpreview within the page properties

What information is used for the snippet preview?
-------------------------------------------------
Title
~~~~~
| The title is being generated based on your page title (General tab), overridden by the "Title for search engines" (SEO tab).
| Based on your frontend configuration the website title gets prepended or appended.

Description
~~~~~~~~~~~
| This uses the content from the "Description" field under the SEO tab.
| A default placeholder is shown when there is no description available.

Favicon and URL
~~~~~~~~~~~~~~~
| The favicon and url are generated from the frontend information.
| If there's no favicon available (`shortcut icon`) the default `favicon.ico` from the root is rendered.

Can I disable the snippet preview?
----------------------------------
There are two ways to disable the snippet preview within the page module:

| **On a specific page**
| If you want to hide the snippet preview for a specific page, you can check
  "Hide snippet preview" in the Advanced settings of the SEO tab.

..  figure:: /Images/snippetpreview-hide-page.png
    :alt: Hide the snippetpreview for a specific page

        Hide the snippetpreview for a specific page

| **Disabling globally**
| If you want to globally disable the rendering of the snippet preview within the page module,
  you can set this within your user settings:

..  figure:: /Images/snippetpreview-hide-global.png
    :alt: Hide the snippetpreview globally

        Hide the snippetpreview globally

Can I disable the analysis?
---------------------------
You can disable the Yoast SEO analysis (readability, SEO and inclusive language scoring)
independently from the snippet preview.

| **On a specific page**
| Check "Disable analysis" in the Advanced settings of the SEO tab.
  When analysis is disabled:

- The readability and SEO score indicators are hidden
- No analysis is calculated or saved
- Existing scores and prominent words for the page are removed
- The snippet preview can remain visible independently

