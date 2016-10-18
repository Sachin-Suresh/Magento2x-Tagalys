<?php


namespace Tagalys\Tglssearch\Controller\Result;

use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Catalog\Model\Layer\Resolver;


class Index extends \Magento\Framework\App\Action\Action
{
	protected $tglssearchHelper;
	 public function __construct(
        Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
       
	) {
		 parent::__construct($context);
         $this->resultPageFactory = $resultPageFactory;
    }

    
    public function execute()
    {
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getLayout()->initMessages();
		$resultPage->getLayout()->getBlock('catalogsearch.leftnav');
		//$resultPage->getLayout()->getBlock('product_list_toolbar');
		
		return $resultPage;
	}
}
