<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class CreateAccount implements ObserverInterface
{
	protected $_request;
	protected $_messageManager;
	
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
	
	public function __construct(
		RequestInterface $request,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
		$this->_request = $request;
		$this->_messageManager = $messageManager;
		$this->storeManager     = $storeManager;
		$this->_customerFactory = $customerFactory;
    }
	
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $this->_request->getPost();
		if(isset($data['create_panel_account']) && ($data['create_panel_account']=='on')){
			$user = $observer->getEvent()->getObject();
			$userPassword = $user->getPassword();
			$firstname = $user->getFirstname();
			$lastname = $user->getLastname();
			$userEmail = $user->getEmail();
			$websites = $this->storeManager->getWebsites();
			if(count($websites)>0){
				foreach($websites as $website){
					$customer = $this->_customerFactory->create()->setWebsiteId($website->getId())->loadByEmail($userEmail);
					if($customer->getId()){
						$this->_messageManager->addError(__('The account with same email already exists on %1', $website->getName()));
					}else{
						$customer->setFirstname($firstname);
						$customer->setLastname($lastname);
						$customer->setEmail($userEmail);
						$customer->setPasswordHash($userPassword);
						$customer->setIsFbuilderAccount(1);
						
						try{
							$customer->save();
							$this->_messageManager->addSuccess(__('Front-end Builder account have been created for website %1.', $website->getName()));
						}catch(\Exception $e){
							$this->_messageManager->addError(__($e->getMessage()));
						}
					}
				}
			}
		}
    }
}
