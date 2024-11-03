<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'plain_faq',
    'Configuration/TypoScript',
    'Plain FAQ'
);
