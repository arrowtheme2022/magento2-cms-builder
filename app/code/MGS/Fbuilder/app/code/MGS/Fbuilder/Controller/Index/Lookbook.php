<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
class Lookbook extends \Magento\Framework\App\Action\Action
{
	/**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_builderHelper;

	
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\MGS\Fbuilder\Helper\Data $helper
    ) {
        parent::__construct($context);
		$this->_builderHelper = $helper;
    }
	
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		$responseData = [];
		
		if($id=$this->getRequest()->getParam('id')){
			$widget = '{{widget type="MGS\Lookbook\Block\Widget\Lookbook" lookbook_id="'.$id.'" template="MGS_Lookbook::widget/lookbook.phtml"}}';
			$html = $this->_builderHelper->getContentByShortcode($widget);
			$responseData[] = ['message'=>'success', 'html'=>$html];
		}
		
		
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
		return $resultJson;
    }
}
