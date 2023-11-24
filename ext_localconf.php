<?php

defined('TYPO3') or die();

(function () {
    // Register a custom indexer
    // Adjust this to your namespace and class name.
    // Adjust the autoloading information in composer.json, too!
    // see also Configuration/TCA/Overrides/tx_kesearch_indexerconfig.php
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerIndexerConfiguration'][] =
        \Tpwd\KeSearchHooks\ExampleIndexer::class;
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] =
        \Tpwd\KeSearchHooks\ExampleIndexer::class;

    // Register hooks for indexing additional fields.
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyPageContentFields'][] =
        \Tpwd\KeSearchHooks\AdditionalContentFields::class;
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyContentFromContentElement'][] =
        \Tpwd\KeSearchHooks\AdditionalContentFields::class;

    // Register hook to check if a content element should be indexed
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['contentElementShouldBeIndexed'][] =
        \Tpwd\KeSearchHooks\AdditionalContentFields::class;

    // Register hook to add a custom autosuggest provider (ke_search_premium feature)
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search_premium']['modifyAutocompleWordList'][] =
        \Tpwd\KeSearchHooks\AutosuggestProvider::class;

    // Register hook to add custom values to the result row partial
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['additionalResultMarker'][] =
        \Tpwd\KeSearchHooks\AdditionalResultMarker::class;

    // Register hook to change the sorting
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['getOrdering'][] =
        \Tpwd\KeSearchHooks\Ordering::class;

    // Register hook to modify the values of the record which will be stored in the index
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['modifyFieldValuesBeforeStoring'][] =
        \Tpwd\KeSearchHooks\modifyFieldValuesBeforeStoring::class;

    // Register hook for a custom filter renderer
    // This example changes the standard "select" filter to a "radio button" filter
    //$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customFilterRenderer'][] =
        //\Tpwd\KeSearchHooks\ExampleFilterRenderer::class;

    // Register hook to register additional fields in the index table
    // Make sure to set the values for the additional fields in every indexer you use!
    // See also Configuration/TCA/Overrides/tx_kesearch_index.php
    //$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerAdditionalFields'][] =
    //\Tpwd\KeSearchHooks\AdditionalIndexerFields::class;

    // Example for showing images of fe_users if you have implemented a fe_users indexer
    //$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['fileReferenceTypes']['fe_users']['table'] = 'fe_users';
    //$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['fileReferenceTypes']['fe_users']['field'] = 'image';
})();
