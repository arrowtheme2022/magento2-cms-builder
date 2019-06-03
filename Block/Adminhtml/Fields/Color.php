<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Adminhtml\Fields;

/**
 * Sitemap edit form container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $output=parent::_getElementHtml($element);
		$output .= "
		<script type='text/javascript'>
			require([
				'jquery'
			], function(jQuery){
				(function($) {
					$('#".$element->getHtmlId()."').attr('data-hex', true).mColorPicker();
				})(jQuery);
			});
		</script>";
        return $output;
    }
}
