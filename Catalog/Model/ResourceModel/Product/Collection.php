<?php
namespace Tagalys\Catalog\Model\ResourceModel\Product;
//use Magento\Catalog\Model\ResourceModel\Product\Collection;
//use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
	/**
     * @var \Tagalys\CatalogSearch\Helper\Data
     */
    protected $tglssearchHelper;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Tagalys\CatalogSearch\Helper\Data $tglssearchHelper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->tglssearchHelper = $tglssearchHelper;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection
        );
    }

	public function getSize() {

		//print_r('TAGgetsize');
	    $tagalys = $this->tglssearchHelper->getTagalysSearchData();

	  	 if($tagalys) {

	  	 	$searchResult = $tagalys;

	  	 	return $searchResult['total'];

	    } else {

	    	return parent::getSize();
	    }

	}


}
