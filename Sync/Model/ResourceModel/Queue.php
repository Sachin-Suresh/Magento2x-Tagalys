<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tagalys\Sync\Model\ResourceModel;

use Tagalys\Sync\Model\Queue as QueueAlias;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;
use Tagalys\Sync\Api\Data\QueueInterface;


/**
 * Product entity resource model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Queue extends AbstractDb
{
	/**
     * @var MetadataPool
     */
    protected $metadataPool;
	
    protected function _construct()
    {
		//echo 'Sync main table';die;
		/**
			* Define main table
			$this->_init('custom_table_name', 'id');  id is the primary key of custom table
		*/
		$this->_init('tgs_queue','id');
    }
	
	
}