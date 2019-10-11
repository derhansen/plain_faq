<?php
declare(strict_types = 1);
namespace Derhansen\PlainFaq\Controller;

/*
 * This file is part of the Extension "plain_faq" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand;
use Derhansen\PlainFaq\Domain\Model\Faq;
use Derhansen\PlainFaq\Domain\Repository\FaqRepository;
use Derhansen\PlainFaq\Utility\PageUtility;

/**
 * FaqController
 */
class FaqController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
    protected $faqRepository = null;

    /**
     * @param FaqRepository $faqRepository
     */
    public function injectFaqRepository(\Derhansen\PlainFaq\Domain\Repository\FaqRepository $faqRepository)
    {
        $this->faqRepository = $faqRepository;
    }

    /**
     * Creates an faq demand object with the given settings
     *
     * @param array $settings The settings
     *
     * @return \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand
     */
    public function createFaqDemandObjectFromSettings(array $settings): FaqDemand
    {
        /** @var \Derhansen\PlainFaq\Domain\Model\Dto\FaqDemand $demand */
        $demand = $this->objectManager->get(FaqDemand::class);
        $demand->setStoragePage(PageUtility::extendPidListByChildren(
            $settings['storagePage'] ?? '',
            $settings['recursive'] !== null ? (int)$settings['recursive'] : 0
        ));
        $demand->setCategoryConjunction($settings['categoryConjunction'] ?? '');
        $demand->setCategories($settings['categories'] ?? '');
        $demand->setIncludeSubcategories(
            $settings['includeSubcategories'] !== null ? (bool)$settings['includeSubcategories'] : false
        );
        $demand->setOrderField($settings['orderField'] ?? '');
        $demand->setOrderFieldAllowed($settings['orderFieldAllowed'] ?? '');
        $demand->setOrderDirection($settings['orderDirection'] ?? 'asc');

        return $demand;
    }

    /**
     * List action
     *
     * @return void
     */
    public function listAction()
    {
        $faqDemand = $this->createFaqDemandObjectFromSettings($this->settings);

        // @todo check new isOverwriteDemand setting
        $faqs = $this->faqRepository->findDemanded($faqDemand);

        $this->view->assignMultiple([
            'faqs' => $faqs,
            'faqDemand' => $faqDemand,
        ]);
    }

    /**
     * Detail action
     *
     * @param Faq|null $faq
     *
     * @return void
     */
    public function detailAction(Faq $faq = null)
    {
        if (is_null($faq)) {
            $this->getTypoScriptFrontendController()->pageNotFoundAndExit('FAQ article not found.');
        }

        $values = [
            'faq' => $faq
        ];

        $this->view->assignMultiple($values);
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
    protected function signalDispatch($signalClassName, $signalName, array $arguments)
    {
        return $this->signalSlotDispatcher->dispatch($signalClassName, $signalName, $arguments);
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'] ?: null;
    }
}