<?php

namespace Derhansen\PlainFaq\Tests\Unit\Domain\Model;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\BaseTestCase;

/**
 * Test case for FaqDemand
 */
class FaqDemandTest extends BaseTestCase
{
    protected FaqDemand $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new FaqDemand();
    }

    #[Test]
    public function orderFieldReturnsInitialValue(): void
    {
        self::assertEquals('', $this->subject->getOrderField());
    }

    #[Test]
    public function orderFieldCanBeSet(): void
    {
        $this->subject->setOrderField('order');
        self::assertEquals('order', $this->subject->getOrderField());
    }

    #[Test]
    public function orderFieldAllowedReturnsInitialValue(): void
    {
        self::assertEquals('', $this->subject->getOrderFieldAllowed());
    }

    #[Test]
    public function orderFieldAllowedCanBeSet(): void
    {
        $this->subject->setOrderFieldAllowed('title');
        self::assertEquals('title', $this->subject->getOrderFieldAllowed());
    }

    #[Test]
    public function orderDirectionReturnsInitalValue(): void
    {
        self::assertEquals('', $this->subject->getOrderDirection());
    }

    #[Test]
    public function orderDirectionCanBeSet(): void
    {
        $this->subject->setOrderDirection('asc');
        self::assertEquals('asc', $this->subject->getOrderDirection());
    }

    #[Test]
    public function categoriesReturnsInitialValue(): void
    {
        self::assertEquals('', $this->subject->getCategories());
    }

    #[Test]
    public function categoriesCanBeSet(): void
    {
        $this->subject->setCategories('1,2,3');
        self::assertEquals('1,2,3', $this->subject->getCategories());
    }

    #[Test]
    public function includeSubcategoriesReturnsInitialValue(): void
    {
        self::assertFalse($this->subject->getIncludeSubcategories());
    }

    #[Test]
    public function includeSubcategoriesCanBeSet(): void
    {
        $this->subject->setIncludeSubcategories(true);
        self::assertTrue($this->subject->getIncludeSubcategories());
    }

    #[Test]
    public function categoryConjunctionReturnsInitialValue(): void
    {
        self::assertEquals('', $this->subject->getCategoryConjunction());
    }

    #[Test]
    public function categoryConjunctionCanBeSet(): void
    {
        $this->subject->setCategoryConjunction('and');
        self::assertEquals('and', $this->subject->getCategoryConjunction());
    }

    #[Test]
    public function storagePageReturnsInitialValue(): void
    {
        self::assertEquals('', $this->subject->getStoragePage());
    }

    #[Test]
    public function storagePageCanBeSet(): void
    {
        $this->subject->setStoragePage('1,2,3');
        self::assertEquals('1,2,3', $this->subject->getStoragePage());
    }

    #[Test]
    public function queryLimitReturnsDefaultValue(): void
    {
        self::assertSame(0, $this->subject->getQueryLimit());
    }

    #[Test]
    public function queryLimitCanBeSet(): void
    {
        $this->subject->setQueryLimit(2);
        self::assertSame(2, $this->subject->getQueryLimit());
    }
}
