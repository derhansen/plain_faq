<?php
declare(strict_types = 1);
namespace Derhansen\PlainFaq\Command;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Symfony\Component\Console\Command\Command;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Torben Hansen <derhansen@gmail.com>
 */
abstract class AbstractMigrateCommand extends Command
{
    /**
     * Returns the sys_category record for the given id-string in the field "faq_import_id"
     *
     * @param string $id
     * @return mixed
     */
    protected function getCategoryByFaqImportId(string $id)
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_category');
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        $res = $queryBuilder
            ->select('*')
            ->from('sys_category')
            ->where(
                $queryBuilder->expr()->eq(
                    'faq_import_id',
                    $queryBuilder->createNamedParameter($id, Connection::PARAM_STR)
                )
            )
            ->execute();

        return $res->fetch(0);
    }
}