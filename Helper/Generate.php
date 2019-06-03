<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Fbuilder\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Contact base helper
 */
class Generate extends \MGS\Core\Helper\Data
{
	protected $_storeManager;
	
	protected $_filesystem;
	
	protected $_ioFile;
	protected $_urlBuilder;
	
	/**
     * @var \MGS\Fbuilder\Model\ResourceModel\Confirm\CollectionFactory
     */
    protected $_confirmCollectionFactory;
    protected $_sectionCollectionFactory;
    protected $_blockCollectionFactory;
	protected $messageManager;

	
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Filesystem\Io\File $ioFile,
		\Magento\Framework\View\Element\Context $context,
		\MGS\Fbuilder\Model\ResourceModel\Confirm\CollectionFactory $collectionFactory,
		\MGS\Fbuilder\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory,
		\MGS\Fbuilder\Model\ResourceModel\Child\CollectionFactory $blockCollectionFactory,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Url $url,
		\Magento\Framework\Filesystem $filesystem
	) {
		$this->scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $storeManager;
		$this->_filesystem = $filesystem;
		$this->_objectManager = $objectManager;
		$this->_confirmCollectionFactory = $collectionFactory;
		$this->_sectionCollectionFactory = $sectionCollectionFactory;
		$this->_blockCollectionFactory = $blockCollectionFactory;
		$this->_ioFile = $ioFile;
		$this->messageManager = $messageManager;
		$this->_urlBuilder = $url;
	}
	
	public function getModel($model){
		return $this->_objectManager->create($model);
	}
	
	public function getStore(){
		return $this->_storeManager->getStore();
	}
	
	/* Get system store config */
	public function getStoreConfig($node, $storeId = NULL){
		if($storeId != NULL){
			return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		}
		return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
	}
	
	public function generateCss(){
		$stores = $this->_storeManager->getStores();
		foreach($stores as $_store){
			$this->generateCssByStore($_store->getId());
		}
	}
	
	public function generateCssByStore($storeId){
		$html = '';
		$singleNavType = $this->getStoreConfig('fbuilder/single_slider/navigation', $storeId);
		$singleNavWidth = $this->getStoreConfig('fbuilder/single_slider/nav_width', $storeId);
		$singleNavHeight = $this->getStoreConfig('fbuilder/single_slider/nav_height', $storeId);
		$singleNavFontsize = $this->getStoreConfig('fbuilder/single_slider/nav_font_size', $storeId);
		$singleNavBorderRadius = $this->getStoreConfig('fbuilder/single_slider/border_radius', $storeId);
		
		$singleNavColor = $this->getStoreConfig('fbuilder/single_slider/color', $storeId);
		$singleNavBorder = $this->getStoreConfig('fbuilder/single_slider/border', $storeId);
		$singleNavBackground = $this->getStoreConfig('fbuilder/single_slider/background', $storeId);
		$singleNavBackgroundTransparent = $this->getStoreConfig('fbuilder/single_slider/background_transparent', $storeId);
		
		$singleNavHoverColor = $this->getStoreConfig('fbuilder/single_slider/hover_color', $storeId);
		$singleNavHoverBorder = $this->getStoreConfig('fbuilder/single_slider/hover_border', $storeId);
		$singleNavHoverBackground = $this->getStoreConfig('fbuilder/single_slider/hover_background', $storeId);
		$singleNavHoverBackgroundTransparent = $this->getStoreConfig('fbuilder/single_slider/hover_background_transparent', $storeId);
		
		$singleDotWidth = $this->getStoreConfig('fbuilder/single_slider/dot_width', $storeId);
		$singleDotHeight = $this->getStoreConfig('fbuilder/single_slider/dot_height', $storeId);
		$singleDotRadius = $this->getStoreConfig('fbuilder/single_slider/dot_radius', $storeId);
		$singleDotbackground = $this->getStoreConfig('fbuilder/single_slider/dot_background', $storeId);
		$singleDotActiveBackground = $this->getStoreConfig('fbuilder/single_slider/dot_active_background', $storeId);

		/* Single Slide */
		if($singleNavWidth!=''){
			$html .= '.mgs-carousel-single .owl-nav div img{width:'.$singleNavWidth.'px}';
			$html .= '.mgs-carousel-single .owl-nav div span{width:'.$singleNavWidth.'px}';
		}
		
		if($singleNavHeight!=''){
			$html .= '.mgs-carousel-single .owl-nav div span, .mgs-carousel-single .owl-nav div span em{height:'.$singleNavHeight.'px; line-height:'.$singleNavHeight.'px}';
			$html .= '.mgs-carousel-single.nav-position-middle-outside .owl-nav div span, .mgs-carousel-single.nav-position-middle-inside .owl-nav div span{margin-top:-' . ($singleNavHeight/2) . 'px}';
		}
		if($singleNavType=='font'){
			if($singleNavFontsize!=''){
				$html .= '.mgs-carousel-single .owl-nav div span em{font-size:'.$singleNavFontsize.'px;}';
			}
			if($singleNavColor!=''){
				$html .= '.mgs-carousel-single .owl-nav div span em{color:'.$singleNavColor.'}';
			}
			if($singleNavBorder!=''){
				$html .= '.mgs-carousel-single .owl-nav div span{border:1px solid '.$singleNavBorder.'}';
			}
			if($singleNavBorderRadius!=''){
				$html .= '.mgs-carousel-single .owl-nav div span{border-radius:'.$singleNavBorderRadius.'px}';
			}
			if($singleNavBackground!=''){
				if($singleNavBackgroundTransparent>0 && $singleNavBackgroundTransparent<1){
					list($r, $g, $b) = sscanf($singleNavBackground, "#%02x%02x%02x");
					$html .= '.mgs-carousel-single .owl-nav div span{background-color:rgba('.$r.', '.$g.', '.$b.', '.$singleNavBackgroundTransparent.')}';
				}else{
					$html .= '.mgs-carousel-single .owl-nav div span{background:'.$singleNavBackground.'}';
				}
			}
			
			if($singleNavHoverColor!=''){
				$html .= '.mgs-carousel-single .owl-nav div span:hover em{color:'.$singleNavHoverColor.'}';
			}
			if($singleNavHoverBorder!=''){
				$html .= '.mgs-carousel-single .owl-nav div span:hover{border:1px solid '.$singleNavHoverBorder.'}';
			}
			if($singleNavHoverBackground!=''){
				if($singleNavHoverBackgroundTransparent!=''){
					list($r, $g, $b) = sscanf($singleNavHoverBackground, "#%02x%02x%02x");
					$html .= '.mgs-carousel-single .owl-nav div span:hover{background-color:rgba('.$r.', '.$g.', '.$b.', '.$singleNavHoverBackgroundTransparent.')}';
				}else{
					if($singleNavBackgroundTransparent>0 && $singleNavBackgroundTransparent<1){
						list($r, $g, $b) = sscanf($singleNavHoverBackground, "#%02x%02x%02x");
						$html .= '.mgs-carousel-single .owl-nav div span:hover{background-color:rgba('.$r.', '.$g.', '.$b.', '.$singleNavBackgroundTransparent.')}';
					}else{
						$html .= '.mgs-carousel-single .owl-nav div span:hover{background:'.$singleNavHoverBackground.'}';
					}
				}
			}
		}

		if($singleDotWidth!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot span{width:'.$singleDotWidth.'px}';
		}
		if($singleDotHeight!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot span{height:'.$singleDotHeight.'px}';
		}
		if($singleDotRadius!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot span{border-radius:'.$singleDotRadius.'px}';
		}
		if($singleDotbackground!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot span{background:'.$singleDotbackground.'}';
		}
		if($singleDotActiveBackground!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot.active span, .mgs-carousel-single .owl-dots .owl-dot span:hover{background:'.$singleDotActiveBackground.'}';
		}
		
		
		/* Multiple Slide */
		$multipleNavType = $this->getStoreConfig('fbuilder/multiple_slider/navigation', $storeId);
		$multipleNavWidth = $this->getStoreConfig('fbuilder/multiple_slider/nav_width', $storeId);
		$multipleNavHeight = $this->getStoreConfig('fbuilder/multiple_slider/nav_height', $storeId);
		$multipleNavFontsize = $this->getStoreConfig('fbuilder/multiple_slider/nav_font_size', $storeId);
		$multipleNavBorderRadius = $this->getStoreConfig('fbuilder/multiple_slider/border_radius', $storeId);
		
		$multipleNavColor = $this->getStoreConfig('fbuilder/multiple_slider/color', $storeId);
		$multipleNavBorder = $this->getStoreConfig('fbuilder/multiple_slider/border', $storeId);
		$multipleNavBackground = $this->getStoreConfig('fbuilder/multiple_slider/background', $storeId);
		$multipleNavBackgroundTransparent = $this->getStoreConfig('fbuilder/multiple_slider/background_transparent', $storeId);
		
		$multipleNavHoverColor = $this->getStoreConfig('fbuilder/multiple_slider/hover_color', $storeId);
		$multipleNavHoverBorder = $this->getStoreConfig('fbuilder/multiple_slider/hover_border', $storeId);
		$multipleNavHoverBackground = $this->getStoreConfig('fbuilder/multiple_slider/hover_background', $storeId);
		$multipleNavHoverBackgroundTransparent = $this->getStoreConfig('fbuilder/multiple_slider/hover_background_transparent', $storeId);
		
		$multipleDotWidth = $this->getStoreConfig('fbuilder/multiple_slider/dot_width', $storeId);
		$multipleDotHeight = $this->getStoreConfig('fbuilder/multiple_slider/dot_height', $storeId);
		$multipleDotRadius = $this->getStoreConfig('fbuilder/multiple_slider/dot_radius', $storeId);
		$multipleDotbackground = $this->getStoreConfig('fbuilder/multiple_slider/dot_background', $storeId);
		$multipleDotActiveBackground = $this->getStoreConfig('fbuilder/multiple_slider/dot_active_background', $storeId);

		/* Single Slide */
		if($multipleNavWidth!=''){
			$html .= '.mgs-carousel-multiple .owl-nav div img{width:'.$multipleNavWidth.'px}';
			$html .= '.mgs-carousel-multiple .owl-nav div span{width:'.$multipleNavWidth.'px}';
		}
		
		if($multipleNavHeight!=''){
			$html .= '.mgs-carousel-multiple .owl-nav div span, .mgs-carousel-multiple .owl-nav div span em{height:'.$multipleNavHeight.'px; line-height:'.$multipleNavHeight.'px}';
			$html .= '.mgs-carousel-multiple.nav-position-middle-outside .owl-nav div span, .mgs-carousel-multiple.nav-position-middle-inside .owl-nav div span{margin-top:-' . ($multipleNavHeight/2) . 'px}';
		}
		if($multipleNavType=='font'){
			if($multipleNavFontsize!=''){
				$html .= '.mgs-carousel-multiple .owl-nav div span em{font-size:'.$multipleNavFontsize.'px;}';
			}
			if($multipleNavColor!=''){
				$html .= '.mgs-carousel-multiple .owl-nav div span em{color:'.$multipleNavColor.'}';
			}
			if($multipleNavBorder!=''){
				$html .= '.mgs-carousel-multiple .owl-nav div span{border:1px solid '.$multipleNavBorder.'}';
			}
			if($multipleNavBorderRadius!=''){
				$html .= '.mgs-carousel-multiple .owl-nav div span{border-radius:'.$multipleNavBorderRadius.'px}';
			}
			if($multipleNavBackground!=''){
				if($multipleNavBackgroundTransparent>0 && $multipleNavBackgroundTransparent<1){
					list($r, $g, $b) = sscanf($multipleNavBackground, "#%02x%02x%02x");
					$html .= '.mgs-carousel-multiple .owl-nav div span{background-color:rgba('.$r.', '.$g.', '.$b.', '.$multipleNavBackgroundTransparent.')}';
				}else{
					$html .= '.mgs-carousel-multiple .owl-nav div span{background:'.$multipleNavBackground.'}';
				}
			}
			
			if($multipleNavHoverColor!=''){
				$html .= '.mgs-carousel-multiple .owl-nav div span:hover em{color:'.$multipleNavHoverColor.'}';
			}
			if($multipleNavHoverBorder!=''){
				$html .= '.mgs-carousel-multiple .owl-nav div span:hover{border:1px solid '.$multipleNavHoverBorder.'}';
			}
			if($multipleNavHoverBackground!=''){
				if($multipleNavHoverBackgroundTransparent!=''){
					list($r, $g, $b) = sscanf($multipleNavHoverBackground, "#%02x%02x%02x");
					$html .= '.mgs-carousel-multiple .owl-nav div span:hover{background-color:rgba('.$r.', '.$g.', '.$b.', '.$multipleNavHoverBackgroundTransparent.')}';
				}else{
					if($multipleNavBackgroundTransparent>0 && $multipleNavBackgroundTransparent<1){
						list($r, $g, $b) = sscanf($multipleNavHoverBackground, "#%02x%02x%02x");
						$html .= '.mgs-carousel-multiple .owl-nav div span:hover{background-color:rgba('.$r.', '.$g.', '.$b.', '.$multipleNavBackgroundTransparent.')}';
					}else{
						$html .= '.mgs-carousel-multiple .owl-nav div span:hover{background:'.$multipleNavHoverBackground.'}';
					}
				}
			}
		}

		if($multipleDotWidth!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot span{width:'.$multipleDotWidth.'px}';
		}
		if($multipleDotHeight!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot span{height:'.$multipleDotHeight.'px}';
		}
		if($multipleDotRadius!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot span{border-radius:'.$multipleDotRadius.'px}';
		}
		if($multipleDotbackground!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot span{background:'.$multipleDotbackground.'}';
		}
		if($multipleDotActiveBackground!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot.active span, .mgs-carousel-multiple .owl-dots .owl-dot span:hover{background:'.$multipleDotActiveBackground.'}';
		}
		
		$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/css/' . $storeId . '/');
		
		$this->generateFile($html, 'fbuilder_config.css', $filePath);


		return;
	}
	
	public function generateFile($content, $fileName, $filePath){
		$io = $this->_ioFile;
		$file = $filePath . $fileName;
		$io->setAllowCreateFolders(true);
		$io->open(array('path' => $filePath));
		$io->write($file, $content, 0644);
		$io->streamClose();
	}
	
	public function importContent($pageId){
		$sectionCollection = $this->_sectionCollectionFactory->create()
			->addFieldToFilter('page_id', $pageId)
			->setOrder('block_position', 'ASC');
			
		$html = '';
		if(count($sectionCollection)>0){
			foreach($sectionCollection as $_section){
				$html .= '<div'.$this->getSectionSetting($_section).'>';
				$html .= '<div class="frame no-padding">';
				
				$cols = $this->getBlockCols($_section);
				$class = $_section->getBlockClass();
				if($class!=''){
					$class = json_decode($class, true);
				}
				
				if(count($cols)>1){
					$html .= '<div class="line">';
						foreach($cols as $key=>$col){
							$blockClass = $this->getBlockClass($_section, $col, $class, $key);
							$html .= '<div class="'.$blockClass.'">';
								$html .= '<div class="line">';
									
									$blocks = $this->getBlocks($_section->getName().'-'.$key, $pageId);
									
									foreach($blocks as $_block){
										$setting = json_decode($_block->getSetting(), true);
											$html .= '<div class="panel-block-row '.$this->getChildClass($_block, $setting).'"';
											if(isset($setting['animation']) && $setting['animation']!=''){
												$html .= ' data-appear-animation="'.$setting['animation'].'"';
											}
											if(isset($setting['animation_delay']) && $setting['animation_delay']!=''){
												$html .= ' data-appear-animation-delay="'.$setting['animation_delay'].'"';
											} 
											$html .= '>';
											$html .= '<div style="'.$this->getInlineSetting($_block).'">';
											$html .= $_block->getBlockContent();
											$html .= '</div>';
											$html .= '</div>';
									}
									
								$html .= '</div>';
							$html .= '</div>';
						}
					$html .= '</div>';
				}else{
					$html .= '<div class="line">';
						$html .= '<div class="col-des-12">';
							$html .= '<div class="line">';
								
								$blocks = $this->getBlocks($_section->getName().'-0', $pageId);
								
								foreach($blocks as $_block){
									$setting = json_decode($_block->getSetting(), true);
										$html .= '<div class="panel-block-row '.$this->getChildClass($_block, $setting).'"';
										if(isset($setting['animation']) && $setting['animation']!=''){
											$html .= ' data-appear-animation="'.$setting['animation'].'"';
										}
										if(isset($setting['animation_delay']) && $setting['animation_delay']!=''){
											$html .= ' data-appear-animation-delay="'.$setting['animation_delay'].'"';
										} 
										$html .= '>';
										$html .= '<div style="'.$this->getInlineSetting($_block).'">';
										$html .= $_block->getBlockContent();
										$html .= '</div>';
										$html .= '</div>';
								}
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';
				}
				
				$html .= '</div></div>';
			}
		}
		
		if($html!=''){
			$cmsPageModel = $this->getModel('Magento\Cms\Model\Page');
			$cmsPageModel->load($pageId);
			$cmsPageModel->setContent($html);
			try {
				$cmsPageModel->save();
				$confirmCollection = $this->_confirmCollectionFactory->create();
				$confirmCollection->addFieldToFilter('page_id', $pageId);
				if(count($confirmCollection)==0){
					$confirmModel = $this->getModel('MGS\Fbuilder\Model\Confirm');
					$confirmModel->setPageId($pageId);
					$confirmModel->save();
					$this->generateCss();
				}
				$this->messageManager->addSuccess(__('You saved the page.'));
			} catch (LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the page.'));
			}
		}else{
			$this->messageManager->addError(__('Have no content to import'));
		}
	}
	
	public function getBlockClass($section, $col, $arrClass, $key){
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

		return $class;
	}
	
	public function getSectionSetting($section){
		$html = ' class="section-builder ';
        if ($section->getId()) {
            if ($section->getClass() != '') {
                $html.= $section->getClass() ;
            }

            if ($section->getParallax() & ($section->getBackgroundImage() != '')) {
                $html.= ' parallax';
            }
			
			if ($section->getFullwidth()) {
                $html.= ' section-builder-full';
            }

            $html.= '" style="';

            if($section->getBackgroundGradient()){
				$gradientFrom = $section->getBackgroundGradientFrom();
				$gradientTo = $section->getBackgroundGradientTo();
				if(($gradientFrom!='') || ($gradientTo!='')){
					if($gradientFrom==''){
						$gradientFrom = '#ffffff';
					}
					if($gradientTo==''){
						$gradientTo = '#ffffff';
					}
					
					switch ($section->getBackgroundGradientOrientation()) {
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
				if ($section->getBackground() != '') {
					$html.= 'background-color: ' .$section->getBackground() . ';';
				}
				
				if ($section->getBackgroundImage() != '') {
					$html.= 'background-image: url(\'' . $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . 'mgs/fbuilder/backgrounds' . $section->getBackgroundImage() . '\');';

					if (!$section->getParallax()) {
						if($section->getBackgroundRepeat()){
							$html.= 'background-repeat:repeat;';
						}else{
							$html.= 'background-repeat:no-repeat;';
						}
						
						if($section->getBackgroundCover()){
							$html.= 'background-size:cover;';
						}
					}
				}
			}



            if ($section->getPaddingTop() != '') {
                $html.= ' padding-top:' . $section->getPaddingTop() . 'px;';
            }

            if ($section->getPaddingBottom() != '') {
                $html.= ' padding-bottom:' . $section->getPaddingBottom() . 'px;';
            }
			
			$html.= '"';
			
			if ($section->getParallax()) {
                $html.= ' data-stellar-vertical-offset="20" data-stellar-background-ratio="0.6"';
            }

        }
		
        return $html;
	}
	
	public function getBlocks($blockName, $pageId){
		$blocks = $this->_blockCollectionFactory->create()
				->addFieldToFilter('block_name', $blockName)
				->addFieldToFilter('page_id', $pageId)
				->setOrder('position', 'ASC');	

		return $blocks;
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
	
	public function getBlockCols($section){
		$cols = $section->getBlockCols();
		$cols = str_replace(' ','',$cols);
		$arr = explode(',', $cols);
		return $arr;
	}
	
	public function getChildClass($block, $setting){
		$class = ' panel-block col-des-' . $block->getCol().' block'.$block->getId();
		
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
}