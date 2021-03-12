<?php

namespace Derhansen\PlainFaq\Tests\Unit\Domain\Model;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use TYPO3\TestingFramework\Core\BaseTestCase;

/**
 * Test case for FaqDemand
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class FaqDemandTest extends BaseTestCase
{
    /**
     * @var \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new FaqDemand();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function orderFieldReturnsInitialValue()
    {
        self::assertEquals('', $this->subject->getOrderField());
    }

    /**
     * @test
     */
    public function orderFieldCanBeSet()
    {
        $this->subject->setOrderField('order');
        self::assertEquals('order', $this->subject->getOrderField());
    }

    /**
     * @test
     */
    public function orderFieldAllowedReturnsInitialValue()
    {
        self::assertEquals('', $this->subject->getOrderFieldAllowed());
    }

    /**
     * @test
     */
    public function orderFieldAllowedCanBeSet()
    {
        $this->subject->setOrderFieldAllowed('title');
        self::assertEquals('title', $this->subject->getOrderFieldAllowed());
    }

    /**
     * @test
     */
    public function orderDirectionReturnsInitalValue()
    {
        self::assertEquals('', $this->subject->getOrderDirection());
    }

    /**
     * @test
     */
    public function orderDirectionCanBeSet()
    {
        $this->subject->setOrderDirection('asc');
        self::assertEquals('asc', $this->subject->getOrderDirection());
    }

    /**
     * @test
     */
    public function categoriesReturnsInitialValue()
    {
        self::assertEquals('', $this->subject->getCategories());
    }

    /**
     * @test
     */
    public function categoriesCanBeSet()
    {
        $this->subject->setCategories('1,2,3');
        self::assertEquals('1,2,3', $this->subject->getCategories());
    }

    /**
     * @test
     */
    public function includeSubcategoriesReturnsInitialValue()
    {
        self::assertFalse($this->subject->getIncludeSubcategories());
    }

    /**
     * @test
     */
    public function includeSubcategoriesCanBeSet()
    {
        $this->subject->setIncludeSubcategories(true);
        self::assertTrue($this->subject->getIncludeSubcategories());
    }

    /**
     * @test
     */
    public function categoryConjunctionReturnsInitialValue()
    {
        self::assertEquals('', $this->subject->getCategoryConjunction());
    }

    /**
     * @test
     */
    public function categoryConjunctionCanBeSet()
    {
        $this->subject->setCategoryConjunction('and');
        self::assertEquals('and', $this->subject->getCategoryConjunction());
    }

    /**
     * @test
     */
    public function storagePageReturnsInitialValue()
    {
        self::assertEquals('', $this->subject->getStoragePage());
    }

    /**
     * @test
     */
    public function storagePageCanBeSet()
    {
        $this->subject->setStoragePage('1,2,3');
        self::assertEquals('1,2,3', $this->subject->getStoragePage());
    }

    /**
     * @test
     */
    public function queryLimitReturnsDefaultValue()
    {
        self::assertSame(0, $this->subject->getQueryLimit());
    }

    /**
     * @test
     */
    public function queryLimitCanBeSet()
    {
        $this->subject->setQueryLimit(2);
        self::assertSame(2, $this->subject->getQueryLimit());
    }
}
