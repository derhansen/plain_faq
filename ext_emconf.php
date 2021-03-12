<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Plain FAQ',
    'description' => 'Plain FAQ extension with list and detail view. Migration commands to migrate data and plugin settings from ext:irfaq',
    'category' => 'plugin',
    'author' => 'Torben Hansen',
    'author_email' => 'torben@derhansen.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '3.0.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '11.1.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
