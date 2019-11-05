.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt


.. _tsref:

TypoScript reference
====================

More TypoScript settings are available on the :ref:`faqplugin-settings` page. Also make sure to check the included
``setup.txt`` file for available/default TypoScript settings.

plugin.tx_plainfaq.pi1
~~~~~~~~~~~~~~~~~~~~~~

.. t3-field-list-table::
 :header-rows: 1

 - :Property:
         Property:

   :Date type:
         Data type:

   :Description:
         Description:

   :Default:
         Default:

 - :Property:
         view.templateRootPath

   :Date type:
         String

   :Description:
         Path to the templates. The default setting is EXT:plain_faq/Resources/Private/Templates/

   :Default:
         **Extbase default**

 - :Property:
         view.partialRootPath

   :Date type:
         String

   :Description:
         Path to the partials. The default setting is EXT:plain_faq/Resources/Private/Partials/

   :Default:
         **Extbase default**

 - :Property:
         view.layoutRootPath

   :Date type:
         String

   :Description:
         Path to the layouts. The default setting is EXT:plain_faq/Resources/Private/Layouts/

   :Default:
         **Extbase default**

 - :Property:
         settings.orderFieldAllowed

   :Date type:
         String

   :Description:
         Comma separated list of fields that are allowed to be set as order fielfs

   :Default:
         uid,question,sorting