<?php
namespace Tagalys\Catalog\Controller\Category;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\Design;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Session;
use Magento\Catalog\Helper\Output;

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

	protected function _initCategory()
    {
		//Custom Change
        $params = $this->getRequest()->getParams();
        $response = array();
        //Custom Change End

        $categoryId = (int)$this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            return false;
        }

        try {
            $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
        if (!$this->_objectManager->get('Magento\Catalog\Helper\Category')->canShow($category)) {
            return false;
        }
        $this->_catalogSession->setLastVisitedCategoryId($category->getId());
        $this->_coreRegistry->register('current_category', $category);
        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after',
                ['category' => $category, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return false;
        }

        return $category;
    }

    /**
     * Category view action
     */
    //public function viewAction()
    public function execute()
    {
		echo 'tag cat exe';

		//Custom Change
        $params = $this->getRequest()->getParams();
        $response = array();
        //Custom Change End
		if ($this->_request->getParam(\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED)) {
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl());
        }
        $category = $this->_initCategory();
        if ($category) {
            $this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
            $settings = $this->_catalogDesign->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $this->_catalogDesign->applyCustomDesign($settings->getCustomDesign());
            }

            $this->_catalogSession->setLastViewedCategoryId($category->getId());

            $page = $this->resultPageFactory->create();
			 //Custom Change
           if(false && $this->getRequest()->isXmlHttpRequest()) {   //Check if it was an AJAX request
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
			else{ // //Custom End Change

				// apply custom layout (page) template once the blocks are generated
				if ($settings->getPageLayout()) {
					$page->getConfig()->setPageLayout($settings->getPageLayout());
				}

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

				// apply custom layout update once layout is loaded
				$layoutUpdates = $settings->getLayoutUpdates();
				if ($layoutUpdates && is_array($layoutUpdates)) {
					foreach ($layoutUpdates as $layoutUpdate) {
						$page->addUpdate($layoutUpdate);
						$page->addPageLayoutHandles(['layout_update' => md5($layoutUpdate)]);
					}
				}

				$page->getConfig()->addBodyClass('page-products')
					->addBodyClass('categorypath-' . $this->categoryUrlPathGenerator->getUrlPath($category))
					->addBodyClass('category-' . $category->getUrlKey());

				return $page;
			}
        } elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
	}
}
