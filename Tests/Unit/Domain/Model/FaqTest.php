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
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Derhansen\PlainFaq\Domain\Model\Faq();
    }

    protected function tearDown()
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

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'question',
            $this->subject
        );
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

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'answer',
            $this->subject
        );
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

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'keywords',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getImagesReturnsInitialValueForFileReference()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
        $image = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $objectStorageHoldingExactlyOneImages = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneImages->attach($image);
        $this->subject->setImages($objectStorageHoldingExactlyOneImages);
        $this->assertEquals($objectStorageHoldingExactlyOneImages, $this->subject->getImages());
    }

    /**
     * @test
     */
    public function addImageToObjectStorageHoldingImages()
    {
        $image = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $imagesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $imagesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($image));
        $this->inject($this->subject, 'images', $imagesObjectStorageMock);

        $this->subject->addImage($image);
    }

    /**
     * @test
     */
    public function removeImageFromObjectStorageHoldingImages()
    {
        $image = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $imagesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $imagesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($image));
        $this->inject($this->subject, 'images', $imagesObjectStorageMock);

        $this->subject->removeImage($image);
    }

    /**
     * @test
     */
    public function getFilesReturnsInitialValueForFileReference()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
        $file = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $objectStorageHoldingExactlyOneFiles = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneFiles->attach($file);
        $this->subject->setFiles($objectStorageHoldingExactlyOneFiles);
        $this->assertEquals($objectStorageHoldingExactlyOneFiles, $this->subject->getFiles());
    }

    /**
     * @test
     */
    public function addFileToObjectStorageHoldingFiles()
    {
        $file = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $filesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $filesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($file));
        $this->inject($this->subject, 'files', $filesObjectStorageMock);

        $this->subject->addFile($file);
    }

    /**
     * @test
     */
    public function removeFileFromObjectStorageHoldingFiles()
    {
        $file = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $filesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $filesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($file));
        $this->inject($this->subject, 'files', $filesObjectStorageMock);

        $this->subject->removeFile($file);
    }

    /**
     * @test
     */
    public function getRelatedReturnsInitialValueForObjectStorage()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
        $objectStorageHoldingExactlyOneFaq = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneFaq->attach($faq);
        $this->subject->setRelated($objectStorageHoldingExactlyOneFaq);
        $this->assertEquals($objectStorageHoldingExactlyOneFaq, $this->subject->getRelated());
    }

    /**
     * @test
     */
    public function addFaqToObjectStorageHoldingFaqs()
    {
        $faq = new Faq();
        $faqObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $faqObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($faq));
        $this->inject($this->subject, 'related', $faqObjectStorageMock);

        $this->subject->addRelated($faq);
    }

    /**
     * @test
     */
    public function removeFaqFromObjectStorageHoldingFaqs()
    {
        $faq = new Faq();
        $faqObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $faqObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($faq));
        $this->inject($this->subject, 'related', $faqObjectStorageMock);

        $this->subject->removeRelated($faq);
    }

    /**
     * @test
     */
    public function getCategoriesReturnsInitialValueForObjectStorage()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
        $objectStorageHoldingExactlyOneCategory = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneCategory->attach($category);
        $this->subject->setCategories($objectStorageHoldingExactlyOneCategory);
        $this->assertEquals($objectStorageHoldingExactlyOneCategory, $this->subject->getCategories());
    }

    /**
     * @test
     */
    public function addCategoryToObjectStorageHoldingCategories()
    {
        $category = new Category();
        $categoryObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $categoryObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($category));
        $this->inject($this->subject, 'categories', $categoryObjectStorageMock);

        $this->subject->addCategory($category);
    }

    /**
     * @test
     */
    public function removeCategoryFromObjectStorageHoldingFaqs()
    {
        $category = new Category();
        $categoryObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $categoryObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($category));
        $this->inject($this->subject, 'categories', $categoryObjectStorageMock);

        $this->subject->removeCategory($category);
    }

}
