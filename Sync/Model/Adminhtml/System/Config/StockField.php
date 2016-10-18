<?php
namespace Tagalys\Sync\Model\Adminhtml\System\Config;
//print_r("hrllo");die;
class StockField implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Tagalys\Sync\Helper\Inventory
     */ 
    protected $syncInventoryHelper; 
    public function __construct(
	
        \Tagalys\Sync\Helper\Inventory $syncInventoryHelper
    ) {
        $this->syncInventoryHelper = $syncInventoryHelper;
    }
    public function toOptionArray()
   {
        //echo 'stock field II';die;
        $helper = $this->syncInventoryHelper;

        $stock_item = $helper->getInventoryStockItemField();

            $stock_field = array();
            foreach ($stock_item as $key => $label) {
              if(!empty($key) && !empty($label)) {
                array_push($stock_field, array('value' => $key,'label'=> $label ));  
              }
            }

          return $stock_field;
    }
}