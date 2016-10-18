<?php
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

namespace Tagalys\MerchandisingPage\Controller\Index;


class Index extends \Magento\Framework\App\Action\Action
{
	/* protected $resultPageFactory;
	private $layerResolver;
	private $_queryFactory;
	protected $_storeManager; */
	protected $resultPageFactory;
	protected $tglssearchClientConnector;
	
	public function __construct( 
		 \Magento\Framework\App\Action\Context $context,
		\Magento\Catalog\Model\Session $catalogSession,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		/*\Magento\Search\Model\QueryFactory $queryFactory,
		\Magento\Catalog\Model\Layer\Resolver $layerResolver, */
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\LayoutInterface $layout,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Tagalys\CatalogSearch\Model\Client\Connector $tglssearchClientConnector,
		\Magento\Catalog\Model\CategoryFactory $catalogCategoryFactory,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory 
	)
	{
		parent::__construct($context);
		$this->request = $request;
		$this->layout = $layout;
		$this->scopeConfig = $scopeConfig;
		$this->catalogSession = $catalogSession;
		$this->storeManager = $storeManager;
		$this->tglssearchClientConnector = $tglssearchClientConnector;
		$this->catalogCategoryFactory = $catalogCategoryFactory;
		$this->registry = $registry;
		$this->resultPageFactory=$resultPageFactory;
	}
	
	
	public function execute()
    {   
		//echo 'merch 2x-6 new';die;
		$service = $this->tglssearchClientConnector;  
		$query = $this->request->getParam('q');   
		$q = $this->request; 
		$re =  array();
		$re = $q->getParams();  
 
		$re['filters'] = true;
		$re['q'] = $re['product'];   //use url path: http://localhost:1338/magento2x_3/merch/index/index/product/test
		unset($re['product']);
		$payload = $re;  
 
  
		if(!empty($re['f'])) {
			$payload['f'] = $re['f'];
		}
      
      //by aaditya 
      if(isset($re['order'])) {
        $payload['sort'] = $re['order'];
        $payload['order'] = $re['dir'];
      } else {
        $payload['sort'] = null; //Mage::getSingleton('catalog/session')->getSortOrder();
      }
      //end 
      if(isset($re['qf'])) {
        $payload['qf'] = $re['qf'];
      }
	
      //$current_list_mode = $this->layout->createBlock('catalog/product_list_toolbar')->setChild('product_list_toolbar_pager', $pager)->getCurrentMode();
	  $current_list_mode = $this->layout->createBlock('Tagalys\Catalog\Block\Product\ProductList\Toolbar')->getCurrentMode();   //print_r($current_list_mode);die;
  
      if( $current_list_mode == "grid" || $current_list_mode == "grid-list") {
        $defaultLimit = $this->scopeConfig->getValue('catalog/frontend/grid_per_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); 
        
      } else if($current_list_mode == "list" || $current_list_mode == "list-grid") {
        $defaultLimit = $this->scopeConfig->getValue('catalog/frontend/list_per_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
      }

      $session_limit = $this->catalogSession->getLimitPage();  

      $payload['per_page'] = !empty($session_limit) ? $session_limit : $defaultLimit;
      $payload['page'] = (!empty($re['p'])) ? $re['p'] : 1;     

			$response = $service->merchandisingPage($payload);	
		$rootCategoryId = (int) $this->storeManager->getStore()->getRootCategoryId();  
        if (!$rootCategoryId) {
            $this->_forward('noRoute');
            return;
        }
        
		 $rootCategory = $this->catalogCategoryFactory->create()
            ->load($rootCategoryId)
            //->setName($this->__('Sale'))
            ->setName('Sale')
            ->setMetaTitle('Sale')
            ->setMetaDescription('Sale')
            ->setMetaKeywords('Sale');
 
        $this->registry->register('current_category', $rootCategory);
       
	   
   		
		/* $this->_view->loadLayout();
        $this->_view->renderLayout(); */
		$this->_view->loadLayout();
            $this->_view->renderLayout();
		
		$resultPage = $this->resultPageFactory->create();   //Blank page rendering issue fix line
		
		return $resultPage;
	}
		
}