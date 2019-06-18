<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Panel;

/**
 * Main contact form block
 */
class HomeContent extends AbstractPanel
{
	public function getSections(){
		$sectionCollection = $this->_sectionCollectionFactory->create();
		if($this->_fullActionName=='cms_index_index'){
			//$sectionCollection->addFieldToFilter('store_id', $this->_storeManager->getStore()->getId());
			$sectionCollection->setOrder('block_position', 'ASC');
			$sectionCollection->getSelect()->where('(main_table.page_id='.$this->_pageId.') or (main_table.page_id IS NULL)');
		}else{
			$sectionCollection
				//->addFieldToFilter('store_id', $this->_storeManager->getStore()->getId())
				->addFieldToFilter('page_id', $this->_pageId);
			$sectionCollection->setOrder('block_position', 'ASC');
		}
		return $sectionCollection;
	}
	
	public function getSectionSetting($section, $canUsePanel){
		$this->_section = $section;
		$html = ' class="';
        if ($this->_section->getId()) {
            if ($this->_section->getClass() != '') {
                $html.= $this->_section->getClass() ;
            }

            if ($this->_section->getParallax() & ($this->_section->getBackgroundImage() != '')) {
                $html.= ' parallax';
            }
			
			if($canUsePanel){
				$html.= ' builder-container section-builder sort-item';
			}
			
			if ($this->_section->getFullwidth()) {
                $html.= ' section-builder-full';
            }

            $html.= '" style="';
			
			if($this->_section->getBackgroundGradient()){
				
				$gradientFrom = $this->_section->getBackgroundGradientFrom();
				$gradientTo = $this->_section->getBackgroundGradientTo();
				if(($gradientFrom!='') || ($gradientTo!='')){
					if($gradientFrom==''){
						$gradientFrom = '#ffffff';
					}
					if($gradientTo==''){
						$gradientTo = '#ffffff';
					}
				
					switch ($this->_section->getBackgroundGradientOrientation()) {
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
				if ($this->_section->getBackground() != '') {
					$html.= 'background-color: ' .$this->_section->getBackground() . ';';
				}
				
				if ($this->_section->getBackgroundImage() != '') {
					$html.= 'background-image: url(\'' . $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . 'mgs/fbuilder/backgrounds' . $this->_section->getBackgroundImage() . '\');';

					if (!$this->_section->getParallax()) {
						if($this->_section->getBackgroundRepeat()){
							$html.= 'background-repeat:repeat;';
						}else{
							$html.= 'background-repeat:no-repeat;';
						}
						
						if($this->_section->getBackgroundCover()){
							$html.= 'background-size:cover;';
						}
					}
				}
			}

            if ($this->_section->getPaddingTop() != '') {
                $html.= ' padding-top:' . $this->_section->getPaddingTop() . 'px;';
            }

            if ($this->_section->getPaddingBottom() != '') {
                $html.= ' padding-bottom:' . $this->_section->getPaddingBottom() . 'px;';
            }
			
			$html.= '"';
			
			if ($this->_section->getParallax()) {
                $html.= ' data-stellar-vertical-offset="20" data-stellar-background-ratio="0.6"';
            }
			
			if($canUsePanel){
				$html.= ' id="panel-section-'.$this->_section->getId().'"';
			}
        }
		
        return $html;
	}
	
	public function getBlockCols(){
		$cols = $this->_section->getBlockCols();
		$cols = str_replace(' ','',$cols);
		$arr = explode(',', $cols);
		return $arr;
	}
	
	public function getEditPanel() {
        $html = '<div class="edit-panel parent-panel"><ul>';
        $html .='<li class="up-link"><a title="' . __('Move Up') . '" onclick="return false;" href="#" class="moveuplink"><em class="fa fa-arrow-up">&nbsp;</em></a></li>';
        $html .='<li class="down-link"><a title="' . __('Move Down') . '" onclick="return false;" href="#" class="movedownlink"><em class="fa fa-arrow-down">&nbsp;</em></a></li>';
        $html .='<li><a id="section-'.$this->_section->getId().'-edit" href="' . $this->_urlBuilder->getUrl('fbuilder/edit/section', ['id' => $this->_section->getId()]) . '" class="popup-link" title="' . __('Edit Section') . '" onclick="return false"><em class="fa fa-gear"></em></a></li>';
		$html .='<li><a href="#" title="' . __('Duplicate Section') . '" onclick="if(confirm(\'' . __('Are you sure you would like to duplicate this section?') . '\')) alert(\'Only available for commercial version.\'); return false"><em class="fa fa-copy"></em></a></li>';
        $html .='<li><a href="#" title="' . __('Delete Section') . '" onclick="if(confirm(\'' . __('Are you sure you would like to remove this section?') . '\')) removeSection(' . $this->_section->getId() . '); return false"><em class="fa fa-close"></em></a></li>';
        
        $html .='</ul></div>';

        return $html;
    }
	
	public function getBlockClass($section, $col, $arrClass, $key, $canUsePanel){
		$class = 'col-des-'.$col;
		
		$colTablets = json_decode($section->getTabletCols(), true);
		if(is_array($colTablets) && isset($colTablets[$key])){
			$class .= ' col-tb-'.$colTablets[$key];
		}
		$colMobiles = json_decode($section->getMobileCols(), true);
		if(is_array($colMobiles) && isset($colMobiles[$key])){
			$class .= ' col-mb-'.$colMobiles[$key];
		}
		if(is_array($arrClass) && isset($arrClass[$key])){
			$class .= ' '.$arrClass[$key];
		}
		if($canUsePanel){
			$class .= ' col-builder';
		}
		return $class;
	}
}

