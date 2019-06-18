<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Panel\Create;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class Block extends Template
{
	protected $_moduleManager;
	
	public function __construct(
		Template\Context $context,
		\Magento\Framework\Module\Manager $moduleManager,
		array $data = []
	){
		$this->_moduleManager = $moduleManager;
        parent::__construct($context, $data);
    }
	
	public function isModuleActive($module){
		if($this->_moduleManager->isOutputEnabled($module) && $this->_moduleManager->isEnabled($module)){
			return true;
		}
		return false;
	}
}

