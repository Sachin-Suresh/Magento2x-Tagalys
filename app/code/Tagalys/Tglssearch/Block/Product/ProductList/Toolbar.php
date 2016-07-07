<?php
//namespace Tagalys\Tglssearch\Block\Catalog\Product\List;
//class Toolbar extends Mage_Catalog_Block_Product_List_Toolbar {

namespace Tagalys\Tglssearch\Block\Product\ProductList;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;


//implements \Magento\Widget\Block\BlockInterface
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{

    /**
     * @var \Tagalys\Tglssearch\Helper\Data
     */
    protected $tglssearchHelper;
	protected $scopeConfig;

    public function __construct(
		/* passing all Constructors parameters to the parent class -Magento\Catalog\Block\Product\ProductList\Toolbar.php*/
		\Magento\Framework\View\Element\Template\Context $context,
		 \Magento\Catalog\Model\Session $catalogSession,
		\Magento\Catalog\Model\Config $catalogConfig,
		ToolbarModel $toolbarModel,
		\Magento\Framework\Url\EncoderInterface $urlEncoder,
		ProductList $productListHelper,
		\Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Framework\Registry $registry,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		//Tagalys Helper Data
		\Tagalys\Tglssearch\Helper\Data $tglssearchHelper, 
		array $data = []
	)  {
			$this->tglssearchHelper = $tglssearchHelper;
			$this->_catalogSession = $catalogSession;
			$this->_catalogConfig = $catalogConfig;
			$this->_toolbarModel = $toolbarModel;
			$this->_urlEncoder = $urlEncoder;
			$this->_productListHelper = $productListHelper;
			$this->_postDataHelper = $postDataHelper;
			$this->_registry = $registry;
			$this->scopeConfig = $scopeConfig;


			parent::__construct($context,$catalogSession,$catalogConfig,$toolbarModel,$urlEncoder,$productListHelper,$postDataHelper,$data);
       }

	 //core method added to override
	/*  public function getCurrentMode()
    {
		//print_r('overriden get query colle323');die;
        $mode = $this->_getData('_current_grid_mode');
		
        if ($mode) {
            return $mode;
        }



        $defaultMode = $this->_productListHelper->getDefaultViewMode($this->getModes());  
        $mode = $this->_toolbarModel->getMode();

        if (!$mode || !isset($this->_availableMode[$mode])) {
            $mode = $defaultMode;
        }

        $this->setData('_current_grid_mode', $mode);
        return $mode;
    } */

    public function getAvailableOrders()
    {
		//print_r('availablOrders');die;
        $tagalys = $this->tglssearchHelper->getTagalysSearchData();

        if($tagalys == false) {

            return $this->_availableOrder;

        } else {

            $data = $tagalys;

            $sort_options =  array();

            foreach ($data['sort_options'] as $key => $value) {
                $sort_options[$value['id']] = $value['label'];
            }

            $this->_availableOrder = $sort_options;

            return $this->_availableOrder;
        }
    }

     public function getOrderDirection($sort_id)
	 {
		print_r('getOrderDirection');die;
      $sort_direction =  array();

      $tagalys = $this->tglssearchHelper->getTagalysSearchData();

       $data = $tagalys;

      foreach ($data['sort_options'] as $key => $value) {
        if (intval($value['id']) === intval($sort_id)) {
          if(sizeof($value['orders']) === 0) {
            $sort_direction[] = null;
          }

          elseif (sizeof($value['orders']) === 1)  {
             $sort_direction[] = $value['orders'][0]["order"];
          }

          elseif (sizeof($value['orders']) > 1) {
              foreach ($value['orders'] as $key => $value) {
                 $sort_direction[] = $value["order"];
              }
          }
        }
      }

       return $sort_direction;
    }

    public function setDefaultOrder($field)
    {
       //print_r('setDefaultOrder');die;
        $tagalys = $this->tglssearchHelper->getTagalysSearchData();
        if($tagalys == false) {

            if (isset($this->_availableOrder[$field])) {
                $this->_orderField = $field;
            }

            return $this;
        } else {

            $data = $tagalys;

            if (isset($this->_availableOrder[$data['sort']])) {
                $this->_orderField = $field;
             }

            return $this;
        }

    }

	public function isOrderCurrent($key)
    {
		print_r('isOrderCurrent');die;
      $tagalys = $this->tglssearchHelper->getTagalysSearchData();
      $order = $tagalys['sort'];
      if (intval($order) == intval($key)) {
        return true;
      }
      return false;
    }

}
