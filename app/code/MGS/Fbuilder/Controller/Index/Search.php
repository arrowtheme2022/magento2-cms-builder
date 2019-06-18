<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
class Search extends \Magento\Framework\App\Action\Action
{
	/**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;
	
	/**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
	
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Catalog\Block\Product\Context $catalogContext,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
    ) {
        parent::__construct($context);
		$this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
		$this->_catalogConfig = $catalogContext->getCatalogConfig();
    }
	
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		if (!$q=$this->getRequest()->getParam('q', false)) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }
		$responseData = [];
		$collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
		$query = $q.'%';
		$collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
			->addAttributeToSelect('name')
            ->addAttributeToFilter('name', ['like'=> $query])
            ->addAttributeToSort('created_at', 'desc');
		
		if(count($collection)>0){
			foreach($collection as $_product){
				$responseData[] = ['name'=>$_product->getName(), 'id'=>$_product->getId()];
			}
		}
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
		return $resultJson;
    }
	
	/**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     * E.g. crosssells, upsells, new products, recently viewed
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _addProductAttributesAndPrices(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->addUrlRewrite();
    }
}
