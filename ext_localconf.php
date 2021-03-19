<?php

defined('TYPO3_MODE') or die();

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'PlainFaq',
            'Pilist',
            [
                \Derhansen\PlainFaq\Controller\FaqController::class => 'list'
            ],
            // non-cacheable actions
            []
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'PlainFaq',
            'Pidetail',
            [
                \Derhansen\PlainFaq\Controller\FaqController::class => 'detail'
            ],
            // non-cacheable actions
            []
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'PlainFaq',
            'Pilistdetail',
            [
                \Derhansen\PlainFaq\Controller\FaqController::class => 'list, detail'
            ],
            // non-cacheable actions
            []
        );

        // DataHandler hooks
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['Derhansen.plain_faq'] =
            \Derhansen\PlainFaq\Hooks\DataHandlerHooks::class;

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['faq_clearcache'] =
            \Derhansen\PlainFaq\Hooks\DataHandlerHooks::class . '->clearCachePostProc';

        // Page layout hooks to show preview of plugins
        foreach (['pilistdetail', 'pilist', 'pidetail'] as $plugin) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['plainfaq_' . $plugin]['faq'] =
                \Derhansen\PlainFaq\Hooks\PageLayoutView::class . '->getPluginSummary';
        }

        // Register switchableControllerActions plugin migrator
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['switchableControllerActionsPluginUpdater']
            = \Derhansen\PlainFaq\Updates\SwitchableControllerActionsPluginUpdater::class;

        // Icon Registry
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'plain_faq-plugin-pi1',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:plain_faq/ext_icon.svg']
        );
    }
);
