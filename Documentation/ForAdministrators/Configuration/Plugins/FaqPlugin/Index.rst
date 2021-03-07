.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../../Includes.txt


.. _faqplugin-settings:

FAQs
====

Nearly all important settings can be made through the plugin, which override the
settings made with TypoScript. All plugin settings can also be configured with TypoScript
(use ``plugin.tx_plainfaq_pi1.settings.`` with the keys shown below).

Tab settings
~~~~~~~~~~~~

.. t3-field-list-table::
 :header-rows: 1

 - :Property:
         Property:

   :View:
         View:

   :Description:
         Description:

   :Key:
         Key:


 - :Property:
         What to display

   :View:
         All

   :Description:
         Which view should be shown on the given page.

         Available options:

         * List view
         * List view (without overloading detail view)
         * Detail view

   :Key:

 - :Property:
         Sort by

   :View:
         List

   :Description:
         Defines which field should be used for sorting FAQs in the frontend. The default sorting field is
         "none", which can be overridden by using this setting.

   :Key:
         orderField

 - :Property:
         Sorting direction

   :View:
         List

   :Description:
         Defines the sorting direction for orderField. The default sorting direction is
         "asc", which can be overridden by using this setting.

         Possible values:

         * <empty value>
         * asc
         * desc

   :Key:
         orderDirection

 - :Property:
         Max records displayed

   :View:
         List

   :Description:
         Maximum amount of FAQ articles to return

   :Key:
         queryLimit

 - :Property:
         Category mode

   :View:
         List

   :Description:
         This setting defines, how categories are taken into account when selecting FAQs.

         The following options are available:

         * Ignore category selection
         * Show FAQs with selected categories (OR)
         * Show FAQs with selected categories (AND)
         * Do NOT show FAQs with selected categories (NOTOR)
         * Do NOT show FAQs with selected categories (NOTAND)

   :Key:
         categoryConjunction

 - :Property:
         Category

   :View:
         List

   :Description:
         Restrict FAQs to be shown by one or more category

   :Key:
         category

 - :Property:
         Include subcategory

   :View:
         List

   :Description:
         Includes subcategories of the selected category

   :Key:
         includeSubcategories

 - :Property:
         Record storage page

   :View:
         List

   :Description:
         One or more sysfolders, where FAQs are stored

   :Key:
         storagePage

Tab additional
~~~~~~~~~~~~~~

.. t3-field-list-table::
 :header-rows: 1

 - :Property:
         Detail Page

   :View:
         List

   :Description:
         Page, where plugin is configured to show FAQ details

   :Key:
         detailPid

 - :Property:
         List Page

   :View:
         Details

   :Description:
         Page, where the listview for FAQs is shown. Only available,
         when the plugin is configured to show FAQ details.

   :Key:
         listPid

 - :Property:
         Disable Override demand

   :View:
         List

   :Description:
         If set, the settings of the plugin can't be overridden by arguments in the URL.

   :Key:
         disableOverrideDemand

Tab pagination
~~~~~~~~~~~~~~

.. t3-field-list-table::
 :header-rows: 1

 - :Property:
         Enable pagination

   :View:
         List

   :Description:
         If checked, pagination will be generated and pagination variables will be given to the view.

   :Key:
         enablePagination

 - :Property:
         Items per page

   :View:
         List

   :Description:
         Items per page for the pagination

   :Key:
         itemsPerPage

 - :Property:
         Maximum number of pages

   :View:
         List

   :Description:
         Maximum number of pages

   :Key:
         maxNumPages

Tab template
~~~~~~~~~~~~

.. t3-field-list-table::
 :header-rows: 1

 - :Property:
         Property:

   :View:
         View:

   :Description:
         Description:

   :Key:
         Key:

 - :Property:
         Template layout

   :View:
         List

   :Description:
         With this setting the plugin can be configured to show different template layouts.

         * Template layouts can be configured with Page TSConfig.
         * Template layout can be used/set by TypoScript (settings.templateLayout)

   :Key:
         templateLayout
