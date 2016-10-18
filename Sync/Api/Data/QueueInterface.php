<?php
/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tagalys\Sync\Api\Data;
/**
 * @api
 */
interface QueueInterface
{
	/**#@+
     * Constants for keys of data array. 
     */
	const PRODUCT_ID                  = 'product_id';
	/**#@-*/
	
	/**
     * Return Prodcut By Limit
     * @param $page int Offset
     * @param $limit int Limit
     */	
    public function getProduct($page,$limit);     //DB table name 
	 /**
     * Get PRODUCT ID
     *
     * @return int|null
     */	
    public function getProductUpdate($limit);    //primary key -id
	
	 /**
     * Set Product ID
     *
     * @param int $productId
     * @return Tagalys\Sync\Api\Data\QueueInterface
     */
	 //public function setProductId($productId);
	 
	 /**
	 Get PRODUCT Total
	 @return int|null
	 **/
	 public function getProductTotal();
	 /**
	 Get PRODUCT Sku
	 @return int|null
	 **/
	 public function getSingleProductBySku($sku);
	 /**
	 Get PRODUCT Update Count
	 @return int|null
	 **/
	 public function getProductUpdateCount();
}