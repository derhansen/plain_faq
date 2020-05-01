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
     *
     * @param array $faqRecords array with faq records
     */
    public function addCacheTagsByFaqRecords(array $faqRecords)
    {
        $cacheTags = [];
        foreach ($faqRecords as $faq) {
            // cache tag for each faq record
            $cacheTags[] = 'tx_plainfaq_uid_' . $faq->getUid();
        }
        if (count($cacheTags) > 0) {
            self::getTypoScriptFrontendController()->addCacheTags($cacheTags);
        }
    }

    /**
     * Adds page cache tags by used storagePages.
     * This adds tags with the scheme tx_plainfaq_pid_[faq:pid]
     *
     * @param FaqDemand $demand
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
            self::getTypoScriptFrontendController()->addCacheTags($cacheTags);
        }
    }

    /**
     * Flushes the page cache by faq tags for the given faq uid and pid
     *
     * @param int $faqUid
     * @param int $faqPid
     */
    public function flushFaqCache(int $faqUid = 0, int $faqPid = 0)
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

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'] ?: null;
    }
}
