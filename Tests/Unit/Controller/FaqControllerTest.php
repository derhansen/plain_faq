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
        $demand = $mockController->createFaqDemandObjectFromSettings([]);

        $this->assertEmpty($demand->getStoragePage());
        $this->assertEmpty($demand->getCategoryConjunction());
        $this->assertEmpty($demand->getCategories());
        $this->assertEmpty($demand->getIncludeSubcategories());
        $this->assertEmpty($demand->getOrderField());
        $this->assertEmpty($demand->getOrderFieldAllowed());
        $this->assertEquals('asc', $demand->getOrderDirection());
        $this->assertEquals(0, $demand->getQueryLimit());
    }

    /**
     * @test
     */
    public function createFaqDemandObjectFromSettingsSetsSettings()
    {
        /** @var FaqController $mockController */
        $mockController = $this->getAccessibleMock(FaqController::class, ['redirect', 'forward', 'addFlashMessage']);

        $settings = [
            'categoryConjunction' => 'OR',
            'categories' => '1,2,3',
            'includeSubcategories' => true,
            'orderField' => 'name',
            'orderFieldAllowed' => 'name',
            'orderDirection' => 'desc',
            'queryLimit' => 1,
        ];

        $demand = $mockController->createFaqDemandObjectFromSettings($settings);
        $this->assertEquals('OR', $demand->getCategoryConjunction());
        $this->assertEquals('1,2,3', $demand->getCategories());
        $this->assertTrue($demand->getIncludeSubcategories());
        $this->assertEquals('name', $demand->getOrderField());
        $this->assertEquals('name', $demand->getOrderFieldAllowed());
        $this->assertEquals('desc', $demand->getOrderDirection());
        $this->assertEquals(1, $demand->getQueryLimit());
    }
}
