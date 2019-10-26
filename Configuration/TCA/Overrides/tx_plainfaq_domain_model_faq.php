<?php
defined('TYPO3_MODE') or die();

$tableName = 'tx_plainfaq_domain_model_faq';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'plain_faq',
    $tableName
);

// Enable language synchronisation for the categories field
$GLOBALS['TCA'][$tableName]['columns']['categories']['config']['behaviour']['allowLanguageSynchronization'] = true;

// Register slug field for TYPO3 9.5
if (\Derhansen\PlainFaq\Utility\MiscUtility::isV9Lts()) {
    $faqColumns['slug'] = [
        'exclude' => true,
        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:pages.slug',
        'config' => [
            'type' => 'slug',
            'size' => 50,
            'generatorOptions' => [
                'fields' => ['question'],
                'replacements' => [
                    '/' => '-'
                ],
            ],
            'fallbackCharacter' => '-',
            'eval' => 'uniqueInSite',
            'default' => ''
        ]
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        $tableName,
        $faqColumns
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $tableName,
        'slug',
        '',
        'after:question'
    );
}