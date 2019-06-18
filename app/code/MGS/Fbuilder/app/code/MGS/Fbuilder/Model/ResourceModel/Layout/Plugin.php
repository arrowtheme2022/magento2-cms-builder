<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Model\ResourceModel\Layout;

/**
 * Class Plugin
 */
class Plugin
{
    /**
     * @var \Magento\Widget\Model\ResourceModel\Layout\Update
     */
    private $update;
	
	/**
     * @var \MGS\Fbuilder\Helper\Data
     */
    private $helper;

    /**
     * @param \Magento\Widget\Model\ResourceModel\Layout\Update $update
     */
    public function __construct(
        \Magento\Widget\Model\ResourceModel\Layout\Update $update,
		\MGS\Fbuilder\Helper\Data $helper
    ) {
        $this->update = $update;
		$this->helper = $helper;
    }

    /**
     * Around getDbUpdateString
     *
     * @param \Magento\Framework\View\Model\Layout\Merge $subject
     * @param callable $proceed
     * @param string $handle
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetDbUpdateString(
        \Magento\Framework\View\Model\Layout\Merge $subject,
        \Closure $proceed,
        $handle
    ) {
		if($this->helper->getStoreConfig('fbuilder/general/is_enabled') && $this->helper->getStoreConfig('fbuilder/general/disable_widgets') &&(($this->helper->getFullActionName()=='cms_index_index') || ($this->helper->getFullActionName()=='cms_page_view'))){
			return;
		}else{
			return $this->update->fetchUpdatesByHandle($handle, $subject->getTheme(), $subject->getScope());
		}
    }
}
