<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tagalys\CatalogImportExport\Model\Import\Product;

interface RowValidatorInterface extends \Magento\Framework\Validator\ValidatorInterface
{
       const ERROR_INVALID_ID= 'InvalidValueID';
       const ERROR_PRODUCTID_IS_EMPTY = 'EmptyID';
    /**
     * Initialize validator
     *
     * @return $this
     */
    public function init($context);
}
