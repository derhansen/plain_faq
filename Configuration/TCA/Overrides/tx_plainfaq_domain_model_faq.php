<?php

defined('TYPO3') or die();

$tableName = 'tx_plainfaq_domain_model_faq';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'plain_faq',
    $tableName
);

// Enable language synchronisation for the categories field
$GLOBALS['TCA'][$tableName]['columns']['categories']['config']['behaviour']['allowLanguageSynchronization'] = true;
