<?php
namespace Tagalys\Sync\Controller\Adminhtml\Import;.

use Magento\ImportExport\Controller\Adminhtml\ImportResult as ImportResultController;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Block\Adminhtml\Import\Frame\Result as ImportResultBlock;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\ImportExport\Model\Import\Adapter as ImportAdapter;
use Magento\ImportExport\Controller\Adminhtml\Import\Validate;

class Validate extends \Magento\ImportExport\Controller\Adminhtml\Import\Validate
{

   
    public function execute() 
    {
		echo 'sync gui run';die;
		try {
            	$bulkimport = $this->->create();
            	$bulkimport->saveConfig('tagalys_sync/product/import_status', "enable", "default", "disable");
            	// Mage::log("Dataflow CSV Import: product import start", null, "tagalys.log");     
        	} catch (Exception $e) {
            
        	}

	    	parent::runAction();
	    }
    }
	
}
