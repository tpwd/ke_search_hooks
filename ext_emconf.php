<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Faceted Search Hooks Example',
    'description' => 'Hooks example for ke_search. Feel free to use this as a kickstarter for your own custom indexer or hooks. Implements a news indexer as example.',
    'category' => 'backend',
    'version' => '4.0.0',
    'dependencies' => 'ke_search',
    'state' => 'stable',
    'author' => 'ke_search Dev Team',
    'author_email' => 'cb@tpwd.de',
    'author_company' => 'TPWD GmbH',
    'constraints' => array(
        'depends' => array(
            'typo3' => '10.4.0-11.5.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
