<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$rteImageSoftref = ExtensionManagementUtility::isLoaded('rte_ckeditor_image') ? ',rtehtmlarea_images' : '';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plainfaq_domain_model_faq',
        'label' => 'question',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'sortby' => 'sorting',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'searchFields' => 'question,answer,keywords,images,files',
        'typeicon_classes' => [
            'default' => 'ext-plainfaq-faq',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => ['showitem' => '
            question, slug, answer, keywords,

            --div--;LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:tabs.categories,
                categories,

            --div--;LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:tabs.media,
                images, files,

            --div--;LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:tabs.relations,
                related,

            --div--;LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:tabs.language,
                --palette--;;language,

            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
                hidden, starttime, endtime, fe_group',
        ],
    ],
    'palettes' => [
        'language' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource'],
    ],
    'columns' => [
        'categories' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plainfaq_domain_model_faq.categories',
            'config' => [
                'type' => 'category',
            ],
        ],

        'question' => [
            'exclude' => true,
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plainfaq_domain_model_faq.question',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'slug' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:pages.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['question'],
                    'replacements' => [
                        '/' => '-',
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
            ],
        ],
        'answer' => [
            'exclude' => true,
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plainfaq_domain_model_faq.answer',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'softref' => 'typolink_tag,email[subst],url' . $rteImageSoftref,
            ],
        ],
        'keywords' => [
            'exclude' => true,
            'label' => 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plainfaq_domain_model_faq.keywords',
            'config' => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 5,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'images' => [
            'exclude' => true,
            'label' => 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plainfaq_domain_model_faq.images',
            'config' => [
                'type' => 'file',
                'maxitems' => 999,
                'allowed' => 'common-image-types',
            ],
        ],
        'files' => [
            'exclude' => true,
            'label' => 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plainfaq_domain_model_faq.files',
            'config' => [
                'type' => 'file',
            ],
        ],
        'related' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plainfaq_domain_model_faq.related',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_plainfaq_domain_model_faq',
                'foreign_table' => 'tx_plainfaq_domain_model_faq',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 100,
                'MM' => 'tx_plainfaq_domain_model_faq_related_mm',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],

    ],
];
