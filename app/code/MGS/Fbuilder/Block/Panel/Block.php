<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Panel;

use MGS\Fbuilder\Block\Panel\AbstractPanel;

/**
 * Main contact form block
 */
class Block extends AbstractPanel
{
	public function getBlocks(){
		$blockName = $this->getBlockName();
		$storeId = $this->_storeManager->getStore()->getId();
		if($this->getRequest()->getFullActionName()=='cms_index_index'){
			$blocks = $this->_childCollectionFactory->create()
				->addFieldToFilter('block_name', $blockName)
				//->addFieldToFilter('store_id', $storeId)
				->setOrder('position', 'ASC');
			$blocks->getSelect()->where('(main_table.page_id='.$this->_pageId.') or (main_table.page_id IS NULL)');
		}else{
			$blocks = $this->_childCollectionFactory->create()
				->addFieldToFilter('block_name', $blockName)
				//->addFieldToFilter('store_id', $storeId)
				->addFieldToFilter('page_id', $this->_pageId)
				->setOrder('position', 'ASC');
		}

		return $blocks;
	}
	
	public function getBlockClass($block, $setting, $canUsePanel){
		$class = 'panel-block col-des-' . $block->getCol().' block'.$block->getId();
		if($canUsePanel){
			$class .= ' sort-item builder-container child-builder';
		}
		if($block->getClass()!=''){
			$class .= ' '.$block->getClass();
		}
        if (isset($setting['custom_class']) && $setting['custom_class'] != '') {
            $class .= ' ' . $setting['custom_class'];
        }
		
		if (isset($setting['animation']) && $setting['animation'] != '') {
            $class .= ' animated';
        }

        return $class;
	}
	
	public function getEditChildHtml($block, $child) {
        $html = '<div class="edit-panel child-panel"><ul>';

        $html .= '<li class="sort-handle"><a href="#" onclick="return false;" title="' . __('Move Block') . '"><em class="fa fa-arrows">&nbsp;</em></a></li>';

        $html .= '<li><a href="' . $this->getUrl('fbuilder/create/element', array('page_id'=>$this->getPageId(), 'block' => $block, 'id' => $child->getId(), 'type' => $child->getType())) . '" class="popup-link" title="' . __('Edit') . '"><em class="fa fa-edit">&nbsp;</em></a></li>';
		
		$html .= '<li><a href="#" title="' . __('Copy') . '" onclick="alert(\'Only available for commercial version.\');return false;"><em class="fa fa-copy">&nbsp;</em></a></li>';

        $html .= '<li class="change-col"><a href="javascript:void(0)" title="' . __('Change column setting') . '"><em class="fa fa-columns">&nbsp;</em></a><ul>';

        for ($i = 1; $i <= 12; $i++) {
            $html .= '<li><a id="changecol-'.$child->getId().'-'.$i.'" href="' . str_replace('https:','',str_replace('http:','',$this->getUrl('fbuilder/element/changecol', array('id' => $child->getId(), 'col' => $i)))) . '" onclick="changeBlockCol(this.href, '.$child->getCol().', '.$child->getId().'); return false"';
			if($i == $child->getCol()){
				$html .= ' class="active"';
			}
			$html .='><span>' . $i . '/12</span></a></li>';
        }

        $html .= '</ul></li>';

        $html .= '<li><a href="' . str_replace('https:','',str_replace('http:','',$this->getUrl('fbuilder/element/delete', array('id' => $child->getId())))) . '" onclick="if(confirm(\'' . __('Are you sure you would like to remove this block?') . '\')) removeBlock(this.href, '.$child->getId().'); return false" title="' . __('Delete Block') . '"><em class="fa fa-trash">&nbsp;</em></a></li>';
        $html .= '</ul></div>';

        return $html;
    }
	
	public function getContentOfBlock($block){
		return $this->_filterProvider->getBlockFilter()->setStoreId($this->_storeManager->getStore()->getId())->filter($block->getBlockContent());
	}
	
