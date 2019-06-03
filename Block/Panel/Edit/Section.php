<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Panel\Edit;

use MGS\Fbuilder\Block\Panel\AbstractPanel;

/**
 * Main contact form block
 */
class Section extends AbstractPanel
{
	protected $_section;
	
	public function getSectionInfo(){
		$id = $this->getRequest()->getParam('id');
		$section = $this->_sectionFactory->create()->load($id);
		$this->_section = $section;
		return $section;
	}
	
	public function getImageSrc(){
		$backgroundImageName = $this->_section->getBackgroundImage();
		$src = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'mgs/fbuilder/backgrounds/'.$backgroundImageName;
		return $src;
	}
	
	
}

