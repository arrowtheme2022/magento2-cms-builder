<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace MGS\Fbuilder\Model;

class Child extends \Magento\Framework\Model\AbstractModel
{
   
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MGS\Fbuilder\Model\ResourceModel\Child');
    }
}
