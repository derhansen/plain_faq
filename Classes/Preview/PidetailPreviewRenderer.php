<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Preview;

use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PidetailPreviewRenderer extends AbstractPluginPreviewRenderer
{
    /**
     * Renders the content of the plugin preview.
     */
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $data = [];
        $record = $item->getRecord();
        $flexFormData = GeneralUtility::xml2array($record['pi_flexform']);
        if (!is_array($flexFormData)) {
            $flexFormData = [];
        }

        $pluginName = $this->getPluginName($record);

        $this->setPluginPidConfig($data, $flexFormData, 'listPid');

        return $this->renderAsTable($data, $pluginName);
    }
}
