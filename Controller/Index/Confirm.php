<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Index;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Cache\Manager as CacheManager;
class Confirm extends \Magento\Framework\App\Action\Action
{
	/**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    protected $_sectionFactory;
    protected $_childsFactory;
    protected $_confirmCollectionFactory;
    protected $_generateHelper;

	public function __construct(
		\Magento\Framework\App\Action\Context $context, 
		\Magento\Framework\View\Element\Context $urlContext,
		CustomerSession $customerSession,
		\MGS\Fbuilder\Helper\Generate $generateHelper,
		CacheManager $cacheManager
	){
		$this->_urlBuilder = $urlContext->getUrlBuilder();
		$this->customerSession = $customerSession;
		$this->cacheManager = $cacheManager;
		parent::__construct($context);
		
		$this->_generateHelper = $generateHelper;
	}
	
	public function urlDecode($url)
    {
        $url = base64_decode(strtr($url, '-_,', '+/='));
        return $this->_urlBuilder->sessionUrlVar($url);
    }
	
    public function execute()
    {
		if(($this->customerSession->getUseFrontendBuilder() == 1) && ($referer = $this->getRequest()->getParam('referrer')) && ($pageId = $this->getRequest()->getParam('page_id')) && ($storeId = $this->getRequest()->getParam('store_id')) && ($page = $this->getRequest()->getParam('page'))){
			$this->_generateHelper->importContent($pageId);
			$this->cacheManager->clean(['full_page']);
			$url = $this->urlDecode($referer);
		}else{
			$url = $this->_redirect->getRefererUrl();
		}
		
		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setUrl($url);
		return $resultRedirect;
    }
}
