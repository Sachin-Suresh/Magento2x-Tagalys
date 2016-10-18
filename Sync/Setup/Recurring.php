<?php
 
namespace Tagalys\Sync\Setup;
 
//use Magento\Framework\EntityManager\MetadataPool;
//use Magento\Framework\Setup\ExternalFKSetup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Tagalys\Sync\Api\Data\ProductInterface;

class Recurring implements InstallSchemaInterface
{
	protected $logger;
    
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface
    ) {
        $this->logger = $loggerInterface;
    }
    
    public function install( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $this->logger->debug('Tagalys Sync install schema running!');
    }
}