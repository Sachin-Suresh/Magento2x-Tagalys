<?php
namespace Tagalys\Sync\Helper;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor;

class Service extends \Magento\Framework\App\Helper\AbstractHelper {

	protected $_storeId;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $catalogResourceModelProductCollectionFactory;

    /**
     * @var \Tagalys\Sync\Model\ProductDetailsFactory
     */
    protected $syncProductDetailsFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $catalogCategoryFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $catalogImageHelper;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\ItemFactory
     */
    protected $catalogInventoryStockItemFactory;

    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\GroupedFactory
     */
    protected $groupedProductProductTypeGroupedFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory
     */
    protected $configurableProductProductTypeConfigurableFactory;

    /**
     * @var \Tagalys\Sync\Model\Mysql4\Queue\CollectionFactory
     */
    protected $syncMysql4QueueCollectionFactory;
    protected $syncQueueFactory;
	
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogResourceModelProductCollectionFactory,
        \Tagalys\Sync\Model\ProductDetailsFactory $syncProductDetailsFactory,
        \Magento\Catalog\Model\CategoryFactory $catalogCategoryFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Helper\Image $catalogImageHelper,
        \Magento\CatalogInventory\Model\Stock\ItemFactory $catalogInventoryStockItemFactory,
        \Magento\GroupedProduct\Model\Product\Type\GroupedFactory $groupedProductProductTypeGroupedFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory $configurableProductProductTypeConfigurableFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        //\Tagalys\Sync\Model\Mysql4\Queue\CollectionFactory $syncMysql4QueueCollectionFactory
		\Tagalys\Sync\Model\ResourceModel\Queue\CollectionFactory $syncMysql4QueueCollectionFactory,
		\Tagalys\Sync\Model\QueueFactory $syncQueueFactory
		//\Magento\CatalogInventory\Api\Data\StockItemInterface $stockinterface
    ) {
        $this->storeManager = $storeManager;
        $this->catalogResourceModelProductCollectionFactory = $catalogResourceModelProductCollectionFactory;
        $this->syncProductDetailsFactory = $syncProductDetailsFactory;
        $this->catalogCategoryFactory = $catalogCategoryFactory;
        $this->eavConfig = $eavConfig;
        $this->catalogImageHelper = $catalogImageHelper;
        $this->catalogInventoryStockItemFactory = $catalogInventoryStockItemFactory;
        $this->groupedProductProductTypeGroupedFactory = $groupedProductProductTypeGroupedFactory;
        $this->configurableProductProductTypeConfigurableFactory = $configurableProductProductTypeConfigurableFactory;
		$this->_objectManager = $objectManager;
        $this->syncMysql4QueueCollectionFactory = $syncMysql4QueueCollectionFactory;
		$this->syncQueueFactory=$syncQueueFactory;
		//$this->stockinterface=$stockinterface;
        parent::__construct(
            $context
        );
    }


	public function productSyncBySoap($page, $limit)
	{
		//echo 'sync by soap';die;
		try {

			if(empty($this->_storeId)) {
		  		$this->_storeId = $this->storeManager->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();	
			} 
		  
		  	//Mage::app()->setCurrentStore($this->_storeId);
			$this->storeManager->setCurrentStore($this->_storeId);
			
          	$collection = $this->catalogResourceModelProductCollectionFactory->create()
                        //->getCollection()
                        ->setStore($this->_storeId)
                        ->setPageSize($limit)
                        ->addAttributeToSelect('*')
                        ->setCurPage($page);
          
          	$payload = $this->getProductWithParentAttribute($collection);
          
          	//$service = Mage::getModel('sync/service');
         	 if(empty($payload)) {
           
            	// Mage::log('Product Object: empty', null, 'tagalys.log');  
          
          	} 

          	return $payload;
			
		} catch (Exception $e) {
			
		}
		  
		  
	}
	
	public function getProductWithParentAttribute($products)
	{
		//echo 'getProductWithParentAttribute';die;
		$payload = array(); 
		foreach ($products as $product) {
			$simpleProduct = $this->getSingleProductWithoutPayload($product);
            if(isset($simpleProduct)) {
                array_push($payload, $simpleProduct);
            }		
		}
		return $payload;
	}


	public function getSingleProductWithoutPayload($productCollection) 
	{
		//echo 'getSingleProductWithoutPayload';die;
		if(empty($this->_storeId)) {
		  	$this->_storeId = $this->storeManager->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();
			//Mage::app()->setCurrentStore($this->_storeId);
			$this->storeManager->setCurrentStore($this->_storeId);
		}

		 $syncField = $this->syncProductDetailsFactory->create();
		 
		 $fields = $syncField->getProductSyncField();
	    
	     //Add Extra field
	     $fields = array_merge($fields, array('created_at', 'updated_at')); 
		 
		 $productObject = new \stdClass();
	     
	     $product = $productCollection;

	     $productObject->product_id = $product->getId();
	    
	     //Get Category Details
	     $category = $product->getCategoryIds(); 
	     
	     $categoryList = array();
	     
	     foreach ($category as $category_id) {
	        $_cat = $this->catalogCategoryFactory->create()->setStoreId($this->storeManager->getStore()->getId())->load($category_id);
	          $categoryList[] = $_cat->getName();             
	     }

	     //Category Container
	     $productObject->categories = $categoryList;  
		 
	     //Get Tag Details    //Tag Class is depricated in Magento 2x  http://magento.stackexchange.com/questions/86259/product-tags-feature-is-not-included-in-magento-2
			//$model=Mage::getModel('tag/tag');
		 //$model = $this->_objectManager->create('Tag\Model\Class\is\Depricated\InMagento2');
				
	     
	     /* $tagsOption = $model->getResourceCollection()
	            ->addPopularity()
	            ->addStatusFilter($model->getApprovedStatus())
	            ->addProductFilter($product->getId())
	            ->setFlag('relation', true)
	            ->addStoreFilter($this->_storeId)
	            ->setActiveFilter()
	            ->load();
	      $tags = array(); */
	      
	      //foreach($tagsOption as $tag) $tags[] = $tag->getName();
	      //$productObject->tags = $tags;
	      
	      //Field Container
	      if(empty($fields)) 
		  {
	        
	        $productObject->sku = $product->getData('sku');  
	       
	        $productObject->price = $product->getData('cost');
	      } 

	      else {
	        
	        foreach ($fields as $key => $name) {
	        	
	        	$attribute = $this->eavConfig->getAttribute('catalog_product', $name);  
				
				if ($attribute->usesSource()) {
  					 $attributeCode = $attribute->setStoreId($this->_storeId)->getAttributeCode(); 
  					$fieldValue = $product->getResource()->getAttribute($attributeCode)
        ->getFrontend()->getValue($product);
  					 $productObject->{$name} = $fieldValue;//$product->getAttributeText($name); 	
				} else {
					$filter = array('image','small_image','thumbnail');
					
					if(in_array($name, $filter)) {
						$productObject->{$name} = '/media/catalog/product' . $product->getImage();
						
						try {
							if($name == "thumbnail") {
								//$productObject->tagalys_thumbnail_url = (string)$this->catalogImageHelper->init($product, 'thumbnail')->resize(170);
								/* ERROR FIXED line modified: 
								     exception 'Exception' with message 'Recoverable Error: Object of class Magento\Catalog\Helper\Image could not be converted to string 
								     in C:\xampp\htdocs\magento2x_3\app\code\Tagalys\Sync\Helper\Service.php on line 221' 
								     in C:\xampp\htdocs\magento2x_3\lib\internal\Magento\Framework\App\ErrorHandler.php:61 */
								$productObject->tagalys_thumbnail_url = $this->catalogImageHelper->init($product, 'thumbnail')->resize(170);
							}	
						} catch(Exception $e) {
							$productObject->tagalys_thumbnail_url = null;
							// Mage::log("Product Image not found", null, "tagalys.log");
						}
						
					} 
					else if($name == 'url_key') {
						$productObject->{$name} = $product->getData($name);	
						$productObject->url = parse_url($product->getProductUrl(), PHP_URL_PATH);		
					}
					else {
						$productObject->{$name} = $product->getData($name);	
					}
  					
				}
	        }

	        //Adding stock field     //To be corrected 
	         /* $stockField = $syncField->getInventorySyncField();  
	        
	        //$stock = $this->catalogInventoryStockItemFactory->create()->loadByProduct($product);
			
			$stockItemFactory = $this->_objectManager->get('Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory');
			$stockItem = $stockItemFactory->create();
			$productId=$stockItemFactory->create();
			//$stockId=$stockItemFactory->create();
			
			$stockItemResource = $this->_objectManager->get('Magento\CatalogInventory\Model\ResourceModel\Stock\Item');
			$stock=$stockItemResource->loadByProductId($stockItem, $productId, $stockId);
			
	        //$model = $this->_objectManager->create('Magento\CatalogInventory\Model\ResourceModel\Stock\Item');
			//$stock=$model->loadByProductId(StockInterface $item, $productId, $stockId);
			
	        foreach ($stockField as $key => $field) {
	        	$productObject->{$field} = $stock->getData($field); 
	        } */

	      } 
		  
	      //Add product type field for identification
	      $productType = $product->getTypeId();  
	      
	      $productObject->tagalys_product_type = $productType;

	      $parentIds = $this->groupedProductProductTypeGroupedFactory->create()->getParentIdsByChild($product->getId());

          if(!$parentIds) {
            
            $parentIds = $this->configurableProductProductTypeConfigurableFactory->create()->getParentIdsByChild($product->getId());  
            
            $parentProducts = $this->catalogResourceModelProductCollectionFactory->create()
              ->addAttributeToSelect('sku')
              ->addAttributeToFilter('entity_id', array('in' => $parentIds))
              ->load();
            
            $parents = array();
            
            foreach ($parentProducts as $key => $pro) {
              array_push($parents, $pro->getSku());
            }
            
            if(isset($parents[0])) {
                $productObject->tagalys_parent_sku = $parents;
             }
          }

	      return $productObject;
	}

	public function getProductTotal() {
		//echo 'getProductTotal';die;
		try {

			$record = $this->catalogResourceModelProductCollectionFactory->create()->getSize();
			
			return $record;
			
		} catch (Exception $e) {
			
		}
		
	}

	public function getProductUpdateCount() {
		//echo 'getProductUpdateCount';die;
			try {

			$record = $this->syncMysql4QueueCollectionFactory->create()->getSize();
			
			return $record;
			
		} catch (Exception $e) {
			
		}
	}

	public function getSingleProductBySku($sku) {
	//echo 'getSingleProductBySku';die;
		try {
			if(isset($sku)) {

				if(empty($this->_storeId)) {
			  		$this->_storeId = $this->storeManager->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();	
			  	} 
			  
			 	//Mage::app()->setCurrentStore($this->_storeId);
				$this->storeManager->setCurrentStore($this->_storeId);

				$collection = $this->catalogResourceModelProductCollectionFactory->create()
						->setStore($this->_storeId)
						->addAttributeToSelect('*')
						->addFilter('sku',$sku);
				$product = $this->getSingleProductWithoutPayload($collection->getFirstItem());
			
				return $product;
			}

			return null;
			
		} catch (Exception $e) {
			
		}
		
	}

	public function getProductUpdate($limit)
	{
		//echo 'getProductUpdate';die;
		try {
			$collection = $this->syncMysql4QueueCollectionFactory->create()->setOrder('id', 'DESC'); 

			$collection->getSelect()->limit($limit); ;

			$products_id = array();  

			$existing_products_id = array();
			$non_existing_ids = array();  

			$count = 0;
			
			foreach ($collection as $key => $queue) {

				$product_id = $queue->getData('product_id');

				array_push($products_id, $product_id);
				
			}

			$productCollection = $this->catalogResourceModelProductCollectionFactory->create()
				->setStore($this->_storeId)
				->addAttributeToSelect('*')
	  	     		 ->addAttributeToFilter( 'entity_id', array( 'in' => $products_id ));
			
	    	$respone = $this->getProductWithParentAttribute($productCollection);
					
	    	foreach ($respone as $key => $value) {
	    		array_push($existing_products_id, $value->product_id);
	    		$count++;
	    	}
	    	$non_existing_ids =  array_merge(array_diff($existing_products_id, $products_id), array_diff($products_id, $existing_products_id));
	    	 
	    	foreach ($non_existing_ids as $key => $value) {
	    		$deleted = new \stdClass;
	    		$deleted->product_id = $value;
	    		$deleted->deleted = true ;
	    			array_push($respone, $deleted);
	    	}
	    	foreach ($collection as $key => $queue) {
	    		$queue->delete();
	    	}

			return $respone;

		} catch (Exception $e) {
			
		}
	} 	  
}
