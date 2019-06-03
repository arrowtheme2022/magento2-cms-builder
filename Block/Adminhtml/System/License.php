<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Adminhtml\System;

/**
 * Export CSV button for shipping table rates
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class License extends \Magento\Config\Block\System\Config\Form\Field
{


    /**
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<span style="color:#ff0000">This is free license. You can purchase commercial version here: <a href="https://www.magesolution.com/front-end-cms-page-builder.html" target="_blank">https://www.magesolution.com/front-end-cms-page-builder.html</a></span>';
    }
}
