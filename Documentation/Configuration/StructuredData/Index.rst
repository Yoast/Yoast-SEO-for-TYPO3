.. include:: /Includes.rst.txt


.. _structureddata:

Structured data
===============

Yoast SEO provides structured data (schema.org) rendering in the frontend. This is done through providers.

You can find all configured providers in typoscript within `config.structuredData.providers`

BreadcrumbList
--------------
The output for the type `BreadcrumbList` is provided by the provider key `breadcrumb`.

An example output for this provider is:

.. code-block:: json

    [
       {
          "@context":"https://www.schema.org",
          "@type":"BreadcrumbList",
          "itemListElement": [
             {
                "@type": "ListItem",
                "position": 1,
                "item": {
                   "@id": "https://example.com/",
                   "name": "Example"
                }
             },
             {
                "@type": "ListItem",
                "position": 2,
                "item": {
                   "@id": "https://example.com/test-page",
                   "name": "Test page"
                }
             }
          ]
       }
    ]

Possible configuration options:

excludedDoktypes
~~~~~~~~~~~~~~~~

:aspect:`Datatype`
    string

:aspect:`Default`
    none **WARNING:** By default all pages within the rootline are placed in the breadcrumb, including folders and separators

:aspect:`Description`
    A comma seperated list of doktypes which should not be listed in the `BreadcrumbList`.

WebSite
-------
The output for the type `WebSite` is provided by the provider key `site`.

An example output for this provider is:

.. code-block:: json

    [
        {
            "@context": "https://www.schema.org",
            "@type": "WebSite",
            "url": "https://example.com/",
            "name": "Example"
        }
    ]

This provider does not have any configurable options.

Disabling provider(s)
---------------------
If you want to disable a certain provider, you can do this by unsetting the provider key through typoscript. Example:

.. code-block:: typoscript

    config.structuredData.providers.breadcrumb >

Custom provider
---------------
It is possible to register a custom provider. For this, it's necessary to create a class which implements the
`StructuredDataProviderInterface` and provides a method `getData` which returns a multidimensional array. An example:

.. code-block:: php

    <?php
    namespace Vendor\Package\StructuredData;

    class CustomStructuredDataProvider implements \YoastSeoForTypo3\YoastSeo\StructuredData\StructuredDataProviderInterface
    {
        public function getData(): array
        {
            return [
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'Person',
                    'name' => 'John Doe',
                    'email' => 'john@doe.com'
                ]
            ];
        }
    }

After you have created the class, you can add it to the provider-list by registering it through typoscript:

.. code-block:: typoscript

    config.structuredData.providers {
        custom {
            provider = Vendor\Package\StructuredData\CustomStructuredDataProvider
        }
    }

If you want to have custom configuration with your provider, you can add a method `setConfiguration` which will be automatically called if available.

Example:

.. code-block:: php

    protected $configuration = [];

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

Structured data provider ordering
---------------------------------
If you want to order the rendering of the providers, you can make use of the `before` and `after` settings.

If, for example, you want your custom provider to be rendered **before** breadcrumb and **after** site:

.. code-block:: typoscript

    config.structuredData.providers {
        custom {
            provider = Vendor\Package\StructuredData\CustomStructuredDataProvider
            before = breadcrumb
            after = site
        }
    }

Typoscript provider
-------------------

It is possible to configure structured data through typoscript.

This is done through the `config.structuredData.data` object. The `type` and `context` keys do not need a `@` and stdWrap is available on all keys.

An example for this configuration:

.. code-block:: typoscript

    config.structuredData.data {
        10 {
            context = https://schema.org
            type = Person
            name = TEXT
            name.data = field:author
            email = TEXT
            email.data = field:author_email
        }
    }
