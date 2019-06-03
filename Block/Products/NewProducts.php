<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Products;

/**
 * Main contact form block
 */
class NewProducts extends \MGS\Fbuilder\Block\Products\AbstractProduct
{
	/**
     * Product collection initialize process
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getProductCollection($category, $attribute=NULL)
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
		
		$now = $this->_date->gmtDate();
		$todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
		
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
		//$collection->addCategoryFilter($category);
		if($category->getId()){
			$categoryIdArray = [$category->getId()];
			$categoryFilter = ['eq'=>$categoryIdArray];
			$collection->addCategoriesFilter($categoryFilter);
		}

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
			->addAttributeToFilter(
				'news_from_date',
				[
					'or' => [
						0 => ['date' => true, 'to' => $todayEndOfDayDate],
						1 => ['is' => new \Zend_Db_Expr('not null')],
					]
				],
				'left'
			)->addAttributeToFilter(
				'news_to_date',
				[
					'or' => [
						0 => ['date' => true, 'from' => $todayStartOfDayDate],
						1 => ['is' => new \Zend_Db_Expr('not null')],
					]
				],
				'left'
			)->addAttributeToFilter(
				[
					['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
					['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
				]
			)->addAttributeToFilter(
				[
					['attribute' => 'news_from_date', array('lt' => $now)],
				]
			)->addAttributeToFilter(
				[
					['attribute' => 'news_to_date', array('gt' => $now)],
				]
			)->addAttributeToSort(
				'news_from_date',
				'desc'
			);

        $collection->setPageSize($this->getLimit())
            ->setCurPage($this->getCurrentPage())
			->setOrder('entity_id', 'DESC');
        return $collection;
    }
	
	public function getProductByCategories($categoryIds, $attribute=NULL){
		
		$now = $this->_date->gmtDate();
		$todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
		
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
            ->addStoreFilter()
			->addAttributeToFilter(
				'news_from_date',
				[
					'or' => [
						0 => ['date' => true, 'to' => $todayEndOfDayDate],
						1 => ['is' => new \Zend_Db_Expr('not null')],
					]
				],
				'left'
			)->addAttributeToFilter(
				'news_to_date',
				[
					'or' => [
						0 => ['date' => true, 'from' => $todayStartOfDayDate],
						1 => ['is' => new \Zend_Db_Expr('not null')],
					]
				],
				'left'
			)->addAttributeToFilter(
				[
					['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
					['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
				]
			)->addAttributeToFilter(
				[
					['attribute' => 'news_from_date', array('lt' => $now)],
				]
			)->addAttributeToFilter(
				[
					['attribute' => 'news_to_date', array('gt' => $now)],
				]
			)->addAttributeToSort(
				'news_from_date',
				'desc'
			);

		
        $collection->setPageSize($this->getLimit())
            ->setCurPage($this->getCurrentPage())
			->setOrder('entity_id', 'DESC');
		//echo $collection->getSelect();
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

