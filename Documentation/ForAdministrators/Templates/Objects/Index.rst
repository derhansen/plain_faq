.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt


.. _template_objects:

Template objects
================

The following objects are available in the different views.

Please have a look at the templates included with the extension, since they show many of the properties
of the given objects and how to use them.

Tip: You can use <f:debug>{object}</f:debug> in your template to see available properties of each object.

Plugin: FAQs
------------

List view
~~~~~~~~~

.. t3-field-list-table::
 :header-rows: 1

 - :Object:
         Object:

   :Description:
         Description:

 - :Object:
         {faqs}

   :Description:
         An object holding all FAQs that matched the configured demand in the plugin settings

 - :Object:
         {faqDemand}

   :Description:
         The faqDemand object

 - :Object:
         {overwriteDemand}

   :Description:
         The overwriteDemand object

 - :Object:
         {contentObjectData}

   :Description:
         The current content object of the plugin

 - :Object:
         {pageData}

   :Description:
         The current page data

Detail view
~~~~~~~~~~~

.. t3-field-list-table::
 :header-rows: 1

 - :Object:
         Object:

   :Description:
         Description:

 - :Object:
         {faq}

   :Description:
         An object holding the given FAQ

 - :Object:
         {contentObjectData}

   :Description:
         The current content object of the plugin

 - :Object:
         {pageData}

   :Description:
         The current page data
