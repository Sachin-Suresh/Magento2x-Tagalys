<?php
namespace Tagalys\Tglssearch\Model;

class Engine extends \Magento\Framework\Model\AbstractModel
{
	//var_dump(get_class($this));die;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Tagalys\Tglssearch\Model\Client\Connector
     */
    protected $tglssearchClientConnector;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\LayoutInterface $layout,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Tagalys\Tglssearch\Model\Client\Connector $tglssearchClientConnector,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\Session $catalogSession,
        \Psr\Log\LoggerInterface $logger,			//log injection
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->layout = $layout;
	    $this->layoutFactory = $layoutFactory;
        $this->scopeConfig = $scopeConfig;
        $this->tglssearchClientConnector = $tglssearchClientConnector;
        $this->request = $request;
        $this->catalogSession = $catalogSession;
        $this->logger = $logger;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }


	private function _constructTagalysQuery($payload) {
		$pattern = '/^t.[0-9]+$/';
		$pricePattern = '/^r.[0-9]+$/';
		$query = array();
		foreach ($payload as $key => $value) {

			if( preg_match($pattern, $key) ) {
				//$query[$key][] = $value;
				$query[$key] = $value;
			}

			if(preg_match($pricePattern, $key)) {
				//$query[$key][] = $value;
				$query[$key] = $value;

			}
		}

		return $query;

	}

	private function _makeTagalysRequest() {
	//	print_r('Tagalays request');die;
		try {
			//Magento\Framework\View\Element\Template
				//print_r('tagRequest');die;
			//$current_list_mode = $this->layout->createBlock('catalog/product_list_toolbar')->setChild('product_list_toolbar_pager', $pager)->getCurrentMode();
			//$current_list_mode = $this->layout->createBlock('Tagalys\Tglssearch\Block\Catalog\Product\SearchList\Toolbar')->setChild('product_list_toolbar_pager', $pager)->getCurrentMode();
			//$current_list_mode = $this->layoutFactory->create()->createBlock('Magento\Catalog\Block\Product\ProductList\Toolbar')->setChild('product_list_toolbar_pager', $pager)->getCurrentMode();

			//$current_list_mode = $this->layout->createBlock('Magento\Catalog\Block\Product\ProductList\Toolbar')->getCurrentMode();
			$current_list_mode = $this->layout->createBlock('Tagalys\Tglssearch\Block\Product\ProductList\Toolbar')->getCurrentMode();

			/* $pager = $this->layout->createBlock('Tagalys\Tglssearch\Block\Catalog\Product\SearchList\Toolbar');
			$this->setChild('toolbar_pager',$pager); */

			if( $current_list_mode == "grid" || $current_list_mode == "grid-list") {
				$defaultLimit = $this->scopeConfig->getValue('catalog/frontend/grid_per_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

			} else if($current_list_mode == "list" || $current_list_mode == "list-grid") {
				$defaultLimit = $this->scopeConfig->getValue('catalog/frontend/list_per_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			}

			$service = $this->tglssearchClientConnector;
			$query = $this->request->getParam('q');
		    $q = $this->request;
			$request =  array();
			$request = $q->getParams();
			$request['filters'] = true;
			$request['q'] = $request['q'];
			$payload = array();

			// $fquery = $this->_constructTagalysQuery($request);

			$payload['filters'] = true;
			$payload['q'] = $query;
			$session_limit = $this->catalogSession->getLimitPage();

			$payload['per_page'] = !empty($session_limit) ? $session_limit : $defaultLimit;
			$payload['page'] = (!empty($request['p'])) ? $request['p'] : 1;

			if(!empty($request['f']))
			{
				$payload['f'] = $request['f'];
			}

			//by aaditya
			if(isset($request['order']))
			{
				$payload['sort'] = $request['order'];
				$payload['order'] = $request['dir'];
			} else {
				$payload['sort'] = null; //Mage::getSingleton('catalog/session')->getSortOrder();
			}
			//end
			if(isset($request['qf'])) {
				$payload['qf'] = $request['qf'];
			}

			// if($payload['page'] > 1) {
			// 		$payload['filters'] = false;
			// }


			 //$this->logger->log(\Psr\Log\LogLevel::DEBUG, 'Tagalys Request Object: '.json_encode($payload));

			 //custom defined log
			 $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Tag.log');
			 $logger = new \Zend\Log\Logger();
			 $logger->addWriter($writer);
			 $logger->info('Tagalys Request Object: '.json_encode($payload));


			$response = $service->searchProduct($payload);

		} catch (Exception $e) {
			//$this->logger->log(null, 'Tagalys Request Error: '.$e->getMessage());
			$this->logger->log(\Psr\Log\LogLevel::DEBUG, 'Tagalys Request Error: '.$e->getMessage());
		}

	}

	public function getCatlogSearchResult() {
			$this->_makeTagalysRequest();
	}

	public function getCatalogResult(\Magento\Framework\Event\Observer $observer) {
			$this->_makeTagalysRequest();
	}

}
