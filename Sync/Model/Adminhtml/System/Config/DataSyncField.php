<?php
namespace Tagalys\Sync\Model\Adminhtml\System\Config;

class DataSyncField
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $catalogResourceModelProductAttributeCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $catalogResourceModelProductAttributeCollectionFactory
    ) {
        $this->catalogResourceModelProductAttributeCollectionFactory = $catalogResourceModelProductAttributeCollectionFactory;
    }
    public function toOptionArray()
   {
	   //echo 'Sync DSF I';die;
       $attributes = $this->catalogResourceModelProductAttributeCollectionFactory->create()->getItems();
    	$field = array();
		  foreach ($attributes as $attribute){
    		  $code = $attribute->getAttributecode();
    		  $label = $attribute->getFrontendLabel();
    		  if(!empty($label) && !empty($code))
    		    array_push($field, array('value' => $code,'label'=> $label ));
		  }

      return $field;
   }
}