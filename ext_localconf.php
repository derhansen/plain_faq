<?php

defined('TYPO3') or die();

use Derhansen\PlainFaq\Controller\FaqController;
use Derhansen\PlainFaq\Hooks\DataHandlerHooks;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin(
    'PlainFaq',
    'Pilist',
    [
        FaqController::class => 'list',
    ],
    // non-cacheable actions
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

ExtensionUtility::configurePlugin(
    'PlainFaq',
    'Pidetail',
    [
        FaqController::class => 'detail',
    ],
    // non-cacheable actions
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

ExtensionUtility::configurePlugin(
    'PlainFaq',
    'Pilistdetail',
    [
        FaqController::class => 'list, detail',
    ],
    // non-cacheable actions
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

// DataHandler hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['Derhansen.plain_faq'] =
    DataHandlerHooks::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['faq_clearcache'] =
    DataHandlerHooks::class . '->clearCachePostProc';
