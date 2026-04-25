<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\EventListener;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

abstract class AbstractPluginPreview
{
    public function __construct(
        protected readonly IconFactory $iconFactory,
        protected readonly PageRenderer $pageRenderer,
        protected readonly ViewFactoryInterface $viewFactory
    ) {
        $pageRenderer->addCssFile('EXT:plain_faq/Resources/Public/Css/Backend/PageLayoutView.css');
    }

    /**
     * Returns the plugin name
     */
    protected function getPluginName(array $record): string
    {
        $pluginId = str_replace('plainfaq_', '', $record['CType']);
        return htmlspecialchars($this->getLanguageService()->translate('plugin.' . $pluginId . '.title', 'plain_faq.be') ?? '');
    }

    /**
     * Returns the records flexform as array
     */
    protected function getFlexFormData(string $flexform): array
    {
        $flexFormData = GeneralUtility::xml2array($flexform);
        if (!is_array($flexFormData)) {
            $flexFormData = [];
        }
        return $flexFormData;
    }

    /**
     * Renders the given data and action as HTML table for plugin preview
     */
    protected function renderAsTable(ServerRequestInterface $request, array $data, string $pluginName = ''): string
    {
        $template = GeneralUtility::getFileAbsFileName('EXT:plain_faq/Resources/Private/Backend/PageLayoutView.fluid.html');
        $viewFactoryData = new ViewFactoryData(
            templatePathAndFilename: $template,
            request: $request,
        );
        $view = $this->viewFactory->create($viewFactoryData);
        $view->assignMultiple([
            'data' => $data,
            'pluginName' => $pluginName,
        ]);

        return $view->render();
    }

    /**
     * Sets the PID config for the configured PID settings in plugin flexform
     */
    protected function setPluginPidConfig(
        array &$data,
        array $flexFormData,
        string $pidSetting,
        string $sheet = 'sDEF'
    ): void {
        $pid = (int)$this->getFlexFormFieldValue($flexFormData, 'settings.' . $pidSetting, $sheet);
        if ($pid > 0) {
            $data[] = [
                'title' => $this->getLanguageService()->translate('flexforms.plugin.field.' . $pidSetting, 'plain_faq.be'),
                'value' => $this->getRecordData($pid),
            ];
        }
    }

    /**
     * Sets category conjunction if a category is selected
     */
    protected function setCategoryConjuction(array &$data, array $flexFormData): void
    {
        // If not category is selected, we do not need to display the category mode
        $categories = $this->getFlexFormFieldValue($flexFormData, 'settings.categories');
        if ($categories === null || $categories === '') {
            return;
        }

        $categoryConjunction = strtolower($this->getFlexFormFieldValue($flexFormData, 'settings.categoryConjunction') ?? '');
        switch ($categoryConjunction) {
            case 'or':
            case 'and':
            case 'notor':
            case 'notand':
                $text = htmlspecialchars((string)$this->getLanguageService()->translate(
                    'flexforms.plugin.field.categoryConjunction.' . $categoryConjunction,
                    'plain_faq.be'
                ));
                break;
            default:
                $text = htmlspecialchars((string)$this->getLanguageService()->translate(
                    'flexforms.plugin.field.categoryConjunction.ignore',
                    'plain_faq.be'
                ));
                $text .= ' <span class="badge badge-warning">' . htmlspecialchars((string)$this->getLanguageService()->translate('flexforms.plugin.field.categories.possibleMisconfiguration', 'plain_faq.be')) . '</span>';
        }

        $data[] = [
            'title' => $this->getLanguageService()->translate('flexforms.plugin.field.categoryConjunction', 'plain_faq.be'),
            'value' => $text,
        ];
    }

    /**
     * Get category settings
     */
    protected function setCategorySettings(array &$data, array $flexFormData): void
    {
        $categories = GeneralUtility::intExplode(',', $this->getFlexFormFieldValue($flexFormData, 'settings.categories'), true);
        if (count($categories) > 0) {
            $categoriesOut = [];
            foreach ($categories as $id) {
                $categoriesOut[] = $this->getRecordData($id, 'sys_category');
            }

            $data[] = [
                'title' => $this->getLanguageService()->translate('flexforms.plugin.field.categories', 'plain_faq.be'),
                'value' => implode(', ', $categoriesOut),
            ];

            $includeSubcategories = $this->getFlexFormFieldValue($flexFormData, 'settings.includeSubcategories');
            if ((int)$includeSubcategories === 1) {
                $data[] = [
                    'title' => $this->getLanguageService()->translate('flexforms.plugin.field.includeSubcategories', 'plain_faq.be'),
                    'value' => 'icon',
                    'icon' => 'actions-check-square',
                ];
            }
        }
    }

