<?php
namespace Derhansen\PlainFaq\Domain\Model;

/***
 *
 * This file is part of the "Plain FAQ" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Torben Hansen <derhansen@gmail.com>
 *
 ***/

/**
 * Faq
 */
class Faq extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * question
     *
     * @var string
     */
    protected $question = '';

    /**
     * answer
     *
     * @var string
     */
    protected $answer = '';

    /**
     * keywords
     *
     * @var string
     */
    protected $keywords = '';

    /**
     * images
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @cascade remove
     */
    protected $images = null;

    /**
     * files
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @cascade remove
     */
    protected $files = null;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->images = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->files = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the question
     *
     * @return string $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Sets the question
     *
     * @param string $question
     * @return void
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * Returns the answer
     *
     * @return string $answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Sets the answer
     *
     * @param string $answer
     * @return void
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * Returns the keywords
     *
     * @return string $keywords
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Sets the keywords
     *
     * @param string $keywords
     * @return void
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Adds a FileReference
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function addImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
    {
        $this->images->attach($image);
    }

    /**
     * Removes a FileReference
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $imageToRemove The FileReference to be removed
     * @return void
     */
    public function removeImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $imageToRemove)
    {
        $this->images->detach($imageToRemove);
    }

    /**
     * Returns the images
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Sets the images
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     * @return void
     */
    public function setImages(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $images)
    {
        $this->images = $images;
    }

    /**
     * Adds a FileReference
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $file
     * @return void
     */
    public function addFile(\TYPO3\CMS\Extbase\Domain\Model\FileReference $file)
    {
        $this->files->attach($file);
    }

    /**
     * Removes a FileReference
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileToRemove The FileReference to be removed
     * @return void
     */
    public function removeFile(\TYPO3\CMS\Extbase\Domain\Model\FileReference $fileToRemove)
    {
        $this->files->detach($fileToRemove);
    }

    /**
     * Returns the files
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $files
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Sets the files
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $files
     * @return void
     */
    public function setFiles(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $files)
    {
        $this->files = $files;
    }
}
