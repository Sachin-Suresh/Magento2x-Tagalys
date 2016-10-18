<?php
/**
 * Copyright © 2016 AionNext Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tagalys\Sync\Model\ResourceModel\Queue;

use Tagalys\Sync\Api\Data\QueueInterface;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
/**
 * Tagalys\Sync collection
 */
class Collection extends AbstractCollection
{
	 /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
     protected $storeManager;  //for multistore support for later use

	 /* public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
		$this->storeManager = $storeManager;
    } */ 
	
	public function _construct()
	{
		/**
			* Define model & resource model
		*/
		//echo 'syn colle';die;
		//$this->_init("sync/queue");
		$this->_init('Tagalys\Sync\Model\Queue','Tagalys\Sync\Model\ResourceModel\Queue');
	}

}
 