<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Add new select group for list_type
 */
ExtensionManagementUtility::addTcaSelectItemGroup(
    'tt_content',
    'CType',
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
        'plainfaq',
        'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:plugin.' . $plugin . '.description',
        'FILE:EXT:plain_faq/Configuration/FlexForms/' . ucfirst($plugin) . '.xml',
    );
}
