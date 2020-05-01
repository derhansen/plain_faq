<?php
namespace Derhansen\PlainFaq\Tests\Unit\Domain\Model;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
    protected $subject = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand();
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
        $this->assertEquals('', $this->subject->getOrderField());
    }

    /**
     * @test
     */
    public function orderFieldCanBeSet()
    {
        $this->subject->setOrderField('order');
        $this->assertEquals('order', $this->subject->getOrderField());
    }

    /**
     * @test
     */
    public function orderFieldAllowedReturnsInitialValue()
    {
        $this->assertEquals('', $this->subject->getOrderFieldAllowed());
    }

    /**
     * @test
     */
    public function orderFieldAllowedCanBeSet()
    {
        $this->subject->setOrderFieldAllowed('title');
        $this->assertEquals('title', $this->subject->getOrderFieldAllowed());
    }

    /**
     * @test
     */
    public function orderDirectionReturnsInitalValue()
    {
        $this->assertEquals('', $this->subject->getOrderDirection());
    }

    /**
     * @test
     */
    public function orderDirectionCanBeSet()
    {
        $this->subject->setOrderDirection('asc');
        $this->assertEquals('asc', $this->subject->getOrderDirection());
    }

    /**
     * @test
     */
    public function categoriesReturnsInitialValue()
    {
        $this->assertEquals('', $this->subject->getCategories());
    }

    /**
     * @test
     */
    public function categoriesCanBeSet()
    {
        $this->subject->setCategories('1,2,3');
        $this->assertEquals('1,2,3', $this->subject->getCategories());
    }

    /**
     * @test
     */
    public function includeSubcategoriesReturnsInitialValue()
    {
        $this->assertFalse($this->subject->getIncludeSubcategories());
    }

    /**
     * @test
     */
    public function includeSubcategoriesCanBeSet()
    {
        $this->subject->setIncludeSubcategories(true);
        $this->assertTrue($this->subject->getIncludeSubcategories());
    }

    /**
     * @test
     */
    public function categoryConjunctionReturnsInitialValue()
    {
        $this->assertEquals('', $this->subject->getCategoryConjunction());
    }

    /**
     * @test
     */
    public function categoryConjunctionCanBeSet()
    {
        $this->subject->setCategoryConjunction('and');
        $this->assertEquals('and', $this->subject->getCategoryConjunction());
    }

    /**
     * @test
     */
    public function storagePageReturnsInitialValue()
    {
        $this->assertEquals('', $this->subject->getStoragePage());
    }

    /**
     * @test
     */
    public function storagePageCanBeSet()
    {
        $this->subject->setStoragePage('1,2,3');
        $this->assertEquals('1,2,3', $this->subject->getStoragePage());
    }
}
