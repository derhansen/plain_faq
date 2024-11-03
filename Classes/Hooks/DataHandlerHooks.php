<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Hooks;

use Derhansen\PlainFaq\Service\FaqCacheService;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataHandlerHooks
{
    /**
     * Flushes the cache if a faq record was edited.
     * This happens on two levels: by UID and by PID.
     */
    public function clearCachePostProc(array $params): void
    {
        if (isset($params['table']) && $params['table'] === 'tx_plainfaq_domain_model_faq') {
            $faqUid = $params['uid'] ?? 0;
            $pageUid = $params['uid_page'] ?? 0;
            if ($faqUid > 0 || $pageUid > 0) {
                $faqCacheService = GeneralUtility::makeInstance(FaqCacheService::class);
                $faqCacheService->flushFaqCache($faqUid, $pageUid);
            }
        }
    }

    /**
     * Checks if the fields defined in $checkFields are set in the data-array of pi_flexform.
     * If a field is present and contains an empty value, the field is unset.
     *
     * Structure of the checkFields array:
     *
     * array('sheet' => array('field1', 'field2'));
     */
    public function processDatamap_postProcessFieldArray(
        string $status,
        string $table,
        string|int $id,
        array &$fieldArray,
        DataHandler $dataHandler
    ): void {
        if ($table === 'tt_content' &&
            $status === 'update' &&
            isset($fieldArray['pi_flexform']) &&
            $dataHandler->checkValue_currentRecord['CType'] === 'list' &&
            in_array(
                $dataHandler->checkValue_currentRecord['list_type'],
                [
                    'plainfaq_pilist',
                    'plainfaq_pilist',
                    'plainfaq_pilist',
                ]
            )
        ) {
            $checkFields = [
                'sDEF' => [
                    'settings.orderField',
                    'settings.orderDirection',
                    'settings.categories',
                    'settings.includeSubcategories',
                    'settings.storagePage',
                    'settings.recursive',
                ],
                'additional' => [
                    'settings.detailPid',
                    'settings.listPid',
                ],
                'template' => [
                    'settings.templateLayout',
                ],
            ];

            $flexformData = GeneralUtility::xml2array($fieldArray['pi_flexform']);
            if (!is_array($flexformData)) {
                return;
            }

            foreach ($checkFields as $sheet => $fields) {
                foreach ($fields as $field) {
                    if (isset($flexformData['data'][$sheet]['lDEF'][$field]['vDEF']) &&
                        $flexformData['data'][$sheet]['lDEF'][$field]['vDEF'] === ''
                    ) {
                        unset($flexformData['data'][$sheet]['lDEF'][$field]);
                    }
                }

                // If remaining sheet does not contain fields, then remove the sheet
                if (isset($flexformData['data'][$sheet]['lDEF']) && $flexformData['data'][$sheet]['lDEF'] === []) {
                    unset($flexformData['data'][$sheet]);
                }
            }

            /** @var \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools $flexFormTools */
            $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
            $fieldArray['pi_flexform'] = $flexFormTools->flexArray2Xml($flexformData);
        }
    }
}
