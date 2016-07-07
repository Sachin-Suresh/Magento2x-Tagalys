<?php
/**
* Override Catalog Layer View
*/
namespace Tagalys\Tglssearch\Block;
//use Magento\LayeredNavigation\Block\Navigation;

class Navigation extends \Magento\Framework\View\Element\Template {

    /**
     * @var \Tagalys\Tglssearch\Helper\Data
     */
    protected $tglssearchHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\Layer\FilterList $filterList,
        //\Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
        \Tagalys\Tglssearch\Helper\Data $tglssearchHelper,
        array $data = []
    ) {
        $this->tglssearchHelper = $tglssearchHelper;
        parent::__construct(
            $context,
            $layerResolver,
            $filterList,
            //$visibilityFlag,
            $data
        );
    }

	//core method added
	protected function _prepareLayout()
    {
		echo 'tag prpeareLayou';die;
        $this->renderer = $this->getChildBlock('renderer');
        foreach ($this->filterList->getFilters($this->_catalogLayer) as $filter) {
            $filter->apply($this->getRequest());
        }
        $this->getLayer()->apply();
        return parent::_prepareLayout();
    }

	 public function getTagalysFilter() {
        print_r('resultff request');die;
        $result = $this->tglssearchHelper->getTagalysSearchData();
        $data = json_decode($result, true);
        $filters = (!empty($data['filters'])) ? $data['filters'] : null ;

        return $filters;
    }

     public function canShowBlock()
	 {
		print_r('canShowBlock');die;
      $service =  $this->tglssearchHelper->isTagalysActive();

      if($service)
	  {
        return true;
      } else {
        parent::canShowBlock();
      }
    }
}
