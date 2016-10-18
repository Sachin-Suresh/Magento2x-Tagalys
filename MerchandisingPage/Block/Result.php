<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tagalys\MerchandisingPage\Block;

//use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\CatalogSearch\Helper\Data;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;
use Tagalys\Catalog\Block\Product\ListProduct;

/**
 * Product search result block
 */
class Result extends \Magento\CatalogSearch\Block\Result
{
    /**
     * Catalog Product collection
     *
     * @var Collection
     */
    protected $productCollection;

    /**
     * Catalog search data
     *
     * @var Data
     */
    protected $catalogSearchData;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;

    /**
     * @var QueryFactory
     */
    private $queryFactory;
	protected $tglssearchHelper;
	protected $catalogResourceModelProductCollectionFactory;

    /**
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param Data $catalogSearchData
     * @param QueryFactory $queryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Data $catalogSearchData,
        QueryFactory $queryFactory,
		\Tagalys\CatalogSearch\Helper\Data $tglssearchHelper,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogResourceModelProductCollectionFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->catalogLayer = $layerResolver->get();
        $this->catalogSearchData = $catalogSearchData;
        $this->queryFactory = $queryFactory;
		$this->tglssearchHelper = $tglssearchHelper;  
		$this->catalogResourceModelProductCollectionFactory = $catalogResourceModelProductCollectionFactory;
        $this->catalogConfig = $catalogConfig;
        $this->storeManager = $storeManager;		
		
        parent::__construct($context,$layerResolver,$catalogSearchData,$queryFactory,$data);
    }

    /**
     * Retrieve query model object
     *
     * @return \Magento\Search\Model\Query
     */
    protected function _getQuery()
    {
        return $this->queryFactory->get();
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
		//echo 'MerchprepareLay';
        $title = $this->getSearchQueryText();
        $this->pageConfig->getTitle()->set($title);
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            )->addCrumb(
                'search',
                ['label' => $title, 'title' => $title]
            );
        }

        return parent::_prepareLayout();
    }

     /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml()
    {
		//echo 'Merch getProductListHtml';
        return $this->getChildHtml('search_result_list');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Collection
     */
    protected function _getProductCollection()
    {
		echo 'Merch _getProductCollection';
        if (null === $this->productCollection) {
            $this->productCollection = $this->getListBlock()->getLoadedProductCollection();
        }

        return $this->productCollection; 
	}
    

    /**
     * Get search query text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getSearchQueryText()
    {
        return __("Search results for: '%1'", $this->catalogSearchData->getEscapedQueryText());
    }

    /**
     * Retrieve search result count
     *
     * @return string
     */
    // public function getResultCount()
    // {
        // if (!$this->getData('result_count')) {
            // $size = $this->_getProductCollection()->getSize();
            // $this->_getQuery()->saveNumResults($size);
            // $this->setResultCount($size);
        // }
        // return $this->getData('result_count');
    // }

    // /**
     // * Retrieve No Result or Minimum query length Text
     // *
     // * @return \Magento\Framework\Phrase|string
     // */
    // public function getNoResultText()
    // {
        // if ($this->catalogSearchData->isMinQueryLength()) {
            // return __('Minimum Search query length is %1', $this->_getQuery()->getMinQueryLength());
        // }
        // return $this->_getData('no_result_text');
    // }

    // /**
     // * Retrieve Note messages
     // *
     // * @return array
     // */
    // public function getNoteMessages()
    // {
        // return $this->catalogSearchData->getNoteMessages();
    // }
}
