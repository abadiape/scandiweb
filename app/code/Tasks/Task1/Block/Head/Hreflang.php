<?php
/**
 * Copyright © Scandiweb, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tasks\Task1\Block\Head;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\ResourceModel\Page as PageResource;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Head Hreflang block
 *
 * @author Pedro Abadia <abadiape@gmail.com>
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Hreflang extends Template
{
    /**
     *
     * @var Page
     */
    protected Page $_cmsPage;

    /**
     *
     * @var PageResource
     */
    protected PageResource $_cmsPageResource;

    /**
     * @var Resolver
     */
    protected Resolver $_localeResolver;

    /**
     * @var StoreRepositoryInterface
     */
    protected StoreRepositoryInterface $_storeRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param Context $context
     * @param Page $cmsPage
     * @param PageResource $cmsPageResource
     * @param Resolver $localeResolver
     * @param StoreRepositoryInterface $storeRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Page    $cmsPage,
        PageResource $cmsPageResource,
        Resolver $localeResolver,
        StoreRepositoryInterface $storeRepository,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_cmsPage = $cmsPage;
        $this->_cmsPageResource = $cmsPageResource;
        $this->_localeResolver = $localeResolver;
        $this->_storeRepository = $storeRepository;
        $this->_scopeConfig = $scopeConfig;

        parent::__construct($context, $data);
    }

    /**
     * Returns Store base Url for a given Store Id
     * @param int $storeId
     * @return string
     *
     * @throws NoSuchEntityException
     */
    public function getStoreUrl(int $storeId): string
    {
        if (! $this->_storeManager->getStore($storeId)) {
            throw new NoSuchEntityException(__("The given Store ID is not valid!."));
        }

        return $this->_storeManager->getStore($storeId)->getBaseUrl();

    }

    /**
     * Returns Store locale code for a given Store Id
     * @param int $storeId
     * @return string
     *
     */
    public function getStoreLocale(int $storeId): string
    {
        return $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Retrieves current store language
     *
     * @return string
     *
     */
    public function getStoreLanguage() : string
    {
        return $this->_localeResolver->getLocale() ?? '';
    }

    /**
     * Returns Cms page Url
     *
     * @return string
     *
     */
    public function getCmsPageUrl() : string
    {
        return $this->isCmsPage() ? $this->_cmsPage->getIdentifier() : 'Not a CMS Page!'; //Current CMS Page Identifier
    }

    /**
     * Checks whether the current page is a Cms one.
     *
     * @return bool
     *
     */
    public function isCmsPage() : bool
    {
        $currentFullAction = $this->getRequest()->getFullActionName();
        $cmsPagesArray = ['cms_index_index','cms_page_view'];

        if (in_array($currentFullAction, $cmsPagesArray)) {
            return true;
        }

        return false;
    }

    /**
     * Returns all existing Store Ids but the Admin one (0).
     *
     * @return array
     *
     */
    public function getAllStoreIds() : array
    {
        $stores = $this->_storeRepository->getList();
        $storeIds = [];
        foreach ($stores as $store) {
            $storeIds[] = $store->getId();
        }

        return array_diff($storeIds, [0 => 0]);
    }

    /**
     * Checks whether the current Cms page is used in multiple Store views.
     *
     * @return array
     */
    public function isUsedInMultipleStores(): array
    {
        $urlKey = $this->isCmsPage() ? $this->getCmsPageUrl() : '';//Checks whether this is a CMS Page before proceeding.

        if ($urlKey)
        {
            $cmsPageStores = (array)$this->_cmsPageResource->lookupStoreIds($this->_cmsPage->getId());

            /* Checks whether the current CMS Page belongs to the Admin Store Id (All Store Views),
             or to more than one store Id. */
            if ($cmsPageStores[0] == 0 || count($cmsPageStores) > 1) {
                return $cmsPageStores[0] == 0 ? $this->getAllStoreIds() : $cmsPageStores;
            }
        }

        return [];
    }

}
