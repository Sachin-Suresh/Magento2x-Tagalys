<?php
namespace Tagalys\CatalogSearch\Controller\Result;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Catalog\Model\Layer\Resolver;


class Index extends \Magento\CatalogSearch\Controller\Result\Index
{
	protected $tglssearchHelper;
	protected $resultPageFactory;
	private $layerResolver;
	private $_queryFactory;
	protected $_storeManager;

	 public function __construct(
        Context $context,
		Session $catalogSession,
		StoreManagerInterface $storeManager,
		QueryFactory $queryFactory,
		Resolver $layerResolver,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		 //parent::__construct($context,$catalogSession,$storeManager,$resultPageFactory,$queryFactory,$layerResolver);
		 parent::__construct($context, $catalogSession, $storeManager, $queryFactory,$layerResolver,$resultPageFactory);
         $this->resultPageFactory = $resultPageFactory;
		 $this->layerResolver = $layerResolver;
		 $this->_queryFactory = $queryFactory;
		 $this->_storeManager = $storeManager;
    }

    public function execute()
    {
		//echo 'tag exe';
		//Custom Change
        $params = $this->getRequest()->getParams();
        $response = array();
        //Custom Change End

		 $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);

        $query = $this->_queryFactory->get();

        $query->setStoreId($this->_storeManager->getStore()->getId());

        if ($query->getQueryText() != '') {
            if ($this->_objectManager->get('Magento\CatalogSearch\Helper\Data')->isMinQueryLength()) {
                $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            } else {
                $query->saveIncrementalPopularity();

                if ($query->getRedirect()) {
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                }
            }

            $this->_objectManager->get('Magento\CatalogSearch\Helper\Data')->checkNotes();

            $this->_view->loadLayout();
			//Custom change

            if(false && $this->getRequest()->isXmlHttpRequest())
			{  echo 'if custrom';
				//Check if it was an AJAX request
                $viewpanel = $this->getLayout()->getBlock('catalogsearch.leftnav')->toHtml(); //Get the new Layered Manu
                $productlist = $this->getLayout()->getBlock('search_result_list')->toHtml(); //New product List
                $response['status'] = 'SUCCESS'; //Send Success
                $response['viewpanel']=$viewpanel;
                $response['productlist'] = $productlist;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                return;
            }
			//End custom search
            $this->_view->renderLayout();
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }

		$resultPage = $this->resultPageFactory->create();
		//$resultPage->getLayout()->initMessages();
		//$resultPage->getLayout()->getBlock('product_list_toolbar');
		return $resultPage;
	}
}
