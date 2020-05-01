<?php

defined('TYPO3_MODE') or die();

call_user_func(
    function () {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_plainfaq_domain_model_faq');

        /**
         * Register icons
         */
        /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $icons = [
            'apps-pagetree-folder-contains-faqs' => 'apps-pagetree-folder-contains-faqs.svg',
            'ext-plainfaq-faq' => 'tx_plainfaq_domain_model_faq.svg',
        ];
        foreach ($icons as $identifier => $path) {
            $iconRegistry->registerIcon(
                $identifier,
                \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
                ['source' => 'EXT:plain_faq/Resources/Public/Icons/' . $path]
            );
        }
    }
);
