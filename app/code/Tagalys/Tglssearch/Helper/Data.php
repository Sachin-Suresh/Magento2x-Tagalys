<?php
namespace Tagalys\Tglssearch\Helper;
use Magento\Search\Model\QueryFactory;

class Data extends \Magento\Search\Helper\Data
{

    /**
     * @var \Tagalys\Tglssearch\Helper\Data
     */
    protected $tglssearchHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Tagalys\Tglssearch\Model\Client\Connector
     */
    protected $tglssearchClientConnector;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    protected $themeHtmlPager;

    /* public function __construct(  							  //Constructor commented to avoid circular dependency in Helper/Data
        \Magento\Framework\App\Helper\Context $context,
		\Tagalys\Tglssearch\Helper\Data $tglssearchHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        //\Magento\Framework\App\Config\Proxy $scopeConfig,   				//added Proxy class - lazy loading to break Circular Dependancy
        \Tagalys\Tglssearch\Model\Client\Connector $ScopeConfigInterface ,
        \Magento\Theme\Block\Html\Pager $themeHtmlPager
    ) {
		$this->tglssearchHelper = $tglssearchHelper;
        $this->scopeConfig = $scopeConfig;
        $this->tglssearchClientConnector = $tglssearchClientConnector;
        $this->themeHtmlPager = $themeHtmlPager;
		parent::__construct(
            $context
        );
    } */

	public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Tagalys\Tglssearch\Model\Client\Connector $tglssearchClientConnector,
		\Magento\Theme\Block\Html\Pager $themeHtmlPager
    ) 	{
			$this->scopeConfig = $scopeConfig;              //to use getValue()
			$this->tglssearchClientConnector = $tglssearchClientConnector;
			$this->themeHtmlPager = $themeHtmlPager;
        }

	public function getTagalysSearchData()
	{
		//$service = $this->tglssearchHelper->isTagalysActive();
		$service = $this->isTagalysActive(); //print_r($service)  ;die;
		//print_r('Tglsearch data');die;
  	if($service)
	{
  		$searchResult = $service->getSearchResult();   print_r($searchResult)  ;die;
  		return $searchResult;
  	}
	else
	{
  		return false;
  	}

	}

	public function isTagalysActive()
	{//echo 'tag is ative';die;
		//$status = $this->scopeConfig->getValue('tgs_search/default/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$status = $this->scopeConfig->getValue('tagalys_tglssearch/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

		if ($status)
		{

			//$service = $this->tglssearchClientConnector;
			$service = $this->tglssearchClientConnector;
		    $tagalys = $service->isRequestSuccess();
		    if($tagalys)
			{
				//print_r('if');die;
		    	return $service;
		    }
			else
			{
				//print_r('else');die;
		    	return false;
		    }

		} else
		{
			//print_r('false');die;
			return false;
		}
		//print_r('nthg');die;
	}

	public function getTagalysFilter()
	{
		print_r('filter');die;
   		$result =  $this->tglssearchHelper->getTagalysSearchData();
   		$data = $result;
   		$filters = (!empty($data['filters'])) ? $data['filters'] : null ;

   		return $filters;
	}

	public function getTagalysFilterParams($params, $item, $filter)
	{
		echo('pager:');print_r($params,'',$item);die;
	  $this_params = array_merge_recursive($params, array('f' => array($filter['prefix'] => array($item['id']))));
	  if ($item['selected']) {
	    $unselected_array = array_diff($this_params['f'][$filter['prefix']], array($item['id']));
	    if (empty($unselected_array)) {
	      $this_params['f'][$filter['prefix']] = null;
	    } else {
	      $this_params['f'][$filter['prefix']] = $unselected_array;
	    }
	  }
	  if (isset($this_params['f'])) {
	  	$updated_f = array();
	  	foreach($this_params['f'] as $f_key => $assoc_array) {
	  		$updated_f[$f_key] = array_values($assoc_array);
	  	}
	  	$this_params['f'] = $updated_f;
	  }
	  $this_params[$this->themeHtmlPager->getPageVarName()] = null;
	  $this_params['isAjax'] = null;
	  return $this_params;
	}

	//overriding getResultURl from Magento/Search/Helper/Data
	public function getResultUrl($query = null)
	{
		echo 'tag geturl';die;
		return $this->_getUrl(
			'tglssearch/result',
			['_query' => [QueryFactory::QUERY_VAR_NAME => $query], '_secure' => $this->_request->isSecure()]
		);
	}
}
