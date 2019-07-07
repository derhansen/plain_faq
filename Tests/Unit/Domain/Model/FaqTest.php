<?php
namespace Derhansen\PlainFaq\Tests\Unit\Domain\Model;

use TYPO3\TestingFramework\Core\BaseTestCase;

/**
 * Test case.
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

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneImages,
            'images',
            $this->subject
        );
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

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneFiles,
            'files',
            $this->subject
        );
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
}
