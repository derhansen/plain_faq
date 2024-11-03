<?php

defined('TYPO3') or die();

// Override FAQ icon
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    'label' =>  'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:faq-folder',
    'value' => 'faqs',
    'icon' => 'apps-pagetree-folder-contains-faqs',
];

$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-faqs'] = 'apps-pagetree-folder-contains-faqs';
