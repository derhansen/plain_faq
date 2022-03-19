<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Hooks;

use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Hook to display verbose information about plugin in Web>Page module
 */
class PageLayoutView
{
    private const LLPATH = 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:';
    public array $flexformData = [];
    public array $data = [];
    protected IconFactory $iconFactory;

    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    /**
     * Returns the plugin summary based on the field "list_type"
     *
     * @param array $params
     * @return string
     */
    public function getPluginSummary(array $params): string
    {
        $flexFormData = GeneralUtility::xml2array($params['row']['pi_flexform']);
        $this->flexformData = is_array($flexFormData) ? $flexFormData : [];

        $result = '';
        switch ($params['row']['list_type']) {
            case 'plainfaq_pilistdetail':
                $result = $this->getPilistdetailPluginSummary();
                break;
            case 'plainfaq_pilist':
                $result = $this->getPilistPluginSummary();
                break;
            case 'plainfaq_pidetail':
                $result = $this->getPidetailPluginSummary();
                break;
        }

        return $result;
    }

    /**
     * Returns plugin summary for "pilistdetail"
     *
     * @return string
     */
    protected function getPilistdetailPluginSummary(): string
    {
        $header = htmlspecialchars($this->getLanguageService()->sL(self::LLPATH . 'plugin.pilistdetail.title'));

        $this->getStoragePage('settings.storagePage');
        $this->getOrderSettings('settings.orderField', 'settings.orderDirection');
        $this->getCategoryConjuction();
        $this->getCategorySettings();
        $this->getPaginationSettings();
        $this->getOverwriteDemandSettings();

        return $this->renderSettingsAsTable($header, $this->data);
    }

    /**
     * Returns plugin summary for "pilist"
     *
     * @return string
     */
    protected function getPilistPluginSummary(): string
    {
        $header = htmlspecialchars($this->getLanguageService()->sL(self::LLPATH . 'plugin.pilist.title'));

        $this->getPluginPidConfig('detailPid', 'additional');
        $this->getStoragePage('settings.storagePage');
        $this->getOrderSettings('settings.orderField', 'settings.orderDirection');
        $this->getCategoryConjuction();
        $this->getCategorySettings();
        $this->getPaginationSettings();
        $this->getOverwriteDemandSettings();

        return $this->renderSettingsAsTable($header, $this->data);
    }

    /**
     * Returns plugin summary for "pilistdetail"
     *
     * @return string
     */
    protected function getPidetailPluginSummary(): string
    {
        $header = htmlspecialchars($this->getLanguageService()->sL(self::LLPATH . 'plugin.pidetail.title'));
        $this->getPluginPidConfig('listPid', 'additional');

        return $this->renderSettingsAsTable($header, $this->data);
    }

