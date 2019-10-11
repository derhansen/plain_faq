<?php
namespace Derhansen\PlainFaq\Tests\Unit\Domain\Model;

use Derhansen\PlainFaq\Controller\FaqController;
use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\BaseTestCase;

/**
 * Test case for FaqController
 */
class FaqControllerTest extends BaseTestCase
{
    /**
     * @var \Derhansen\PlainFaq\Controller\FaqController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Derhansen\PlainFaq\Controller\FaqController();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     * @return void
     */
    public function createEventDemandObjectFromSettingsWithoutEmptySettings()
    {
        $mockController = $this->getMockBuilder(FaqController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->getMock();

        $settings = [];

        $mockDemand = $this->getMockBuilder(FaqDemand::class)->getMock();
        $mockDemand->expects($this->at(0))->method('setStoragePage')->with('');
        $mockDemand->expects($this->at(1))->method('setCategoryConjunction')->with('');
        $mockDemand->expects($this->at(2))->method('setCategories')->with('');
        $mockDemand->expects($this->at(3))->method('setIncludeSubcategories')->with('');
        $mockDemand->expects($this->at(4))->method('setOrderField')->with('');
        $mockDemand->expects($this->at(5))->method('setOrderFieldAllowed')->with('');
        $mockDemand->expects($this->at(6))->method('setOrderDirection')->with('asc');

        $objectManager = $this->getMockBuilder(ObjectManager::class)->getMock();
        $objectManager->expects($this->any())->method('get')->will($this->returnValue($mockDemand));
        $this->inject($mockController, 'objectManager', $objectManager);

        $mockController->createFaqDemandObjectFromSettings($settings);
    }
}
