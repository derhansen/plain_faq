<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Utility;

use Doctrine\DBAL\ArrayParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CategoryUtility
{
    /**
     * Returns the given categories including their subcategories
     */
    public static function getCategoryListWithChilds(string $categories): string
    {
        return self::getChildrenCategoriesRecursive($categories);
    }

    /**
     * Get child categories
     */
    private static function getChildrenCategoriesRecursive(string $idList, int $counter = 0): string
    {
        $result = [];

        // add idlist to the output too
        if ($counter === 0) {
            $result[] = self::cleanIntList($idList);
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category');
        $res = $queryBuilder
            ->select('uid')
            ->from('sys_category')
            ->where(
                $queryBuilder->expr()->in(
                    'parent',
                    $queryBuilder->createNamedParameter(
                        array_map('intval', explode(',', $idList)),
                        ArrayParameterType::INTEGER
                    )
                )
            )
            ->executeQuery();

        while (($row = $res->fetchAssociative())) {
            $counter++;
            if ($counter > 10000) {
                GeneralUtility::makeInstance(TimeTracker::class)
                    ->setTSlogMessage('EXT:plain_faq: one or more recursive categories where found');

                return implode(',', $result);
            }
            $subcategories = self::getChildrenCategoriesRecursive((string)$row['uid'], $counter);
            $result[] = $row['uid'] . ($subcategories ? ',' . $subcategories : '');
        }

        return implode(',', $result);
    }

    /**
     * Clean list of integers
     */
    private static function cleanIntList(string $list): string
    {
        return implode(',', GeneralUtility::intExplode(',', $list));
    }
}
