<?php
namespace Tagalys\Sync\Observer;

use \Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\Data\Collection\AbstractDb;
use Tagalys\Sync\Model\Queue;

class SyncObserver implements ObserverInterface
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
	
    function __construct
	(
		//\Tagalys\Sync\Model\Engine $syncEngineFactory
		\Tagalys\Sync\Model\QueueFactory $_queue,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\App\ResourceConnection $resourceConnection,   //custom
        \Magento\Eav\Model\Entity\AttributeFactory $eavAttributeFactory
		//\Tagalys\Sync\Model\Queue $queue
	)
	{	
		$this->_queue = $_queue;
		$this->_logger = $logger;
		$this->productFactory = $productFactory;
		$this->resourceConnection = $resourceConnection;
		$this->eavAttributeFactory = $eavAttributeFactory;
	  }
	
	public function execute(EventObserver $observer)
	{
		//echo 'Sync obssrexe 2';die;
		$product = $observer->getEvent()->getProduct();
		//$payment = $observer->getEvent()->getPayment();
		$this->productUpdate($product);
		//$this->saleOrderComplete($payment);
			return $this;
		
	}
	
	public function productUpdate($product)
	{
		/* $sku='OUTIPCAM1';
		$product = $this->productFactory->create();
		//$product->load($product->getIdBySku($sku));
		$product->loadByAttribute('sku', $sku);
		echo '<pre>';print_r($product);die; */
		
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		$product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');  
		//$productid=$product->getEntityId(); 
		$sku=$product->getSku();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('tgs_queue'); 
		$sql = "Select * FROM " . $tableName;   
		
		if($sku)
		{								
			$sql = "SELECT count(*) as 'cnt' FROM `".$tableName."` WHERE `sku`='" .$sku."'";  
			$countData = $connection->fetchOne($sql);
				
			if($countData==0) {
				//$connection->query("Insert Into " . $tableName . " (sku,created_at) Values ('".$sku."', now())");
				$connection->query("Insert Into " . $tableName . " (sku) Values ('".$sku."')");
			}
			/* else {
				$connection->query("UPDATE " . $tableName . " SET updated_at = now() WHERE sku = '".$sku."'");
			} */
		}
		else {
		}
	}
	
	/* public function saleOrderComplete($payment)
	{
		//echo 'Sync sales order 2';die;
		try {

			//$payment = $observer->getEvent()->getPayment();

        	$items = $payment->getOrder()->getItemsCollection();

        	foreach($items as $item) {
            
            	$product = $item->getProduct();

            	$product_id = $product->getId(); 
			
				if(!$product_id) {
					return;
				}
				//Check already record is exists in queue table
				$existingProduct = $this->_queue->load($product_id,'product_id');

				$_id = $existingProduct->getId();

				if(empty($_id)) {
					$data = array(
						"product_id" => (int) $product_id
					);
		
						$this->_queue->setData($data);
						try {
							$queue_id = $this->_queue->save()->getId();
							// Mage::log("Queueing: [ ".$product_id ." ] - new product", null, "tagalys.log");
						} catch(Exception $e) {
							$this->logger->log(null, "Sync Queue: error adding product [ ".$product_id." ] in queue");

						}
					
				} else {
					$this->logger->log(null, "Sync: product already in queue [ ".$product_id." ]");
				}
	           
        	}

			// Mage::log("Sync: sales order complete", null, "tagalys.log");
			
		} catch (Exception $e) {
			$this->logger->log(null, "Sync: ".$e->getMessage());
		}
 
	} */
}
