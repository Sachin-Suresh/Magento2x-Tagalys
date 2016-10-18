<?php
namespace Tagalys\Sync\Model;

class ProductDetails extends \Magento\Framework\Model\AbstractModel {
	
	protected $syncfield;

	protected $inventorySyncField;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $catalogResourceModelProductAttributeCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Tagalys\Sync\Helper\Inventory
     */
    protected $syncInventoryHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $catalogResourceModelProductAttributeCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Tagalys\Sync\Helper\Inventory $syncInventoryHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->catalogResourceModelProductAttributeCollectionFactory = $catalogResourceModelProductAttributeCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->syncInventoryHelper = $syncInventoryHelper;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }


	public function getProductSyncField() {
		//echo 'getProductSyncField';die;
		try {

			if(empty($this->syncfield)) {
				$attributes = $this->catalogResourceModelProductAttributeCollectionFactory->create()->getItems();
    			$default_field = array();
				foreach ($attributes as $attribute) {
    				$code = $attribute->getAttributecode();
    				//$label = $attribute->getFrontendLabel();
    				if(isset($code)) {
    					array_push($default_field, $code);
					}
				}
				$unsync_field = $this->_getProductUnSyncField();
				$this->syncfield = array_diff($default_field, $unsync_field);
				return $this->syncfield;	
			}
				
			return $this->syncfield;
			
		} catch (Exception $e) {
			
		}		
   }

   protected function _getProductUnSyncField() 
   {
	  //echo 'getProductUnSyncField';die;
   		$config = $this->scopeConfig->getValue('sync/product/sync_fields', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    $fields= explode(',',$config);
	    return $fields;
   }

   public function getInventorySyncField() {
	//echo 'getInventorySyncField';die;
   		try {

   			if(empty($this->inventorySyncField)) {
				$helper = $this->syncInventoryHelper;
	        	$stock_item = $helper->getInventoryStockItemField();

	    		$default_field = array();
				foreach ($stock_item as $key => $label) {
	    			if(isset($key)) {
	    				array_push($default_field, $key);
					}
				}
				$unsync_field = $this->_getInventoryUnSyncField();
				$this->inventorySyncField = array_diff($default_field, $unsync_field);
				return $this->inventorySyncField;	
			}

			return $this->inventorySyncField;
   			
   		} catch (Exception $e) {
   			
   		}
		
   }

   protected function _getInventoryUnSyncField()
   {
	  //echo 'getInventoryUnSyncField';die;
   		$config = $this->scopeConfig->getValue('sync/inventory/inventory_fields', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);  
	    $fields= explode(',',$config); 
	    return $fields;
   }
}