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

class PilistdetailPreviewRenderer extends AbstractPluginPreviewRenderer
{
    /**
     * Renders the content of the plugin preview.
     */
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $data = [];
        $record = $item->getRecord();
        $flexFormData = GeneralUtility::xml2array($record['pi_flexform']);

        $pluginName = $this->getPluginName($record);

        $this->setStoragePage($data, $flexFormData, 'settings.storagePage');

        $this->setOrderSettings($data, $flexFormData, 'settings.orderField', 'settings.orderDirection');
        $this->setOverrideDemandSettings($data, $flexFormData);

        $this->setCategoryConjuction($data, $flexFormData);
        $this->setCategorySettings($data, $flexFormData);

        return $this->renderAsTable($data, $pluginName);
    }
}
