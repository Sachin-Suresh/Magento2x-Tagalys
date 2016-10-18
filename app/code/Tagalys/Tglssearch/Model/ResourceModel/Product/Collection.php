<?php
namespace Tagalys\Tglssearch\Model\ResourceModel\Product;
//use Magento\Catalog\Model\ResourceModel\Product\Collection;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
	//print_r('Tag model');die;
    /**
     * @var \Tagalys\Tglssearch\Helper\Data
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
        \Tagalys\Tglssearch\Helper\Data $tglssearchHelper,
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
        
		print_r('getsize');die;
	    $tagalys = $this->tglssearchHelper->getTagalysSearchData(); 

	  	 if($tagalys) {

	  	 	$searchResult = $tagalys;

	  	 	return $searchResult['total'];

	    } else {

	    	return parent::getSize();
	    }

	}
	
	public function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init('Magento\Catalog\Model\Product', 'Magento\Catalog\Model\ResourceModel\Product\Flat');
        } else {
            $this->_init('Magento\Catalog\Model\Product', 'Magento\Catalog\Model\ResourceModel\Product');
        }
        $this->_initTables();
    }
	
	//protected functions from Magento Core collection class
	public function _init($model, $entityModel)
    {
        if ($this->isEnabledFlat()) {
            $entityModel = 'Magento\Catalog\Model\ResourceModel\Product\Flat';
        }
        return parent::_init($model, $entityModel);
    }
	
	protected function _initTables()
    {
        $this->_productWebsiteTable = $this->getResource()->getTable('catalog_product_website');
        $this->_productCategoryTable = $this->getResource()->getTable('catalog_category_product');
    }
	
	protected function _prepareStaticFields()
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_prepareStaticFields();
    }
	
	protected function _initSelect()
    {
        if ($this->isEnabledFlat()) {
            $this->getSelect()->from(
                [self::MAIN_TABLE_ALIAS => $this->getEntity()->getFlatTableName()],
                null
            )->columns(
                ['status' => new \Zend_Db_Expr(ProductStatus::STATUS_ENABLED)]
            );
            $this->addAttributeToSelect($this->getResource()->getDefaultAttributes());
            if ($this->_catalogProductFlatState->getFlatIndexerHelper()->isAddChildData()) {
                $this->getSelect()->where('e.is_child=?', 0);
                $this->addAttributeToSelect(['child_id', 'is_child']);
            }
        } else {
            $this->getSelect()->from([self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()]);
        }
        return $this;
    }
	
	protected function _afterLoad()
    {
        if ($this->_addUrlRewrite) {
            $this->_addUrlRewrite();
        }

        $this->_prepareUrlDataObject();

        if (count($this)) {
            $this->_eventManager->dispatch('catalog_product_collection_load_after', ['collection' => $this]);
        }

        return $this;
    }
	
	 protected function _prepareUrlDataObject()
    {
        $objects = [];
        /** @var $item \Magento\Catalog\Model\Product */
        foreach ($this->_items as $item) {
            if ($this->getFlag('do_not_use_category_id')) {
                $item->setDoNotUseCategoryId(true);
            }
            if (!$item->isVisibleInSiteVisibility() && $item->getItemStoreId()) {
                $objects[$item->getEntityId()] = $item->getItemStoreId();
            }
        }

        if ($objects && $this->hasFlag('url_data_object')) {
            $objects = $this->_catalogUrl->getRewriteByProductStore($objects);
            foreach ($this->_items as $item) {
                if (isset($objects[$item->getEntityId()])) {
                    $object = new \Magento\Framework\DataObject($objects[$item->getEntityId()]);
                    $item->setUrlDataObject($object);
                }
            }
        }

        return $this;
    }
	
	protected function doAddWebsiteNamesToResult()
    {
        $productWebsites = [];
        foreach ($this as $product) {
            $productWebsites[$product->getId()] = [];
        }

        if (!empty($productWebsites)) {
            $select = $this->getConnection()->select()->from(
                ['product_website' => $this->_productWebsiteTable]
            )->join(
                ['website' => $this->getResource()->getTable('store_website')],
                'website.website_id = product_website.website_id',
                ['name']
            )->where(
                'product_website.product_id IN (?)',
                array_keys($productWebsites)
            )->where(
                'website.website_id > ?',
                0
            );

            $data = $this->getConnection()->fetchAll($select);
            foreach ($data as $row) {
                $productWebsites[$row['product_id']][] = $row['website_id'];
            }
        }

        foreach ($this as $product) {
            if (isset($productWebsites[$product->getId()])) {
                $product->setData('websites', $productWebsites[$product->getId()]);
            }
        }
        return $this;
    }
	
	private function mapConditionType($conditionType)
    {
        $conditionsMap = [
            'eq' => 'in',
            'neq' => 'nin'
        ];
        return isset($conditionsMap[$conditionType]) ? $conditionsMap[$conditionType] : $conditionType;
    }
	
	protected function _getSelectCountSql($select = null, $resetLeftJoins = true)
    {
        $this->_renderFilters();
        $countSelect = is_null($select) ? $this->_getClearSelect() : $this->_buildClearSelect($select);
        $countSelect->columns('COUNT(DISTINCT e.entity_id)');
        if ($resetLeftJoins) {
            $countSelect->resetJoinLeft();
        }
        return $countSelect;
    }
	
	protected function _prepareStatisticsData()
    {
        $select = clone $this->getSelect();
        $priceExpression = $this->getPriceExpression($select) . ' ' . $this->getAdditionalPriceExpression($select);
        $sqlEndPart = ') * ' . $this->getCurrencyRate() . ', 2)';
        $select = $this->_getSelectCountSql($select, false);
        $select->columns(
            [
                'max' => 'ROUND(MAX(' . $priceExpression . $sqlEndPart,
                'min' => 'ROUND(MIN(' . $priceExpression . $sqlEndPart,
                'std' => $this->getConnection()->getStandardDeviationSql('ROUND((' . $priceExpression . $sqlEndPart),
            ]
        );
        $select->where($this->getPriceExpression($select) . ' IS NOT NULL');
        $row = $this->getConnection()->fetchRow($select, $this->_bindParams, \Zend_Db::FETCH_NUM);
        $this->_pricesCount = (int)$row[0];
        $this->_maxPrice = (double)$row[1];
        $this->_minPrice = (double)$row[2];
        $this->_priceStandardDeviation = (double)$row[3];

        return $this;
    }
	
	protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Build clear select
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (null === $select) {
            $select = clone $this->getSelect();
        }
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);

        return $select;
    }

	protected function _addUrlRewrite()
    {
        $productIds = [];
        foreach ($this->getItems() as $item) {
            $productIds[] = $item->getEntityId();
        }
        if (!$productIds) {
            return;
        }

        $select = $this->getConnection()
            ->select()
            ->from(['u' => $this->getTable('url_rewrite')], ['u.entity_id', 'u.request_path'])
            ->where('u.store_id = ?', $this->_storeManager->getStore($this->getStoreId())->getId())
            ->where('u.is_autogenerated = 1')
            ->where('u.entity_type = ?', ProductUrlRewriteGenerator::ENTITY_TYPE)
            ->where('u.entity_id IN(?)', $productIds);

        if ($this->_urlRewriteCategory) {
            $select->joinInner(
                ['cu' => $this->getTable('catalog_url_rewrite_product_category')],
                'u.url_rewrite_id=cu.url_rewrite_id'
            )->where('cu.category_id IN (?)', $this->_urlRewriteCategory);
        }

        // more priority is data with category id
        $urlRewrites = [];

        foreach ($this->getConnection()->fetchAll($select) as $row) {
            if (!isset($urlRewrites[$row['entity_id']])) {
                $urlRewrites[$row['entity_id']] = $row['request_path'];
            }
        }

        foreach ($this->getItems() as $item) {
            if (isset($urlRewrites[$item->getEntityId()])) {
                $item->setData('request_path', $urlRewrites[$item->getEntityId()]);
            } else {
                $item->setData('request_path', false);
            }
        }
    }

	protected function getEntityPkName(\Magento\Eav\Model\Entity\AbstractEntity $entity)
    {
        return $entity->getLinkField();
    }
	
	protected function _prepareProductLimitationFilters()
    {
        if (isset(
            $this->_productLimitationFilters['visibility']
        ) && !isset(
            $this->_productLimitationFilters['store_id']
        )
        ) {
            $this->_productLimitationFilters['store_id'] = $this->getStoreId();
        }
        if (isset(
            $this->_productLimitationFilters['category_id']
        ) && !isset(
            $this->_productLimitationFilters['store_id']
        )
        ) {
            $this->_productLimitationFilters['store_id'] = $this->getStoreId();
        }
        if (isset(
            $this->_productLimitationFilters['store_id']
        ) && isset(
            $this->_productLimitationFilters['visibility']
        ) && !isset(
            $this->_productLimitationFilters['category_id']
        )
        ) {
            $this->_productLimitationFilters['category_id'] = $this->_storeManager->getStore(
                $this->_productLimitationFilters['store_id']
            )->getRootCategoryId();
        }

        return $this;
    }
	protected function _productLimitationJoinWebsite()
    {
        $joinWebsite = false;
        $filters = $this->_productLimitationFilters;
        $conditions = ['product_website.product_id = e.entity_id'];

        if (isset($filters['website_ids'])) {
            $joinWebsite = true;
            if (count($filters['website_ids']) > 1) {
                $this->getSelect()->distinct(true);
            }
            $conditions[] = $this->getConnection()->quoteInto(
                'product_website.website_id IN(?)',
                $filters['website_ids']
            );
        } elseif (isset(
            $filters['store_id']
        ) && (!isset(
            $filters['visibility']
        ) && !isset(
            $filters['category_id']
        )) && !$this->isEnabledFlat()
        ) {
            $joinWebsite = true;
            $websiteId = $this->_storeManager->getStore($filters['store_id'])->getWebsiteId();
            $conditions[] = $this->getConnection()->quoteInto('product_website.website_id = ?', $websiteId);
        }

        $fromPart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::FROM);
        if (isset($fromPart['product_website'])) {
            if (!$joinWebsite) {
                unset($fromPart['product_website']);
            } else {
                $fromPart['product_website']['joinCondition'] = join(' AND ', $conditions);
            }
            $this->getSelect()->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);
        } elseif ($joinWebsite) {
            $this->getSelect()->join(
                ['product_website' => $this->getTable('catalog_product_website')],
                join(' AND ', $conditions),
                []
            );
        }

        return $this;
    }

    /**
     * Join additional (alternative) store visibility filter
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _productLimitationJoinStore()
    {
        $filters = $this->_productLimitationFilters;
        if (!isset($filters['store_table'])) {
            return $this;
        }

        $hasColumn = false;
        foreach ($this->getSelect()->getPart(\Magento\Framework\DB\Select::COLUMNS) as $columnEntry) {
            list(, , $alias) = $columnEntry;
            if ($alias == 'visibility') {
                $hasColumn = true;
            }
        }
        if (!$hasColumn) {
            $this->getSelect()->columns('visibility', 'cat_index');
        }

        $fromPart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::FROM);
        if (!isset($fromPart['store_index'])) {
            $this->getSelect()->joinLeft(
                ['store_index' => $this->getTable('store')],
                'store_index.store_id = ' . $filters['store_table'] . '.store_id',
                []
            );
        }
        if (!isset($fromPart['store_group_index'])) {
            $this->getSelect()->joinLeft(
                ['store_group_index' => $this->getTable('store_group')],
                'store_index.group_id = store_group_index.group_id',
                []
            );
        }
        if (!isset($fromPart['store_cat_index'])) {
            $this->getSelect()->joinLeft(
                ['store_cat_index' => $this->getTable('catalog_category_product_index')],
                join(
                    ' AND ',
                    [
                        'store_cat_index.product_id = e.entity_id',
                        'store_cat_index.store_id = ' . $filters['store_table'] . '.store_id',
                        'store_cat_index.category_id=store_group_index.root_category_id'
                    ]
                ),
                ['store_visibility' => 'visibility']
            );
        }
        // Avoid column duplication problems
        $this->_resourceHelper->prepareColumnsList($this->getSelect());

        $whereCond = join(
            ' OR ',
            [
                $this->getConnection()->quoteInto('cat_index.visibility IN(?)', $filters['visibility']),
                $this->getConnection()->quoteInto('store_cat_index.visibility IN(?)', $filters['visibility'])
            ]
        );

        $wherePart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
        $hasCond = false;
        foreach ($wherePart as $cond) {
            if ($cond == '(' . $whereCond . ')') {
                $hasCond = true;
            }
        }

        if (!$hasCond) {
            $this->getSelect()->where($whereCond);
        }

        return $this;
    }

    /**
     * Join Product Price Table
     *
     * @return $this
     */
    protected function _productLimitationJoinPrice()
    {
        return $this->_productLimitationPrice();
    }

    /**
     * Join Product Price Table with left-join possibility
     *
     * @see \Magento\Catalog\Model\ResourceModel\Product\Collection::_productLimitationJoinPrice()
     * @param bool $joinLeft
     * @return $this
     */
    protected function _productLimitationPrice($joinLeft = false)
    {
        $filters = $this->_productLimitationFilters;
        if (!$filters->isUsingPriceIndex()) {
            return $this;
        }

        $connection = $this->getConnection();
        $select = $this->getSelect();
        $joinCond = join(
            ' AND ',
            [
                'price_index.entity_id = e.entity_id',
                $connection->quoteInto('price_index.website_id = ?', $filters['website_id']),
                $connection->quoteInto('price_index.customer_group_id = ?', $filters['customer_group_id'])
            ]
        );

        $fromPart = $select->getPart(\Magento\Framework\DB\Select::FROM);
        if (!isset($fromPart['price_index'])) {
            $least = $connection->getLeastSql(['price_index.min_price', 'price_index.tier_price']);
            $minimalExpr = $connection->getCheckSql(
                'price_index.tier_price IS NOT NULL',
                $least,
                'price_index.min_price'
            );
            $colls = [
                'price',
                'tax_class_id',
                'final_price',
                'minimal_price' => $minimalExpr,
                'min_price',
                'max_price',
                'tier_price',
            ];
            $tableName = ['price_index' => $this->getTable('catalog_product_index_price')];
            if ($joinLeft) {
                $select->joinLeft($tableName, $joinCond, $colls);
            } else {
                $select->join($tableName, $joinCond, $colls);
            }
            // Set additional field filters
            foreach ($this->_priceDataFieldFilters as $filterData) {
                $select->where(call_user_func_array('sprintf', $filterData));
            }
        } else {
            $fromPart['price_index']['joinCondition'] = $joinCond;
            $select->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);
        }
        //Clean duplicated fields
        $this->_resourceHelper->prepareColumnsList($select);

        return $this;
    }
	
	protected function _applyProductLimitations()
    {
        $this->_prepareProductLimitationFilters();
        $this->_productLimitationJoinWebsite();
        $this->_productLimitationJoinPrice();
        $filters = $this->_productLimitationFilters;

        if (!isset($filters['category_id']) && !isset($filters['visibility'])) {
            return $this;
        }

        $conditions = [
            'cat_index.product_id=e.entity_id',
            $this->getConnection()->quoteInto('cat_index.store_id=?', $filters['store_id']),
        ];
        if (isset($filters['visibility']) && !isset($filters['store_table'])) {
            $conditions[] = $this->getConnection()->quoteInto('cat_index.visibility IN(?)', $filters['visibility']);
        }
        $conditions[] = $this->getConnection()->quoteInto('cat_index.category_id=?', $filters['category_id']);
        if (isset($filters['category_is_anchor'])) {
            $conditions[] = $this->getConnection()->quoteInto('cat_index.is_parent=?', $filters['category_is_anchor']);
        }

        $joinCond = join(' AND ', $conditions);
        $fromPart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::FROM);
        if (isset($fromPart['cat_index'])) {
            $fromPart['cat_index']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);
        } else {
            $this->getSelect()->join(
                ['cat_index' => $this->getTable('catalog_category_product_index')],
                $joinCond,
                ['cat_index_position' => 'position']
            );
        }

        $this->_productLimitationJoinStore();
        $this->_eventManager->dispatch(
            'catalog_product_collection_apply_limitations_after',
            ['collection' => $this]
        );
        return $this;
    }

    /**
     * Apply limitation filters to collection base on API
     * Method allows using one time category product table
     * for combinations of category_id filter states
     *
     * @return $this
     */
    protected function _applyZeroStoreProductLimitations()
    {
        $filters = $this->_productLimitationFilters;

        $conditions = [
            'cat_pro.product_id=e.entity_id',
            $this->getConnection()->quoteInto(
                'cat_pro.category_id=?',
                $filters['category_id']
            ),
        ];
        $joinCond = join(' AND ', $conditions);

        $fromPart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::FROM);
        if (isset($fromPart['cat_pro'])) {
            $fromPart['cat_pro']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);
        } else {
            $this->getSelect()->join(
                ['cat_pro' => $this->getTable('catalog_category_product')],
                $joinCond,
                ['cat_index_position' => 'position']
            );
        }
        $this->_joinFields['position'] = ['table' => 'cat_pro', 'field' => 'position'];

        return $this;
    }
	
	private function createLimitationFilters()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitation');
    }
	
	
}