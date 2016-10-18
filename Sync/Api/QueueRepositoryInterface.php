<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tagalys\Sync\Api;

use Tagalys\Sync\Api\Data\QueueInterface;

/**
 * Defines the service contract for some simple maths functions. The purpose is
 * to demonstrate the definition of a simple web service, not that these
 * functions are really useful in practice. The function prototypes were therefore
 * selected to demonstrate different parameter and return values, not as a good
 * calculator design.
 */
interface QueueRepositoryInterface
{
	/**
     * Return the product limit.
     *
     * @api
     * @param int $limit
     * @return
     */
	public function getProductUpdate($limit); 
	/**
     * Returns the product.
     *
     * @api
     * @param int $page
     * @param int $limit
     * @return
     */
	 public function getProduct($page,$limit);
	 /**
     * Returns the total no of products.
     *
     * @api
	 * @param
     * @return
     */
	 public function getProductTotal();
	 /**
     * Returns single product by SKU.
     *
     * @api
     * @param string $sku
     * @return JSON json_string
     */
	 public function getSingleProductBySku($sku);
	 /**
     * Returns product count.
     *
     * @api
     * @param
     * @return
     */
	 public function getProductUpdateCount();
	 
}
