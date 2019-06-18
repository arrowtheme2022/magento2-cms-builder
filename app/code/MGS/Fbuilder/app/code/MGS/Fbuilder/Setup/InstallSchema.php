<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Fbuilder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'mgs_fbuilder_section'
         */
        $mgs_fbuilder_section = $installer->getConnection()->newTable(
            $installer->getTable('mgs_fbuilder_section')
        )->addColumn(
            'block_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Block Id'
        )->addColumn(
            'page_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Page ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Block Name'
        )->addColumn(
            'theme_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Theme Name'
        )->addColumn(
            'block_cols',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Block Columns'
        )->addColumn(
            'tablet_cols',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Table columns'
        )->addColumn(
            'mobile_cols',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Mobile columns'
        )->addColumn(
            'block_class',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Block Class'
        )->addColumn(
            'class',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Class'
        )->addColumn(
            'background',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Section Background Color'
        )->addColumn(
            'background_image',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Section Background Image'
        )->addColumn(
            'background_gradient',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Background Gradient'
        )->addColumn(
            'background_gradient_from',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Gradient from'
        )->addColumn(
            'background_gradient_to',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Gradient to'
        )->addColumn(
            'background_gradient_orientation',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Gradient Orientation'
        )->addColumn(
            'background_repeat',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Background Repeat'
        )->addColumn(
            'background_cover',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Background Cover'
        )->addColumn(
            'parallax',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Background Parallax'
        )->addColumn(
            'fullwidth',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Section Full Width'
        )->addColumn(
            'padding_top',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Padding Top'
        )->addColumn(
            'padding_bottom',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Padding Bottom'
        )->addColumn(
			'store_id', 
			Table::TYPE_SMALLINT, 
			null, 
			['unsigned' => true, 'nullable' => false, 'primary' => true], 
			'Store Id'
		)->addColumn(
            'block_position',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Block Position'
        )->addIndex(
			$setup->getIdxName('mgs_fbuilder_section', ['store_id']), ['store_id']
		)->addIndex(
			$setup->getIdxName('mgs_fbuilder_section', ['page_id']), ['page_id']
		)->addForeignKey(
			$setup->getFkName('mgs_fbuilder_section', 'store_id', 'store', 'store_id'), 'store_id', $setup->getTable('store'), 'store_id', Table::ACTION_CASCADE
		)->addForeignKey(
			$setup->getFkName('mgs_fbuilder_section', 'page_id', 'cms_page', 'page_id'), 'page_id', $setup->getTable('cms_page'), 'page_id', Table::ACTION_CASCADE
		);

        $installer->getConnection()->createTable($mgs_fbuilder_section);
		
		/**
         * Create table 'mgs_fbuilder_child'
         */
        $mgs_fbuilder_child = $installer->getConnection()->newTable(
            $installer->getTable('mgs_fbuilder_child')
        )->addColumn(
            'child_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'page_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Page ID'
        )->addColumn(
            'block_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Block Name'
        )->addColumn(
            'home_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Home Name'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Block Type'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Block Position'
        )->addColumn(
            'setting',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Block Setting'
        )->addColumn(
            'col',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Columns'
        )->addColumn(
            'class',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Block Class'
        )->addColumn(
            'background',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Section Background Color'
        )->addColumn(
            'background_image',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Block Background Image'
        )->addColumn(
            'background_gradient',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Background Gradient'
        )->addColumn(
            'background_gradient_from',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Gradient from'
        )->addColumn(
            'background_gradient_to',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Gradient to'
        )->addColumn(
            'background_gradient_orientation',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Gradient Orientation'
        )->addColumn(
            'background_repeat',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Background Repeat'
        )->addColumn(
            'background_cover',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Background Cover'
        )->addColumn(
			'store_id', 
			Table::TYPE_SMALLINT, 
			null, 
			['unsigned' => true, 'nullable' => false, 'primary' => true], 
			'Store Id'
		)->addColumn(
            'static_block_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Static Block Id'
        )->addColumn(
            'block_content',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Block Short Code'
        )->addColumn(
            'custom_style',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => true],
            'Custom Style'
        )->addIndex(
			$setup->getIdxName('mgs_fbuilder_child', ['store_id']), ['store_id']
		)->addIndex(
			$setup->getIdxName('mgs_fbuilder_child', ['page_id']), ['page_id']
		)->addForeignKey(
			$setup->getFkName('mgs_fbuilder_child', 'store_id', 'store', 'store_id'), 'store_id', $setup->getTable('store'), 'store_id', Table::ACTION_CASCADE
		)->addForeignKey(
			$setup->getFkName('mgs_fbuilder_child', 'page_id', 'cms_page', 'page_id'), 'page_id', $setup->getTable('cms_page'), 'page_id', Table::ACTION_CASCADE
		);

        $installer->getConnection()->createTable($mgs_fbuilder_child);
		
		
		/**
         * Create table 'mgs_fbuilder_confirm'
         */
        $mgs_fbuilder_confirm = $installer->getConnection()->newTable(
            $installer->getTable('mgs_fbuilder_confirm')
        )->addColumn(
            'confirm_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'page_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Page ID'
        )->addIndex(
			$setup->getIdxName('mgs_fbuilder_confirm', ['page_id']), ['page_id']
		)->addForeignKey(
			$setup->getFkName('mgs_fbuilder_confirm', 'page_id', 'cms_page', 'page_id'), 'page_id', $setup->getTable('cms_page'), 'page_id', Table::ACTION_CASCADE
		);

        $installer->getConnection()->createTable($mgs_fbuilder_confirm);
		
        $installer->endSetup();

    }
}
