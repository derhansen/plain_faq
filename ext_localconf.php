<?php

defined('TYPO3') or die();

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'PlainFaq',
            'Pilist',
            [
                \Derhansen\PlainFaq\Controller\FaqController::class => 'list',
            ],
            // non-cacheable actions
            []
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'PlainFaq',
            'Pidetail',
            [
                \Derhansen\PlainFaq\Controller\FaqController::class => 'detail',
            ],
            // non-cacheable actions
            []
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'PlainFaq',
            'Pilistdetail',
            [
                \Derhansen\PlainFaq\Controller\FaqController::class => 'list, detail',
            ],
            // non-cacheable actions
            []
        );

        // DataHandler hooks
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['Derhansen.plain_faq'] =
            \Derhansen\PlainFaq\Hooks\DataHandlerHooks::class;

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['faq_clearcache'] =
            \Derhansen\PlainFaq\Hooks\DataHandlerHooks::class . '->clearCachePostProc';
    }
);
