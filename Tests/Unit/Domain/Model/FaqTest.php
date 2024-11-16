<?php

namespace Derhansen\PlainFaq\Tests\Unit\Domain\Model;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Domain\Model\Faq;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\BaseTestCase;

/**
 * Test case for FAQ
 */
class FaqTest extends BaseTestCase
{
    protected Faq $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Faq();
    }

    #[Test]
    public function getQuestionReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getQuestion()
        );
    }

    #[Test]
    public function setQuestionForStringSetsQuestion(): void
    {
        $this->subject->setQuestion('Conceived at T3CON10');
        self::assertEquals('Conceived at T3CON10', $this->subject->getQuestion());
    }

    #[Test]
    public function getAnswerReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getAnswer()
        );
    }

    #[Test]
    public function setAnswerForStringSetsAnswer(): void
    {
        $this->subject->setAnswer('Conceived at T3CON10');
        self::assertEquals('Conceived at T3CON10', $this->subject->getAnswer());
    }

    #[Test]
    public function getKeywordsReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getKeywords()
        );
    }

    #[Test]
    public function setKeywordsForStringSetsKeywords(): void
    {
        $this->subject->setKeywords('Conceived at T3CON10');
        self::assertEquals('Conceived at T3CON10', $this->subject->getKeywords());
    }

    #[Test]
    public function getImagesReturnsInitialValueForFileReference(): void
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getImages()
        );
    }

    #[Test]
    public function setImagesForFileReferenceSetsImages(): void
    {
        $image = new FileReference();
        $objectStorageHoldingExactlyOneImages = new ObjectStorage();
        $objectStorageHoldingExactlyOneImages->attach($image);
        $this->subject->setImages($objectStorageHoldingExactlyOneImages);
        self::assertEquals($objectStorageHoldingExactlyOneImages, $this->subject->getImages());
    }

    #[Test]
    public function addImageToObjectStorageHoldingImages(): void
    {
        $image = new FileReference();
        $imagesObjectStorageMock = $this->createMock(ObjectStorage::class);
        $imagesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($image));
        $this->subject->setImages($imagesObjectStorageMock);

        $this->subject->addImage($image);
    }

    #[Test]
    public function removeImageFromObjectStorageHoldingImages(): void
    {
        $image = new FileReference();
        $imagesObjectStorageMock = $this->createMock(ObjectStorage::class);
        $imagesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($image));
        $this->subject->setImages($imagesObjectStorageMock);

        $this->subject->removeImage($image);
    }

    #[Test]
    public function getFilesReturnsInitialValueForFileReference(): void
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getFiles()
        );
    }

    #[Test]
    public function setFilesForFileReferenceSetsFiles(): void
    {
        $file = new FileReference();
        $objectStorageHoldingExactlyOneFiles = new ObjectStorage();
        $objectStorageHoldingExactlyOneFiles->attach($file);
        $this->subject->setFiles($objectStorageHoldingExactlyOneFiles);
        self::assertEquals($objectStorageHoldingExactlyOneFiles, $this->subject->getFiles());
    }

    #[Test]
    public function addFileToObjectStorageHoldingFiles(): void
    {
        $file = new FileReference();
        $filesObjectStorageMock = $this->createMock(ObjectStorage::class);
        $filesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($file));
        $this->subject->setFiles($filesObjectStorageMock);

        $this->subject->addFile($file);
    }

    #[Test]
    public function removeFileFromObjectStorageHoldingFiles(): void
    {
        $file = new FileReference();
        $filesObjectStorageMock = $this->createMock(ObjectStorage::class);
        $filesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($file));
        $this->subject->setFiles($filesObjectStorageMock);

        $this->subject->removeFile($file);
    }

    #[Test]
    public function getRelatedReturnsInitialValueForObjectStorage(): void
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getRelated()
        );
    }

    #[Test]
    public function setRelatedForFileReferenceSetsRelated(): void
    {
        $faq = new Faq();
        $objectStorageHoldingExactlyOneFaq = new ObjectStorage();
        $objectStorageHoldingExactlyOneFaq->attach($faq);
        $this->subject->setRelated($objectStorageHoldingExactlyOneFaq);
        self::assertEquals($objectStorageHoldingExactlyOneFaq, $this->subject->getRelated());
    }

    #[Test]
    public function addFaqToObjectStorageHoldingFaqs(): void
    {
        $faq = new Faq();
        $faqObjectStorageMock = $this->createMock(ObjectStorage::class);
        $faqObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($faq));
        $this->subject->setRelated($faqObjectStorageMock);

        $this->subject->addRelated($faq);
    }

    #[Test]
    public function removeFaqFromObjectStorageHoldingFaqs(): void
    {
        $faq = new Faq();
        $faqObjectStorageMock = $this->createMock(ObjectStorage::class);
        $faqObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($faq));
        $this->subject->setRelated($faqObjectStorageMock);

        $this->subject->removeRelated($faq);
    }

    #[Test]
    public function getCategoriesReturnsInitialValueForObjectStorage(): void
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getCategories()
        );
    }

    #[Test]
    public function setCategoriesForCategorySetsCategory(): void
    {
        $category = new Category();
        $objectStorageHoldingExactlyOneCategory = new ObjectStorage();
        $objectStorageHoldingExactlyOneCategory->attach($category);
        $this->subject->setCategories($objectStorageHoldingExactlyOneCategory);
        self::assertEquals($objectStorageHoldingExactlyOneCategory, $this->subject->getCategories());
    }

    #[Test]
    public function addCategoryToObjectStorageHoldingCategories(): void
    {
        $category = new Category();
        $categoryObjectStorageMock = $this->createMock(ObjectStorage::class);
        $categoryObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($category));
        $this->subject->setCategories($categoryObjectStorageMock);

        $this->subject->addCategory($category);
    }

    #[Test]
    public function removeCategoryFromObjectStorageHoldingFaqs(): void
    {
        $category = new Category();
        $categoryObjectStorageMock = $this->createMock(ObjectStorage::class);
        $categoryObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($category));
        $this->subject->setCategories($categoryObjectStorageMock);

        $this->subject->removeCategory($category);
    }
}
