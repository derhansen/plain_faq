.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _migration:

Migration from "irfaq"
======================

If you use the TYPO3 extension "irfaq", then you migrate to "Plain FAQ" using 3 Symfony Console commands.
Note, that the migration has some limits and does not support all fields "irfaq" has.

Migration is done in 3 steps:

Step 1 - Migrate categories
---------------------------

Command::

  ./typo3/sysext/core/bin/typo3 plain_faq:migrate_categories

This command migrates all categories of "irfaq" to TYPO3 sys_categories. Note, that the fields *shortcut* and *fe_group*
are not supported.

The migrator creates a new category on root level called "FAQ". All pages, where "irfaq" categories are found will be
created as child categories of the root "FAQ" category and all "irfaq" categories will be created as child categories
per page

**Example:**

- FAQ
    - Page 1
        - Category 1 on Page 1
        - Category 2 on Page 2
    - Page 2
        - Category 1 on Page 2
        - Category 2 on Page 2
        - ...


Step 2 - Migrate FAQs
---------------------

Command::

  ./typo3/sysext/core/bin/typo3 plain_faq:migrate_faqs

This command simply migrates all "irfaq" to "Plain FAQ" records. The following fields are not supported (mostly due
to features not available in "Plain FAQ")

* q_from
* expert
* related
* related_links
* faq_files
* enable_ratings
* disable_comments
* comments_closetime

Step 3 - Migrate Plugins
------------------------

Command::

  ./typo3/sysext/core/bin/typo3 plain_faq:migrate_plugins

Options:

*  -d, --defaultOrderField[=DEFAULTORDERFIELD]  The default order field when no sort order is defined in the ext:irfaq plugin
*  -p, --pids[=PIDS]

This command migrates all Plugins from "irfaq" to "Plain FAQ" plugins. Note, that existing "irfaq" plugins will simply
be replaced (fields *list_type* and *pi_flexform* will be replaced).

The migration migrates the following settings:

* storagePage
* Recursive
* orderField (if set - if not set, you can specify a defaultOrderField)
* categoryConjunction
* categories


After all 3 migration steps are processed, you should check, if the migration worked as expected. Feel free to
modify the migrator to suite your own needs.
