<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Derhansen.PlainFaq',
            'Pi1',
            [
                'Faq' => 'list, detail'
            ],
            // non-cacheable actions
            []
        );

        // DataHandler hooks
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['Derhansen.plain_faq'] =
            \Derhansen\PlainFaq\Hooks\DataHandlerHooks::class;

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['faq_clearcache'] =
            \Derhansen\PlainFaq\Hooks\DataHandlerHooks::class . '->clearCachePostProc';

        // Icon Registry
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'plain_faq-plugin-pi1',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:plain_faq/Resources/Public/Icons/user_plugin_pi1.svg']
        );
    }
);
