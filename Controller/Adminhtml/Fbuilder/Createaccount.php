<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Controller\Adminhtml\Fbuilder;
use Magento\Framework\Controller\ResultFactory;

class Createaccount extends \MGS\Fbuilder\Controller\Adminhtml\Fbuilder
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
	
	/**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;
	
	/**
     * User model factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
	
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
		\Magento\Backend\App\Action\Context $context, 
		\Magento\Framework\Registry $coreRegistry, 
		\Magento\User\Model\UserFactory $userFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)
    {
        parent::__construct($context);
		$this->_coreRegistry = $coreRegistry;
		$this->storeManager     = $storeManager;
		$this->_userFactory = $userFactory;
		$this->_customerFactory = $customerFactory;
    }

    /**
     * Edit sitemap
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
		if ($userId = $this->getRequest()->getParam('user_id')) {
			$user = $this->_userFactory->create()->load($userId);
			$userPassword = $user->getPassword();
			$firstname = $user->getFirstname();
			$lastname = $user->getLastname();
			$userEmail = $user->getEmail();
			
			$websites = $this->storeManager->getWebsites();
			if(count($websites)>0){
				foreach($websites as $website){
					$customer = $this->_customerFactory->create()->setWebsiteId($website->getId())->loadByEmail($userEmail);
					if($customer->getId()){
						$this->messageManager->addError(__('The account with same email already exists on %1', $website->getName()));
					}else{
						$customer->setFirstname($firstname);
						$customer->setLastname($lastname);
						$customer->setEmail($userEmail);
						$customer->setPasswordHash($userPassword);
						$customer->setIsFbuilderAccount(1);
						
						try{
							$customer->save();
							$this->messageManager->addSuccess(__('Front-end Builder account have been created for website %1.', $website->getName()));
						}catch(\Exception $e){
							$this->messageManager->addError(__($e->getMessage()));
						}
					}
				}
			}
		}
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$resultRedirect->setUrl($this->_redirect->getRefererUrl());
		return $resultRedirect;
    }
}
