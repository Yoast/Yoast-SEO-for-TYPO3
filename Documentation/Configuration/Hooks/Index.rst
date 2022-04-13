.. include:: /Includes.rst.txt


.. _hooks:

Hooks
=====

Hook to change the URL to analyse
---------------------------------
By default the extension is generating the URL to analyse based on the Site Configuration and configured Route Enhancers. In some cases
you want to change the URL that needs to be analysed. With this hook, you will be able to alter the URL that will be used to analyse your
content.

First you need to create a class and method that will handle the request:

.. code-block:: php

    <?php
    namespace Vendor\Package\Hooks;

    class YoastUrlToCheckHook
    {
        public function alterUrl(array $params, $pObj)
        {
            $urlToCheck = $params['urlToCheck'];

            // Make sure your altered uri will be returned

            return $urlToCheck;
        }
    }

After you created your own method to alter the URL, you need to register it in your :file:`ext_localconf.php`. To do so, add something
like the example below to your :file:`ext_localconf.php` of your own extension.

.. code-block::  php

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\YoastSeoForTypo3\YoastSeo\Service\UrlService::class]['urlToCheck'][]
        = \Vendor\Package\Hooks\YoastUrlToCheckHook::class . '->alterUrl';

To get this working, you need to clear the TYPO3 system cache. After that, you should be able to see it directly.
