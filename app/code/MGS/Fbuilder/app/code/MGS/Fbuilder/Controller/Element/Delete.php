<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Element;

use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Cache\Manager as CacheManager;
class Delete extends \Magento\Framework\App\Action\Action
{
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		CustomerSession $customerSession,
		CacheManager $cacheManager
	)     
	{
		$this->customerSession = $customerSession;
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
				$block = $this->getModel('MGS\Fbuilder\Model\Child')->load($id);
				$result['result'] = $id;
				if($block->getId()){
					try{
						if($this->customerSession->getBlockCopied()==$block->getId()){
							$this->customerSession->setBlockCopied(false);
							$result['block_copied'] = 1;
						}
						$block->delete();
						$this->cacheManager->clean(['full_page']);
						
					}catch (\Exception $e) {
						$result['result'] = $e->getMessage();
					}
				}else{
					$result['result'] = __('Can not delete this block !');
				}
			}else{
				$result['result'] = __('Can not delete this block !');
			}
			return $this->getResponse()->setBody(json_encode($result));
		}else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			return $resultRedirect;
		}
    }
}
