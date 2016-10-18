<?php
/**
 * Merchanding Page Filter Helper 
 */
namespace Tagalys\MerchandisingPage\Block;

use Magento\Framework\View\Element\Template;

class Filter extends \Magento\Framework\View\Element\Template
{
	protected $tglssearchHelper;
	
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Tagalys\CatalogSearch\Helper\Data $tglssearchHelper,
		array $data = []
	)
	{	
		parent::__construct($context);
		$this->tglssearchHelper = $tglssearchHelper;
	}
	
	
	/* protected function _construct()
    {
		parent::_construct();
        $this->setTemplate('Tagalys_LayeredNavigation::layer/view.phtml'); 
        //$this->setTemplate('tagalys_search/filter.phtml');
    } */ 

     /* protected function _toHtml() {
        return parent::_toHtml();  
    } */ 

    
	 public function canShowBlock()
	{
		print_r('canShowBlock');
      $service =  $this->tglssearchHelper->isTagalysActive();

      if($service)
	  {
        return true;
      } else {
        parent::canShowBlock();
      }
    }
}
