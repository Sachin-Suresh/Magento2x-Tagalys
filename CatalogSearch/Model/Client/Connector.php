<?php
namespace Tagalys\CatalogSearch\Model\Client;


class Connector extends \Magento\Framework\Model\AbstractModel {

	protected $_api_key;
	protected $_api_server;
	protected $_create_or_update_ep;
	protected $_search_ep;
	protected $_delete_ep;
	protected $_search_suggestions_ep;
	protected $_merchandising_ep;
	protected $_similar_products_ep;

	protected $_search = array();

	protected $_error = false;

	protected $timeout = 5;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        //\Magento\Framework\App\Config\ScopeConfigInterface\Proxy $scopeConfig,			//Proxy class added to break cir dependency
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }



	public function getSearchResult()
	{
		if(!empty($this->_search)) {
			return $this->_search;
		}

		return $this->_search;
	}

	protected function _construct()
	{

									/*getValue(path/to/adminhtml/system)  modified.   */
		$this->_api_server = $this->scopeConfig->getValue('server/api/hostname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //Sync/etc/system.xml
		$this->_api_key = $this->scopeConfig->getValue('server/api/key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

		$this->_create_or_update_ep = $this->scopeConfig->getValue('search/endpoint/create_or_update', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //no change //Tglssearch/etc/config.xml
		$this->_search_ep = $this->scopeConfig->getValue('search/endpoint/search', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);  //no change
		$this->_delete_ep = $this->scopeConfig->getValue('search/endpoint/delete', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);	//no change
		$this->_search_suggestions_ep = $this->scopeConfig->getValue('search/endpoint/search_suggestions', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //no change
		$this->_merchandising_ep = $this->scopeConfig->getValue('search/endpoint/merchandising', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);     //no change
		$this->_similar_products_ep = $this->scopeConfig->getValue('search/endpoint/similar_products', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //no change
	}

	protected function getUrl($e_type)
	{
		 //print_r('getUrl'); die;
		switch ($e_type) {
			case 'update':
			case 'create':
				$url = $this->_api_server.$this->_create_or_update_ep;
				return $url;
				break;
			case 'delete':
				$url =$this->_api_server.$this->_delete_ep;
				return $url;
				break;
			case 'search':
				$url =$this->_api_server.$this->_search_ep;
				return $url;
				break;
			case 'suggestions':
				$url = $this->_api_server.$this->_search_suggestions_ep;
				return $url;
				break;
			case 'merchandising':
				$url = $this->_api_server.$this->_merchandising_ep;
				return $url;
				break;
			case 'similar':
				$url = $this->_api_server.$this->_similar_products_ep;
				return $url;
				break;
			default:
				break;
		}
		//return 'urlll';

	}

	protected function createPayload($payload = null, $action)
	{
		if($action == 'search') {
			$request = array(
				'api_key' => $this->_api_key,
				'payload' => $payload
		 	);
		} else if( $action == 'merchandising' || $action == 'similar') {
			$request = array(
				'api_key' => $this->_api_key,
				'payload' => $payload
		 	);
		} else {
			$request = array(
				'api_key' => $this->_api_key,
				'perform' => $action,
				'payload' => $payload
		 	);
		}

		$payloadData = json_encode($request);
		return $payloadData;
	}

	public function createProduct($payload)
	{
		try {
			$url = $this->getUrl('create');
			$payloadData = $this->createPayload($payload, $action = 'create_or_update');
			return $this->_payloadAgent($url, $payloadData);
		} catch(Exception $e) {

		}

	}

	public function updateProduct($payload) {
		try {

			$url = $this->getUrl('update');
			$payloadData = $this->createPayload($payload, $action = 'create_or_update');
			return $this->_payloadAgent($url, $payloadData);

		} catch(Exception $e) {

		}

	}

	public function deleteProduct($payload) {

		try {

			$url = $this->getUrl('delete');
			$payloadData = $this->createPayload($payload, $action = 'delete');
			return $this->_payloadAgent($url, $payloadData);

		} catch (Exception $e) {

		}


	}

	public function searchSuggestion($query = '')
	{
		//print_r('searchSuggestion'); die;
		try {
			$url = $this->getUrl('suggestions');
			$result = $this->_queryAgent($url, $query);
			return $result;
		} catch (Exception $e) {

		}

	}

	public function searchProduct($payload, $filter = false)
	{
	 //print_r('searchProduct'); die;
		try {
			$url = $this->getUrl('search');

			$payload['api_key'] = $this->_api_key;
			 //$this->logger->log(null, 'Search Filter Query String:'.json_encode($payload));

			 //custom defined log
			 $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Tag.log');
			 $logger = new \Zend\Log\Logger();
			 $logger->addWriter($writer);
			 $logger->info('Search Filter Query String:'.json_encode($payload));

			$this->_search = $this->_payloadAgent($url, json_encode($payload));
			return $this->_search;
		} catch (Exception $e) {


		}
	}
	public function similarProduct($payload)
	{
		//print_r('similarProduct'); die;
		try {
				$url = $this->getUrl('similar');
				$payloadData = $this->createPayload($payload, $action = 'similar');
				return $this->_payloadAgent($url, $payloadData);

		} catch (Exception $e) {

		}

	}

	/* public function merchandisingPage($payload)
	{
		//print_r('merchandisingPage'); die;
		try {
			$url = $this->getUrl('merchandising');
			$payloadData = $this->createPayload($payload, $action = 'merchandising');
			return $this->_payloadAgent($url, $payloadData);

		} catch (Exception $e) {

		}

	} */
	public function merchandisingPage($payload)
	{
		try {
			 $url = $this->getUrl('merchandising');     //  replace -:name-of-merchandising-page, test,  http://staging-api.tagalys.com//api/v1/merchandising_pages/test/products
			 $payload['api_key']=$this->_api_key; 
			 $payload['filters'] = true;
			 $url = str_replace(":name-of-merchandising-page",$payload['q'],$url);  
			 $this->_search = $this->_payloadAgent($url,json_encode($payload));   //echo '<pre>';print_r($this->_search);die;
			  return $this->_search;
               } catch (Exception $e) {

               }
	}

	private function _getAgent($url)
	{
		//print_r('_getAgent'); die;
		$agent = curl_init($url);
		return $agent;
	}

	private function _queryAgent($url, $query)
	{
		//print_r('_queryAgent'); die;
		$q_url = $url;
		$q_url .= '?q='.$query;
		$q_url .= '&api_key='.$this->_api_key;
		$agent = $this->_getAgent($url);
		curl_setopt($agent, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt( $agent, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($agent, CURLOPT_TIMEOUT, $this->timeout);

		$result = curl_exec($agent);
		$info = curl_getinfo($agent);

		 $this->logger->log(null, 'Tagalys Request Info: '.json_encode($info));
		 $this->logger->log(null, 'Tagalys Response Info: '.$result);

		 //custom defined log
		 $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Tag.log');
		 $logger = new \Zend\Log\Logger();
		 $logger->addWriter($writer);
		 $logger->info('Tagalys Request Info: '.json_encode($info));
		 $logger->info('Tagalys Response Info: '.$result);

		if(curl_errno($agent)) {
    			$this->logger->log(null, 'Tagalys API Server Request Error:'.curl_error($agent));
		} else {
			if (empty($result)) {
				$this->_error = false;
    			$this->logger->log(null, 'Tagalys API Server Get Method Error');
			}
			return $result;
		}

		curl_close($agent);

	}

	private function _payloadAgent($url, $payload)
	{
		//print_r('_payloadAgent'); die;
		$agent = $this->_getAgent($url);
		curl_setopt( $agent, CURLOPT_POSTFIELDS, $payload );
		curl_setopt($agent, CURLOPT_POST,1);
		curl_setopt( $agent, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt( $agent, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $agent, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt($agent, CURLOPT_TIMEOUT, $this->timeout);
		$result = curl_exec($agent);

		$info = curl_getinfo($agent);

		//$this->logger->log(null, 'Tagalys Response Info: '.$result);

		 //custom defined log
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Tag.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Tagalys Response Info: '.$result);

		if(curl_errno($agent)) {
				$this->_error = true;
    		//$this->logger->log(null, 'Tagalys API Server Request Error:'.curl_error($agent));
			$logger->info('Tagalys API Server Request Error:'.curl_error($agent));
		} else {
			if (empty($result)) {
				$this->_error = true;
    		//$this->logger->log(null, 'API Server Payload Post Method Error');
			$logger->info('API Server Payload Post Method Error');
			}
		}

		curl_close($agent);

		if (!$this->_error) {
			$decoded = json_decode($result, true);
			return $decoded;
		} else {
			return $result;
		}

	}

	public function isRequestSuccess()
	{
		//print_r('isRequestSuccess'); 
		if( $this->_error == true || empty($this->_search) )
		{
			return false;
		}

		return true;
	}
}
