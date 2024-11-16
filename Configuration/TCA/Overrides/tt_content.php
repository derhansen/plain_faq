<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Add new select group for list_type
 */
ExtensionManagementUtility::addTcaSelectItemGroup(
    'tt_content',
    'list_type',
    'plainfaq',
    'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:CType.div.plugingroup',
    'after:default'
);

/**
 * Register plugins, flexform and remove unused fields
 */
foreach (['pilistdetail', 'pilist', 'pidetail'] as $plugin) {
    $contentTypeName = 'plainfaq_' . strtolower($plugin);
    ExtensionUtility::registerPlugin(
        'PlainFaq',
        ucfirst($plugin),
        'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:plugin.' . $plugin . '.title',
        'plainfaq-default',
        'plainfaq'
    );

    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:plain_faq/Configuration/FlexForms/' . ucfirst($plugin) . '.xml',
        $contentTypeName
    );

    $GLOBALS['TCA']['tt_content']['types'][$contentTypeName]['showitem'] = '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
            pi_flexform,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ';
}
