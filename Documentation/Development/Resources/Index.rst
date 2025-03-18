.. include:: /Includes.rst.txt


.. _development-resources:

=================================
JavaScript/Sass build pipeline
=================================

The resources build pipeline in ``Build/resources/`` uses Webpack to bundle React / Yoast
components (wrapped as web components via ``@r2wc/react-to-web-component``), build the Yoast webworker and compile
SCSS into production CSS assets.

Prerequisites
=============

- **Node.js** v22.20.0 (see ``Build/resources/.nvmrc``)
- **Yarn** package manager

Install dependencies:

.. code-block:: bash

   cd Build/resources
   yarn install

Commands
========

Build (production)
------------------

.. code-block:: bash

   cd Build/resources
   yarn build

Start (development server)
--------------------------

.. code-block:: bash

   cd Build/resources
   yarn start

Start with Hot Module Replacement
----------------------------------

.. code-block:: bash

   cd Build/resources
   yarn start:hot

Tech stack
==========

- **Webpack 5** via ``@wordpress/scripts``
- **React** with ``@r2wc/react-to-web-component`` for wrapping React components as
  custom elements
- **Babel** for JavaScript transpilation
- **SCSS** / PostCSS for stylesheets
- **Yoast packages**: ``yoastseo``, ``@yoast/analysis-report``, ``@yoast/components``,
  ``@yoast/search-metadata-previews``, ``@yoast/social-metadata-previews``, and more

JavaScript entries
==================

Configured in ``Build/resources/webpack.config.js``:

``webcomponents.tsx``
---------------------

:aspect:`Entry`
    ``./javascript/webcomponents.tsx``

:aspect:`Output`
    ``Resources/Public/JavaScript/dist/webcomponents.js``

:aspect:`Description`
    Bundles all 13 React-based web components. Each React component is wrapped with
    ``@r2wc/react-to-web-component`` and registered as a custom element.
    See :ref:`development-webcomponents` for the full component reference.

``worker.js``
-------------

:aspect:`Entry`
    ``./javascript/worker.js``

:aspect:`Output`
    ``Resources/Public/JavaScript/dist/worker.js``

:aspect:`Description`
    The Yoast SEO analysis web worker that performs text analysis in a background thread.

SCSS entries
============

``backend-module.scss``
-----------------------

:aspect:`Entry`
    ``./sass/backend-module.scss``

:aspect:`Output`
    ``Resources/Public/CSS/yoast-seo-backend.min.css``

:aspect:`Description`
    Styles for the Yoast SEO backend module (overview, dashboard).

``yoast.scss``
--------------

:aspect:`Entry`
    ``./sass/yoast.scss``

:aspect:`Output`
    ``Resources/Public/CSS/yoast.min.css``

:aspect:`Description`
    Core Yoast SEO styles used across the TYPO3 backend interface.

Webpack configuration
=====================

The build uses a base configuration in ``Build/resources/webpack.config.base.js`` that
extends ``@wordpress/scripts/config/webpack.config`` with:

- Babel loader for all ``.js`` files
- ``RemoveEmptyScriptsPlugin`` to clean up empty JS files generated from SCSS-only entries
- Custom ``DefinePlugin`` for ``process.env.NODE_DEBUG`` and ``SCRIPT_DEBUG``
- The ``DependencyExtractionWebpackPlugin`` from ``@wordpress/scripts`` is removed since
  dependencies are bundled rather than externalized
