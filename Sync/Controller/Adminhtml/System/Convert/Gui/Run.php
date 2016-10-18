<?php
namespace Tagalys\Sync\Controller\Adminhtml\System\Convert\Gui;

class Run extends Tagalys\Sync\Controller\Adminhtml\System\Convert\Gui;
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
