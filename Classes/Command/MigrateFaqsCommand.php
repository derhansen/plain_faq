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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class MigrateFaqsCommand
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class MigrateFaqsCommand extends AbstractMigrateCommand
{
    /**
     * Configuring the command options
     */
    public function configure()
    {
        $this
            ->setDescription('Migrates FAQs from ext:irfaq to ext:plain_faq')
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'If set, only the given amount of FAQs are imported (useful for debugging)'
            );
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

        $limit = $input->hasOption('limit') && $input->getOption('limit') ? (int)$input->getOption('limit') : 0;

        $faqs = $this->getImportData();
        $translatedFaqs = [];
        $amountImported = 0;

        foreach ($faqs as $oldFaq) {
            if ((int)$oldFaq['sys_language_uid'] === 0) {
                $created = $this->migrateFaq($oldFaq, $io);
                if ($created) {
                    $amountImported++;
                }
            } else {
                $translatedFaqs[] = $oldFaq;
            }

            if ($limit > 0 && $amountImported === $limit) {
                break;
            }
        }

        // Finally add translations for created FAQs
        foreach ($translatedFaqs as $translatedFaq) {
            $parentFaq = $this->getFaqByFaqImportId((string)$translatedFaq['l18n_parent']);
            $this->migrateFaq($translatedFaq, $io, $parentFaq['uid']);
        }

        $io->success('All done!');
        return 1;
    }

    /**
     * Migrates the given ext:irfaq category record to ext:plain_faq
     *
     * @param array $oldFaq
     * @param SymfonyStyle $io
     * @param int $l10nParent
     * @return bool
     */
    protected function migrateFaq(array $oldFaq, SymfonyStyle $io, int $l10nParent = 0): bool
    {
        $newFaqUid = 0;
        $faq = $this->getFaqByFaqImportId((string)$oldFaq['uid']);
        if (!$faq) {
            $data = [
                'pid' => (int)$oldFaq['pid'],
                'tstamp' => $oldFaq['tstamp'],
                'crdate' => $oldFaq['crdate'],
                'cruser_id' => $oldFaq['cruser_id'],
                'sorting' => $oldFaq['sorting'],
                'hidden' => $oldFaq['hidden'],
                'fe_group' => $oldFaq['fe_group'],
                'question' => $oldFaq['q'],
                'answer' => $oldFaq['a'],
                'sys_language_uid' => $oldFaq['sys_language_uid'],
                'l10n_parent' => $l10nParent,
                'faq_import_id' => $oldFaq['uid']
            ];
            $newFaqUid = $this->createFaq($data);
            $created = true;
            $io->text('Created FAQ "' . $oldFaq['q'] . '"');
        } else {
            $created = false;
            $io->text('FAQ "' . $oldFaq['q'] . '" already migrated');
        }

        // We only migrate categories, when a new FAQ record with categories has been created
        if ((int)$oldFaq['cat'] > 0 && $created && $newFaqUid > 0) {
            $amountMigrated = $this->migrateCategoryRelations((int)$oldFaq['uid'], $newFaqUid, $io);
            $io->text('Migrated ' . $amountMigrated . ' category releations');
        }

        return $created;
    }

    /**
     * Migrates ext:irfaq category relations for the given FAQ record
     *
     * @param int $oldFaqUid
     * @param int $newFaqUid
     * @param SymfonyStyle $io
     * @return int
     */
    protected function migrateCategoryRelations(int $oldFaqUid, int $newFaqUid, SymfonyStyle $io)
    {
        $amountMigrated = 0;
        $faqRelations = $this->getFaqCategoryRelations($oldFaqUid);

        foreach ($faqRelations as $faqRelation) {
            $migratedCategory = $this->getCategoryByFaqImportId((string)$faqRelation['uid_foreign']);
            if ($migratedCategory) {
                $newRelationData = [
                    'uid_local' => $migratedCategory['uid'],
                    'uid_foreign' => $newFaqUid,
                    'tablenames' => 'tx_plainfaq_domain_model_faq',
                    'fieldname' => 'categories',
                    'sorting' => $faqRelation['sorting'],
                ];
                $this->createFaqCategoryRelation($newRelationData);
                $amountMigrated++;
            } else {
                $io->note(
                    'Unable to migrate category relation for uid_local: ' . $faqRelation['uid_local'] .
                    ' and uid_foreign: ' . $faqRelation['uid_foreign'] . '. ext:irfaq category record propably deleted.'
                );
            }
        }

        return $amountMigrated;
    }

    /**
     * Returns all FAQs from the table "tx_irfaq_q" for the migration
     *
     * @return array
     */
    protected function getImportData()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_irfaq_q');
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $query = $queryBuilder
            ->select('*')
            ->from('tx_irfaq_q')
            ->orderBy('pid', 'ASC');

        return $query->execute()->fetchAll();
    }

    /**
     * Returns all mm-records from the table "tx_irfaq_q_cat_mm" for the given categoryUid
     *
     * @param int $categoryUid
     * @return array
     */
    protected function getFaqCategoryRelations(int $categoryUid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_irfaq_q_cat_mm');

        $res = $queryBuilder
            ->select('*')
            ->from('tx_irfaq_q_cat_mm')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_local',
                    $queryBuilder->createNamedParameter($categoryUid, Connection::PARAM_INT)
                )
            )
            ->orderBy('uid_foreign', 'ASC')
            ->execute();

        return $res->fetchAll();
    }

    /**
     * Creates a FAQ record for the given data and returns the UID of the created FAQ
     *
     * @param array $data
     * @return int
     */
    protected function createFaq(array $data): int
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $databaseConnectionForFaq = $connectionPool->getConnectionForTable('tx_plainfaq_domain_model_faq');
        $databaseConnectionForFaq->insert(
            'tx_plainfaq_domain_model_faq',
            $data
        );

        return (int)$databaseConnectionForFaq->lastInsertId('tx_plainfaq_domain_model_faq');
    }

    /**
     * Creates a record in the table "tx_plainfaq_domain_model_faq_related_mm" with the given data
     *
     * @param array $data
     */
    protected function createFaqCategoryRelation(array $data): void
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $databaseConnectionForFaq = $connectionPool->getConnectionForTable('tx_plainfaq_domain_model_faq_related_mm');
        $databaseConnectionForFaq->insert(
            'sys_category_record_mm',
            $data
        );
    }

    /**
     * Returns the faq record for the given id-string in the field "faq_import_id"
     *
     * @param string $id
     * @return mixed
     */
    protected function getFaqByFaqImportId(string $id)
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_plainfaq_domain_model_faq');
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        $res = $queryBuilder
            ->select('*')
            ->from('tx_plainfaq_domain_model_faq')
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
