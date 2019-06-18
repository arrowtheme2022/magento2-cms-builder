<?php

namespace MGS\Fbuilder\Block\Widget;
use Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Framework\View\Element\Template;

class Map extends Template
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
	
	public function getPinImageUrl(){
		if($this->hasData('map_icon') && ($this->getData('map_icon')!='')){
			$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/map') . $this->getData('map_icon');
			if ($this->_file->isExists($filePath))  {
				return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'mgs/fbuilder/map'.$this->getData('map_icon');
			}
		}
		return;
	}
}