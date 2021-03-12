<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Event;

use Derhansen\PlainFaq\Controller\FaqController;

/**
 * This event is triggered before the detail view is rendered
 */
final class ModifyDetailViewVariablesEvent
{
    private array $variables;
    private FaqController $faqController;

    public function __construct(array $variables, FaqController $faqController)
    {
        $this->variables = $variables;
        $this->faqController = $faqController;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    public function getFaqController(): FaqController
    {
        return $this->faqController;
    }
}
