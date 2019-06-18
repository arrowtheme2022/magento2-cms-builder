<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Adminhtml\System;

/**
 * Export CSV button for shipping table rates
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Import extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;
	
	protected $collectionFactory;
	
    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
		\Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->_backendUrl = $backendUrl;
		$this->collectionFactory = $collectionFactory;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
		$collection = $this->collectionFactory->create();
		
		//echo $collection->getSelect(); die();
		
		$html = '<input type="file" id="fbuilder_import_file" name="import_file" accept="application/xml" style="margin-bottom:5px"/><br/><select id="fbuilder_import_page_id" name="groups[import][fields][page_id][value]" class="select admin__control-select" data-ui-id="select-groups-import-fields-page_id-value" style="width:210px; margin-right:10px">
		<option value="">'.__('Choose Page to Import').'</option>';
		if(count($collection)>0){
			foreach($collection as $page){
				if($page->getId()){
					$html .= '<option value="'.$page->getId().'">'. $page->getTitle() .'</option>';
				}
			}
		}
		
		$html .= '</select>';
		
		$html .= '<button type="button" class="action-default scalable" data-ui-id="widget-button-2" disabled="disabled"><span id="wait-text" style="display:none">'.__('Please wait...').'</span><span id="import-text">'.__('Import').'</span></button>';

        return $html;
    }
}
