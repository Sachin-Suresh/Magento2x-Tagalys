<?php
namespace Tagalys\Sync\Model;
use Tagalys\Sync\Model\Engine as QueueAlias;

class Engine extends \Magento\Framework\Model\AbstractModel
{
	protected $layout;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Tagalys\Tglssearch\Model\Client\Connector
     */
    //protected $tglssearchClientConnector;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
	protected $_queue;
	//protected $syncQueueFactory;

	public function __construct(
         \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
		\Magento\Framework\View\LayoutInterface $layout,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\Session $catalogSession, 
        \Psr\Log\LoggerInterface $logger,	
		//\Tagalys\Sync\Model\Queue $_queue,
		\Tagalys\Sync\Model\ResourceModel\Queue $_queue,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        	
        array $data = []
    ) {
		$this->layout = $layout;
	    $this->layoutFactory = $layoutFactory;
        $this->scopeConfig = $scopeConfig;
        //$this->tglssearchClientConnector = $tglssearchClientConnector;
        $this->request = $request;
        $this->catalogSession = $catalogSession;
        $this->logger = $logger;
		//$this->_queue=$this->_init('Tagalys\Sync\Model\Queue');
		$this->_queue=$_queue;
		parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }
		
	public function productUpdate(\Magento\Framework\Event\Observer $observer)
	{
		try {
			$product = $observer->getEvent()->getProduct();   
			$product_id = $product->getId();  
			
			if(!$product_id) {
				return;
			}
			//Check already record is exists in queue table
			$existingProduct = $this->_queue->load($product_id,'entity_id'); 

			$_id = $existingProduct->getId();

			if(empty($_id)) {
				$data = array(
					"product_id" => (int) $product_id
				);
	
					$this->_queue->setData($data);
					try {
						$queue_id = $this->_queue->save()->getId();
											
						//custom defined log
						/* $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Sync-productUpdate.log');
						$logger = new \Zend\Log\Logger();
						$logger->addWriter($writer);
						$logger->info("Queueing: [ ".$product_id." ] new product"); */
						
					} catch(Exception $e) {
						$this->logger->log(null, "Sync Queue: error adding product [ ".$product_id." ] in queue");

					}
				
			} else {
				$logger->info("Sync Queue: Error adding product  [".$product_id." ] in queue");
			}
			
		} catch (Exception $e) {
			$this->logger->log(null, "Sync: product update error");
		}
		
	}
	
	public function productDelete(\Magento\Framework\Event\Observer $observer)
	{
		//echo 'new syn produ delete 3';die;
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Sync-productDelete.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		try {
			$product = $observer->getEvent()->getProduct();
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
						
						//custom log
						
						$logger->info("Queueing: [ ".$product_id." ]- new product");
					} catch(Exception $e) {
						$logger->info("Sync Queue: Error adding product  [".$product_id." ] in queue");

					}

				} else {
				$logger->info("Sync: product already in queue [ ".$product_id." ]");
			}

		} catch (Exception $e) {
			$logger->info("Sync: product delete error");
		}	
	}
	
	/* public function saleOrderComplete(\Magento\Framework\Event\Observer $observer)
	{
		try {

			$payment = $observer->getEvent()->getPayment(); //echo '<pre>';print_r($observer);
			$orderid = $payment->getOrder()->getId();  
			//$orderItems = $order->getAllItems();
			
        	$items = $payment->getOrder()->getItemsCollection();
        	foreach($items as $item) {
 
            	$product = $item->getProduct();
            	$product_id = $product->getId();
				
				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Sync-salesProductID.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info("Queueing: [ ".$product_id." ]- new product");
				
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
							//custom log
							$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Sync-sales_order-Complete.log');
							$logger = new \Zend\Log\Logger();
							$logger->addWriter($writer);
							$logger->info("Queueing: [ ".$product_id." ]- new product");
						} catch(Exception $e) {
							$logger->info("Sync Queue: Error adding product  [".$product_id." ] in queue");

						}
					
				} else {
					$logger->info("Sync: product already in queue [ ".$product_id." ]");
				}
	           
        	}
		} catch (Exception $e) {
			//$this->logger->log(null, "Sync: ".$e->getMessage());
			$logger->info("Sync: product delete error");
		}
 	}  */  
}