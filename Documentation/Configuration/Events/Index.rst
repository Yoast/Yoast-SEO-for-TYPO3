.. include:: /Includes.rst.txt


.. _events:

Event
=====

Event to change the URL to analyse
---------------------------------
By default the extension is generating the URL to analyse based on the Site Configuration and configured Route Enhancers. In some cases
you want to change the URL that needs to be analysed. With the `ModifyPreviewUrlEvent`, you will be able to alter the URL that will be
used to analyse your content.

First you need to create an EventListener that will handle the event:

.. code-block:: php

    <?php

    namespace Vendor\Package\EventListener;

    use YoastSeoForTypo3\YoastSeo\Event\ModifyPreviewUrlEvent;

    final class ModifyPreviewUrlEventListener
    {
        public function __invoke(ModifyPreviewUrlEvent $event)
        {
            $url = $event->getUrl();
            $site = $event->getSite();
            $pageId = $event->getPageId();
            $languageId = $event->getLanguageId();

            // Create a new url based on your needs

            $event->setUrl($newUrl);
        }
    }

After you created your event listener to alter the URL, you need to register it in your `Services.yaml`, or if
you are using TYPO3 13, you can use the `AsEventListener` attribute to register the listener directly in your class.

Check the TYPO3 documentation for your used TYPO3 version on how to register this correctly.

After you have registered the listener, you need to clear the TYPO3 cache through the `Maintenance` module
(or `vendor/bin/typo3 cache:flush`) before your code will be activated.
