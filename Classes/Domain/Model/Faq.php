<?php
declare(strict_types = 1);
namespace Derhansen\PlainFaq\Domain\Model;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Faq
 */
class Faq extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $question = '';

    /**
     * @var string
     */
    protected $answer = '';

    /**
     * @var string
     */
    protected $keywords = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    protected $categories = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $images = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $files = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Derhansen\PlainFaq\Domain\Model\Faq>
     */
    protected $related = null;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->images = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->files = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->related = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @return string $question
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @param string $question
     * @return void
     */
    public function setQuestion(string $question)
    {
        $this->question = $question;
    }

    /**
     * @return string $answer
     */
    public function getAnswer(): string
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     * @return void
     */
    public function setAnswer(string $answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return string $keywords
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     * @return void
     */
    public function setKeywords(string $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function addImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
    {
        $this->images->attach($image);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $imageToRemove The FileReference to be removed
     * @return void
     */
    public function removeImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $imageToRemove)
    {
        $this->images->detach($imageToRemove);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     */
    public function getImages(): ObjectStorage
    {
        return $this->images;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     * @return void
     */
    public function setImages(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $images)
    {
        $this->images = $images;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $file
     * @return void
     */
    public function addFile(\TYPO3\CMS\Extbase\Domain\Model\FileReference $file)
    {
        $this->files->attach($file);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileToRemove The FileReference to be removed
     * @return void
     */
    public function removeFile(\TYPO3\CMS\Extbase\Domain\Model\FileReference $fileToRemove)
    {
        $this->files->detach($fileToRemove);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $files
     */
    public function getFiles(): ObjectStorage
    {
        return $this->files;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $files
     * @return void
     */
    public function setFiles(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $files)
    {
        $this->files = $files;
    }

    /**
     * @param \Derhansen\PlainFaq\Domain\Model\Faq $faq
     * @return void
     */
    public function addRelated(\Derhansen\PlainFaq\Domain\Model\Faq $faq)
    {
        $this->related->attach($faq);
    }

    /**
     * @param \Derhansen\PlainFaq\Domain\Model\Faq $faq
     * @return void
     */
    public function removeRelated(\Derhansen\PlainFaq\Domain\Model\Faq $faq)
    {
        $this->related->detach($faq);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Derhansen\PlainFaq\Domain\Model\Faq> $related
     */
    public function getRelated(): ObjectStorage
    {
        return $this->related;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Derhansen\PlainFaq\Domain\Model\Faq> $related
     * @return void
     */
    public function setRelated(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $related)
    {
        $this->related = $related;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param ObjectStorage $categories
     */
    public function setCategories(ObjectStorage $categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     */
    public function addCategory(Category $category)
    {
        $this->categories->attach($category);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $category
     */
    public function removeCategory(Category $category)
    {
        $this->categories->detach($category);
    }
}
