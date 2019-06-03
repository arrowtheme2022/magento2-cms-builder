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

class Section extends \Magento\Framework\App\Action\Action
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
		if(($this->customerSession->getUseFrontendBuilder() == 1) && ($id = $this->getRequest()->getParam('id'))){
			$data = $this->getRequest()->getPostValue();
			if(!isset($data['background_repeat'])){
				$data['background_repeat'] = 0;
			}
			if(!isset($data['background_gradient'])){
				$data['background_gradient'] = 0;
			}
			if(!isset($data['parallax'])){
				$data['parallax'] = 0;
			}
			if(!isset($data['fullwidth'])){
				$data['fullwidth'] = 0;
			}
			if(!isset($data['background_cover'])){
				$data['background_cover'] = 0;
			}
			$model = $this->_objectManager->create('MGS\Fbuilder\Model\Section')->load($id);
			
			if (!$model->getId()) {
                $this->messageManager->addError(__('This section no longer exists.'));
            }else{
				/* Remove Image */
				if(isset($data['remove_background']) && ($data['remove_background']==1)){
					if($model->getBackgroundImage()!=''){
						$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/backgrounds') . $model->getBackgroundImage();
						if ($this->_file->isExists($filePath))  {
							$this->_file->deleteFile($filePath);
						}
					}
					
					$data['background_image'] = '';
				}

				/* Update Image */
				if(isset($_FILES['background_image']['name']) && $_FILES['background_image']['name'] != '') {
					$uploader = $this->_fileUploaderFactory->create(['fileId' => 'background_image']);
					$file = $uploader->validateFile();
					
					if(($file['name']!='') && ($file['size'] >0)){
						$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
						$uploader->setAllowRenameFiles(true);
						$uploader->setFilesDispersion(true);
						
						$path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/backgrounds');
						$uploader->save($path);
						$data['background_image'] = $uploader->getUploadedFileName();
					}
				}
				
				if (isset($data['block_class']) && count($data['block_class']) > 0) {
					$data['block_class'] = json_encode($data['block_class']);
				} else {
					$data['block_class'] = NULL;
				}
				
				if (isset($data['tablet_cols']) && count($data['tablet_cols']) > 0) {
					$data['tablet_cols'] = json_encode($data['tablet_cols']);
				} else {
					$data['tablet_cols'] = NULL;
				}
				
				
				if (isset($data['mobile_cols']) && count($data['mobile_cols']) > 0) {
					$data['mobile_cols'] = json_encode($data['mobile_cols']);
				} else {
					$data['mobile_cols'] = NULL;
				}
				
				
				$storeId = $this->_storeManager->getStore()->getId();
				
				if ($model->getBlockCols() != '') {
                    $oldCols = $model->getBlockCols();
                    $arrCol = explode(',', $oldCols);
                    $lastKey = key(array_slice($arrCol, -1, 1, TRUE));

                    $newCols = $data['block_cols'];
                    if ($newCols != '') {
                        $arrNewCol = explode(',', $newCols);
                        $lastNewKey = key(array_slice($arrNewCol, -1, 1, TRUE));

                        if ($lastKey > $lastNewKey) {
                            //echo $lastKey.' - '.$lastNewKey;
                            $arrKey = array();
                            foreach ($arrCol as $key => $value) {
                                if ($key > $lastNewKey) {
                                    $arrKey[] = $model->getName() . '-' . $key;
                                }
                            }

                            if (count($arrKey) > 0) {
                                $childs = $this->_objectManager->create('MGS\Fbuilder\Model\Child')->getCollection()
                                        ->addFieldToFilter('block_name', array('in' => $arrKey))
                                        ->addFieldToFilter('store_id', $storeId);
                                if (count($childs) > 0) {
                                    $lastBlock = $model->getName() . '-' . $lastNewKey;
                                    foreach ($childs as $_child) {
                                        $_child->setBlockName($lastBlock)->save();
                                    }
                                }
                            }
                        }
                    }
                }
				
				$model->setData($data)->setId($id);
				
				try {
					// save the data
					$model->save();
					$this->cacheManager->clean(['full_page']);
					// display success message
					return $this->getMessageHtml('success', __('You saved the section. Please wait to reload page.'), true);
					
				} catch (\Exception $e) {
					return $this->getMessageHtml('danger', $e->getMessage(), false);
				}
			}
			
			
		}else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			return $resultRedirect;
		}
    }
	
	public function getMessageHtml($type, $message, $reload){
		$html = '<style type="text/css">
			.container {
				padding: 0px 15px;
				margin-top:60px;
			}
			.page.messages .message {
				padding: 15px;
				font-family: "Lato",arial,tahoma;
				font-size: 14px;
			}
			.page.messages .message-success {
				background-color: #dff0d8;
			}
			.page.messages .message-danger {
				background-color: #f2dede;
			}
		</style>';
		$html .= '<main class="page-main container">
			<div class="page messages"><div data-placeholder="messages"></div><div>
				<div class="messages">
					<div class="message-'.$type.' '.$type.' message" data-ui-id="message-'.$type.'">
						<div>'.$message.'</div>
					</div>
				</div>
			</div>
		</div></main>';
		
		if($reload){
			$html .= '<script type="text/javascript">window.parent.location.reload();</script>';
		}
		
		return $this->getResponse()->setBody($html);
	}
}
