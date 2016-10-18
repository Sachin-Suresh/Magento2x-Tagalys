<?php
namespace Tagalys\Catalog\Block\Product;

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
	protected $_defaultToolbarBlock = 'Tagalys\Catalog\Block\Product\ProductList\Toolbar';

    public function __construct(
		/*passing all Constructors parameters to the parent class */
		\Magento\Catalog\Block\Product\Context $context,
		\Magento\Framework\Data\Helper\PostHelper $postDataHelper,
		\Magento\Catalog\Model\Layer\Resolver $layerResolver,
         CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
		/*passing all Constructors parameters to the parent class */

        \Tagalys\CatalogSearch\Helper\Data $tglssearchHelper,
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
		echo '_getProductCollection()--Tagalys List----';
		$tagalys = $this->tglssearchHelper->getTagalysSearchData();

		if($tagalys == false)
		{//echo 'tag';die;
			return parent::_getProductCollection();
		}
		else
		{
			$searchResult = $tagalys;
			if(empty($searchResult)) {
				return parent::_getProductCollection();
			}

			$collection = $this->_productCollection = $this->catalogResourceModelProductCollectionFactory->create()
				 ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
				 ->setStore($this->storeManager->getStore())
				 ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
				 ->addAttributeToFilter( 'entity_id', array( 'in' => $searchResult['results'] ) );
				 //->addAttributeToFilter( 'sku', array( 'in' => $searchResult['results'] ) );

			$orderString = array('CASE e.entity_id');
			//$orderString = array('CASE e.sku');
			foreach($searchResult['results'] as $i => $productId) {
				$orderString[] = 'WHEN '.$productId.' THEN '.$i;
			}
			$orderString[] = 'END';
			$orderString = implode(' ', $orderString);

			$collection->getSelect()->order(new \Zend_Db_Expr($orderString));

			return $this->_productCollection;
		}
	}

}
