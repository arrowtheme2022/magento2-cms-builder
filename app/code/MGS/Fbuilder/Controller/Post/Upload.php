<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Post;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Cache\Manager as CacheManager;

class Upload extends \Magento\Framework\App\Action\Action
{
	protected $_storeManager;
	
	protected $_filesystem;
	
	protected $_file;
	
	/**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
	
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\Filesystem\Driver\File $file,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		CustomerSession $customerSession,
		CacheManager $cacheManager
	)     
	{
		$this->customerSession = $customerSession;
		parent::__construct($context);
		
		$this->_storeManager = $storeManager;
		$this->_filesystem = $filesystem;
		$this->_file = $file;
        $this->_fileUploaderFactory = $fileUploaderFactory;
		$this->cacheManager = $cacheManager;
	}
    
    public function execute()
    {
		if($this->getRequest()->isAjax() && ($this->customerSession->getUseFrontendBuilder() == 1)){
			$type = $this->getRequest()->getParam('type');
			$result = ['result'=>'error', 'data'=>__('Can not upload file.')];
			if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
				$uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
				$file = $uploader->validateFile();
				
				if(($file['name']!='') && ($file['size'] >0)){
					$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
					$uploader->setAllowRenameFiles(true);
					$path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('wysiwyg/'.$type);
					$uploader->save($path);
					$fileName = $uploader->getUploadedFileName();
					if($this->isFile('wysiwyg/'.$type.'/'.$fileName)){
						$result['result'] = 'success';
						$result['data'] = $fileName;
					}else{
						$result['data'] = $_FILES['file']['name'];
					}
				}
			}
			$this->cacheManager->clean(['full_page']);
			return $this->getResponse()->setBody(json_encode($result));
		}else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			return $resultRedirect;
		}
    }
	
	public function isFile($filename)
    {
        $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

        return $mediaDirectory->isFile($filename);
    }
}
