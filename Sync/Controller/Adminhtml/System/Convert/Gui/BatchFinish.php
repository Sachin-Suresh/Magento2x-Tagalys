<?php
namespace Tagalys\Sync\Controller\Adminhtml\System\Convert\Gui;

class BatchFinish extends Tagalys\Sync\Controller\Adminhtml\System\Convert\Gui;
{

    /* /**
     * @var 
     
    protected $;

    public function __construct(
         $
    

    ) {
        $this-> = $;
    } */
    public function execute()
	{
		echo 'sync gui execute';die;
	    	try {
            	$bulkimport = $this->create();
           		$bulkimport->saveConfig('tagalys_sync/product/import_status', "disable", "default", "disable");
            	// Mage::log("Dataflow CSV Import: product import finish", null, "tagalys.log");     
        	} catch (Exception $e) {
            
        	}
	    	parent::batchFinishAction();
	    }
}
