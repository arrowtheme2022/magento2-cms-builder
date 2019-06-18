<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Fbuilder\Block\Adminhtml\User;

/**
 * User edit page
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\User\Block\User\Edit
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $objId = $this->getRequest()->getParam($this->_objectId);

        if (!empty($objId)) {
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
    }
	
	public function getCreatePanelAccountUrl(){
		return $this->getUrl('adminhtml/fbuilder/createaccount', ['_current' => true]);
	}
}
