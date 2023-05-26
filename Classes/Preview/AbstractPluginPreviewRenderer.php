<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Preview;

use TYPO3\CMS\Backend\Preview\PreviewRendererInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

abstract class AbstractPluginPreviewRenderer implements PreviewRendererInterface
{
    protected const LLPATH_PLAINFAQ = 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:';
    protected const LLPATH_CORE = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';
    protected const LLPATH_FRONTEND = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';

    protected IconFactory $iconFactory;

    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addCssFile('EXT:plain_faq/Resources/Public/Css/Backend/PageLayoutView.css');
    }

    /**
     * Renders the header (actually empty, since header is rendered in content)
     */
    public function renderPageModulePreviewHeader(GridColumnItem $item): string
    {
        return '';
    }

    /**
     * Renders the content of the plugin preview. Must be overwritten in extending class.
     */
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        return '';
    }

    /**
     * Render the footer. Can be overwritten in extending class if required
     */
    public function renderPageModulePreviewFooter(GridColumnItem $item): string
    {
        return '';
    }

    /**
     * Render the plugin preview
     */
    public function wrapPageModulePreview(string $previewHeader, string $previewContent, GridColumnItem $item): string
    {
        return $previewHeader . $previewContent;
    }

    /**
     * Returns the plugin name
     */
    protected function getPluginName(array $record): string
    {
        $pluginId = str_replace('plainfaq_', '', $record['list_type']);
        return htmlspecialchars($this->getLanguageService()->sL(self::LLPATH_PLAINFAQ . 'plugin.' . $pluginId . '.title'));
    }

    /**
     * Renders the given data and action as HTML table for plugin preview
     */
    protected function renderAsTable(array $data, string $pluginName = ''): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:plain_faq/Resources/Private/Backend/PageLayoutView.html')
        );
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
                'title' => $this->getLanguageService()->sL(self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.' . $pidSetting),
                'value' => $this->getRecordData($pid),
            ];
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
                $recursiveLevelText = $this->getLanguageService()->sL(self::LLPATH_FRONTEND . 'recursive.I.5');
            } elseif ($recursiveLevel > 0) {
                $recursiveLevelText = $this->getLanguageService()->sL(self::LLPATH_FRONTEND . 'recursive.I.' . $recursiveLevel);
            }

            if (!empty($recursiveLevelText)) {
                $recursiveLevelText = '<br />' .
                    htmlspecialchars($this->getLanguageService()->sL(self::LLPATH_CORE . 'LGL.recursive')) . ' ' .
                    $recursiveLevelText;
            }

            $data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH_CORE . 'LGL.startingpoint'),
                'value' => implode(', ', $pagesOut) . $recursiveLevelText,
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
                    self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.categoryConjunction.' . $categoryConjunction
                ));
                break;
            default:
                $text = htmlspecialchars($this->getLanguageService()->sL(
                    self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.categoryConjunction.ignore'
                ));
                $text .= ' <span class="badge badge-warning">' . htmlspecialchars($this->getLanguageService()->sL(self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.categories.possibleMisconfiguration')) . '</span>';
        }

        $data[] = [
            'title' => $this->getLanguageService()->sL(self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.categoryConjunction'),
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
                'title' => $this->getLanguageService()->sL(self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.categories'),
                'value' => implode(', ', $categoriesOut),
            ];

            $includeSubcategories = $this->getFlexFormFieldValue($flexFormData, 'settings.includeSubcategories');
            if ((int)$includeSubcategories === 1) {
                $data[] = [
                    'title' => $this->getLanguageService()->sL(self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.includeSubcategories'),
                    'value' => 'icon',
                    'icon' => 'actions-check-square',
                ];
            }
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
                'title' => $this->getLanguageService()->sL(self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.disableOverwriteDemand'),
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
            $text = $this->getLanguageService()->sL(self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.orderField.' . $orderField);

            // Order direction (asc, desc)
            $orderDirection = $this->getOrderDirectionSetting($flexFormData, $orderDirectionField);
            if ($orderDirection) {
                $text .= ', ' . strtolower($orderDirection);
            }

            $data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.orderField'),
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
                . $this->iconFactory->getIconForRecord($table, $record, Icon::SIZE_SMALL)->render()
                . '</span> ';
            $content = BackendUtility::wrapClickMenuOnIcon($data, $table, $record['uid'], '');

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
                self::LLPATH_PLAINFAQ . 'flexforms.plugin.field.orderDirection.' . $orderDirection . 'ending'
            );
        }

        return $text;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
