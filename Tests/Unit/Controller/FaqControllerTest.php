<?php

namespace Derhansen\PlainFaq\Tests\Unit\Domain\Model;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Controller\FaqController;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\BaseTestCase;

/**
 * Test case for FaqController
 */
class FaqControllerTest extends BaseTestCase
{
    #[Test]
    public function createFaqDemandObjectFromSettingsWithEmptySettings(): void
    {
        /** @var FaqController $mockController */
        $mockController = $this->getAccessibleMock(FaqController::class, ['redirect']);
        $demand = $mockController->createFaqDemandObjectFromSettings([]);

        self::assertEmpty($demand->getStoragePage());
        self::assertEmpty($demand->getCategoryConjunction());
        self::assertEmpty($demand->getCategories());
        self::assertEmpty($demand->getIncludeSubcategories());
        self::assertEmpty($demand->getOrderField());
        self::assertEmpty($demand->getOrderFieldAllowed());
        self::assertEquals('asc', $demand->getOrderDirection());
        self::assertEquals(0, $demand->getQueryLimit());
    }

    #[Test]
    public function createFaqDemandObjectFromSettingsSetsSettings(): void
    {
        /** @var FaqController $mockController */
        $mockController = $this->getAccessibleMock(FaqController::class, ['redirect']);

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
        self::assertEquals('OR', $demand->getCategoryConjunction());
        self::assertEquals('1,2,3', $demand->getCategories());
        self::assertTrue($demand->getIncludeSubcategories());
        self::assertEquals('name', $demand->getOrderField());
        self::assertEquals('name', $demand->getOrderFieldAllowed());
        self::assertEquals('desc', $demand->getOrderDirection());
        self::assertEquals(1, $demand->getQueryLimit());
    }
}
