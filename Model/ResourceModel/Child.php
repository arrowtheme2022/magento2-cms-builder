<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Model\ResourceModel;

class Child extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mgs_fbuilder_child', 'child_id');
    }
}
