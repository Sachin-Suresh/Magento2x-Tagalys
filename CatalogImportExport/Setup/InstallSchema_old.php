<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
*/
namespace Tagalys\CatalogImportExport\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
	 /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
	/* The class must extend \Magento\Framework\Setup\InstallSchemaInterface
	   The class must have install() method with 2 arguments SchemaSetupInterface and ModuleContextInterface
		The SchemaSetupInterface(similar to-Mage_Core_Model_Resource_Setup) is the setup object which provide many function to interact with database server.
		The ModuleContextInterface has only 1 method getVersion() which will return the current version of your module.*/
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
        $installer->startSetup();
		 /**
         * Create new table 'tgs_queue'
         */
		$table = $installer->getConnection()
            ->newTable($installer->getTable('temp_tgs'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
			->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Product ID'
            )
			->setComment('Temp test Table');
		$installer->getConnection()->createTable($table);
		//Create another table 2,3,... like above.
		$installer->endSetup();
	}
}
 