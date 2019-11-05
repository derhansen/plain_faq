.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt


.. _changing_templates:

Changing paths of the template
==============================

Please do never change templates directly in the Ressources folder of the extensions,
since your changes will get overwritten by extension updates.

Configure your TypoScript setup like shown below (note the **plural** of the path-name)::

  plugin.tx_plainfaq.pi1 {
    view {
      templateRootPaths {
        0 = {$plugin.tx_plainfaq_pi1.view.templateRootPath}
        1 = fileadmin/templates/faq/Templates/
      }
      partialRootPaths {
        0 = {$plugin.tx_plainfaq_pi1.view.partialRootPath}
        1 = fileadmin/templates/faq/Partials/
      }
      layoutRootPaths {
        0 = {$plugin.tx_plainfaq_pi1.view.layoutRootPath}
        1 = fileadmin/templates/faq/Layouts/
      }
    }
  }

Doing so, you can just **override single files** from the original templates.
