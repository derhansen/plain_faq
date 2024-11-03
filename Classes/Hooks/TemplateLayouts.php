<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Hooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class TemplateLayouts
{
    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     *
     * @param array $config Configuration array
     */
    public function userTemplateLayout(array &$config)
    {
        $pid = $config['flexParentDatabaseRow']['pid'] ?? 0;
        if ($pid === 0) {
            return;
        }
        $templateLayouts = $this->getTemplateLayoutsFromTsConfig($pid);
        foreach ($templateLayouts as $index => $layout) {
            $additionalLayout = [
                $GLOBALS['LANG']->sL($layout),
                $index,
            ];
            array_push($config['items'], $additionalLayout);
        }
    }

    /**
     * Get template layouts defined in TsConfig
     *
     * @param int $pageUid PageUID
     *
     * @return array
     */
    protected function getTemplateLayoutsFromTsConfig(int $pageUid): array
    {
        $templateLayouts = [];
        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        if (isset($pagesTsConfig['tx_plainfaq.']['templateLayouts.']) &&
            is_array($pagesTsConfig['tx_plainfaq.']['templateLayouts.'])
        ) {
            $templateLayouts = $pagesTsConfig['tx_plainfaq.']['templateLayouts.'];
        }

        return $templateLayouts;
    }
}
