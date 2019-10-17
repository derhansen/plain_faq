<?php
declare(strict_types = 1);
namespace Derhansen\PlainFaq\Controller;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use Derhansen\PlainFaq\Domain\Model\Faq;
use Derhansen\PlainFaq\Domain\Repository\FaqRepository;
use Derhansen\PlainFaq\Service\FaqCacheService;
use Derhansen\PlainFaq\Utility\PageUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * FaqController
 */
class FaqController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * Properties in this array will be ignored by overwriteDemandObject()
     *
     * @var array
     */
    protected $ignoredSettingsForOverwriteDemand = ['orderfieldallowed'];

    /**
     * @var FaqRepository
     */
    protected $faqRepository = null;

    /**
     * @var FaqCacheService
     */
    protected $faqCacheService = null;

    /**
     * @param FaqRepository $faqRepository
     */
    public function injectFaqRepository(\Derhansen\PlainFaq\Domain\Repository\FaqRepository $faqRepository)
    {
        $this->faqRepository = $faqRepository;
    }

    /**
     * @param FaqCacheService $cacheService
     */
    public function injectFaqCacheService(FaqCacheService $cacheService)
    {
        $this->faqCacheService = $cacheService;
    }

    /**
     * Assign contentObjectData and pageData to earch view
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     */
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        $view->assign('contentObjectData', $this->configurationManager->getContentObject()->data);
        if (is_object($GLOBALS['TSFE'])) {
            $view->assign('pageData', $GLOBALS['TSFE']->page);
        }
        parent::initializeView($view);
    }

    /**
     * Creates an faq demand object with the given settings
     *
     * @param array $settings The settings
     *
     * @return \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand
     */
    public function createFaqDemandObjectFromSettings(array $settings): FaqDemand
    {
        /** @var \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand $demand */
        $demand = $this->objectManager->get(FaqDemand::class);
        $demand->setStoragePage(PageUtility::extendPidListByChildren(
            $settings['storagePage'] ?? '',
            $settings['recursive'] !== null ? (int)$settings['recursive'] : 0
        ));
        $demand->setCategoryConjunction($settings['categoryConjunction'] ?? '');
        $demand->setCategories($settings['categories'] ?? '');
        $demand->setIncludeSubcategories(
            $settings['includeSubcategories'] !== null ? (bool)$settings['includeSubcategories'] : false
        );
        $demand->setOrderField($settings['orderField'] ?? '');
        $demand->setOrderFieldAllowed($settings['orderFieldAllowed'] ?? '');
        $demand->setOrderDirection($settings['orderDirection'] ?? 'asc');

        return $demand;
    }

    /**
     * List action
     *
     * @param array $overwriteDemand
     * @return void
     */
    public function listAction(array $overwriteDemand = [])
    {
        $faqDemand = $this->createFaqDemandObjectFromSettings($this->settings);
        if ($this->isOverwriteDemand($overwriteDemand)) {
            $faqDemand = $this->overwriteFaqDemandObject($faqDemand, $overwriteDemand);
        }

        $faqs = $this->faqRepository->findDemanded($faqDemand);

        $values = [
            'faqs' => $faqs,
            'faqDemand' => $faqDemand,
        ];

        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [&$values, $this]);

        $this->view->assignMultiple($values);

        $this->faqCacheService->addPageCacheTagsByFaqDemandObject($faqDemand);
    }

    /**
     * Detail action
     *
     * @param Faq|null $faq
     *
     * @return void
     */
    public function detailAction(Faq $faq = null)
    {
        if (is_null($faq)) {
            $this->getTypoScriptFrontendController()->pageNotFoundAndExit('FAQ article not found.');
        }

        $values = ['faq' => $faq];
        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [&$values, $this]);

        $this->view->assignMultiple($values);

        if ($faq !== null) {
            $this->faqCacheService->addCacheTagsByFaqRecords([$faq]);
        }
    }

    /**
     * Returns if a demand object can be overwritten with the given overwriteDemand array
     *
     * @param array $overwriteDemand
     * @return bool
     */
    protected function isOverwriteDemand($overwriteDemand)
    {
        return (int)$this->settings['disableOverwriteDemand'] !== 1 && $overwriteDemand !== [];
    }

    /**
     * Overwrites a given demand object by an propertyName =>  $propertyValue array
     *
     * @param FaqDemand $demand Demand
     * @param array $overwriteDemand OwerwriteDemand
     *
     * @return FaqDemand
     */
    protected function overwriteFaqDemandObject(FaqDemand $demand, array $overwriteDemand)
    {
        foreach ($this->ignoredSettingsForOverwriteDemand as $property) {
            unset($overwriteDemand[$property]);
        }

        foreach ($overwriteDemand as $propertyName => $propertyValue) {
            if (in_array(strtolower($propertyName), $this->ignoredSettingsForOverwriteDemand, true)) {
                continue;
            }
            ObjectAccess::setProperty($demand, $propertyName, $propertyValue);
        }

        return $demand;
    }

    /**
     * Dispatches the signal with the given name
     *
     * @param string $signalClassName
     * @param string $signalName
     * @param array $arguments
     *
     * @return mixed
     */
    protected function signalDispatch($signalClassName, $signalName, array $arguments)
    {
        return $this->signalSlotDispatcher->dispatch($signalClassName, $signalName, $arguments);
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'] ?: null;
    }
}