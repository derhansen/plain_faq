<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Derhansen\PlainFaq\Service;

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class FaqCacheService
 */
class FaqCacheService
{
    /**
     * Adds cache tags to page cache by faq records.
     *
     * Following cache tags will be added to tsfe:
     * "tx_plainfaq_uid_[faq:uid]"
     */
    public function addCacheTagsByFaqRecords(array $faqRecords): void
    {
        $cacheTags = [];
        foreach ($faqRecords as $faq) {
            // cache tag for each faq record
            $cacheTags[] = 'tx_plainfaq_uid_' . $faq->getUid();
        }
        if (count($cacheTags) > 0) {
            $this->getTypoScriptFrontendController()->addCacheTags($cacheTags);
        }
    }

    /**
     * Adds page cache tags by used storagePages.
     * This adds tags with the scheme tx_plainfaq_pid_[faq:pid]
     */
    public function addPageCacheTagsByFaqDemandObject(FaqDemand $demand)
    {
        $cacheTags = [];
        if ($demand->getStoragePage()) {
            // Add cache tags for each storage page
            foreach (GeneralUtility::trimExplode(',', $demand->getStoragePage()) as $pageId) {
                $cacheTags[] = 'tx_plainfaq_pid_' . $pageId;
            }
        }
        if (count($cacheTags) > 0) {
            $this->getTypoScriptFrontendController()->addCacheTags($cacheTags);
        }
    }

    /**
     * Flushes the page cache by faq tags for the given faq uid and pid
     */
    public function flushFaqCache(int $faqUid = 0, int $faqPid = 0): void
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheTagsToFlush = [];

        if ($faqUid > 0) {
            $cacheTagsToFlush[] = 'tx_plainfaq_uid_' . $faqUid;
        }
        if ($faqPid > 0) {
            $cacheTagsToFlush[] = 'tx_plainfaq_pid_' . $faqPid;
        }

        foreach ($cacheTagsToFlush as $cacheTagToFlush) {
            $cacheManager->flushCachesInGroupByTag('pages', $cacheTagToFlush);
        }
    }

    protected function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'] ?: null;
    }
}
