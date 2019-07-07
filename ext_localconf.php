<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Derhansen.PlainFaq',
            'Pi1',
            [
                'Faq' => 'list, detail, search'
            ],
            // non-cacheable actions
            [
                
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    pi1 {
                        iconIdentifier = plain_faq-plugin-pi1
                        title = LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plain_faq_pi1.name
                        description = LLL:EXT:plain_faq/Resources/Private/Language/locallang_db.xlf:tx_plain_faq_pi1.description
                        tt_content_defValues {
                            CType = list
                            list_type = plainfaq_pi1
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'plain_faq-plugin-pi1',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:plain_faq/Resources/Public/Icons/user_plugin_pi1.svg']
			);
		
    }
);