    /**
     * Sets the storagePage configuration
     */
    protected function setStoragePage(array &$data, array $flexFormData, string $field): void
    {
        $value = $this->getFlexFormFieldValue($flexFormData, $field);

        if (!empty($value)) {
            $pageIds = GeneralUtility::intExplode(',', $value, true);
            $pagesOut = [];

            foreach ($pageIds as $id) {
                $pagesOut[] = $this->getRecordData($id, 'pages');
            }

            $recursiveLevel = (int)$this->getFlexFormFieldValue($flexFormData, 'settings.recursive');
            $recursiveLevelText = null;
            if ($recursiveLevel === 250) {
                $recursiveLevelText = $this->getLanguageService()->translate('recursive.I.5', 'frontend.ttc');
            } elseif ($recursiveLevel > 0) {
                $recursiveLevelText = $this->getLanguageService()->translate('recursive.I.' . $recursiveLevel, 'frontend.ttc');
            }

            if ($recursiveLevelText) {
                $recursiveLevelText = ' <em>(' .
                    htmlspecialchars((string)$this->getLanguageService()->translate('LGL.recursive', 'core.general')) . ' ' .
                    $recursiveLevelText . ')</em>';
            }

            $data[] = [
                'title' => $this->getLanguageService()->translate('LGL.startingpoint', 'core.general'),
                'value' => implode(', ', $pagesOut) . $recursiveLevelText,
            ];
        }
    }

    /**
     * Sets information to the data array if override demand setting is disabled
     */
    protected function setOverrideDemandSettings(array &$data, array $flexFormData): void
    {
        $field = (int)$this->getFlexFormFieldValue($flexFormData, 'settings.disableOverwriteDemand', 'additional');

        if ($field === 1) {
            $data[] = [
                'title' => $this->getLanguageService()->translate('flexforms.plugin.field.disableOverwriteDemand', 'plain_faq.be'),
                'value' => '',
                'icon' => 'actions-check-square',
            ];
        }
    }
    /**
     * Sets the order settings
     */
    protected function setOrderSettings(
        array &$data,
        array $flexFormData,
        string $orderByField,
        string $orderDirectionField
    ): void {
        $orderField = $this->getFlexFormFieldValue($flexFormData, $orderByField);
        if (!empty($orderField)) {
            $text = $this->getLanguageService()->translate('flexforms.plugin.field.orderField.' . $orderField, 'plain_faq.be');

            // Order direction (asc, desc)
            $orderDirection = $this->getOrderDirectionSetting($flexFormData, $orderDirectionField);
            if ($orderDirection) {
                $text .= ', ' . strtolower($orderDirection);
            }

            $data[] = [
                'title' => $this->getLanguageService()->translate('flexforms.plugin.field.orderField', 'plain_faq.be'),
                'value' => $text,
            ];
        }
    }

    /**
     * Returns field value from flexform configuration, including checks if flexform configuration is available
     */
    protected function getFlexFormFieldValue(array $flexformData, string $key, string $sheet = 'sDEF'): ?string
    {
        return $flexformData['data'][$sheet]['lDEF'][$key]['vDEF'] ?? '';
    }

    /**
     * Returns the record data item
     */
    protected function getRecordData(int $id, string $table = 'pages'): string
    {
        $content = '';
        $record = BackendUtility::getRecord($table, $id);

        if (is_array($record)) {
            $data = '<span data-toggle="tooltip" data-placement="top" data-title="id=' . $record['uid'] . '">'
                . $this->iconFactory->getIconForRecord($table, $record, IconSize::SMALL)->render()
                . '</span> ';
            $content = BackendUtility::wrapClickMenuOnIcon($data, $table, $record['uid'], '', $record);

            $linkTitle = htmlspecialchars(BackendUtility::getRecordTitle($table, $record));
            $content .= $linkTitle;
        }

        return $content;
    }

    /**
     * Returns order direction
     */
    private function getOrderDirectionSetting(array $flexFormData, string $orderDirectionField): string
    {
        $text = '';

        $orderDirection = $this->getFlexFormFieldValue($flexFormData, $orderDirectionField);
        if (!empty($orderDirection)) {
            $text = $this->getLanguageService()->translate(
                'flexforms.plugin.field.orderDirection.' . $orderDirection . 'ending',
                'plain_faq.be'
            );
        }

        return $text;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
