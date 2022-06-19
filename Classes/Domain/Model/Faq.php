<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Faq
 */
class Faq extends AbstractEntity
{
    protected string $question = '';
    protected string $answer = '';
    protected string $keywords = '';

    /**
     * @var ObjectStorage<Category>
     * @Extbase\ORM\Lazy
     */
    protected ObjectStorage $categories;

    /**
     * @var ObjectStorage<FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @Extbase\ORM\Lazy
     */
    protected ObjectStorage $images;

    /**
     * @var ObjectStorage<FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @Extbase\ORM\Lazy
     */
    protected ObjectStorage $files;

    /**
     * @var ObjectStorage<Faq>
     * @Extbase\ORM\Lazy
     */
    protected ObjectStorage $related;

    public function __construct()
    {
        $this->initializeObject();
    }

    /**
     * Initialize all ObjectStorages as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void
    {
        $this->images = new ObjectStorage();
        $this->files = new ObjectStorage();
        $this->related = new ObjectStorage();
        $this->categories = new ObjectStorage();
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function addImage(FileReference $image): void
    {
        $this->images->attach($image);
    }

    public function removeImage(FileReference $imageToRemove): void
    {
        $this->images->detach($imageToRemove);
    }

    public function getImages(): ?ObjectStorage
    {
        return $this->images;
    }

    public function setImages(ObjectStorage $images): void
    {
        $this->images = $images;
    }

    public function addFile(FileReference $file): void
    {
        $this->files->attach($file);
    }

    public function removeFile(FileReference $fileToRemove): void
    {
        $this->files->detach($fileToRemove);
    }

    public function getFiles(): ?ObjectStorage
    {
        return $this->files;
    }

    public function setFiles(ObjectStorage $files): void
    {
        $this->files = $files;
    }

    public function addRelated(Faq $faq): void
    {
        $this->related->attach($faq);
    }

    public function removeRelated(Faq $faq): void
    {
        $this->related->detach($faq);
    }

    public function getRelated(): ?ObjectStorage
    {
        return $this->related;
    }

    public function setRelated(ObjectStorage $related): void
    {
        $this->related = $related;
    }

    public function getCategories(): ?ObjectStorage
    {
        return $this->categories;
    }

    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

    public function addCategory(Category $category): void
    {
        $this->categories->attach($category);
    }

    public function removeCategory(Category $category): void
    {
        $this->categories->detach($category);
    }
}
