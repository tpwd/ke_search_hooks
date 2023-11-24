.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. _start:

===============
ke_search_hooks
===============

ke_search_hooks contains an example for extending the TYPO3 ke_search with
a custom indexer and other function using the hooks ke_search provides.

Please feel free to use it as a kickstarter for your own indexer.

If you find bugs or want to ask for a feature, please use
https://github.com/tpwd/ke_search_hooks/issues

**Note:** This is the version for ke_search version 4 and above. (The namespace
of ke_search has changed in version 4.)

Included examples
-----------------

The hooks are registered in the file `ext_localconf.php` and point to the PHP
class which implements the function itself.

* Custom Indexer: Indexes records from the extension "News" (ext:news)
* Hook for addtional content fields: Indexes additional fields from the
  tt_content table, e.g. the subheader
* Hook for a check if a content element should be indexed at all
* Hook to add a custom autosuggest provider (ke_search_premium feature)
* Hook to add custom values to the result row partial
* Hook to change the sorting
* Hook to modify the values of the record which will be stored in the index
* Hook for a custom filter renderer
* Hook to register additional fields in the index table
* Example for showing images of fe_users if you have implemented
  a fe_users indexer
