<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Plain FAQ',
    'description' => 'Plain FAQ extension with list and detail view. Migration commands to migrate data and plugin settings from ext:irfaq',
    'category' => 'plugin',
    'author' => 'Torben Hansen',
    'author_email' => 'torben@derhansen.com',
    'state' => 'stable',
    'version' => '3.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
