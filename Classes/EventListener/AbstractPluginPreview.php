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
    protected const LLPATH = 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:';

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
        return htmlspecialchars($this->getLanguageService()->sL(self::LLPATH . 'plugin.' . $pluginId . '.title'));
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
        $template = GeneralUtility::getFileAbsFileName('EXT:plain_faq/Resources/Private/Backend/PageLayoutView.html');
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
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.' . $pidSetting),
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
                $text = htmlspecialchars($this->getLanguageService()->sL(
                    self::LLPATH . 'flexforms.plugin.field.categoryConjunction.' . $categoryConjunction
                ));
                break;
            default:
                $text = htmlspecialchars($this->getLanguageService()->sL(
                    self::LLPATH . 'flexforms.plugin.field.categoryConjunction.ignore'
                ));
                $text .= ' <span class="badge badge-warning">' . htmlspecialchars($this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.categories.possibleMisconfiguration')) . '</span>';
        }

        $data[] = [
            'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.categoryConjunction'),
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
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.categories'),
                'value' => implode(', ', $categoriesOut),
            ];

            $includeSubcategories = $this->getFlexFormFieldValue($flexFormData, 'settings.includeSubcategories');
            if ((int)$includeSubcategories === 1) {
                $data[] = [
                    'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.includeSubcategories'),
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
            $recursiveLevelText = '';
            if ($recursiveLevel === 250) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:recursive.I.5');
            } elseif ($recursiveLevel > 0) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:recursive.I.' . $recursiveLevel);
            }

            if (!empty($recursiveLevelText)) {
                $recursiveLevelText = '<br />' .
                    htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.recursive')) . ' ' .
                    $recursiveLevelText;
            }

            $data[] = [
                'title' => $this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.startingpoint'),
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
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.disableOverwriteDemand'),
                'value' => 'icon',
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
            $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.orderField.' . $orderField);

            // Order direction (asc, desc)
            $orderDirection = $this->getOrderDirectionSetting($flexFormData, $orderDirectionField);
            if ($orderDirection) {
                $text .= ', ' . strtolower($orderDirection);
            }

            $data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.orderField'),
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
            $text = $this->getLanguageService()->sL(
                self::LLPATH . 'flexforms.plugin.field.orderDirection.' . $orderDirection . 'ending'
            );
        }

        return $text;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
