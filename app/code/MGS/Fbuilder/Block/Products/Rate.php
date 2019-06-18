<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Products;

/**
 * Main contact form block
 */
class Rate extends \MGS\Fbuilder\Block\Products\AbstractProduct
{
	/**
     * Product collection initialize process
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getProductCollection($category, $attribute=NULL)
    {		
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
		//$collection->addCategoryFilter($category);
		if($category->getId()){
			$categoryIdArray = [$category->getId()];
			$categoryFilter = ['eq'=>$categoryIdArray];
			$collection->addCategoriesFilter($categoryFilter);
		}
		
		
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter();
		
		$storeId = $this->_storeManager->getStore(true)->getId();
		$reviewTable =  $this->_resource->getTableName('review_entity_summary');

		$collection->getSelect()->distinct()->joinLeft(['review'=>$reviewTable], 'entity_pk_value=e.entity_id', ['reviews_count','rating_summary', 'store_id'])->where('(review.store_id = '.$storeId.') & (review.rating_summary !="") && (review.reviews_count != "")')->order('review.rating_summary DESC')->order('review.reviews_count DESC');
		
        $collection->setPageSize($this->getLimit())
            ->setCurPage($this->getCurrentPage());

        return $collection;
    }
	
	public function getProductByCategories($categoryIds, $attribute=NULL)
    {
		
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
		if($categoryIds!=''){
			$categoryIdArray = explode(',',$categoryIds);
			if(count($categoryIdArray)>0){
				$categoryFilter = ['eq'=>$categoryIdArray];
				$collection->addCategoriesFilter($categoryFilter);
			}
		}
		
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter();
		$storeId = $this->_storeManager->getStore(true)->getId();
		$reviewTable =  $this->_resource->getTableName('review_entity_summary');

		$collection->getSelect()->distinct()->joinLeft(['review'=>$reviewTable], 'entity_pk_value=e.entity_id', ['reviews_count','rating_summary', 'store_id'])->where('(review.store_id = '.$storeId.') & (review.rating_summary !="") && (review.reviews_count != "")')->order('review.rating_summary DESC')->order('review.reviews_count DESC')->order('review.primary_id DESC');

        $collection->setPageSize($this->getLimit())
            ->setCurPage($this->getCurrentPage());
        return $collection;
    }
	
	public function getAllProductCount(){
		//return $this->_count;
	}
	
	public function getCurrentPage(){
		if ($this->getCurPage()) {
            return $this->getCurPage();
        }
		return 1;
	}
	
	public function getProductsPerRow(){
		if ($this->hasData('per_row')) {
            return $this->getData('per_row');
        }
		return false;
	}
	
	public function getCustomClass(){
		if ($this->hasData('custom_class')) {
            return $this->getData('custom_class');
        }
	}
	
	public function getCategoryByIds(){
		$result = [];
		if($this->hasData('category_ids')){
			$categoryIds = $this->getData('category_ids');
			$categoryArray = explode(',',$categoryIds);
			if(count($categoryArray)>0){
				foreach($categoryArray as $categoryId){
					$category = $this->getModel('Magento\Catalog\Model\Category')->load($categoryId);
					if($category->getId()){
						$result[] = $category;
					}
					
				}
			}
		}
		return $result;
	}
}

