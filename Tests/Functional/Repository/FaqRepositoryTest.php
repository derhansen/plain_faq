<?php
namespace Derhansen\PlainFaq\Tests\Functional\Repository;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use Derhansen\PlainFaq\Domain\Repository\FaqRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case for FaqRepository
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class FaqTest extends FunctionalTestCase
{
    /** @var ObjectManager */
    protected $objectManager;

    /** @var FaqRepository */
    protected $faqRepository;

    protected $testExtensionsToLoad = ['typo3conf/ext/plain_faq'];

    protected $coreExtensionsToLoad = ['fluid', 'extensionmanager'];

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->faqRepository = $this->objectManager->get(FaqRepository::class);
        $this->importDataSet(__DIR__ . '/../Fixtures/tx_plainfaq_domain_model_faq.xml');
    }


    /**
     * @test
     */
    public function findRecordsByUid()
    {
        $faq = $this->faqRepository->findByUid(1);
        $this->assertEquals($faq->getQuestion(), 'First question on Page UID 1');
    }

    /**
     * @return array
     */
    public function findDemandedRespectsStoragePageDataProvider()
    {
        return [
            'no storage page' => [
                '',
                5
            ],
            'storage page 1' => [
                '1',
                4
            ],
            'storage page 1 and 2' => [
                '1,2',
                5
            ]
        ];
    }

    /**
     * @dataProvider findDemandedRespectsStoragePageDataProvider
     * @test
     */
    public function findDemandedRespectsStoragePage($storagePage, $expected)
    {
        $demand = $this->objectManager->get(FaqDemand::class);
        $demand->setStoragePage($storagePage);

        $result = $this->faqRepository->findDemanded($demand);
        $this->assertEquals($expected, $result->count());
    }

    /**
     * @return array
     */
    public function findDemandedRespectsCategoryDataProvider()
    {
        return [
            'no conjuction' => [
                '1',
                '',
                false,
                4
            ],
            'category 1 with AND - no subcategories' => [
                '1',
                'and',
                false,
                3
            ],
            'category 1 with OR - with subcategories' => [
                '1',
                'or',
                true,
                4
            ],
            'category 1,4 with OR - no subcategories' => [
                '1,4',
                'or',
                false,
                4
            ],
            'category 1,4 with NOTOR - no subcategories' => [
                '1,4',
                'notor',
                false,
                0
            ],
            'category 1,4 with NOTAND - no subcategories' => [
                '2,4',
                'notor',
                false,
                2
            ],
        ];
    }

    /**
     * @dataProvider findDemandedRespectsCategoryDataProvider
     * @test
     */
    public function findDemandedRespectsCategory($categories, $conjunction, $includeSub, $expected)
    {
        $demand = $this->objectManager->get(FaqDemand::class);
        $demand->setStoragePage(1);
        $demand->setCategoryConjunction($conjunction);
        $demand->setCategories($categories);
        $demand->setIncludeSubcategories($includeSub);

        $result = $this->faqRepository->findDemanded($demand);
        $this->assertEquals($expected, $result->count());
    }

    /**
     * @return array
     */
    public function findDemandedRespectsOrderingDataProvider()
    {
        return [
            'noSorting' => [
                '',
                '',
                1
            ],
            'question ASC' => [
                'question',
                'asc',
                1
            ],
            'question DESC' => [
                'question',
                'desc',
                3
            ]
        ];
    }
    
    /**
     * @dataProvider findDemandedRespectsOrderingDataProvider
     * @test
     */
    public function findDemandedRespectsOrdering($orderField, $orderDirection, $expected)
    {
        $demand = $this->objectManager->get(FaqDemand::class);
        $demand->setStoragePage(1);
        $demand->setOrderField($orderField);
        $demand->setOrderFieldAllowed($orderField);
        $demand->setOrderDirection($orderDirection);

        $result = $this->faqRepository->findDemanded($demand);
        $this->assertEquals($expected, $result->getFirst()->getUid());
    }


}
