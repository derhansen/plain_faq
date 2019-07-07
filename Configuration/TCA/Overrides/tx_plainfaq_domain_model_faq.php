<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'plain_faq',
    'tx_plainfaq_domain_model_faq'
);
