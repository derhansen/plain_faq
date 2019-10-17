<?php
defined('TYPO3_MODE') or die();

// Override events icon
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    0 => 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:faq-folder',
    1 => 'faqs',
    2 => 'apps-pagetree-folder-contains-faqs'
];

$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-faqs'] = 'apps-pagetree-folder-contains-faqs';
