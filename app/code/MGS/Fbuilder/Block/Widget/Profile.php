<?php

namespace MGS\Fbuilder\Block\Widget;
use Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Framework\View\Element\Template;

class Profile extends Template
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
	
	public function getProfilePhoto(){
		if($this->hasData('profile_photo') && ($this->getData('profile_photo')!='')){
			$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/profiles') . $this->getData('profile_photo');
			if ($this->_file->isExists($filePath))  {
				return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'mgs/fbuilder/profiles'.$this->getData('profile_photo');
			}
		}
		return;
	}
}