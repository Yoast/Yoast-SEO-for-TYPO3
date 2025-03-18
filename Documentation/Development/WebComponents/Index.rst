.. include:: /Includes.rst.txt


.. _development-webcomponents:

==============
Web Components
==============

Yoast SEO for TYPO3 provides 13 reusable web components for displaying SEO analysis
results, snippet previews, social media previews, and more. These components are React
components wrapped as custom elements using ``@r2wc/react-to-web-component``, making them
usable in any HTML context.

How to import
=============

The web components are bundled in a single file that can be imported via the TYPO3
JavaScript module system. The module mapping is configured in
``Configuration/JavaScriptModules.php``:

.. code-block:: php

   return [
       'dependencies' => ['core', 'backend', 'frontend_request'],
       'imports' => [
           '@yoast/yoast-seo-for-typo3/' => 'EXT:yoast_seo/Resources/Public/JavaScript/',
       ],
   ];

To load all web components, import the bundle in your JavaScript module:

.. code-block:: javascript

   import "@yoast/yoast-seo-for-typo3/dist/webcomponents.js"

After importing, all ``<yoast-*>`` custom elements are available for use in HTML.

Attribute naming convention
===========================

The React components use camelCase property names, which are automatically converted to
kebab-case HTML attributes by ``@r2wc/react-to-web-component``. For example:

- ``faviconSrc`` becomes ``favicon-src``
- ``wordsToHighlight`` becomes ``words-to-highlight``
- ``readingTime`` becomes ``reading-time``
- ``isLarge`` becomes ``is-large``

Quick reference
===============

.. list-table::
   :header-rows: 1
   :widths: 30 70

   * - Component
     - Attributes
   * - ``<yoast-loading-spinner>``
     - *(none)*
   * - ``<yoast-title-progress-bar>``
     - ``title``
   * - ``<yoast-description-progress-bar>``
     - ``description``, ``date``
   * - ``<yoast-snippet-preview>``
     - ``title``, ``url``, ``description``, ``favicon-src``, ``locale``, ``words-to-highlight``, ``site-name``, ``error``
   * - ``<yoast-status-icon>``
     - ``analysis-done``, ``result-type``, ``text``, ``score``
   * - ``<yoast-analysis-result>``
     - ``analysis``
   * - ``<yoast-insights>``
     - ``keywords``
   * - ``<yoast-flesch-reading-score>``
     - ``score``, ``difficulty``, ``unsupported-language``
   * - ``<yoast-reading-time>``
     - ``reading-time``
   * - ``<yoast-word-count>``
     - ``count``, ``unit``
   * - ``<yoast-linking-suggestions>``
     - ``links``, ``is-checking``
   * - ``<yoast-facebook-preview>``
     - ``site-base``, ``title``, ``description``, ``image-url``
   * - ``<yoast-twitter-preview>``
     - ``site-base``, ``title``, ``description``, ``image-url``, ``is-large``

Component reference
===================

yoast-loading-spinner
---------------------

Displays a loading spinner animation. Used as a placeholder while content is being
analysed.

This component has no attributes.

.. code-block:: html

   <yoast-loading-spinner></yoast-loading-spinner>

yoast-title-progress-bar
------------------------

Displays a progress bar indicating the SEO title length relative to the recommended
character count.

.. list-table::
   :header-rows: 1
   :widths: 20 15 65

   * - Attribute
     - Type
     - Description
   * - ``title``
     - string
     - The page title to measure

.. code-block:: html

   <yoast-title-progress-bar title="My page title"></yoast-title-progress-bar>

yoast-description-progress-bar
------------------------------

Displays a progress bar indicating the meta description length relative to the
recommended character count.

.. list-table::
   :header-rows: 1
   :widths: 20 15 65

   * - Attribute
     - Type
     - Description
   * - ``description``
     - string
     - The meta description to measure
   * - ``date``
     - string
     - Optional date string displayed before the description in the preview

.. code-block:: html

   <yoast-description-progress-bar
       description="This is my meta description"
       date="Jan 1, 2025">
   </yoast-description-progress-bar>

yoast-snippet-preview
---------------------

Renders a Google-style SERP (Search Engine Results Page) snippet preview showing how the
page would appear in search results. Supports desktop and mobile preview modes.

.. list-table::
   :header-rows: 1
   :widths: 25 15 60

   * - Attribute
     - Type
     - Description
   * - ``title``
     - string
     - The page title displayed in the snippet
   * - ``url``
     - string
     - The page URL displayed in the snippet
   * - ``description``
     - string
     - The meta description displayed in the snippet
   * - ``favicon-src``
     - string
     - URL to the site favicon
   * - ``locale``
     - string
     - Locale string (e.g. ``en_US``) for text direction
   * - ``words-to-highlight``
     - json
     - JSON array of words to highlight in the description
   * - ``site-name``
     - string
     - The site name displayed next to the favicon
   * - ``error``
     - json
     - JSON object with ``url`` and ``statusCode`` properties to display an error state

.. code-block:: html

   <yoast-snippet-preview
       title="My Page Title"
       url="https://example.com/my-page"
       description="A description of my page for search engines."
       favicon-src="/favicon.ico"
       locale="en_US"
       words-to-highlight='["SEO", "TYPO3"]'
       site-name="Example">
   </yoast-snippet-preview>

