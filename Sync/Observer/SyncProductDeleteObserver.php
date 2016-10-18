<?php
namespace Tagalys\Sync\Observer;

use \Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\Data\Collection\AbstractDb;
use Tagalys\Sync\Model\Queue;
use Magento\CatalogImportExport\Model\Import\Product;

class SyncProductDeleteObserver implements ObserverInterface
{
	protected $_queue;

    /**
     * @var \Tagalys\Sync\Model\QueueFactory
     */
    protected $syncEngineFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
	
	/**
     * @param \Tagalys\Sync\Model\ResourceModel\Queue $queue
     */
	 
	protected $resourceConnection;
	protected $eavAttributeFactory;
	//const COL_SKU = 'sku';
	//protected $skuProcessor;
	//private $productEntityLinkField;
	
    function __construct
	(
		//\Tagalys\Sync\Model\Engine $syncEngineFactory
		\Tagalys\Sync\Model\QueueFactory $_queue,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\App\ResourceConnection $resourceConnection,   //custom
        \Magento\Eav\Model\Entity\AttributeFactory $eavAttributeFactory
		//\Magento\CatalogImportExport\Model\Import\Product\SkuProcessor $skuProcessor
		//\Tagalys\Sync\Model\Queue $queue
	)
	{	
		$this->_queue = $_queue;
		$this->_logger = $logger;
		$this->productFactory = $productFactory;
		$this->resourceConnection = $resourceConnection;
		$this->eavAttributeFactory = $eavAttributeFactory;
		//$this->skuProcessor = $skuProcessor;
	  }
	
		
	public function execute(EventObserver $observer)
	{
		//$productdel = $observer->getEvent()->getProduct()->getId();
		$sku=$observer->getEvent()->getProduct()->getSku(); 
		$this->productDelete($sku);
		return $this;
		
	}
	
	public function productDelete($sku)
	{
		//$productid=$productdel;
		$productSku=$sku;
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		//$productdel = $objectManager->get('Magento\Framework\Registry')->registry('current_product');  
			//$currentproduct = $objectManager->create('Tagalys\Sync\Model\ResourceModel\Queue')->load($productdel,'entity_id');  
		//$productid=$productdel->getEntityId();  
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('tgs_queue'); 
		$sql = "Select * FROM " . $tableName;   
		
		if($productSku)
		{								
			//$sql = "SELECT count(*) as 'cnt' FROM `".$tableName."` WHERE `entity_id`='" .$productSku."'";  
			$sql = "SELECT count(*) as 'cnt' FROM `".$tableName."` WHERE `sku`='" .$productSku."'";  
			$countData = $connection->fetchOne($sql);
				
			if($countData==0) {
				//$connection->query("Insert Into " . $tableName . " (sku,created_at) Values ('".$productSku."', now())");
				$connection->query("Insert Into " . $tableName . " (sku) Values ('".$productSku."')");
			}
			/* else {
				$connection->query("UPDATE " . $tableName . " SET updated_at = now() WHERE sku = '".$productSku."'");
			} */
		}
		else {
		}
	}
	
}
