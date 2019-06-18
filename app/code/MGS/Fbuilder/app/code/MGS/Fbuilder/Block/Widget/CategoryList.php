<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Widget;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Element\Template;
/**
 * Main contact form block
 */
class CategoryList extends Template
{
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
	/**
     * @var Category
     */
    protected $_categoryInstance;
	
	protected $_file;
	protected $_filesystem;
	
	public function __construct(
		Template\Context $context,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Filesystem\Driver\File $file,
		array $data = []
	){
		$this->_objectManager = $objectManager;
		$this->_file = $file;
		$this->_filesystem = $context->getFilesystem();
		$this->_categoryInstance = $categoryFactory->create();
        parent::__construct($context, $data);
		
    }
	
	public function getModel($model){
		return $this->_objectManager->create($model);
	}

    /**
     * Get url for category data
     *
     * @param Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        if ($category instanceof Category) {
            $url = $category->getUrl();
        } else {
            $url = $this->_categoryInstance->setData($category->getData())->getUrl();
        }

        return $url;
    }
	
	public function getCategoryByIds(){
		$result = [];
		if($this->hasData('category_ids')){
			$categoryIds = $this->getData('category_ids');
			$categoryArray = explode(',',$categoryIds);
			if(count($categoryArray)>0){
				foreach($categoryArray as $categoryId){
					$category = $this->getModel('Magento\Catalog\Model\Category')->load($categoryId);
					if ($category->getIsActive() && $category->getId()){
						$result[] = $category;
					}
				}
			}
		}
		return $result;
	}
	
	public function getCategoryImageHtml($category){
		if($category->getFbuilderThumbnail()!=''){
			$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/category/') . $category->getFbuilderThumbnail();
			if ($this->_file->isExists($filePath))  {
				return '<img src="'.$this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'catalog/category/'.$category->getFbuilderThumbnail().'" alt=""/>';
			}
		}
		return;
	}
	
	public function getCategoryIconHtml($category){
		if($category->getFbuilderFontClass()!=''){
			return '<span class="category-icon font-icon '.$category->getFbuilderFontClass().'"></span>';
		}else{
			if($category->getFbuilderIcon()!=''){
				$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/category/') . $category->getFbuilderIcon();
				if ($this->_file->isExists($filePath))  {
					return '<span class="category-icon"><img src="'.$this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'catalog/category/'.$category->getFbuilderIcon().'" alt=""/></span>';
				}
			}
		}
		
		return;
	}
}

