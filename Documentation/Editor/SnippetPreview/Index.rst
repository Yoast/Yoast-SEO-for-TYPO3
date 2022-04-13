.. include:: /Includes.rst.txt


.. _user-snippetpreview:

Snippet preview
===============

Where can I find the snippet preview?
-------------------------------------
The snippet preview is located in 2 places.

By default it is rendered within the page module above your content:
|img-snippetpreview-page|

You can also find the snippet preview within the SEO tab of the page properties:
|img-snippetpreview-properties|

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
| If you want to disable the snippet preview for a specific page, you can set the property
  "Hide Yoast SEO snippet preview in page module for this page" under the "Social media" tab:

|img-snippetpreview-hide-page|

| **Disabling globally**
| If you want to globally disable the rendering of the snippet preview within the page module,
  you can set this within your user settings:

|img-snippetpreview-hide-global|