	public function getInlineSetting($block){
		$setting = json_decode($block->getSetting(), true);
		$html = '';
		if(isset($setting['margin_top']) && ($setting['margin_top']!='')){
			$html .= ' margin-top:'.$setting['margin_top'].'px;';
		}
		if(isset($setting['margin_bottom']) && ($setting['margin_bottom']!='')){
			$html .= ' margin-bottom:'.$setting['margin_bottom'].'px;';
		}
		if(isset($setting['margin_left']) && ($setting['margin_left']!='')){
			$html .= ' margin-left:'.$setting['margin_left'].'px;';
		}
		if(isset($setting['margin_right']) && ($setting['margin_right']!='')){
			$html .= ' margin-right:'.$setting['margin_right'].'px;';
		}
		if(isset($setting['padding_top']) && ($setting['padding_top']!='')){
			$html .= ' padding-top:'.$setting['padding_top'].'px;';
		}
		if(isset($setting['padding_bottom']) && ($setting['padding_bottom']!='')){
			$html .= ' padding-bottom:'.$setting['padding_bottom'].'px;';
		}
		if(isset($setting['padding_left']) && ($setting['padding_left']!='')){
			$html .= ' padding-left:'.$setting['padding_left'].'px;';
		}
		if(isset($setting['padding_right']) && ($setting['padding_right']!='')){
			$html .= ' padding-right:'.$setting['padding_right'].'px;';
		}
		if(isset($setting['main_block_color']) && ($setting['main_block_color']!='')){
			$html .= ' color:'.$setting['main_block_color'].';';
		}
		
		if($block->getBackgroundGradient()){
			$gradientFrom = $block->getBackgroundGradientFrom();
			$gradientTo = $block->getBackgroundGradientTo();
			if(($gradientFrom!='') || ($gradientTo!='')){
				if($gradientFrom==''){
					$gradientFrom = '#ffffff';
				}
				if($gradientTo==''){
					$gradientTo = '#ffffff';
				}
				switch ($block->getBackgroundGradientOrientation()) {
					case "vertical":
						$html.= 'background: '.$gradientFrom.'; background: -moz-linear-gradient(top, '.$gradientFrom.' 0%, '.$gradientTo.' 100%); background: -webkit-linear-gradient(top, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); background: linear-gradient(to bottom, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='.$gradientFrom.', endColorstr='.$gradientTo.',GradientType=0 );';
						break;
					case "diagonal":
						$html.= 'background: '.$gradientFrom.'; background: -moz-linear-gradient(-45deg, '.$gradientFrom.' 0%, '.$gradientTo.' 100%); background: -webkit-linear-gradient(-45deg, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); background: linear-gradient(135deg, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='.$gradientFrom.', endColorstr='.$gradientTo.',GradientType=1 );';
						break;
					case "diagonal-bottom":
						$html.= 'background: '.$gradientFrom.'; background: -moz-linear-gradient(45deg, '.$gradientFrom.' 0%, '.$gradientTo.' 100%); background: -webkit-linear-gradient(45deg, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); background: linear-gradient(45deg, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='.$gradientFrom.', endColorstr='.$gradientTo.',GradientType=1 );';
						break;
					case "radial":
						$html.= 'background: '.$gradientFrom.'; background: -moz-radial-gradient(center, ellipse cover, '.$gradientFrom.' 0%, '.$gradientTo.' 100%); background: -webkit-radial-gradient(center, ellipse cover, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); background: radial-gradient(ellipse at center, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='.$gradientFrom.', endColorstr='.$gradientTo.',GradientType=1 );';
						break;
					default:
						$html.= 'background: '.$gradientFrom.'; background: -moz-linear-gradient(left, '.$gradientFrom.' 0%, '.$gradientTo.' 100%); background: -webkit-linear-gradient(left, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); background: linear-gradient(to right, '.$gradientFrom.' 0%,'.$gradientTo.' 100%); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='.$gradientFrom.', endColorstr='.$gradientTo.',GradientType=1 );';
						break;
				}
			}
		}else{
			if ($block->getBackground() != '') {
				$html.= 'background-color: ' .$block->getBackground() . ';';
			}
			
			if ($block->getBackgroundImage() != '') {
				$html.= 'background-image: url(\'' . $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . 'mgs/fbuilder/backgrounds' . $block->getBackgroundImage() . '\');';


				if($block->getBackgroundRepeat()){
					$html.= 'background-repeat:repeat;';
				}else{
					$html.= 'background-repeat:no-repeat;';
				}
				
				if($block->getBackgroundCover()){
					$html.= 'background-size:cover;';
				}

			}
		}
		
		
		return $html;
	}
}

