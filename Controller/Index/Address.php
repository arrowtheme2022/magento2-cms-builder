<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
class Address extends \Magento\Framework\App\Action\Action
{
	protected $_scopeConfig;
	
	protected $_storeManager;
	
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
		$this->_storeManager = $storeManager;
		$this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
	
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		$storeId = $this->_storeManager->getStore()->getId();
		
		$region = strtolower($this->_scopeConfig->getValue('general/country/default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));
		$apiKey = $this->_scopeConfig->getValue('fbuilder/social/google_api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		
		if (!$address=$this->getRequest()->getParam('q', false)) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }
		
		$address = str_replace(" ", "+", $address);		
 		
		$url =    "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region&key=$apiKey";		 		
		$ch = curl_init();		 		
		curl_setopt($ch, CURLOPT_URL, $url);		 		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		 		
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);		 		
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);		 		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);		 		
		$response = curl_exec($ch);				
		$response = json_decode($response, true);				
		$address_result = $response['results'];		

		$responseData = [];
		
		if (count($address_result) > 0) {
			foreach ($address_result as $_address) {    
				$responseData[] = ['name'=>$_address['formatted_address'], 'id'=>$_address['formatted_address']];
			}
		}
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
		return $resultJson;
    }
	
}
