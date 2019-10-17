<?php
namespace Derhansen\PlainFaq\Tests\Functional\Repository;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Domain\Repository\FaqRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case for raqRespotiroy
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
        $this->assertEquals($faq->getQuestion(), 'First question');
    }
}
