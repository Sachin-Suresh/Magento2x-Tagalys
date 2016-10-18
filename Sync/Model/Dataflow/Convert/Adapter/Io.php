<?php
namespace Tagalys\Sync\Model\Dataflow\Convert\Adapter;


class Io extends Mage_Dataflow_Model_Convert_Adapter_Io 
{

    /**
     * @var 
     */
    protected $;

    public function __construct(
        
    ) {
        $this-> = $;
    }
    public function save() 
	{
		echo 'syn adapter';die;
	    	try {
            	$bulkimport = $this->->create();
           		$bulkimport->saveConfig('sync/product/import_status', "disable", "default", "disable");
            	// Mage::log("Dataflow CSV Import: product import finish", null, "tagalys.log");     
        	} catch (Exception $e) {
            
        	}     
        	
	    	return parent::save();
	}

}