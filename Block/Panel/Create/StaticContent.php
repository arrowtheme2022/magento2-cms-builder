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
class StaticContent extends Template
{
	/**
     * @var \Magento\Widget\Model\WidgetFactory
     */
    protected $_widgetFactory;
	
    protected $_theme;
	
	public function __construct(
		Template\Context $context,
		\MGS\Fbuilder\Model\WidgetFactory $widgetFactory,
		array $data = []
	){
        parent::__construct($context, $data);
		$this->_widgetFactory = $widgetFactory;
    }
	
	public function getStaticUrl(){
		return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_STATIC]);
	}
	
	public function getWidgetPlaceHolder(){
		return $this->_widgetFactory->create()->getPlaceholderImageUrls();
	}
	
	public function getThemePath(){
		$viewFileUrl = $this->getViewFileUrl('');
	}
}

