<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Controller;

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use Derhansen\PlainFaq\Domain\Model\Faq;
use Derhansen\PlainFaq\Domain\Repository\FaqRepository;
use Derhansen\PlainFaq\Pagination\NumberedPagination;
use Derhansen\PlainFaq\Service\FaqCacheService;
use Derhansen\PlainFaq\Utility\PageUtility;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Frontend\Controller\ErrorController;

/**
 * FaqController
 */
class FaqController extends ActionController
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
    protected $faqRepository;

    /**
     * @var FaqCacheService
     */
    protected $faqCacheService;

    /**
     * @param FaqRepository $faqRepository
     */
    public function injectFaqRepository(FaqRepository $faqRepository)
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
    protected function initializeView(ViewInterface $view)
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
     * @return FaqDemand
     */
    public function createFaqDemandObjectFromSettings(array $settings): FaqDemand
    {
        /** @var \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand $demand */
        $demand = $this->objectManager->get(FaqDemand::class);
        $demand->setStoragePage(PageUtility::extendPidListByChildren(
            $settings['storagePage'] ?? '',
            isset($settings['recursive']) ? (int)$settings['recursive'] : 0
        ));
        $demand->setCategoryConjunction($settings['categoryConjunction'] ?? '');
        $demand->setCategories($settings['categories'] ?? '');
        $demand->setIncludeSubcategories(
            isset($settings['includeSubcategories']) ? (bool)$settings['includeSubcategories'] : false
        );
        $demand->setOrderField($settings['orderField'] ?? '');
        $demand->setOrderFieldAllowed($settings['orderFieldAllowed'] ?? '');
        $demand->setOrderDirection($settings['orderDirection'] ?? 'asc');
        $demand->setQueryLimit(isset($settings['queryLimit']) ? (int)$settings['queryLimit'] : 0);

        return $demand;
    }

    /**
     * List action
     *
     * @param array $overwriteDemand
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
            'overwriteDemand' => $overwriteDemand,
            'pagination' => $this->getPagination($faqs)
        ];

        $this->signalDispatch(__CLASS__, __FUNCTION__ . 'BeforeRenderView', [&$values, $this]);

        $this->view->assignMultiple($values);

        $this->faqCacheService->addPageCacheTagsByFaqDemandObject($faqDemand);
    }

    /**
     * Returns an array with variables for the pagination
     *
     * @param QueryResultInterface $faqs
     * @return array
     */
    protected function getPagination(QueryResultInterface $faqs): array
    {
        $pagination = [];
        $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
        if ((bool)$this->settings['enablePagination'] && (int)$this->settings['itemsPerPage'] > 0) {
            $paginator = new QueryResultPaginator($faqs, $currentPage, (int)$this->settings['itemsPerPage']);
            $pagination = new NumberedPagination($paginator, (int)$this->settings['maxNumPages']);
            $pagination = [
                'paginator' => $paginator,
                'pagination' => $pagination,
            ];
        }

        return $pagination;
    }

    /**
     * Detail action
     *
     * @param Faq|null $faq
     */
    public function detailAction(Faq $faq = null)
    {
        if (is_null($faq)) {
            $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction(
                $GLOBALS['TYPO3_REQUEST'],
                'FAQ not found.'
            );
            throw new ImmediateResponseException($response, 1549896549734);
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
    protected function isOverwriteDemand(array $overwriteDemand): bool
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
    protected function overwriteFaqDemandObject(FaqDemand $demand, array $overwriteDemand): FaqDemand
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
    protected function signalDispatch(string $signalClassName, string $signalName, array $arguments)
    {
        return $this->signalSlotDispatcher->dispatch($signalClassName, $signalName, $arguments);
    }
}
