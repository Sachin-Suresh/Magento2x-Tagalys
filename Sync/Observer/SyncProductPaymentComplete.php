<?php
namespace Tagalys\Sync\Observer;

use \Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\Data\Collection\AbstractDb;
use Tagalys\Sync\Model\Queue;

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
		$payment = $observer->getEvent()->getPayment();
		$this->saleOrderComplete($payment);
		return $this;
		
	}
	
	public function saleOrderComplete($payment)
	{
		//$productId=12;
		//echo 'product compete';die;
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		$payment = $objectManager->get('Magento\Framework\Registry')->registry('current_product');  
		//$productdel = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);   //echo '<pre>';print_r($productdel);die;
		$productid=$payment->getEntityId();  //echo 'id';print_r($productid);die;
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('tgs_queue'); 
		$sql = "Select * FROM " . $tableName;   
		
		if($productid)
		{								
			$sql = "SELECT count(*) as 'cnt' FROM `".$tableName."` WHERE `entity_id`='" .$productid."'";  
			$countData = $connection->fetchOne($sql);
				
			if($countData==0) {
				$connection->query("Insert Into " . $tableName . " (entity_id,created_at) Values ('".$productid."', now())");
			}
			else {
				$connection->query("UPDATE " . $tableName . " SET updated_at = now() WHERE entity_id = '".$productid."'");
			}
		}
		else {
		}
	}
	
}
