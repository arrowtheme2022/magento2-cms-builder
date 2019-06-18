<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace MGS\Fbuilder\Model\Config\Source;

class Navigation implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
			['value' => 'image', 'label' => __('Use Image')], 
			['value' => 'font', 'label' => __('Use Font Icon')]
		];
    }
}
