<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Plain FAQ',
    'description' => 'Plain FAQ extension with list and detail view. Migration commands to migrate data and plugin settings from ext:irfaq',
    'category' => 'plugin',
    'author' => 'Torben Hansen',
    'author_email' => 'torben@derhansen.com',
    'state' => 'stable',
    'version' => '5.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
