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
    /**
     * @var string
     */
    const LLPATH = 'LLL:EXT:plain_faq/Resources/Private/Language/locallang_be.xlf:';

    /**
     * Data rendered in table of Plugin settings
     *
     * @var array
     */
    public $data = [];

    /**
     * Flexform information
     *
     * @var array
     */
    public $flexformData = [];

    /**
     * @var IconFactory
     */
    protected $iconFactory;

    /**
     * PageLayoutView constructor
     */
    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    /**
     * Returns information about this extension's faq plugin
     *
     * @param array $params Parameters to the hook
     * @return string Information about plugin
     */
    public function getPi1PluginSummary(array $params)
    {
        $header = htmlspecialchars($this->getLanguageService()->sL(self::LLPATH . 'plugin.title'));

        // Add flexform switchable controller actions
        $this->flexformData = GeneralUtility::xml2array($params['row']['pi_flexform']);

        // Extend header by flexible controller action
        $action = htmlspecialchars($this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.mode')) . ': ';
        $action .= $this->getSwitchableControllerActionTitle();

        $this->getPluginPidConfig('listPid', 'additional');
        $this->getPluginPidConfig('detailPid', 'additional');
        $this->getStoragePage('settings.storagePage');
        $this->getOrderSettings('settings.orderField', 'settings.orderDirection');
        $this->getOverwriteDemandSettings();

        if ($this->showFieldsForListViewOnly()) {
            $this->getCategoryConjuction();
            $this->getCategorySettings();
        }

        return $this->renderSettingsAsTable($header, $action, $this->data);
    }

    /**
     * Returns the current title of the switchableControllerAction
     *
     * @return string
     */
    protected function getSwitchableControllerActionTitle(): string
    {
        $title = '';
        $actions = $this->getFieldFromFlexform('switchableControllerActions');
        switch ($actions) {
            case 'Faq->list;Faq->detail':
                $title = $this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.mode.listDetail');
                break;
            case 'Faq->list':
                $title = $this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.mode.list');
                break;
            case 'Faq->detail':
                $title = $this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.mode.detail');
                break;
            default:
        }

        return $title;
    }

    /**
     * Returns, if fields, that are only visible for list view, should be shown
     *
     * @return bool
     */
    protected function showFieldsForListViewOnly(): bool
    {
        $actions = $this->getFieldFromFlexform('switchableControllerActions');
        switch ($actions) {
            case 'Faq->list;Faq->detail':
            case 'Faq->list':
                $result = true;
                break;
            default:
                $result = false;
        }

        return $result;
    }

    /**
     * Returns the PID config for the given PID
     *
     * @param string $pidSetting
     * @param string $sheet
     */
    public function getPluginPidConfig(string $pidSetting, string $sheet = 'sDEF')
    {
        $pid = (int)$this->getFieldFromFlexform('settings.' . $pidSetting, $sheet);
        if ($pid > 0) {
            $this->data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.' . $pidSetting),
                'value' => $this->getRecordData($pid)
            ];
        }
    }

    /**
     * @param int $id
     * @param string $table
     * @return string
     */
    public function getRecordData($id, $table = 'pages'): string
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
    public function getStoragePage(string $field)
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
                'value' => implode(', ', $pagesOut) . $recursiveLevelText
            ];
        }
    }

    /**
     * Get order settings
     *
     * @param string $orderByField
     * @param string $orderDirectionField
     */
    public function getOrderSettings(string $orderByField, string $orderDirectionField)
    {
        $orderField = $this->getFieldFromFlexform($orderByField);
        if (!empty($orderField)) {
            $text = $this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.orderField.' . $orderField);

            // Order direction (asc, desc)
            $orderDirection = $this->getOrderDirectionSetting($orderDirectionField);
            if ($orderDirection) {
                $text .= ', ' . strtolower($orderDirection);
            }

            $this->data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.orderField'),
                'value' => $text
            ];
        }
    }

    /**
     * Get order direction
     * @param string $orderDirectionField
     * @return string
     */
    public function getOrderDirectionSetting(string $orderDirectionField): string
    {
        $text = '';

        $orderDirection = $this->getFieldFromFlexform($orderDirectionField);
        if (!empty($orderDirection)) {
            $text = $this->getLanguageService()->sL(
                self::LLPATH . 'flexforms.pi1.field.orderDirection.' . $orderDirection . 'ending'
            );
        }

        return $text;
    }

    /**
     * Get category conjunction if a category is selected
     */
    public function getCategoryConjuction()
    {
        // If not category is selected, we do not need to display the category mode
        $categories = $this->getFieldFromFlexform('settings.categories');
        if ($categories === null || $categories === '') {
            return;
        }

        $categoryConjunction = strtolower($this->getFieldFromFlexform('settings.categoryConjunction'));
        switch ($categoryConjunction) {
            case 'or':
            case 'and':
            case 'notor':
            case 'notand':
                $text = htmlspecialchars($this->getLanguageService()->sL(
                    self::LLPATH . 'flexforms.pi1.field.categoryConjunction.' . $categoryConjunction
                ));
                break;
            default:
                $text = htmlspecialchars($this->getLanguageService()->sL(
                    self::LLPATH . 'flexforms.pi1.field.categoryConjunction.ignore'
                ));
        }

        $this->data[] = [
            'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.categoryConjunction'),
            'value' => $text
        ];
    }

    /**
     * Get information if override demand setting is disabled or not
     */
    public function getOverwriteDemandSettings()
    {
        $field = $this->getFieldFromFlexform('settings.disableOverwriteDemand', 'additional');

        if ($field === '1') {
            $text = '<i class="fa fa-check"></i>';
            $this->data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.disableOverwriteDemand'),
                'value' => $text
            ];
        }
    }

    /**
     * Get category settings
     */
    public function getCategorySettings()
    {
        $categories = GeneralUtility::intExplode(',', $this->getFieldFromFlexform('settings.categories'), true);
        if (count($categories) > 0) {
            $categoriesOut = [];
            foreach ($categories as $id) {
                $categoriesOut[] = $this->getRecordData($id, 'sys_category');
            }

            $this->data[] = [
                'title' => $this->getLanguageService()->sL(self::LLPATH . 'flexforms.pi1.field.categories'),
                'value' => implode(', ', $categoriesOut)
            ];

            $includeSubcategories = $this->getFieldFromFlexform('settings.includeSubcategories');
            if ((int)$includeSubcategories === 1) {
                $this->data[] = [
                    'title' => $this->getLanguageService()->sL(
                        self::LLPATH . 'flexforms.pi1.field.includeSubcategories'
                    ),
                    'value' => '<i class="fa fa-check"></i>'
                ];
            }
        }
    }

    /**
     * Render the settings as table for Web>Page module
     * System settings are displayed in mono font
     *
     * @param string $header
     * @param string $action
     * @param array $data
     * @return string
     */
    protected function renderSettingsAsTable(string $header, string $action, array $data): string
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/News/PageLayout');
        $pageRenderer->addCssFile('EXT:plain_faq/Resources/Public/Css/Backend/PageLayoutView.css');

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:plain_faq/Resources/Private/Backend/PageLayoutView.html')
        );
        $view->assignMultiple([
            'header' => $header,
            'action' => $action,
            'data' => $data
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
            if (is_array($flexform) && is_array($flexform[$sheet]) && is_array($flexform[$sheet]['lDEF'])
                && is_array($flexform[$sheet]['lDEF'][$key]) && isset($flexform[$sheet]['lDEF'][$key]['vDEF'])
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
