<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Page;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Main contact form block
 */
class Head extends Template
{	
	protected $_scopeConfig;
	protected $_storeManager;
	public function __construct(
        Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {       
		$this->_scopeConfig = $scopeConfig;
		$this->_storeManager = $context->getStoreManager();
        parent::__construct($context);
    }
	
	public function getStoreConfig($node, $storeId = NULL){
		if($storeId != NULL){
			return $this->_scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		}
		return $this->_scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
	}
	
	protected function _prepareLayout(){
		
		if($this->getStoreConfig('fbuilder/font_style/fontawesome')){
			$this->pageConfig->addPageAsset('MGS_Fbuilder::css/fontawesome.v4.7.0/fontawesome.css');
		}
		$this->pageConfig->addPageAsset('MGS_Fbuilder::css/owl.carousel.css');
		$this->pageConfig->addPageAsset('MGS_Fbuilder::css/owl.theme.min.css');
		$this->pageConfig->addPageAsset('MGS_Fbuilder::css/animate.css');
		$this->pageConfig->addPageAsset('MGS_Fbuilder::css/magnific-popup.css');
		$this->pageConfig->addPageAsset('MGS_Fbuilder::css/pbanner.css');
		$this->pageConfig->addPageAsset('MGS_Fbuilder::css/styles.css');
		
		$this->pageConfig->addPageAsset('MGS_Fbuilder::js/timer.js');
		return parent::_prepareLayout();
	}
}

