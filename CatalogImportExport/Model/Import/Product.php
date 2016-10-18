<?php
namespace Tagalys\CatalogImportExport\Model\Import;

use Tagalys\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;

class Product extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{

    const SKU = 'sku';
    const COL_STORE_VIEW_CODE = 'store_view_code';
    const COL_ATTR_SET_CODE = 'attribute_set_code';
    const COL_PRODUCT_TYPE = 'product_type';
    const COL_CATEGORIES = 'categories';
	const COL_PRODUCT_WEBSITES='product_websites';
	const COL_NAME	='name';
	const COL_DESCRIPTION='description';
	const COL_SHORT_DESCRIPTION	='short_description';
	const COL_WEIGHT='weight';
	const COL_PRODUCT_ONLINE='product_online';
	const COL_TAX_CLASS_NAME='tax_class_name';
	const COL_VISIBILITY='visibility';
	const COL_PRICE	='price';
	const COL_SPECIAL_PRICE	='special_price';
	const COL_SPECIAL_PRICE_FROM_DATE=	'special_price_from_date';
	const COL_SPECIAL_PRICE_TO_DATE	='special_price_to_date';
	const COL_URL_KEY=	'url_key';
	const COL_META_TITLE='meta_title';
	const COL_META_KEYWORDS='meta_keywords';
	const COL_META_DESCRIPTION=	'meta_description';
	const COL_BASE_IMAGE='base_image';
	const COL_BASE_IMAGE_LABEL=	'base_image_label';
	const COL_SMALL_IMAGE='small_image';
	const COL_SMALL_IMAGE_LABEL	='small_image_label';
	const COL_THUMBNAIL_IMAGE	='thumbnail_image';
	const COL_THUMBNAIL_IMAGE_LABEL=	'thumbnail_image_label';
	const COL_SWATCH_IMAGE	='swatch_image';
	const COL_SWATCH_IMAGE_LABEL=	'swatch_image_label';
	const COL_CREATED_AT='created_at';
	const COL_UPDATED_AT='updated_at';
	const COL_NEW_FROM_DATE	='new_from_date';
	const COL_NEW_TO_DATE='new_to_date';
	const COL_DISPLAY_PRODUCT_OPTIONS_IN=	'display_product_options_in';
	const COL_MAP_PRICE	='map_price';
	const COL_MSRP_PRICE='msrp_price';
	const COL_MAP_ENABLED='map_enabled';
	const COL_GIFT_MESSAGE_AVAILABLE=	'gift_message_available';
	const COL_CUSTOM_DESIGN	='custom_design';
	const COL_CUSTOM_DESIGN_FROM='custom_design_from';
	const COL_CUSTOM_DESIGN_TO	='custom_design_to';
	const COL_CUSTOM_LAYOUT_UPDATE	='custom_layout_update';
	const COL_PAGE_LAYOUT='page_layout';
	const COL_PRODUCT_OPTIONS_CONTAINER	='product_options_container';
	const COL_MSRP_DISPLAY_ACTUAL_PRICE_TYPE='msrp_display_actual_price_type';
	const COL_COUNTRY_OF_MANUFACTURE='country_of_manufacture';
	const COL_ADDITIONAL_ATTRIBUTES	='additional_attributes';
	const COL_QTY='qty';
	const COL_OUT_OF_STOCK_QTY	='out_of_stock_qty';
	const COL_USE_CONFIG_MIN_QTY='use_config_min_qty';
	const COL_IS_QTY_DECIMAL='is_qty_decimal';
	const COL_ALLOW_BACKORDERS=	'allow_backorders';
	const COL_USE_CONFIG_BACKORDERS	='use_config_backorders';
	const COL_MIN_CART_QTY='min_cart_qty';
	const COL_USE_CONFIG_MIN_SALE_QTY='use_config_min_sale_qty';
	const COL_MAX_CART_QTY='max_cart_qty';
	const COL_USE_CONFIG_MAX_SALE_QTY='use_config_max_sale_qty';
	const COL_IS_IN_STOCK='is_in_stock';
	const COL_NOTIFY_ON_STOCK_BELOW	='notify_on_stock_below';
	const COL_USE_CONFIG_NOTIFY_STOCK_QTY	='use_config_notify_stock_qty';
	const COL_MANAGE_STOCK='manage_stock';
	const COL_USE_CONFIG_MANAGE_STOCK='use_config_manage_stock';
	const COL_USE_CONFIG_QTY_INCREMENTS	='use_config_qty_increments';
	const COL_QTY_INCREMENTS='qty_increments';
	const COL_USE_CONFIG_ENABLE_QTY_INC	='use_config_enable_qty_inc';
	const COL_ENABLE_QTY_INCREMENTS='enable_qty_increments';
	const COL_IS_DECIMAL_DIVIDED='is_decimal_divided';
	const COL_WEBSITE_ID='website_id';
	const COL_RELATED_SKUS='related_skus';
	const COL_RELATED_POSITION=	'related_position';
	const COL_CROSSSELL_SKUS='crosssell_skus';
	const COL_CROSSSELL_POSITION='crosssell_position';
	const COL_UPSELL_SKUS='upsell_skus';
	const COL_UPSELL_POSITION='upsell_position';
	const COL_ADDITIONAL_IMAGES	='additional_images';
	const COL_ADDITIONAL_IMAGE_LABELS=	'additional_image_labels';
	const COL_HIDE_FROM_PRODUCT_PAGE=	'hide_from_product_page';
	const COL_BUNDLE_PRICE_TYPE	='bundle_price_type';
	const COL_BUNDLE_SKU_TYPE='bundle_sku_type';
	const COL_BUNDLE_PRICE_VIEW	='bundle_price_view';
	const COL_BUNDLE_WEIGHT_TYPE='bundle_weight_type';
	const COL_BUNDLE_VALUES	='bundle_values';
	const COL_BUNDLE_SHIPMENT_TYPE	='bundle_shipment_type';
	const COL_ASSOCIATED_SKUS='associated_skus'; 


