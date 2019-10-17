<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function () {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_plainfaq_domain_model_faq');

        /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'ext-plainfaq-faq',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:plain_faq/Resources/Public/Icons/tx_plainfaq_domain_model_faq.svg']
        );
    }
);
