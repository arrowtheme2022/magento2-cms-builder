<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Cache\Manager as CacheManager;
class Removesection extends \Magento\Framework\App\Action\Action
{
	protected $_storeManager;
	
	/**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		CustomerSession $customerSession,
		\Magento\Framework\View\Element\Context $urlContext,
		CacheManager $cacheManager
	)     
	{
		$this->_storeManager = $storeManager;
		$this->customerSession = $customerSession;
		$this->_urlBuilder = $urlContext->getUrlBuilder();
		$this->cacheManager = $cacheManager;
		parent::__construct($context);
	}
	
	public function getModel($model){
		return $this->_objectManager->create($model);
	}
	
    public function execute()
    {
		if($this->getRequest()->isAjax() && ($this->customerSession->getUseFrontendBuilder() == 1)){
			$result['block_copied'] = 0;
			if($id = $this->getRequest()->getParam('id')){
				$result['result'] = $id;
				$section = $this->getModel('MGS\Fbuilder\Model\Section')->load($id);
				if($section->getId()){
					$childs = $this->getModel('MGS\Fbuilder\Model\Child')
						->getCollection()
						->addFieldToFilter('block_name', ['like'=>$section->getName().'-%'])
						->addFieldToFilter('page_id', $section->getPageId());
					try{
						if(count($childs)>0){
							foreach($childs as $_child){
								if($this->customerSession->getBlockCopied()==$_child->getId()){
									$this->customerSession->setBlockCopied(false);
									$result['block_copied'] = 1;
								}
								$_child->delete();
							}
						}
						
						$section->delete();
						$this->cacheManager->clean(['full_page']);
					}catch (\Exception $e) {
						$result['result'] = $e->getMessage();
					}
				}else{
					$result['result'] = __('Can not find section to delete.');
				}
			}
			return $this->getResponse()->setBody(json_encode($result));
		}else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			return $resultRedirect;
		}
		
    }
}
