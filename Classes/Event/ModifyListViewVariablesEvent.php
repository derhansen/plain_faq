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
use Psr\Http\Message\ServerRequestInterface;

/**
 * This event is triggered before the list view is rendered
 */
final class ModifyListViewVariablesEvent
{
    public function __construct(
        private readonly ServerRequestInterface $request,
        private array $variables,
        private readonly FaqController $faqController
    ) {}

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
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
