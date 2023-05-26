<?php

defined('TYPO3') or die();

use Derhansen\PlainFaq\Preview\PidetailPreviewRenderer;
use Derhansen\PlainFaq\Preview\PilistdetailPreviewRenderer;
use Derhansen\PlainFaq\Preview\PilistPreviewRenderer;

/**
 * Add new select group for list_type
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
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
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'PlainFaq',
        ucfirst($plugin),
        'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:plugin.' . $plugin . '.title',
        'plainfaq-default',
        'plainfaq'
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['plainfaq_' . $plugin] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'plainfaq_' . $plugin,
        'FILE:EXT:plain_faq/Configuration/FlexForms/' . ucfirst($plugin) . '.xml'
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['plainfaq_' . $plugin] = 'layout,select_key,pages,recursive';
}

/**
 * Register plugin preview renderer
 */
$GLOBALS['TCA']['tt_content']['types']['list']['previewRenderer']['plainfaq_pilist'] = PilistPreviewRenderer::class;
$GLOBALS['TCA']['tt_content']['types']['list']['previewRenderer']['plainfaq_pidetail'] = PidetailPreviewRenderer::class;
$GLOBALS['TCA']['tt_content']['types']['list']['previewRenderer']['plainfaq_pilistdetail'] = PilistdetailPreviewRenderer::class;