    /**
     * Returns the PID config for the given PID
     *
     * @param string $pidSetting
     * @param string $sheet
     */
    protected function getPluginPidConfig(string $pidSetting, string $sheet = 'sDEF')
    {
        $pid = (int)$this->getFieldFromFlexform('settings.' . $pidSetting, $sheet);
        if ($pid > 0) {
            $this->data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.' . $pidSetting),
                'value' => $this->getRecordData($pid),
            ];
        }
    }

    /**
     * @param int $id
     * @param string $table
     * @return string
     */
    protected function getRecordData($id, $table = 'pages'): string
    {
        $content = '';
        $record = BackendUtilityCore::getRecord($table, $id);

        if (is_array($record)) {
            $data = '<span data-toggle="tooltip" data-placement="top" data-title="id=' . $record['uid'] . '">'
                . $this->iconFactory->getIconForRecord($table, $record, Icon::SIZE_SMALL)->render()
                . '</span> ';
            $content = BackendUtilityCore::wrapClickMenuOnIcon($data, $table, $record['uid']);

            $linkTitle = htmlspecialchars(BackendUtilityCore::getRecordTitle($table, $record));
            $content .= $linkTitle;
        }

        return $content;
    }

    /**
     * Get the storagePage
     *
     * @param string $field
     */
    protected function getStoragePage(string $field)
    {
        $value = $this->getFieldFromFlexform($field);

        if (!empty($value)) {
            $pageIds = GeneralUtility::intExplode(',', $value, true);
            $pagesOut = [];

            foreach ($pageIds as $id) {
                $pagesOut[] = $this->getRecordData($id, 'pages');
            }

            $recursiveLevel = (int)$this->getFieldFromFlexform('settings.recursive');
            $recursiveLevelText = '';
            if ($recursiveLevel === 250) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:recursive.I.5');
            } elseif ($recursiveLevel > 0) {
                $recursiveLevelText = $this->getLanguageService()->sL(
                    'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:recursive.I.' . $recursiveLevel
                );
            }

            if (!empty($recursiveLevelText)) {
                $recursiveLevelText = '<br />' .
                    htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.recursive')) . ' ' .
                    $recursiveLevelText;
            }

            $this->data[] = [
                'title' => $this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.startingpoint'),
                'value' => implode(', ', $pagesOut) . $recursiveLevelText,
            ];
        }
    }

    /**
     * Get order settings
     *
     * @param string $orderByField
     * @param string $orderDirectionField
     */
    protected function getOrderSettings(string $orderByField, string $orderDirectionField)
    {
        $orderField = $this->getFieldFromFlexform($orderByField);
        if (!empty($orderField)) {
            $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.orderField.' . $orderField);

            // Order direction (asc, desc)
            $orderDirection = $this->getOrderDirectionSetting($orderDirectionField);
            if ($orderDirection) {
                $text .= ', ' . strtolower($orderDirection);
            }

            $this->data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.orderField'),
                'value' => $text,
            ];
        }
    }

    /**
     * Get order direction
     * @param string $orderDirectionField
     * @return string
     */
    protected function getOrderDirectionSetting(string $orderDirectionField): string
    {
        $text = '';

        $orderDirection = $this->getFieldFromFlexform($orderDirectionField);
        if (!empty($orderDirection)) {
            $text = $this->getLanguageService()->sL(
                self::LLPATH . 'flexforms.plugin.field.orderDirection.' . $orderDirection . 'ending'
            );
        }

        return $text;
    }

    /**
     * Get category conjunction if a category is selected
     */
    protected function getCategoryConjuction()
    {
        // If not category is selected, we do not need to display the category mode
        $categories = $this->getFieldFromFlexform('settings.categories');
        if ($categories === null || $categories === '') {
            return;
        }

        $categoryConjunction = strtolower($this->getFieldFromFlexform('settings.categoryConjunction') ?? '');
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
        }

        $this->data[] = [
            'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.categoryConjunction'),
            'value' => $text,
        ];
    }

    /**
     * Get information if override demand setting is disabled or not
     */
    protected function getOverwriteDemandSettings()
    {
        $field = $this->getFieldFromFlexform('settings.disableOverwriteDemand', 'additional');

        if ($field === '1') {
            $text = '<i class="fa fa-check"></i>';
            $this->data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.disableOverwriteDemand'),
                'value' => $text,
            ];
        }
    }

    /**
     * Get information if pagination is active
     */
    protected function getPaginationSettings()
    {
        $field = $this->getFieldFromFlexform('settings.enablePagination', 'pagination');

        if ($field === '1') {
            $text = '<i class="fa fa-check"></i>';
            $this->data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.enablePagination'),
                'value' => $text,
            ];
        }
    }

    /**
     * Get category settings
     */
    protected function getCategorySettings()
    {
        $categories = GeneralUtility::intExplode(',', $this->getFieldFromFlexform('settings.categories') ?? '', true);
        if (count($categories) > 0) {
            $categoriesOut = [];
            foreach ($categories as $id) {
                $categoriesOut[] = $this->getRecordData($id, 'sys_category');
            }

            $this->data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.plugin.field.categories'),
                'value' => implode(', ', $categoriesOut),
            ];

            $includeSubcategories = $this->getFieldFromFlexform('settings.includeSubcategories');
            if ((int)$includeSubcategories === 1) {
                $this->data[] = [
                    'title' => $this->getLanguageService()->sL(
                        self::LLPATH . 'flexforms.plugin.field.includeSubcategories'
                    ),
                    'value' => '<i class="fa fa-check"></i>',
                ];
            }
        }
    }

    /**
     * Render the settings as table for Web>Page module
     * System settings are displayed in mono font
     *
     * @param string $header
     * @param array $data
     * @return string
     */
    protected function renderSettingsAsTable(string $header, array $data): string
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addCssFile('EXT:plain_faq/Resources/Public/Css/Backend/PageLayoutView.css');

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:plain_faq/Resources/Private/Backend/PageLayoutView.html')
        );
        $view->assignMultiple([
            'extensionTitle' => $this->getLanguageService()->sL(self::LLPATH . 'extension.title'),
            'header' => $header,
            'data' => $data,
        ]);

        return $view->render();
    }

    /**
     * Get field value from flexform configuration,
     * including checks if flexform configuration is available
     *
     * @param string $key name of the key
     * @param string $sheet name of the sheet
     * @return string|null if nothing found, value if found
     */
    public function getFieldFromFlexform(string $key, string $sheet = 'sDEF'): ?string
    {
        $flexform = $this->flexformData;
        if (isset($flexform['data'])) {
            $flexform = $flexform['data'];
            if (is_array($flexform) && is_array($flexform[$sheet] ?? false)
                && is_array($flexform[$sheet]['lDEF'] ?? false)
                && is_array($flexform[$sheet]['lDEF'][$key] ?? false) &&
                isset($flexform[$sheet]['lDEF'][$key]['vDEF'])
            ) {
                return $flexform[$sheet]['lDEF'][$key]['vDEF'];
            }
        }

        return null;
    }

    /**
     * Return language service instance
     *
     * @return LanguageService
     */
    public function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
