.. include:: /Includes.rst.txt


.. _events:

Events
======

Starting with version 12.0 of the extension, events to modify the url or request have been moved to the new
separate package `maxserv/frontend-request` / extension `frontend_request`.

The previously available `ModifyPreviewUrlEvent` has been replaced by `MaxServ\FrontendRequest\Event\ModifyUrlEvent`.

For more information on how to use the new event, please check the documentation of the `maxserv/frontend-request`
package.