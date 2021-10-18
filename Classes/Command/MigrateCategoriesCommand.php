<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class MigrateCategories
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class MigrateCategoriesCommand extends AbstractMigrateCommand
{
    const ROOT_CATEGORY_FAQ_IMPORT_ID = 'ROOT';
    const ROOT_CATEGORY_PID = 0;
    const ROOT_CATEGORY_TITLE = 'FAQ';
    const MAIN_CATEGORY_PREFIX = 'PAGE';

    /**
     * Configuring the command options
     */
    public function configure()
    {
        $this->setDescription('Migrates categories from ext:irfaq to sys_categories for usage in ext:plain_faq');
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $faqCategories = $this->getImportData();
        $translatedCategories = [];
        $currentPid = 0;
        $currentMainCategoryUid = 0;

        // First create the root category
        $categoryRootUid = $this->getCreateRootCategory($io);

        foreach ($faqCategories as $faqCategory) {
            if ($currentPid !== (int)$faqCategory['pid']) {
                $currentPid = (int)$faqCategory['pid'];
                $currentMainCategoryUid = $this->getCreateMainCategory($currentPid, $categoryRootUid, $io);
            }

            // Create all categories for sys_language_uid = 0
            if ((int)$faqCategory['sys_language_uid'] === 0) {
                $this->getCreateCategory($faqCategory, $currentMainCategoryUid, $io);
            } else {
                $translatedCategories[] = $faqCategory;
            }
        }

        // Finally add translations for created categories
        foreach ($translatedCategories as $translatedCategory) {
            $parentCategory = $this->getCategoryByFaqImportId((string)$translatedCategory['l18n_parent']);
            $this->getCreateCategory($translatedCategory, $parentCategory['uid'], $io, $parentCategory['uid']);
        }

        $io->success('All done!');
        return 1;
    }

    /**
     * Returns the UID of the migrated sys_category for the given old category record. If no sys_category record
     * exists, one is created
     *
     * @param array $oldCategory
     * @param int $parentCategoryUid
     * @param SymfonyStyle $io
     * @param int $l10nParent
     * @return int
     */
    protected function getCreateCategory(
        array $oldCategory,
        int $parentCategoryUid,
        SymfonyStyle $io,
        int $l10nParent = 0
    ) {
        $category = $this->getCategoryByFaqImportId((string)$oldCategory['uid']);
        if (!$category) {
            $data = [
                'pid' => (int)$oldCategory['pid'],
                'tstamp' => $oldCategory['tstamp'],
                'crdate' => $oldCategory['crdate'],
                'hidden' => $oldCategory['hidden'],
                'cruser_id' => $oldCategory['cruser_id'],
                'sorting' => $oldCategory['sorting'],
                'sys_language_uid' => $oldCategory['sys_language_uid'],
                'l10n_parent' => $l10nParent,
                'title' => $oldCategory['title'],
                'parent' => $parentCategoryUid,
                'faq_import_id' => $oldCategory['uid'],
            ];
            $categoryUid = $this->createSysCategory($data);
            $io->text('Created category "' . $oldCategory['title'] . '"');
        } else {
            $categoryUid = (int)$category['uid'];
            $io->text('Category "' . $oldCategory['title'] . '" already migrated');
        }

        return $categoryUid;
    }

    /**
     * Returns the UID of the root sys_category for the migration. If no sys_category record
     * exists, one is created
     *
     * @param SymfonyStyle $io
     * @return int
     */
    protected function getCreateRootCategory(SymfonyStyle $io)
    {
        $rootCategory = $this->getCategoryByFaqImportId(self::ROOT_CATEGORY_FAQ_IMPORT_ID);
        if (!$rootCategory) {
            $data = [
                'pid' => self::ROOT_CATEGORY_PID,
                'tstamp' => time(),
                'crdate' => time(),
                'title' => self::ROOT_CATEGORY_TITLE,
                'faq_import_id' => self::ROOT_CATEGORY_FAQ_IMPORT_ID,
            ];
            $categoryUid = $this->createSysCategory($data);
            $io->text('Created import root category');
        } else {
            $categoryUid = (int)$rootCategory['uid'];
            $io->text('Import root category already created');
        }

        return $categoryUid;
    }

    /**
     * Returns the UID of the migrated main sys_category for the given page ID. If no sys_category record
     * exists, one is created
     *
     * @param int $pid
     * @param int $rootCategoryUid
     * @param SymfonyStyle $io
     * @return int
     */
    protected function getCreateMainCategory(int $pid, int $rootCategoryUid, SymfonyStyle $io)
    {
        $mainCategory = $this->getCategoryByFaqImportId(self::MAIN_CATEGORY_PREFIX . (string)$pid);
        if (!$mainCategory) {
            $pageRecord = BackendUtility::getRecord('pages', $pid);
            $data = [
                'pid' => $pid,
                'tstamp' => time(),
                'crdate' => time(),
                'title' => $pageRecord['title'],
                'parent' => $rootCategoryUid,
                'faq_import_id' => self::MAIN_CATEGORY_PREFIX . (string)$pid,
            ];
            $categoryUid = $this->createSysCategory($data);
            $io->text('Created main category "' . $pageRecord['title'] . '"');
        } else {
            $categoryUid = (int)$mainCategory['uid'];
            $io->text('Main category for PID ' . $pid . ' already migrated');
        }

        return $categoryUid;
    }

    /**
     * Returns all categories from the table "tx_irfaq_cat" for the import
     *
     * @return array
     */
    protected function getImportData()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_irfaq_cat');
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $res = $queryBuilder
            ->select('*')
            ->from('tx_irfaq_cat')
            ->orderBy('pid', 'ASC')
            ->execute();

        return $res->fetchAll();
    }

    /**
     * Creates a sys_category record for the given data and returns the UID of the created sys_category
     *
     * @param array $data
     * @return int
     */
    protected function createSysCategory(array $data): int
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $databaseConnectionForSysCategory = $connectionPool->getConnectionForTable('sys_category');
        $databaseConnectionForSysCategory->insert(
            'sys_category',
            $data
        );

        return (int)$databaseConnectionForSysCategory->lastInsertId('sys_category');
    }
}
