<?php
//namespace Tagalys\Tglssearch\Block\Catalog\Product\List;
//class Toolbar extends Mage_Catalog_Block_Product_List_Toolbar {

namespace Tagalys\Catalog\Block\Product\ProductList;

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
	//core properties
	protected $_availableMode = [];
	protected $_direction = ProductList::DEFAULT_SORT_DIRECTION;
	protected $_isExpanded = true;
	protected $_enableViewSwitcher = true;
	protected $_orderField = null;
	protected $_collection = null;
	protected $_template = 'product/list/toolbar.phtml';
	//protected $_availableOrder = null;
		

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
		\Tagalys\CatalogSearch\Helper\Data $tglssearchHelper, 
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
	
	
	protected function _prepareLayout()
    {
		//echo 'prepare Layout';die;
         parent::_prepareLayout();
        if ($this->getCollection()) {
            // create pager block for collection 
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'tgl.pager'
            )->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $this->setChild('pager', $pager);// set pager block in layout
        } 
		
        return $this;
    }
	
	/* public function isExpanded()
    {  //echo 'tag expanded';die;
        return $this->_isExpanded;
    }
	 */
    public function getAvailableOrders()   //overriden method
    {
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

     public function getOrderDirection($sort_id)    //getCurrentDirection() overriden 
	 {
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

    public function setDefaultOrder($field)   //overriden 
    {
		//echo 'tag setDefaultOrder'; die;
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

	public function isOrderCurrent($key)  //overriden
    {
		
      $tagalys = $this->tglssearchHelper->getTagalysSearchData();
      $order = $tagalys['sort'];
      if (intval($order) == intval($key)) {
        return true;
      }
      return false;
    }
	
    public function getPagerHtml()
    {
		//echo 'getpagerhtml';die;
         $pagerBlock = $this->getChildBlock('product_list_toolbar_pager'); //print_r($pagerBlock);die;

        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            // @var $pagerBlock \Magento\Theme\Block\Html\Pager 
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );

            return $pagerBlock->toHtml();
        } 

        return '';
		//return $this->getChildHtml('pager');
    }
}
