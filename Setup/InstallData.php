<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Fbuilder\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;


class InstallData implements InstallDataInterface
{
	/**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;
    
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
	
	private $eavSetupFactory;
	
	/**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory, CustomerSetupFactory $customerSetupFactory, AttributeSetFactory $attributeSetFactory, CategorySetupFactory $categorySetupFactory)
    {
		$this->eavSetupFactory = $eavSetupFactory;
		$this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
		$this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
		/* Create customer attribute is_fbuilder_account for front-end builder*/
		/** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        
        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        
        $customerSetup->addAttribute(Customer::ENTITY, 'is_fbuilder_account', [
            'type' => 'int',
            'label' => 'CMS Page Builder Account',
            'input' => 'select',
            'required' => false,
            'visible' => true,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'user_defined' => false,
			'is_user_defined' => false,
            'sort_order' => 1000,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'position' => 1000,
            'default' => 0,
            'system' => 0,
        ]);
        
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'is_fbuilder_account')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer'],
        ]);
        
        $attribute->save();
		
		/* Create thumbnail image attribute for category */
		$categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
		$categoryEntityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $categoryAttributeSetId = $categorySetup->getDefaultAttributeSetId($categoryEntityTypeId);
		
		$categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
			'fbuilder_thumbnail',
			[
				'type' => 'varchar',
				'label' => 'Thumbnail Image',
				'input' => 'image',
				'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
				'required' => false,
				'sort_order' => 45,
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group' => 'Content',
			]
        );
		
		$categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
			'fbuilder_icon',
			[
				'type' => 'varchar',
				'label' => 'Icon (Image)',
				'input' => 'image',
				'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
				'required' => false,
				'sort_order' => 46,
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group' => 'Content',
			]
        );
		
		$categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
			'fbuilder_font_class',
			[
				'type' => 'varchar',
				'label' => 'Icon (Font class)',
				'input' => 'text',
				'required' => false,
				'sort_order' => 47,
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group' => 'Content'
			]
        );
    }
}