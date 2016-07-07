<?php
namespace Tagalys\Tglssearch\Block;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
//use Magento\LayeredNavigation\Block\Navigation;
use Magento\Search\Model\QueryFactory;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;

		
class Result extends \Magento\Framework\View\Element\Template
{

	protected $tglssearchHelper;
	protected $catalogLayer;
	protected $visibilityFlag;
	protected $filterableAttributes;
	protected $objectManager;
	
	//protected $moduleResource;
	
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
		\Magento\Catalog\Model\Layer\FilterList $filterList,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		FilterableAttributeListInterface $filterableAttributes,
		\Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
	    \Tagalys\Tglssearch\Helper\Data $tglssearchHelper,
		QueryFactory $queryFactory,
		//\Magento\Framework\Module\ResourceInterface $moduleResource,
		array $data = []
    ) {
        $this->catalogLayer = $layerResolver->get();
		$this->tglssearchHelper = $tglssearchHelper;
		$this->queryFactory = $queryFactory;
		$this->filterableAttributes = $filterableAttributes;
		$this->visibilityFlag = $visibilityFlag;
        parent::__construct(
            $context,
            $data
        );
    }
		
		
	//core method added
	 protected function _prepareLayout()
    {
		 //echo ' tag prpeareLayou';die;
        $this->renderer = $this->getChildBlock('renderer');
        foreach ($this->filterList->getFilters($this->_catalogLayer) as $filter) {
            $filter->apply($this->getRequest());
        }
        $this->getLayer()->apply();
        return parent::_prepareLayout();
    }
		 			//core method added
	public function getAdditionalHtml()
    {
		print_r('tag get additional html');die;
        return $this->getLayout()->getBlock('search_result_list')->getChildHtml('additional');
    }
	
	public function getListBlock()
    {
		print_r('tag get listblock');die;
        return $this->getChildBlock('search_result_list');
    }
	
	public function getProductListHtml()
    {
		print_r('tag get prodlist');die;
        return $this->getChildHtml('search_result_list');
    }



    
    public function getTagalysFilter() {
        print_r('resultgg request');die;
        $result = $this->tglssearchHelper->getTagalysSearchData();
        $data = json_decode($result, true);
        $filters = (!empty($data['filters'])) ? $data['filters'] : null ;
        
        return $filters;
    }

     public function canShowBlock()
	 {
        //print_r('canShowBlock22');die;
      $service =  $this->tglssearchHelper->isTagalysActive();
      
      if($service) {
        return true;
      } else {
        parent::canShowBlock();
      }
    }
    
}
