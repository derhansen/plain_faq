.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _signals:

PSR-14 Events
=============

The extensions contains many PSR-14 Events which make it possible to extend the extension with own functionality.

Please note, that there is no documentation for each PSR-14 event in detail, so you have to check each event
individually for supported properties. Generally I tried to make the events as self explaining as possible.

If you are new to PSR-14 Events, please reffer to the official TYPO3 documentation about
PSR-14 Events and Event Listeners.

https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Hooks/EventDispatcher/Index.html

The following PSR-14 Events are available:

Faq Controller
--------------

* ModifyListViewVariablesEvent
* ModifyDetailViewVariablesEvent

Faq Repository
--------------

* ModifyFaqQueryConstraintsEvent
