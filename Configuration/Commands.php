<?php

return [
    'plain_faq:migrate_categories' => [
        'class' => \Derhansen\PlainFaq\Command\MigrateCategoriesCommand::class
    ],
    'plain_faq:migrate_faqs' => [
        'class' => \Derhansen\PlainFaq\Command\MigrateFaqsCommand::class
    ],
    'plain_faq:migrate_plugins' => [
        'class' => \Derhansen\PlainFaq\Command\MigratePluginsCommand::class
    ]
];