yoast-status-icon
-----------------

Displays a coloured status indicator (traffic light style) reflecting an SEO or
readability score.

.. list-table::
   :header-rows: 1
   :widths: 25 15 60

   * - Attribute
     - Type
     - Description
   * - ``analysis-done``
     - boolean
     - Whether the analysis has completed
   * - ``result-type``
     - string
     - The type of result (e.g. ``seo``, ``readability``)
   * - ``text``
     - boolean
     - Whether to display the score as text alongside the icon
   * - ``score``
     - number
     - The numeric score value

.. code-block:: html

   <yoast-status-icon
       analysis-done
       result-type="seo"
       score="75">
   </yoast-status-icon>

yoast-analysis-result
---------------------

Renders the full content analysis results grouped by severity (errors, problems,
improvements, considerations, and good results).

.. list-table::
   :header-rows: 1
   :widths: 20 15 65

   * - Attribute
     - Type
     - Description
   * - ``analysis``
     - json
     - JSON object containing the analysis results with keys: ``errorsResults``,
       ``problemsResults``, ``improvementsResults``, ``considerationsResults``,
       ``goodResults``

.. code-block:: html

   <yoast-analysis-result
       analysis='{"problemsResults":[],"improvementsResults":[],"goodResults":[],"considerationsResults":[],"errorsResults":[]}'>
   </yoast-analysis-result>

yoast-insights
--------------

Displays a list of prominent words (keywords) found in the content.

.. list-table::
   :header-rows: 1
   :widths: 20 15 65

   * - Attribute
     - Type
     - Description
   * - ``keywords``
     - json
     - JSON array of keyword objects to display

.. code-block:: html

   <yoast-insights keywords='[{"name":"TYPO3","count":5},{"name":"SEO","count":3}]'></yoast-insights>

yoast-flesch-reading-score
--------------------------

Displays the Flesch Reading Ease score with a visual indicator of the text difficulty.

.. list-table::
   :header-rows: 1
   :widths: 30 15 55

   * - Attribute
     - Type
     - Description
   * - ``score``
     - number
     - The Flesch Reading Ease score (0-100)
   * - ``difficulty``
     - number
     - The difficulty level
   * - ``unsupported-language``
     - boolean
     - Set when the content language does not support Flesch scoring

.. code-block:: html

   <yoast-flesch-reading-score score="65" difficulty="3"></yoast-flesch-reading-score>

yoast-reading-time
------------------

Displays the estimated reading time for the content.

.. list-table::
   :header-rows: 1
   :widths: 20 15 65

   * - Attribute
     - Type
     - Description
   * - ``reading-time``
     - number
     - Estimated reading time in minutes

.. code-block:: html

   <yoast-reading-time reading-time="5"></yoast-reading-time>

yoast-word-count
----------------

Displays the word count for the content.

.. list-table::
   :header-rows: 1
   :widths: 20 15 65

   * - Attribute
     - Type
     - Description
   * - ``count``
     - number
     - The number of words
   * - ``unit``
     - string
     - The unit label to display (e.g. ``words``)

.. code-block:: html

   <yoast-word-count count="350" unit="words"></yoast-word-count>

yoast-linking-suggestions
-------------------------

Displays a list of internal linking suggestions for the current page content.

.. list-table::
   :header-rows: 1
   :widths: 20 15 65

   * - Attribute
     - Type
     - Description
   * - ``links``
     - json
     - JSON array of link suggestion objects
   * - ``is-checking``
     - boolean
     - Whether link suggestions are currently being fetched

.. code-block:: html

   <yoast-linking-suggestions
       links='[{"title":"Related page","url":"/related-page"}]'>
   </yoast-linking-suggestions>

yoast-facebook-preview
----------------------

Renders a preview of how the page would appear when shared on Facebook.

.. list-table::
   :header-rows: 1
   :widths: 20 15 65

   * - Attribute
     - Type
     - Description
   * - ``site-base``
     - string
     - The base URL of the site
   * - ``title``
     - string
     - The Open Graph title
   * - ``description``
     - string
     - The Open Graph description
   * - ``image-url``
     - string
     - URL to the Open Graph image

.. code-block:: html

   <yoast-facebook-preview
       site-base="https://example.com"
       title="My Page Title"
       description="A description for Facebook sharing."
       image-url="https://example.com/og-image.jpg">
   </yoast-facebook-preview>

yoast-twitter-preview
---------------------

Renders a preview of how the page would appear when shared on X (Twitter).

.. list-table::
   :header-rows: 1
   :widths: 20 15 65

   * - Attribute
     - Type
     - Description
   * - ``site-base``
     - string
     - The base URL of the site
   * - ``title``
     - string
     - The Twitter Card title
   * - ``description``
     - string
     - The Twitter Card description
   * - ``image-url``
     - string
     - URL to the Twitter Card image
   * - ``is-large``
     - boolean
     - Whether to display the large summary card layout

.. code-block:: html

   <yoast-twitter-preview
       site-base="https://example.com"
       title="My Page Title"
       description="A description for X sharing."
       image-url="https://example.com/twitter-image.jpg"
       is-large>
   </yoast-twitter-preview>
