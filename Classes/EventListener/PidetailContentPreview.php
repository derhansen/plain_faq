<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\EventListener;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

final class PidetailContentPreview extends AbstractPluginPreview
{
    #[AsEventListener('plainfaq/pidetail-preview')]
    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        if ($event->getTable() !== 'tt_content' ||
            $event->getRecordType() !== 'plainfaq_pidetail'
        ) {
            return;
        }

        $previewContent = $this->renderPreviewContent(
            $event->getRecord(),
            $event->getPageLayoutContext()->getCurrentRequest()
        );
        $event->setPreviewContent($previewContent);
    }

    private function renderPreviewContent(array $record, ServerRequestInterface $request): string
    {
        $data = [];
        $flexFormData = $this->getFlexFormData($record['pi_flexform']);
        $pluginName = $this->getPluginName($record);

        $this->setPluginPidConfig($data, $flexFormData, 'listPid');

        return $this->renderAsTable($request, $data, $pluginName);
    }
}
