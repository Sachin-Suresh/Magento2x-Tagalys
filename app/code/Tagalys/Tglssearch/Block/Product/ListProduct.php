<?php
namespace Tagalys\Tglssearch\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
	/**
     * @var \Tagalys\Tglssearch\Helper\Data
     */
    protected $tglssearchHelper;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $catalogResourceModelProductCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
		/*passing all Constructors parameters to the parent class */
		\Magento\Catalog\Block\Product\Context $context,
		\Magento\Framework\Data\Helper\PostHelper $postDataHelper,
		\Magento\Catalog\Model\Layer\Resolver $layerResolver,
         CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
		/*passing all Constructors parameters to the parent class */

        \Tagalys\Tglssearch\Helper\Data $tglssearchHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogResourceModelProductCollectionFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		array $data = []
    ) {
        $this->tglssearchHelper = $tglssearchHelper;
        $this->catalogResourceModelProductCollectionFactory = $catalogResourceModelProductCollectionFactory;
        $this->catalogConfig = $catalogConfig;
        $this->storeManager = $storeManager;

		  parent::__construct(
            $context,
			$postDataHelper,
			$layerResolver,
			$categoryRepository,
			$urlHelper,
            $data
        );
		//$this->tglssearchHelper = $tglssearchHelper;
    }
    protected function _getProductCollection()
    {
		//print_r('get prod coll');die;
		$tagalys = $this->tglssearchHelper->getTagalysSearchData();
			
			if($tagalys == false) {
				return parent::_getProductCollection();
			} else {

       		$searchResult = $tagalys;

       		if(empty($searchResult)) {
       			return parent::_getProductCollection();
       		}

	    	$collection = $this->_productCollection = $this->catalogResourceModelProductCollectionFactory->create()
	    		 ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
	    		 ->setStore($this->storeManager->getStore())
	    		 ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
	    		 ->addAttributeToFilter( 'entity_id', array( 'in' => $searchResult['results'] ) );  echo 'list product';print_r($collection);die;

	    	$orderString = array('CASE e.entity_id');
			foreach($searchResult['results'] as $i => $productId) {
			    $orderString[] = 'WHEN '.$productId.' THEN '.$i;
			}
			$orderString[] = 'END';
			$orderString = implode(' ', $orderString);

	    $collection->getSelect()->order(new \Zend_Db_Expr($orderString));

			return $this->_productCollection;

		}
	}
	
	protected function _beforeToHtml()
    {
		//print_r('atg _getProductCollection5');die;
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection(); //print_r($collection);die;

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $this->_getProductCollection()]
        );

        $this->_getProductCollection()->load();

        //return parent::_beforeToHtml();
    }
}
