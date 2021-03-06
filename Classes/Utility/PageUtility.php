<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Utility;

use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Page Utility
 */
class PageUtility
{
    /**
     * Find all ids from given ids and level
     *
     * @param string $pidList comma separated list of ids
     * @param int $recursive recursive levels
     * @return string comma separated list of ids
     */
    public static function extendPidListByChildren(string $pidList = '', int $recursive = 0): string
    {
        $recursive = (int)$recursive;

        if ($recursive <= 0) {
            return $pidList;
        }

        $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
        $recursiveStoragePids = $pidList;
        $storagePids = GeneralUtility::intExplode(',', $pidList);
        foreach ($storagePids as $startPid) {
            $pids = $queryGenerator->getTreeList($startPid, $recursive);
            if (strlen((string)$pids) > 0) {
                $recursiveStoragePids .= ',' . $pids;
            }
        }

        return $recursiveStoragePids;
    }
}
