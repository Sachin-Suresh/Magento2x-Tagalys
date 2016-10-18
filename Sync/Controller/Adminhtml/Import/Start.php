<?php
namespace Tagalys\Sync\Controller\Adminhtml\Import;

class Start extends Tagalys\Sync\Controller\Adminhtml\Import;
{

    /**
     * @var \Magento\ImportExport\Model\ImportFactory
     */
    protected $importExportImportFactory;

    public function __construct(
        \Magento\ImportExport\Model\ImportFactory $importExportImportFactory
    ) {
        $this->importExportImportFactory = $importExportImportFactory;
    }
    public function execute()
    {
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Tagalays_Sync-Validate.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("Queueing: start test");
		
		$data = $this->getRequest()->getPost();
        if ($data) {
            $this->loadLayout(false);

            /** @var $resultBlock Mage_ImportExport_Block_Adminhtml_Import_Frame_Result */
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');
            /** @var $importModel Mage_ImportExport_Model_Import */
            $importModel = $this->importExportImportFactory->create();

            try {
                $importModel->importSource();
                $importModel->invalidateIndex();
                $resultBlock->addAction('show', 'import_validation_container')
                    ->addAction('innerHTML', 'import_validation_container_header', $this->__('Status'));
            } catch (Exception $e) {
            	//[Custom Changes]
            	// Mage::log("CSV Import: Data import process error", null, "tagalys.log");
                $resultBlock->addError($e->getMessage());
                $this->renderLayout();
                return;
            }
            //[Custom Changes]
            // Mage::log("CSV Import: Data import process success", null, "tagalys.log");
            $resultBlock->addAction('hide', array('edit_form', 'upload_button', 'messages'))
                ->addSuccess($this->__('Import successfully done.'));
            $this->renderLayout();
        } else {
        	//[Custom Changes]
        	// Mage::log("CSV Import: Record not found", null, "tagalys.log");
            $this->_redirect('*/*/index');
        }
    }

}
