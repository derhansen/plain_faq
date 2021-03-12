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
        $mockController = $this->getAccessibleMock(FaqController::class, ['redirect', 'forward', 'addFlashMessage']);

        $settings = [];

        $mockDemand = $this->getMockBuilder(FaqDemand::class)->getMock();
        $mockDemand->expects(self::once())->method('setStoragePage')->with('');
        $mockDemand->expects(self::once())->method('setCategoryConjunction')->with('');
        $mockDemand->expects(self::once())->method('setCategories')->with('');
        $mockDemand->expects(self::once())->method('setIncludeSubcategories')->with('');
        $mockDemand->expects(self::once())->method('setOrderField')->with('');
        $mockDemand->expects(self::once())->method('setOrderFieldAllowed')->with('');
        $mockDemand->expects(self::once())->method('setOrderDirection')->with('asc');
        $mockDemand->expects(self::once())->method('setQueryLimit')->with(0);

        $objectManager = $this->getMockBuilder(ObjectManager::class)->disableOriginalConstructor()->getMock();
        $objectManager->expects(self::any())->method('get')->willReturn($mockDemand);
        $mockController->injectObjectManager($objectManager);

        $mockController->createFaqDemandObjectFromSettings($settings);
    }
}
