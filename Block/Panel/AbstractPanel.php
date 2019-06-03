<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Panel;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Main contact form block
 */
abstract class AbstractPanel extends Template
{

    protected $_helper;
	
    protected $_sectionFactory;
    protected $_sectionCollectionFactory;
	
    protected $_section;
	
    protected $customerSession;
	
	protected $_attributeCollection;
	
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
	
	protected $_fullActionName;
	protected $_pageId;
	
	/**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;
	
	/**
     * Page factory
     *
     * @var \MGS\Fbuilder\Model\ChildFactory
     */
    protected $_childFactory;
	
    protected $_childCollectionFactory;
	
	
	public function __construct(
		Template\Context $context,
		\MGS\Fbuilder\Helper\Data $helper,
		CustomerSession $customerSession,
		\Magento\Cms\Model\PageFactory $pageFactory,
		\MGS\Fbuilder\Model\ChildFactory $childFactory,
		\MGS\Fbuilder\Model\SectionFactory $sectionFactory,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\MGS\Fbuilder\Model\ResourceModel\Child\CollectionFactory $childCollectionFactory,
		\MGS\Fbuilder\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory,
		\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollection,
		array $data = []
	){
        parent::__construct($context, $data);
		$this->_sectionFactory = $sectionFactory;
		$this->_sectionCollectionFactory = $sectionCollectionFactory;
		$this->customerSession = $customerSession;
		$this->_filterProvider = $filterProvider;
		$this->_attributeCollection = $attributeCollection;
		$this->_helper = $helper;
		$this->_pageFactory = $pageFactory;
		$this->_childFactory = $childFactory;
		$this->_childCollectionFactory = $childCollectionFactory;
		
		$this->_fullActionName = $this->getRequest()->getFullActionName();
		if($this->_fullActionName == 'cms_index_index'){
			$pageIdentifier = $this->_helper->getStoreConfig('web/default/cms_home_page',$this->_storeManager->getStore()->getId());
			$arrIdentifier = explode('|', $pageIdentifier);
			
			$page = $this->_pageFactory->create()->setStoreId($this->_storeManager->getStore()->getId())->load($arrIdentifier[0]);
			
			$this->_pageId = $page->getId();
		}else{
			$this->_pageId = $this->getRequest()->getParam('page_id');
		}
		
    }
	
	public function getModel($model){
		if($model == 'MGS\Fbuilder\Model\Child'){
			return $this->_childFactory->create();
		}
	}
	
	public function getPageId(){
		return $this->_pageId;
	}
	
	public function wasSave(){
		if($this->customerSession->getSaved()){
			return true;
		}
		return false;
	}
	
	public function unsetSaveSection(){
		$this->customerSession->setSaved(false);
		return ;
	}
	
	public function getHelper(){
		return $this->_helper;
	}
	
	public function getAvailableAttributes(){
		$attrs = [];
		
		$attributes = $this->_attributeCollection->create()->addVisibleFilter()
			->addFieldToFilter('backend_type', 'int')
			->addFieldToFilter('frontend_input', 'boolean');
		
		if(count($attributes)>0){
			foreach ($attributes as $productAttr) { 
				$attrs[$productAttr->getAttributeCode()] = $productAttr->getFrontendLabel();
			}
		}
		
        return $attrs;
	}
	
	public function checkCopiedBlock(){
		if($this->customerSession->getBlockCopied()){
			return $this->customerSession->getBlockCopied();
		}
		return false;
	}
}

