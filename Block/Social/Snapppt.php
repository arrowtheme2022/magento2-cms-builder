<?php

namespace MGS\Fbuilder\Block\Social;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;


class Snapppt extends Template{
	
	
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
	
	public function getInstagramShopContent(){
		if($this->getStoreConfig('fbuilder/social/snapppt_script')!=''){
			return $this->getStoreConfig('fbuilder/social/snapppt_script');
		}
		return;
	}
}
?>