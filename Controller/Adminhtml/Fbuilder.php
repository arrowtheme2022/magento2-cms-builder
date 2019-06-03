<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Controller\Adminhtml;
abstract class Fbuilder extends \Magento\Backend\App\Action
{
	/**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'MGS_Fbuilder::fbuilder_manage'
        )->_addBreadcrumb(
            __('Front-end Builder'),
            __('Front-end Builder')
        );
        return $this;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
