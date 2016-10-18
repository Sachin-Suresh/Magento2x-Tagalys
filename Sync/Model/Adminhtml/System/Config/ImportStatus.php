<?php
namespace Tagalys\Sync\Model\Adminhtml\System\Config;

class ImportStatus
{
   public function toOptionArray()
   {
      //echo 'import status';die;
      $options = array(
        "enable" => "Enable",
        "disable" => "Disable"
      ); 
      $field = array();
		  foreach ($options as $key => $value){
    		  array_push($field, array('value' => $key,'label'=> $value ));
		  }
      return $field;
   }
}