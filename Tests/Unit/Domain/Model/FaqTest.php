<?php

namespace Derhansen\PlainFaq\Tests\Unit\Domain\Model;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Domain\Model\Faq;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\BaseTestCase;

/**
 * Test case for FAQ
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class FaqTest extends BaseTestCase
{
    /**
     * @var \Derhansen\PlainFaq\Domain\Model\Faq
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Faq();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getQuestionReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getQuestion()
        );
    }

    /**
     * @test
     */
    public function setQuestionForStringSetsQuestion()
    {
        $this->subject->setQuestion('Conceived at T3CON10');
        self::assertEquals('Conceived at T3CON10', $this->subject->getQuestion());
    }

    /**
     * @test
     */
    public function getAnswerReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getAnswer()
        );
    }

    /**
     * @test
     */
    public function setAnswerForStringSetsAnswer()
    {
        $this->subject->setAnswer('Conceived at T3CON10');
        self::assertEquals('Conceived at T3CON10', $this->subject->getAnswer());
    }

    /**
     * @test
     */
    public function getKeywordsReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getKeywords()
        );
    }

    /**
     * @test
     */
    public function setKeywordsForStringSetsKeywords()
    {
        $this->subject->setKeywords('Conceived at T3CON10');
        self::assertEquals('Conceived at T3CON10', $this->subject->getKeywords());
    }

    /**
     * @test
     */
    public function getImagesReturnsInitialValueForFileReference()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getImages()
        );
    }

    /**
     * @test
     */
    public function setImagesForFileReferenceSetsImages()
    {
        $image = new FileReference();
        $objectStorageHoldingExactlyOneImages = new ObjectStorage();
        $objectStorageHoldingExactlyOneImages->attach($image);
        $this->subject->setImages($objectStorageHoldingExactlyOneImages);
        self::assertEquals($objectStorageHoldingExactlyOneImages, $this->subject->getImages());
    }

    /**
     * @test
     */
    public function addImageToObjectStorageHoldingImages()
    {
        $image = new FileReference();
        $imagesObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $imagesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($image));
        $this->subject->setImages($imagesObjectStorageMock);

        $this->subject->addImage($image);
    }

    /**
     * @test
     */
    public function removeImageFromObjectStorageHoldingImages()
    {
        $image = new FileReference();
        $imagesObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $imagesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($image));
        $this->subject->setImages($imagesObjectStorageMock);

        $this->subject->removeImage($image);
    }

    /**
     * @test
     */
    public function getFilesReturnsInitialValueForFileReference()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getFiles()
        );
    }

    /**
     * @test
     */
    public function setFilesForFileReferenceSetsFiles()
    {
        $file = new FileReference();
        $objectStorageHoldingExactlyOneFiles = new ObjectStorage();
        $objectStorageHoldingExactlyOneFiles->attach($file);
        $this->subject->setFiles($objectStorageHoldingExactlyOneFiles);
        self::assertEquals($objectStorageHoldingExactlyOneFiles, $this->subject->getFiles());
    }

    /**
     * @test
     */
    public function addFileToObjectStorageHoldingFiles()
    {
        $file = new FileReference();
        $filesObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $filesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($file));
        $this->subject->setFiles($filesObjectStorageMock);

        $this->subject->addFile($file);
    }

    /**
     * @test
     */
    public function removeFileFromObjectStorageHoldingFiles()
    {
        $file = new FileReference();
        $filesObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $filesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($file));
        $this->subject->setFiles($filesObjectStorageMock);

        $this->subject->removeFile($file);
    }

    /**
     * @test
     */
    public function getRelatedReturnsInitialValueForObjectStorage()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getRelated()
        );
    }

    /**
     * @test
     */
    public function setRelatedForFileReferenceSetsRelated()
    {
        $faq = new Faq();
        $objectStorageHoldingExactlyOneFaq = new ObjectStorage();
        $objectStorageHoldingExactlyOneFaq->attach($faq);
        $this->subject->setRelated($objectStorageHoldingExactlyOneFaq);
        self::assertEquals($objectStorageHoldingExactlyOneFaq, $this->subject->getRelated());
    }

    /**
     * @test
     */
    public function addFaqToObjectStorageHoldingFaqs()
    {
        $faq = new Faq();
        $faqObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $faqObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($faq));
        $this->subject->setRelated($faqObjectStorageMock);

        $this->subject->addRelated($faq);
    }

    /**
     * @test
     */
    public function removeFaqFromObjectStorageHoldingFaqs()
    {
        $faq = new Faq();
        $faqObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $faqObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($faq));
        $this->subject->setRelated($faqObjectStorageMock);

        $this->subject->removeRelated($faq);
    }

    /**
     * @test
     */
    public function getCategoriesReturnsInitialValueForObjectStorage()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function setCategoriesForCategorySetsCategory()
    {
        $category = new Category();
        $objectStorageHoldingExactlyOneCategory = new ObjectStorage();
        $objectStorageHoldingExactlyOneCategory->attach($category);
        $this->subject->setCategories($objectStorageHoldingExactlyOneCategory);
        self::assertEquals($objectStorageHoldingExactlyOneCategory, $this->subject->getCategories());
    }

    /**
     * @test
     */
    public function addCategoryToObjectStorageHoldingCategories()
    {
        $category = new Category();
        $categoryObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $categoryObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($category));
        $this->subject->setCategories($categoryObjectStorageMock);

        $this->subject->addCategory($category);
    }

    /**
     * @test
     */
    public function removeCategoryFromObjectStorageHoldingFaqs()
    {
        $category = new Category();
        $categoryObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $categoryObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($category));
        $this->subject->setCategories($categoryObjectStorageMock);

        $this->subject->removeCategory($category);
    }
}
