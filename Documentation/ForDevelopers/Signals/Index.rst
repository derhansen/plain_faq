﻿.. ==================================================
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

 - :Class:
         Derhansen\\PlainFaq\\Domain\\Repository\\FaqRepository

   :Name:
         findDemandedModifyQueryConstraints

   :Arguments:
         &$constraints, $query, $faqDemand, $this

   :Description:
         Signal is called after all query constraints are collected. The signal enables the possibility to add/modify
         the query constraints for the findDemanded function. Very usefull, when you extend the faqDemand with custom
         properties.