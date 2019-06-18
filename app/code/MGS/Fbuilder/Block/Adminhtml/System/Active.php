<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Adminhtml\System;

/**
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Active extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @return string
     */
    public function getElementHtml()
    {	
		$html = '<button type="button" id="fbuilder_active_button" class="action-default scalable" data-ui-id="widget-button-0"><span>'.__('Active').'</span></button>';

        return $html;
    }
}
