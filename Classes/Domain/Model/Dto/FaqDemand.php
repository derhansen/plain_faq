<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Domain\Model\Dto;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class FaqDemand extends AbstractEntity
{
    protected string $orderField = '';
    protected string $orderFieldAllowed = '';
    protected string $orderDirection = '';
    protected string $categories = '';
    protected int $queryLimit = 0;
    protected bool $includeSubcategories = false;
    protected string $categoryConjunction = '';
    protected string $storagePage = '';

    public function getOrderField(): string
    {
        return $this->orderField;
    }

    public function setOrderField(string $orderField): void
    {
        $this->orderField = $orderField;
    }

    public function getOrderFieldAllowed(): string
    {
        return $this->orderFieldAllowed;
    }

    public function setOrderFieldAllowed(string $orderFieldAllowed): void
    {
        $this->orderFieldAllowed = $orderFieldAllowed;
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    public function setOrderDirection(string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }

    public function getCategories(): string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): void
    {
        $this->categories = $categories;
    }

    public function getIncludeSubcategories(): bool
    {
        return $this->includeSubcategories;
    }

    public function setIncludeSubcategories(bool $includeSubcategories): void
    {
        $this->includeSubcategories = $includeSubcategories;
    }

    public function getQueryLimit(): int
    {
        return $this->queryLimit;
    }

    public function setQueryLimit(int $queryLimit): void
    {
        $this->queryLimit = $queryLimit;
    }

    public function getCategoryConjunction(): string
    {
        return $this->categoryConjunction;
    }

    public function setCategoryConjunction(string $categoryConjunction): void
    {
        $this->categoryConjunction = $categoryConjunction;
    }

    public function getStoragePage(): string
    {
        return $this->storagePage;
    }

    public function setStoragePage(string $storagePage): void
    {
        $this->storagePage = $storagePage;
    }
}
