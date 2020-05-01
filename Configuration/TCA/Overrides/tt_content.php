<?php

defined('TYPO3_MODE') or die();

/**
 * Plugins
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'PlainFaq',
    'Pi1',
    'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:plugin.pi1.title'
);

/**
 * Remove unused fields
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['plainfaq_pi1'] = 'layout,select_key,pages,recursive';

/**
 * Add Flexform for FAQ plugin
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['plainfaq_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'plainfaq_pi1',
    'FILE:EXT:plain_faq/Configuration/FlexForms/Pi1.xml'
);
