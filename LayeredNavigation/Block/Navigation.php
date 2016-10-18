<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog layered navigation view block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Tagalys\LayeredNavigation\Block;

use Magento\Framework\View\Element\Template;

class Navigation extends \Magento\LayeredNavigation\Block\Navigation
{
	/**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Model\Layer\FilterList
     */
    protected $filterList;

    /**
     * @var \Magento\Catalog\Model\Layer\AvailabilityFlagInterface
     */
    protected $visibilityFlag;
	protected $tglssearchHelper;

    /**
     * @param Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Model\Layer\FilterList $filterList
     * @param \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag
     * @param array $data
     */
    public function __construct(
         \Magento\Framework\View\Element\Template\Context $context,
         \Magento\Catalog\Model\Layer\Resolver $layerResolver,
         \Magento\Catalog\Model\Layer\FilterList $filterList,
         \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
		\Tagalys\CatalogSearch\Helper\Data $tglssearchHelper,
        array $data = []
    ) {
        //$this->_catalogLayer = $layerResolver->get();
        //$this->filterList = $filterList;
        //$this->visibilityFlag = $visibilityFlag;
		$this->tglssearchHelper = $tglssearchHelper;
        parent::__construct($context,$layerResolver,$filterList,$visibilityFlag, $data);
    }

    
    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
	{
	  print_r('canShowBlock');
      $service =  $this->tglssearchHelper->isTagalysActive();

      if($service)
	  {
        return true;
      } else {
        parent::canShowBlock();
      }
    }
}
