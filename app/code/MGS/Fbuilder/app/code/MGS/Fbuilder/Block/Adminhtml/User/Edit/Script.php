<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Fbuilder\Block\Adminhtml\User\Edit;

/**
 * User edit page
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Script extends \Magento\Backend\Block\Template
{
    public function isNew(){
		if(!$this->getRequest()->getParam('user_id')){
			return true;
		}
		return false;
	}
}
