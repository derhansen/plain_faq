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
 * Class MigratePluginsCommand
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class MigratePluginsCommand extends AbstractMigrateCommand
{
    /**
     * Configuring the command options
     */
    public function configure()
    {
        $this
            ->setDescription('Migrates plugin settings from ext:irfaq to ext:plain_faq')
            ->addOption(
                'defaultOrderField',
                'd',
                InputOption::VALUE_OPTIONAL,
                'The default order field when no sort order is defined in the ext:irfaq plugin'
            )
            ->addOption(
                'pids',
                'p',
                InputOption::VALUE_OPTIONAL,
                'If set, only the plugins on the given list of page UIDs are migrated (useful for debugging)'
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

        $pids = $input->hasOption('pids') && $input->getOption('pids') ?
            GeneralUtility::intExplode(',', $input->getOption('pids'), true) :
            [];

        $defaultOrderField = $input->hasOption('defaultOrderField') && $input->getOption('defaultOrderField') ?
            $input->getOption('defaultOrderField') :
            '';
        if ($defaultOrderField !== '' && !in_array($defaultOrderField, ['question', 'sorting'])) {
            $defaultOrderField = '';
        }

        $contentElements = $this->getContentElementsWithPlugin($pids);
        $io->text('Content elements for migration: ' . count($contentElements));

        foreach ($contentElements as $contentElement) {
            $this->migratePlugin($contentElement, $defaultOrderField, $io);
        }

        $io->success('All done!');
        return 1;
    }

    /**
     * Migrates the content element from ext:irfaq to ext:plain_faq
     *
     * @param array $contentElement
     * @param string $defaultOrderField
     * @param SymfonyStyle $io
     * @throws \Exception
     */
    protected function migratePlugin(array $contentElement, string $defaultOrderField, SymfonyStyle $io)
    {
        if ($contentElement['pi_flexform'] === '') {
            $io->text('Skipping content element uid "' . $contentElement['uid'] . '". Empty FlexForm.');
            return;
        }

        $newFlexform = $this->getDefaultFlexFormArray();
        $data = $this->getFlexFormAsArray($contentElement['pi_flexform']);

        // Migrate pages (storagePage)
        if (isset($data['pages']) && $data['pages'] !== null && $data['pages'] !== '') {
            $newFlexform['data']['sDEF']['lDEF']['settings.storagePage'] = ['vDEF' => $data['pages']];
        } else {
            // If no pages are configured, ext:irfaq selects FAQs from the current PID
            $newFlexform['data']['sDEF']['lDEF']['settings.storagePage'] = ['vDEF' => $contentElement['pid']];
        }

        // Migrate recursive
        if (isset($data['recursive']) && $data['recursive'] !== '') {
            $newFlexform['data']['sDEF']['lDEF']['settings.recursive'] = ['vDEF' => $data['recursive']];
        }

        // Migrate sorting
        if (isset($data['sorting']) && $data['sorting'] !== '') {
            $migratedSorting = $this->getMigratedSorting($data['sorting']);
            if ($migratedSorting) {
                $newFlexform['data']['sDEF']['lDEF']['settings.orderField'] = ['vDEF' => $migratedSorting];
            }
        }
        if ($defaultOrderField !== '' && !isset($newFlexform['data']['sDEF']['lDEF']['settings.orderField'])) {
            $newFlexform['data']['sDEF']['lDEF']['settings.orderField'] = ['vDEF' => $defaultOrderField];
        }

        // Migrate categoryMode
        if (isset($data['categoryMode']) && $data['categoryMode'] !== '') {
            $migratedCategoryConjunction = $this->getMigratedCategoryConjunction($data['categoryMode']);
            if ($migratedCategoryConjunction) {
                $newFlexform['data']['sDEF']['lDEF']['settings.categoryConjunction'] =
                    ['vDEF' => $migratedCategoryConjunction];
            }
        }

        // Migrate categories
        if (isset($data['categorySelection']) && $data['categorySelection'] !== '') {
            $newFlexform['data']['sDEF']['lDEF']['settings.categories'] = [
                'vDEF' => $this->getMigratedCategories($data['categorySelection']),
            ];
        }

        $flexform = $this->array2xml($newFlexform);
        $this->updateContentElement($contentElement['uid'], $flexform);

        $io->text('Content element uid "' . $contentElement['uid'] . '" migrated.');
    }

    /**
     * Updates a tt_content element
     *
     * @param int $uid
     * @param string $flexform
     */
    protected function updateContentElement(int $uid, string $flexform)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->update('tt_content')
            ->set('list_type', 'plainfaq_pilistdetail')
            ->set('pi_flexform', $flexform)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)
                )
            )
            ->execute();
    }

    /**
     * Returns all migrated categories
     *
     * @param string $categories
     * @return string
     */
    protected function getMigratedCategories(string $categories)
    {
        $newCategoryUids = [];
        $oldCategoryUids = GeneralUtility::intExplode(',', $categories, true);
        foreach ($oldCategoryUids as $oldCategoryUid) {
            $newSysCategory = $this->getCategoryByFaqImportId((string)$oldCategoryUid);
            if ($newSysCategory) {
                $newCategoryUids[] = $newSysCategory['uid'];
            }
        }

        return implode(',', $newCategoryUids);
    }

    /**
     * Returns migrated category conjunctions
     *
     * @param string $sorting
     * @return string
     */
    protected function getMigratedCategoryConjunction(string $sorting)
    {
        switch ($sorting) {
            case '1':
                $result = 'OR';
                break;
            case '-1':
                // Note: Not sure it this is correct
                $result = 'NOTOR';
                break;
            default:
                $result = '';
        }
        return $result;
    }

    /**
     * Returns migrated sorting
     *
     * @param string $sorting
     * @return string
     */
    protected function getMigratedSorting(string $sorting)
    {
        switch ($sorting) {
            case 'sorting':
                $result = 'sorting';
                break;
            case 'q':
                $result = 'question';
                break;
            default:
                $result = '';
        }
        return $result;
    }

    /**
     * Returns the given flexform as array respecting the current TYPO3 version
     *
     * @param string $flexform
     * @return array
     * @throws \Exception
     */
    protected function getFlexFormAsArray(string $flexform)
    {
        $flexformService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Service\FlexFormService::class);
        return $flexformService->convertFlexFormContentToArray($flexform);
    }

    /**
     * Returns all content elements with the ext:irfaq plugin limited the the given array of PIDs (if not empty)
     *
     * @param array $pids
     * @return array
     */
    protected function getContentElementsWithPlugin(array $pids)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);

        $query = $queryBuilder
            ->select('*')
            ->from('tt_content');

        $conditions = [
            $queryBuilder->expr()->eq(
                'ctype',
                $queryBuilder->createNamedParameter('list', Connection::PARAM_STR)
            ),
            $queryBuilder->expr()->eq(
                'list_type',
                $queryBuilder->createNamedParameter('irfaq_pi1', Connection::PARAM_STR)
            ),
        ];

        if (!empty($pids)) {
            $conditions[] = $queryBuilder->expr()->in(
                'pid',
                $queryBuilder->createNamedParameter($pids, Connection::PARAM_INT_ARRAY)
            );
        }

        $query->where(...$conditions);

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * Transforms the given array to FlexForm XML
     *
     * @param array $input
     * @return string
     */
    protected function array2xml(array $input = []): string
    {
        $options = [
            'parentTagMap' => [
                'data' => 'sheet',
                'sheet' => 'language',
                'language' => 'field',
                'el' => 'field',
                'field' => 'value',
                'field:el' => 'el',
                'el:_IS_NUM' => 'section',
                'section' => 'itemType',
            ],
            'disableTypeAttrib' => 2,
        ];
        $spaceInd = 4;
        $output = GeneralUtility::array2xml($input, '', 0, 'T3FlexForms', $spaceInd, $options);
        $output = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . LF . $output;
        return $output;
    }

    /**
     * @return array
     */
    protected function getDefaultFlexFormArray()
    {
        return [
            'data' => [
                'sDEF' => [
                    'lDEF' => [
                        'settings.includeSubcategories' => [
                            'vDEF' => '0',
                        ],
                        'settings.recursive' => [
                            'vDEF' => '0',
                        ],
                    ],
                ],
                'additional' => [
                    'lDEF' => [
                        'settings.disableOverwriteDemand' => [
                            'vDEF' => '1',
                        ],
                    ],
                ],
            ],
        ];
    }
}
