<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Event;

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use Derhansen\PlainFaq\Domain\Repository\FaqRepository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * This event is triggered before findDemanded in FAQ repository is executed
 */
final class ModifyFaqQueryConstraintsEvent
{
    private array $constraints;
    private QueryInterface $query;
    private FaqDemand $faqDemand;
    private FaqRepository $faqRepository;

    public function __construct(
        array $constraints,
        QueryInterface $query,
        FaqDemand $eventDemand,
        FaqRepository $faqRepository
    ) {
        $this->constraints = $constraints;
        $this->query = $query;
        $this->faqDemand = $eventDemand;
        $this->faqRepository = $faqRepository;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }

    public function getFaqDemand(): FaqDemand
    {
        return $this->faqDemand;
    }

    public function getFaqRepository(): FaqRepository
    {
        return $this->faqRepository;
    }

    public function setConstraints(array $constraints): void
    {
        $this->constraints = $constraints;
    }
}
