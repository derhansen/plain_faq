<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Domain\Model\Dto;

/**
 * FaqDemand
 */
class FaqDemand extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Order field
     *
     * @var string
     */
    protected $orderField = '';

    /**
     * Allowed order fields
     *
     * @var string
     */
    protected $orderFieldAllowed = '';

    /**
     * Order direction
     *
     * @var string
     */
    protected $orderDirection = '';

    /**
     * Categories separated by comma
     *
     * @var string
     */
    protected $categories = '';

    /**
     * Include subcategories
     *
     * @var bool
     */
    protected $includeSubcategories = false;

    /**
     * Category Conjunction
     *
     * @var string
     */
    protected $categoryConjunction = '';

    /**
     * Storage page
     *
     * @var string
     */
    protected $storagePage = '';

    /**
     * @return string
     */
    public function getOrderField(): string
    {
        return $this->orderField;
    }

    /**
     * @param string $orderField
     */
    public function setOrderField(string $orderField)
    {
        $this->orderField = $orderField;
    }

    /**
     * @return string
     */
    public function getOrderFieldAllowed(): string
    {
        return $this->orderFieldAllowed;
    }

    /**
     * @param string $orderFieldAllowed
     */
    public function setOrderFieldAllowed(string $orderFieldAllowed)
    {
        $this->orderFieldAllowed = $orderFieldAllowed;
    }

    /**
     * @return string
     */
    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    /**
     * @param string $orderDirection
     */
    public function setOrderDirection(string $orderDirection)
    {
        $this->orderDirection = $orderDirection;
    }

    /**
     * @return string
     */
    public function getCategories(): string
    {
        return $this->categories;
    }

    /**
     * @param string $categories
     */
    public function setCategories(string $categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return bool
     */
    public function getIncludeSubcategories(): bool
    {
        return $this->includeSubcategories;
    }

    /**
     * @param bool $includeSubcategories
     */
    public function setIncludeSubcategories(bool $includeSubcategories)
    {
        $this->includeSubcategories = $includeSubcategories;
    }

    /**
     * @return string
     */
    public function getCategoryConjunction(): string
    {
        return $this->categoryConjunction;
    }

    /**
     * @param string $categoryConjunction
     */
    public function setCategoryConjunction(string $categoryConjunction)
    {
        $this->categoryConjunction = $categoryConjunction;
    }

    /**
     * @return string
     */
    public function getStoragePage(): string
    {
        return $this->storagePage;
    }

    /**
     * @param string $storagePage
     */
    public function setStoragePage(string $storagePage)
    {
        $this->storagePage = $storagePage;
    }
}
