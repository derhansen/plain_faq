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
use Derhansen\PlainFaq\Event\ModifyDetailViewVariablesEvent;
use Derhansen\PlainFaq\Event\ModifyListViewVariablesEvent;
use Derhansen\PlainFaq\Pagination\NumberedPagination;
use Derhansen\PlainFaq\Service\FaqCacheService;
use Derhansen\PlainFaq\Utility\PageUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class FaqController extends ActionController
{
    /**
     * Properties in this array will be ignored by overwriteDemandObject()
     */
    protected array $ignoredSettingsForOverwriteDemand = ['orderfieldallowed'];

    protected FaqRepository $faqRepository;
    protected FaqCacheService $faqCacheService;

    public function injectFaqRepository(FaqRepository $faqRepository)
    {
        $this->faqRepository = $faqRepository;
    }

    public function injectFaqCacheService(FaqCacheService $cacheService)
    {
        $this->faqCacheService = $cacheService;
    }

    /**
     * Assign contentObjectData and pageData view
     */
    protected function initializeView(): void
    {
        $this->view->assign('contentObjectData', $this->request->getAttribute('currentContentObject')->data);
        if ($this->getTypoScriptFrontendController()) {
            $this->view->assign('pageData', $this->getTypoScriptFrontendController()->page);
        }
    }

    /**
     * Creates an faq demand object with the given settings
     */
    public function createFaqDemandObjectFromSettings(array $settings): FaqDemand
    {
        $demand = GeneralUtility::makeInstance(FaqDemand::class);
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
     */
    public function listAction(array $overwriteDemand = []): ResponseInterface
    {
        $faqDemand = $this->createFaqDemandObjectFromSettings($this->settings);
        if ($this->isOverwriteDemand($overwriteDemand)) {
            $faqDemand = $this->overwriteFaqDemandObject($faqDemand, $overwriteDemand);
        }

        $faqs = $this->faqRepository->findDemanded($faqDemand);

        $variables = [
            'faqs' => $faqs,
            'faqDemand' => $faqDemand,
            'overwriteDemand' => $overwriteDemand,
            'pagination' => $this->getPagination($faqs),
        ];

        $modifyListViewVariablesEvent = new ModifyListViewVariablesEvent($variables, $this);
        $this->eventDispatcher->dispatch($modifyListViewVariablesEvent);
        $variables = $modifyListViewVariablesEvent->getVariables();
        $this->view->assignMultiple($variables);

        $this->faqCacheService->addPageCacheTagsByFaqDemandObject($faqDemand);

        return $this->htmlResponse();
    }

    /**
     * Returns an array with variables for the pagination
     */
    protected function getPagination(QueryResultInterface $faqs): array
    {
        $pagination = [];
        $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
        if ((bool)($this->settings['enablePagination'] ?? false) && (int)($this->settings['itemsPerPage'] ?? 10) > 0) {
            $paginator = new QueryResultPaginator($faqs, $currentPage, (int)($this->settings['itemsPerPage'] ?? 10));
            $pagination = new NumberedPagination($paginator, (int)($this->settings['maxNumPages'] ?? 10));
            $pagination = [
                'paginator' => $paginator,
                'pagination' => $pagination,
            ];
        }

        return $pagination;
    }

    /**
     * Detail action
     */
    public function detailAction(?Faq $faq = null): ResponseInterface
    {
        if (is_null($faq)) {
            $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction(
                $this->request,
                'FAQ not found.'
            );
            throw new PropagateResponseException($response, 1634749577);
        }

        $modifyDetailViewVariablesEvent = new ModifyDetailViewVariablesEvent(['faq' => $faq], $this);
        $this->eventDispatcher->dispatch($modifyDetailViewVariablesEvent);
        $variables = $modifyDetailViewVariablesEvent->getVariables();

        $this->view->assignMultiple($variables);
        $this->faqCacheService->addCacheTagsByFaqRecords([$faq]);

        return $this->htmlResponse();
    }

    /**
     * Returns if a demand object can be overwritten with the given overwriteDemand array
     */
    protected function isOverwriteDemand(array $overwriteDemand): bool
    {
        return (int)($this->settings['disableOverwriteDemand'] ?? 0) !== 1 && $overwriteDemand !== [];
    }

    /**
     * Overwrites a given demand object by an propertyName =>  $propertyValue array
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

    protected function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?? null;
    }
}
