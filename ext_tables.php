<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Derhansen.PlainFaq',
            'Pi1',
            'FAQs'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('plain_faq', 'Configuration/TypoScript', 'Plain FAQ');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_plainfaq_domain_model_faq');

    }
);