    const TABLE_Entity = 'tgs_queue';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_PRODUCTID_IS_EMPTY => 'TITLE is empty',
    ];

     protected $_permanentAttributes = [self::SKU];
    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;
    protected $groupFactory;
    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
	self::SKU,
	self::COL_STORE_VIEW_CODE,
	self::COL_ATTR_SET_CODE,
	self::COL_PRODUCT_TYPE,
	self::COL_CATEGORIES,
	self::COL_PRODUCT_WEBSITES,
	self::COL_NAME,
	self::COL_DESCRIPTION,
	self::COL_SHORT_DESCRIPTION,
	self::COL_PRODUCT_ONLINE,
	self::COL_TAX_CLASS_NAME,
	self::COL_WEIGHT,
	self::COL_VISIBILITY,
	self::COL_PRICE	,
	self::COL_SPECIAL_PRICE	,
	 self::COL_SPECIAL_PRICE_FROM_DATE,
	self::COL_SPECIAL_PRICE_TO_DATE,
	self::COL_URL_KEY,
	self::COL_META_TITLE,
	self::COL_META_KEYWORDS,
	self::COL_META_DESCRIPTION,
	self::COL_BASE_IMAGE,
	self::COL_BASE_IMAGE_LABEL,
	self::COL_SMALL_IMAGE,
	self::COL_SMALL_IMAGE_LABEL,
	self::COL_THUMBNAIL_IMAGE,
	self::COL_THUMBNAIL_IMAGE_LABEL,
	self::COL_SWATCH_IMAGE,
	self::COL_SWATCH_IMAGE_LABEL,
	self::COL_CREATED_AT,
	self::COL_UPDATED_AT,
	self::COL_NEW_FROM_DATE,
	self::COL_NEW_TO_DATE,
	self::COL_DISPLAY_PRODUCT_OPTIONS_IN,
	self::COL_MAP_PRICE,
	self::COL_MSRP_PRICE,
	self::COL_MAP_ENABLED,
	self::COL_GIFT_MESSAGE_AVAILABLE,
	self::COL_CUSTOM_DESIGN	,
	self::COL_CUSTOM_DESIGN_FROM,
	self::COL_CUSTOM_DESIGN_TO,
	self::COL_CUSTOM_LAYOUT_UPDATE,
	self::COL_PAGE_LAYOUT,
	self::COL_PRODUCT_OPTIONS_CONTAINER	,
	self::COL_MSRP_DISPLAY_ACTUAL_PRICE_TYPE,
	self::COL_COUNTRY_OF_MANUFACTURE,
	self::COL_ADDITIONAL_ATTRIBUTES,
	self::COL_QTY,
	self::COL_OUT_OF_STOCK_QTY,
	self::COL_USE_CONFIG_MIN_QTY,
	self::COL_IS_QTY_DECIMAL,
	self::COL_ALLOW_BACKORDERS,
	self::COL_USE_CONFIG_BACKORDERS	,
	self::COL_MIN_CART_QTY,
	self::COL_USE_CONFIG_MIN_SALE_QTY,
	self::COL_MAX_CART_QTY,
	self::COL_USE_CONFIG_MAX_SALE_QTY,
	self::COL_IS_IN_STOCK,
	self::COL_NOTIFY_ON_STOCK_BELOW	,
	self::COL_USE_CONFIG_NOTIFY_STOCK_QTY,
	self::COL_MANAGE_STOCK,
	self::COL_USE_CONFIG_MANAGE_STOCK,
	self::COL_USE_CONFIG_QTY_INCREMENTS,
	self::COL_QTY_INCREMENTS,
	self::COL_USE_CONFIG_ENABLE_QTY_INC	,
	self::COL_ENABLE_QTY_INCREMENTS,
	self::COL_IS_DECIMAL_DIVIDED,
	self::COL_WEBSITE_ID,
	self::COL_RELATED_SKUS,
	self::COL_RELATED_POSITION,
	self::COL_CROSSSELL_SKUS,
	self::COL_CROSSSELL_POSITION,
	self::COL_UPSELL_SKUS,
	self::COL_UPSELL_POSITION,
	self::COL_ADDITIONAL_IMAGES,
	self::COL_ADDITIONAL_IMAGE_LABELS,
	self::COL_HIDE_FROM_PRODUCT_PAGE,
	self::COL_BUNDLE_PRICE_TYPE,
	self::COL_BUNDLE_SKU_TYPE,
	self::COL_BUNDLE_PRICE_VIEW	,
	self::COL_BUNDLE_WEIGHT_TYPE,
	self::COL_BUNDLE_VALUES,
	self::COL_BUNDLE_SHIPMENT_TYPE,
	self::COL_ASSOCIATED_SKUS, 
    ];

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    protected $_validators = [];


    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_connection;
    protected $_resource;
	protected $logger;

    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Customer\Model\GroupFactory $groupFactory,
		\Psr\Log\LoggerInterface $logger
		//\Magento\CatalogImportExport\Model\Import\Product\SkuProcessor $skuProcessor,  
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->groupFactory = $groupFactory;
		 $this->logger = $logger;
		 //$this->skuProcessor = $skuProcessor;
    }
    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'my_import';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
		
	    $title = false;

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;
        // BEHAVIOR_DELETE use specific validation logic
       // if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (!isset($rowData[self::SKU]) || empty($rowData[self::SKU]) ) {
                $this->addRowError(ValidatorInterface::ERROR_PRODUCTID_IS_EMPTY, $rowNum);
                return false;
            }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }


    /**
     * Create Advanced price data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }

        return true;
    }
    /**
     * Save newsletter subscriber
     *
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }
    /**
     * Replace newsletter subscriber
     *
     * @return $this
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }
	
	/*  public function getNewSku($sku = null)
    {
        return $this->skuProcessor->getNewSku($sku);
    } */
   
 /**
     * Save and replace newsletter subscriber
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceEntity()
    {
		 
		//$data = new \Magento\Framework\DataObject();		
		$behavior = $this->getBehavior();  
        $listTitle = []; 		//print_r($data->debug($listTitle));
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_PRODUCTID_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
				
                $rowTtile= $rowData[self::SKU];
				$listTitle[] = $rowTtile;
                $entityList[$rowTtile][] = [
                  self::SKU => $rowData[self::SKU],
                  //self::COL_STORE_VIEW_CODE => $rowData[self::COL_STORE_VIEW_CODE],
                  //self::COL_ATTR_SET_CODE => $rowData[self::COL_ATTR_SET_CODE],
                  //self::COL_PRODUCT_TYPE => $rowData[self::COL_PRODUCT_TYPE],
                  //self::COL_CATEGORIES => $rowData[self::COL_CATEGORIES],
                  //self::COL_ENTITY_ID => $rowData[self::COL_ENTITY_ID],
				  //'entity_id'= $this->skuProcessor->getNewSku($rowSku)['entity_id'],
                ];
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listTitle) {
                    if ($this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity)) {
                        $this->saveEntityFinish($entityList, self::TABLE_Entity);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, self::TABLE_Entity);
            }
        }
		
        return $this;
    }
    /**
     * Save product prices.
     *
     * @param array $priceData
     * @param string $table
     * @return $this
     */
    protected function saveEntityFinish(array $entityData, $table)
    {
		if ($entityData) {
            $tableName = $this->_connection->getTableName($table);
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                    foreach ($entityRows as $row) {
                        $entityIn[] = $row;
                    }
            }
				
            if ($entityIn) {
                $this->_connection->insertOnDuplicate($tableName, $entityIn,[
				self::SKU,
				//self::COL_STORE_VIEW_CODE,
				//self::COL_ATTR_SET_CODE,
				//self::COL_PRODUCT_TYPE,
				//self::COL_CATEGORIES
				//$this->_connection->lastInsertId(),
				]);
                //$this->_connection->insertOnDuplicate($tableName, $entityIn,[self::SKU,]);
            }
        }
        return $this; 
		
    }
    
}
