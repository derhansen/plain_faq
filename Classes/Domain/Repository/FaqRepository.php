<?php
declare(strict_types = 1);
namespace Derhansen\PlainFaq\Domain\Repository;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use Derhansen\PlainFaq\Utility\CategoryUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for Faqs
 */
class FaqRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Set default sorting
     *
     * @var array
     */
    protected $defaultOrderings = [
        'question' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * Disable the use of storage records, because the StoragePage can be set
     * in the plugin
     *
     * @return void
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

        $this->setOrderingsFromDemand($query, $faqDemand);

        if (count($constraints) > 0) {
            $query->matching($query->logicalAnd($constraints));
        }

        return $query->execute();
    }

    /**
     * Sets the storagePage constraint to the given constraints array
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand $faqDemand
     * @param array $constraints Constraints
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    protected function setOrderingsFromDemand(QueryInterface $query, FaqDemand $faqDemand)
    {
        $orderings = [];
        $orderFieldAllowed = GeneralUtility::trimExplode(',', $faqDemand->getOrderFieldAllowed(), true);
        if ($faqDemand->getOrderField() !== '' && $faqDemand->getOrderDirection() !== '' &&
            !empty($orderFieldAllowed) && in_array($faqDemand->getOrderField(), $orderFieldAllowed, true)) {
            $orderings[$faqDemand->getOrderField()] = ((strtolower($faqDemand->getOrderDirection()) == 'desc') ?
                \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING :
                \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING);
            $query->setOrderings($orderings);
        }
    }
}
