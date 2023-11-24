<?php
// Add your custom indexer here in order to make the "sysfolder" field visible when your custom indexer is selected to
// make it possible to select the folder where the records which should be indexed are stored.
$GLOBALS['TCA']['tx_kesearch_indexerconfig']['columns']['sysfolder']['displayCond']
    .= ',' . \Tpwd\KeSearchHooks\ExampleIndexer::KEY;
