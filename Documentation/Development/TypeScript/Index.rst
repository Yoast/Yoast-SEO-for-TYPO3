.. include:: /Includes.rst.txt


.. _development-typescript:

==========
TypeScript
==========

All the backend functionality JavaScript in this extension is written in TypeScript. This allows us to write more
maintainable and robust code, with better tooling support. The TypeScript source files are located in
``Build/typescript/src/``. The build pipeline compiles these into JavaScript files in ``Resources/Public/JavaScript/``,
which are then loaded as ES modules in the TYPO3 backend.

WebComponents and the Yoast webworker (see :ref:`development-webcomponents` and :ref:`development-resources`) are
built separately in the resources build pipeline, so the TypeScript build only handles the core backend JavaScript.

Prerequisites
=============

- **Node.js** v22.20.0 (see ``Build/typescript/.nvmrc``)
- **Yarn** package manager

Install dependencies:

.. code-block:: bash

   cd Build/typescript
   yarn install

Commands
========

Build (compile once)
--------------------

.. code-block:: bash

   cd Build/typescript
   yarn build

Dev (watch mode)
----------------

.. code-block:: bash

   cd Build/typescript
   yarn dev

This watches for changes and recompiles automatically.

TypeScript configuration
========================

The ``tsconfig.json`` maps ``@yoast/yoast-seo-for-typo3/*`` to ``./src/*`` via path
aliases, matching the TYPO3 JavaScript module import map configured in
``Configuration/JavaScriptModules.php``.

Output structure
================

The compiler outputs flat ``.js`` files and subdirectories into ``Resources/Public/JavaScript/``.

Code style
==========

The project uses **Prettier**.
Configuration is in ``Build/typescript/prettier.config.js``.
