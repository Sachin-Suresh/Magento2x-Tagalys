<?php
//require_once(Mage::getModuleDir('controllers','Mage_Catalog').DS.'CategoryController.php');

namespace Tagalys\Tglssearch\Controller\Category;
//use Magento\Catalog\Controller\Category\View;   //core CategoryController
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\Design;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Session;



class View extends \Magento\Catalog\Controller\Category\View
{
	protected $_coreRegistry = null;
	protected $_catalogSession;
	protected $layerResolver;
	protected $_catalogDesign;
	protected $_storeManager;
	protected $categoryUrlPathGenerator;
	protected $resultPageFactory;
	protected $resultForwardFactory;
	protected $categoryRepository;


	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManager  $storeManager,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository
    ) {
		$this->_storeManager = $storeManager;
        $this->_catalogDesign = $catalogDesign;
        $this->_catalogSession = $catalogSession;
        $this->_coreRegistry = $coreRegistry;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layerResolver = $layerResolver;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context,$catalogDesign,$catalogSession,$coreRegistry,$storeManager,$categoryUrlPathGenerator,$resultPageFactory,$resultForwardFactory,$layerResolver,$categoryRepository);

    }

    /**
     * Category view action
     */
    //public function viewAction()
    public function execute()
    {
		//print_r('Category action');die;
        
        $params = $this->getRequest()->getParams();
        $response = array();
        $category = $this->_initCategory();
        
        if ($category)
		{
            
			$this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
            $settings = $this->_catalogDesign->getDesignSettings($category);

           
            if ($settings->getCustomDesign()) {
                $this->_catalogDesign->applyCustomDesign($settings->getCustomDesign());
            }
            
			$this->_catalogSession->setLastViewedCategoryId($category->getId());
			$page = $this->resultPageFactory->create();
			$hasChildren = $category->hasChildren();
            if ($category->getIsAnchor()) {
                $type = $hasChildren ? 'layered' : 'layered_without_children';
            } else {
                $type = $hasChildren ? 'default' : 'default_without_children';
            }
		    if (!$hasChildren) {
                // Two levels removed from parent.  Need to add default page type.
                $parentType = strtok($type, '_');
                $page->addPageLayoutHandles(['type' => $parentType]);
            }
			$page->addPageLayoutHandles(['type' => $type, 'id' => $category->getId()]);

           
			$layoutUpdates = $settings->getLayoutUpdates();
            if ($layoutUpdates && is_array($layoutUpdates)) {
                foreach ($layoutUpdates as $layoutUpdate) {
                    $page->addUpdate($layoutUpdate);
                    $page->addPageLayoutHandles(['layout_update' => md5($layoutUpdate)]);
                }
            }
			
			//calling toolbar phtml block
			$page->getLayout()->initMessages();
			$page->getLayout()->getBlock('product_list_toolbar');
            
           if(false && $this->getRequest()->isXmlHttpRequest())
		   {   //Check if it was an AJAX request
				echo 'Category ajax';die;
                // Generate New Layered Navigation Menu
                $viewPanel = $this->getLayout()->getBlock('catalog.leftnav')->toHtml();
                // Generate product list
                $productList = $this->getLayout()->getBlock('product_list')->toHtml();
                $response['status'] = 'SUCCESS';
                $response['viewpanel']=$viewPanel;
                $response['productlist'] = $productList;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                return;
            }
			else
			{ 
                if ($settings->getPageLayout()) {
                   $page->getConfig()->setPageLayout($settings->getPageLayout());
                }

             	$page->getConfig()->addBodyClass('page-products')
									->addBodyClass('categorypath-' . $this->categoryUrlPathGenerator->getUrlPath($category))
									->addBodyClass('category-' . $category->getUrlKey());
				return $page;

               
				$this->_view->renderLayout();
            }
        } elseif (!$this->getResponse()->isRedirect()) {
            
			return $this->resultForwardFactory->create()->forward('noroute');
        }
		return $page;
    }
}
