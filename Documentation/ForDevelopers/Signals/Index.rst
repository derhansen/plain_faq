.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _signals:

Signal slots
============

Signals
-------

.. t3-field-list-table::
 :header-rows: 1

 - :Class:
         Class:

   :Name:
         Name:

   :Arguments:
         Arguments:

   :Description:
         Description:

 - :Class:
         Derhansen\\PlainFaq\\Controller\\FaqController

   :Name:
         listActionBeforeRenderView

   :Arguments:
         &$values, $this

   :Description:
         Signal is called before rendering the list view. An array with all view values is passed by reference.

 - :Class:
         Derhansen\\PlainFaq\\Controller\\FaqController

   :Name:
         detailActionBeforeRenderView

   :Arguments:
         &$values, $this

   :Description:
         Signal is called before rendering the calendar view. An array with all view values is passed by reference.