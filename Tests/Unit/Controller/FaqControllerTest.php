<?php

namespace Derhansen\PlainFaq\Tests\Unit\Domain\Model;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
     * @test
     */
    public function createFaqDemandObjectFromSettingsWithEmptySettings()
    {
        /** @var FaqController $mockController */
        $mockController = $this->getMockBuilder(FaqController::class)
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->getMock();

        $settings = [];

        $mockDemand = $this->getMockBuilder(FaqDemand::class)->getMock();
        $mockDemand->expects(self::at(0))->method('setStoragePage')->with('');
        $mockDemand->expects(self::at(1))->method('setCategoryConjunction')->with('');
        $mockDemand->expects(self::at(2))->method('setCategories')->with('');
        $mockDemand->expects(self::at(3))->method('setIncludeSubcategories')->with('');
        $mockDemand->expects(self::at(4))->method('setOrderField')->with('');
        $mockDemand->expects(self::at(5))->method('setOrderFieldAllowed')->with('');
        $mockDemand->expects(self::at(6))->method('setOrderDirection')->with('asc');

        $objectManager = $this->getMockBuilder(ObjectManager::class)->disableOriginalConstructor()->getMock();
        $objectManager->expects(self::any())->method('get')->willReturn($mockDemand);
        $this->inject($mockController, 'objectManager', $objectManager);

        $mockController->createFaqDemandObjectFromSettings($settings);
    }
}
