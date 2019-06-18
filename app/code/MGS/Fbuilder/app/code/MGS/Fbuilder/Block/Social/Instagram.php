<?php

namespace MGS\Fbuilder\Block\Social;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;


class Instagram extends Template{
	
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
	
	public function getWidgetInstagramData() {
		$result = [];
		$instagramToken = $this->getStoreConfig('fbuilder/social/instagram_access_token');
		if($instagramToken!=''){
			$host = "https://api.instagram.com/v1/users/self/media/recent/?access_token=".$instagramToken;
			if($this->_iscurl()) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $host);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				$content = curl_exec($ch);
				curl_close($ch);
			}
			else {
				$content = file_get_contents($host);
			}

			$content = json_decode($content, true);
			
			$i=0;
			$limit = $this->getLimit();
			if(isset($content['data']) && count($content['data']) > 0){
				foreach($content['data'] as $data){
					$i++;
					$result[] = [
						'src' => $data['images'][$this->getResolution()]['url'],
						'link' => $data['link'],
						'like' => $data['likes']['count'],
						'comment' => $data['comments']['count']
					];
					if(($limit!='') && ($limit!=0) && ($i==$limit)){
						break;
					}
				}
			}
		}
		
		return $result;
	}
	
	public function _iscurl(){
		if(function_exists('curl_version')) {
			return true;
		} else {
			return false;
		}
	}
}
?>