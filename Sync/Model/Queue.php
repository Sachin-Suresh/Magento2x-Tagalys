<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tagalys\Sync\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
//use Tagalys\Sync\Api\Data\QueueInterface;
use Tagalys\Sync\Api\QueueRepositoryInterface;

class Queue extends AbstractModel implements QueueRepositoryInterface,IdentityInterface
{
	/**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Tagalays Sync cache tag
     */
    const CACHE_TAG = 'tgs_queue';

    /**
     * @var string
     */
    protected $_cacheTag = 'tgs_queue';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'tgs_queue';
	
	protected $logger;
	protected $scopeConfig;
	protected $_status;
	protected $_bulkImport;
	protected $serviceHelper;
	
	public function __construct(
         \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		 \Psr\Log\LoggerInterface $logger,
		 \Tagalys\Sync\Helper\Service $serviceHelper,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
		$this->serviceHelper=$serviceHelper;
	  }
	
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
	{
		$this->_init('Tagalys\Sync\Model\ResourceModel\Queue');
		$this->_status = $this->scopeConfig->getValue('tagalys_sync/default/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->_bulkImport = $this->scopeConfig->getValue('tagalys_sync/product/import_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		//$this->_helper = Mage::helper("sync/service");
    }
	
	/**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
		//echo 'syn ident';die;
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getId()];
    }
	
	/**
     * Return Prodcut By Limit
     * @param $page int Offset
     * @param $limit int Limit
     */	
	public function getProduct($page,$limit) 
	{
		//echo 'SYNC getproduct';die;
		/* $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Sync.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer); */
			 
		/* if(!$this->_status)
		{
			return json_encode(array("result"=>false,"message" => 'Tagalys Sync Plugin is disabled'));;
		} */

		if($this->_bulkImport == "enable")
		{
			return json_encode(array("result"=>false,"message" => 'Bulk Product Import in progress, Please try again later'));;  
		} 

		if(!isset($page) && !isset($limit))
		{
			return json_encode(array("result"=>false,"message" => 'Argument is missing'));
		}
		
		$data = $this->serviceHelper->productSyncBySoap($page,$limit);
		if(isset($data))
		{
				return json_encode(array("result" => true,"data" => $data));
		}
		
		return json_encode(array("result" => false,"message" => "Record not found"));
	}
	
	/**
     * Return the product limit.
     *
     * @api
     * @param $limit int Limit
     */
	public function getProductUpdate($limit)
	{
		//echo 'SYNC getproductudate';die;
		if(!isset($limit)) 
		{
			return json_encode(array("result"=>false,"message" => 'Argument is missing'));
		}

		/* if(!$this->_status)
		{
			return json_encode(array("result"=>false,"message" => 'Tagalys Sync Plugin is disabled'));
		} */

		if($this->_bulkImport == "enable")
		{
			return json_encode(array("result"=>false,"message" => 'Bulk Product Import in progress, Please try again later'));;  
		} 

		$data = $this->serviceHelper->getProductUpdate($limit);
		if(isset($data))
		{	
			return json_encode(array("result" => true,"data" => $data));
		}

		return json_encode(array("result" => false,"message" => "Record not found"));
	}
	
	 /**
     * Return Product Total
     * @return JSON json_string
     */
    public function getProductTotal()
	{
		//echo 'SYNc getproductstotal';die;
		/* if(!$this->_status)
		{
		   return json_encode(array("result"=>false,"message" => 'Tagalys Sync Plugin is disabled'));;
		} */

		if($this->_bulkImport == "enable")
		{
		   return json_encode(array("result"=>false,"message" => 'Bulk Product Import in progress, Please try again later'));;  
		} 

	   $total = $this->serviceHelper->getProductTotal();
	   return json_encode(array("result"=>true,"total" => $total));
    }
	
	/**
      * Returns Single Prodcut
      * @param $sku string Product Sku
      * @return JSON json_string
      */
     public function getSingleProductBySku($sku)
	 {
		//echo 'getsingle produskuproducts';die;
		/* if(!$this->_status)
		{
			return json_encode(array("result"=>false,"message" => 'Tagalys Sync Plugin is disabled'));;
		} */

		if($this->_bulkImport == "enable")
		{
			return json_encode(array("result"=>false,"message" => 'Bulk Product Import in progress, Please try again later'));;  
		} 
		
		if(!isset($sku)) 
		{
			return json_encode(array("result"=>false,"message" => 'Argument is missing'));
		}

		$collection = $this->serviceHelper->getSingleProductBySku($sku);
		if(isset($collection))
		{
			return json_encode(array("result"=>true,"data" => $collection));        
		}
		else
		{
			return json_encode(array("result"=>false,"message" => 'Record not found'));
		}
    }
	
	public function getProductUpdateCount()
	{
		//echo 'getupdatecountproducts';die;
		/* if(!$this->_status) 
		{
			return json_encode(array("result"=>false,"message" => 'Tagalys Sync Plugin is disabled'));;
		} */

		if($this->_bulkImport == "enable")
		{
			return json_encode(array("result"=>false,"message" => 'Bulk Product Import in progress, Please try again later'));;  
		} 
	
		$total = $this->serviceHelper->getProductUpdateCount();
		return json_encode(array("result"=>true,"total" => $total));
    }
}	
	 