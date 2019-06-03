<?php

namespace MGS\Fbuilder\Block\Widget;
use Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Framework\View\Element\Template;

class PromoBanner extends Template
{
	protected $_filesystem;
	
	protected $_file;
	
	public function __construct(
		Template\Context $context,
		\Magento\Framework\Filesystem\Driver\File $file,
		array $data = []
	){
        parent::__construct($context, $data);
		$this->_filesystem = $context->getFilesystem();
		$this->_file = $file;
    }
	
	public function getBannerUrl(){
		if($this->hasData('url') && ($this->getData('url')!='')){
			if (filter_var($this->getData('url'), FILTER_VALIDATE_URL)) { 
				return $this->getData('url');
			}else{
				return $this->getUrl($this->getData('url'));
			}
		}
		return '#';
	}
	
	public function getImageUrl(){
		if($this->hasData('banner_image') && ($this->getData('banner_image')!='')){
			$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/promobanners') . $this->getData('banner_image');
			if ($this->_file->isExists($filePath))  {
				return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'mgs/fbuilder/promobanners'.$this->getData('banner_image');
			}
		}
		return;
	}
}