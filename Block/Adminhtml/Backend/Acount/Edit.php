<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Adminhtml\Backend\Acount;

/**
 * Adminhtml edit admin user account
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Edit extends \Magento\Backend\Block\System\Account\Edit
{
    protected function _construct()
    {
        parent::_construct();

		$deleteConfirmMsg = __("Are you sure you want to use this account to create front-end builder account ?");

		$this->addButton(
			'panel',
			[
				'label' => __('Front-end Builder Account'),
				'class' => 'save',
				'onclick' => "deleteConfirm('" . $deleteConfirmMsg . "', '" . $this->getCreatePanelAccountUrl() . "')",
			]
		);

    }
	
	public function getCreatePanelAccountUrl(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$authSession = $objectManager->get('Magento\Backend\Model\Auth\Session');
		
		return $this->getUrl('adminhtml/fbuilder/createaccount', ['_current' => true, 'user_id'=>$authSession->getUser()->getId()]);
	}
}
