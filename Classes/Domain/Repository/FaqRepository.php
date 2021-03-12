<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Domain\Repository;

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use Derhansen\PlainFaq\Event\ModifyFaqQueryConstraintsEvent;
use Derhansen\PlainFaq\Utility\CategoryUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * The repository for Faqs
 */
class FaqRepository extends Repository
{
    /**
     * Set default sorting
     *
     * @var array
     */
    protected $defaultOrderings = [
        'question' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function injectEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Disable the use of storage records, because the StoragePage can be set
     * in the plugin
     */
    public function initializeObject()
    {
        $this->defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $this->defaultQuerySettings->setRespectStoragePage(false);
    }

    /**
     * Returns faq articles matching the demands of the given demand object
     *
     * @param FaqDemand $faqDemand
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findDemanded(FaqDemand $faqDemand)
    {
        $constraints = [];
        $query = $this->createQuery();

        $this->setStoragePageConstraint($query, $faqDemand, $constraints);
        $this->setCategoryConstraint($query, $faqDemand, $constraints);

        $modifyFaqQueryConstraintsEvent = new ModifyFaqQueryConstraintsEvent(
            $constraints,
            $query,
            $faqDemand,
            $this
        );
        $this->eventDispatcher->dispatch($modifyFaqQueryConstraintsEvent);
        $constraints = $modifyFaqQueryConstraintsEvent->getConstraints();

        $this->setOrderingsFromDemand($query, $faqDemand);

        if (count($constraints) > 0) {
            $query->matching($query->logicalAnd($constraints));
        }

        $this->setQueryLimitFromDemand($query, $faqDemand);

        return $query->execute();
    }

    /**
     * Sets the storagePage constraint to the given constraints array
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand $faqDemand
     * @param array $constraints Constraints
     */
    protected function setStoragePageConstraint(QueryInterface $query, FaqDemand $faqDemand, array &$constraints)
    {
        if ($faqDemand->getStoragePage() && $faqDemand->getStoragePage() !== '') {
            $pidList = GeneralUtility::intExplode(',', $faqDemand->getStoragePage(), true);
            $constraints[] = $query->in('pid', $pidList);
        }
    }

    /**
     * Sets the category constraint to the given constraints array
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand $faqDemand
     * @param array $constraints Constraints
     */
    protected function setCategoryConstraint(QueryInterface $query, FaqDemand $faqDemand, array &$constraints)
    {
        // If no category constraint is set, categories should not be respected in the query
        if ($faqDemand->getCategoryConjunction() === '') {
            return;
        }

        if ($faqDemand->getCategories() !== '') {
            $categoryConstraints = [];
            if ($faqDemand->getIncludeSubcategories()) {
                $categoryList = CategoryUtility::getCategoryListWithChilds($faqDemand->getCategories());
                $categories = GeneralUtility::intExplode(',', $categoryList, true);
            } else {
                $categories = GeneralUtility::intExplode(',', $faqDemand->getCategories(), true);
            }
            foreach ($categories as $category) {
                $categoryConstraints[] = $query->contains('categories', $category);
            }
            if (count($categoryConstraints) > 0) {
                $constraints[] = $this->getCategoryConstraint($query, $faqDemand, $categoryConstraints);
            }
        }
    }

    /**
     * Returns the category constraint depending on the category conjunction configured in faqDemand
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand $faqDemand
     * @param array $categoryConstraints
     * @return mixed
     */
    public function getCategoryConstraint(QueryInterface $query, FaqDemand $faqDemand, array $categoryConstraints)
    {
        switch (strtolower($faqDemand->getCategoryConjunction())) {
            case 'and':
                $constraint = $query->logicalAnd($categoryConstraints);
                break;
            case 'notor':
                $constraint = $query->logicalNot($query->logicalOr($categoryConstraints));
                break;
            case 'notand':
                $constraint = $query->logicalNot($query->logicalAnd($categoryConstraints));
                break;
            case 'or':
            default:
                $constraint = $query->logicalOr($categoryConstraints);
        }

        return $constraint;
    }

    /**
     * Sets the ordering to the given query for the given demand
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand $faqDemand
     */
    protected function setOrderingsFromDemand(QueryInterface $query, FaqDemand $faqDemand)
    {
        $orderings = [];
        $orderFieldAllowed = GeneralUtility::trimExplode(',', $faqDemand->getOrderFieldAllowed(), true);
        if ($faqDemand->getOrderField() !== '' && $faqDemand->getOrderDirection() !== '' &&
            !empty($orderFieldAllowed) && in_array($faqDemand->getOrderField(), $orderFieldAllowed, true)) {
            $orderings[$faqDemand->getOrderField()] = ((strtolower($faqDemand->getOrderDirection()) == 'desc') ?
                QueryInterface::ORDER_DESCENDING :
                QueryInterface::ORDER_ASCENDING);
            $query->setOrderings($orderings);
        }
    }

    /**
     * Sets a query limit to the given query for the given demand
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query Query
     * @param \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand $faqDemand
     */
    protected function setQueryLimitFromDemand(QueryInterface $query, FaqDemand $faqDemand)
    {
        if ($faqDemand->getQueryLimit() !== null &&
            MathUtility::canBeInterpretedAsInteger($faqDemand->getQueryLimit()) &&
            (int)$faqDemand->getQueryLimit() > 0
        ) {
            $query->setLimit((int)$faqDemand->getQueryLimit());
        }
    }
}
