.. include:: /Includes.rst.txt


.. _development:

===========
Development
===========

Local environment with DDEV
===========================

In this repository we added a DDEV setup so you can easily test your
contributions in all the TYPO3 versions the extension should work with.

First of all, make sure you have installed DDEV and Docker. See the
`DDEV documentation <https://ddev.readthedocs.io/en/stable/#installation>`__
how to do that. After you have installed DDEV, run the following command in the
root of this repository.

.. code-block:: bash

   ddev start

After the setup is started, you can use the following command to make sure all
installations are up and running.

.. code-block:: bash

   ddev install-all

When the script is finished, you can go to https://yoast-seo.ddev.site and check
the TYPO3 installations that are available to test your work.

Instances
---------

The following instances will be up and running for you after you have installed
the DDEV setup.

- https://v9.yoast-seo.ddev.site
- https://v10.yoast-seo.ddev.site
- https://v11.yoast-seo.ddev.site

Login
-----

You will be able to login to the backend of the instances above, by using the
following credentials:

:Username: admin
:Password: password

Reset setup
-----------

You want to reset the instances? Use the following two commands. Be aware that
this will also reset the databases!

.. code-block:: bash

   ddev rm -O -R

and after that:

.. code-block:: bash

   docker volume rm yoast-seo-v9-data yoast-seo-v10-data yoast-seo-v11-data

If you change the code, you can directly see the changes in all the
installations of your DDEV setup.

Thanks to Armin Vieweg for this
`example DDEV setup <https://github.com/a-r-m-i-n/ddev-for-typo3-extensions>`__
for TYPO3 extensions.
