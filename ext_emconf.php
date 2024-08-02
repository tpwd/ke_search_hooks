<?php

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = array(
    'title' => 'Faceted Search Hooks Example',
    'description' => 'Hooks example for ke_search. Feel free to use this as a kickstarter for your own custom indexer or hooks. Implements a news indexer as example.',
    'category' => 'backend',
    'version' => '5.1.0',
    'dependencies' => 'ke_search',
    'state' => 'stable',
    'author' => 'ke_search Dev Team',
    'author_email' => 'ke_search@tpwd.de',
    'author_company' => 'TPWD AG',
    'constraints' => array(
        'depends' => array(
            'typo3' => '11.5.0-12.4.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
