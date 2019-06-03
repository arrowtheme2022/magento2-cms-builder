<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace MGS\Fbuilder\Controller\Element;

use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Cache\Manager as CacheManager;
class Save extends \Magento\Framework\App\Action\Action
{
	protected $_storeManager;
	
	/**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
	
	protected $_filesystem;
	
	protected $_file;

	protected $builderHelper;

    /**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		CustomerSession $customerSession,
		\Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\Filesystem\Driver\File $file,
		\Magento\Framework\View\Element\Context $urlContext,
		CacheManager $cacheManager,
		\MGS\Fbuilder\Helper\Generate $builderHelper
	)     
	{
		$this->_storeManager = $storeManager;
		$this->customerSession = $customerSession;
		$this->_urlBuilder = $urlContext->getUrlBuilder();
		$this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
		$this->_file = $file;
		$this->builderHelper = $builderHelper;
		$this->cacheManager = $cacheManager;
		parent::__construct($context);
	}
	
	public function getModel($model){
		return $this->_objectManager->create($model);
	}
	
    public function execute()
    {
		if($this->customerSession->getUseFrontendBuilder() == 1){
			$data = $this->getRequest()->getPostValue();
			switch ($data['type']) {
				/* Static content Block */
				case "static":
					$this->removePanelImages('panel',$data);
					$content = $data['content'];
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Text content block. Please wait for page reload.');
					break;
					
				/* New Products Block */
				case "new_products":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'use_tabs', 'hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'tab_font_bold', 'tab_italic', 'tab_uppercase', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					if($data['setting']['template']=='list.phtml'){
						$data['setting']['use_tabs'] = 0;
					}
					if($data['setting']['use_tabs']){
						$template = 'products/category-tabs.phtml';
					}else{
						$template = 'products/'.$data['setting']['template'];
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\NewProducts" block_type="new" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					if($data['setting']['use_tabs']){
						$content .= ' tab_style="'.$data['setting']['tab_style'].'"';
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
					}
					
					$content .= ' template="'.$template.'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the New Products block. Please wait for page reload.');
					break;
					
				/* Top Rate Products Block */
				case "rate":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'use_tabs', 'hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'tab_font_bold', 'tab_italic', 'tab_uppercase', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					if($data['setting']['template']=='list.phtml'){
						$data['setting']['use_tabs'] = 0;
					}
					if($data['setting']['use_tabs']){
						$template = 'products/category-tabs.phtml';
					}else{
						$template = 'products/'.$data['setting']['template'];
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\Rate" block_type="rate" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					if($data['setting']['use_tabs']){
						$content .= ' tab_style="'.$data['setting']['tab_style'].'"';
						if($data['setting']['tab_font_bold']){
							$content .= ' tab_font_bold="1"';
						}
						if($data['setting']['tab_italic']){
							$content .= ' tab_italic="1"';
						}
						if($data['setting']['tab_uppercase']){
							$content .= ' tab_uppercase="1"';
						}
						$content .= ' tab_align="'.$data['setting']['tab_align'].'"';
						
						$data['custom_style_temp']['tab-style'] = ['tab-'.$data['setting']['tab_style']=>[
								'font-size' => $data['setting']['font_size'],
								'primary-color' => $data['setting']['tab_primary_color'],
								'secondary-color' => $data['setting']['tab_secondary_color'],
								'third-color' => $data['setting']['tab_third_color']
							]
						];
					}
					
					$content .= ' template="'.$template.'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Top Rate Products block. Please wait for page reload.');
					break;
				
				
				/* Single Product Block */
				case "special_product":
					$dataInit = ['hide_name', 'hide_review', 'hide_price', 'hide_addcart', 'hide_addwishlist', 'hide_addcompare', 'hide_description'];
					$data = $this->reInitData($data, $dataInit);
					
					$content = '{{block class="MGS\Fbuilder\Block\Products\SpecialProduct" product_id="'.$data['setting']['product_id'].'" hide_name="'.$data['setting']['hide_name'].'" hide_review="'.$data['setting']['hide_review'].'" hide_price="'.$data['setting']['hide_price'].'" hide_addcart="'.$data['setting']['hide_addcart'].'" hide_addwishlist="'.$data['setting']['hide_addwishlist'].'" hide_addcompare="'.$data['setting']['hide_addcompare'].'" hide_description="'.$data['setting']['hide_description'].'" truncate="'.$data['setting']['truncate'].'"';
					
					$content .= ' template="products/single/default.phtml"}}';

					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Single Product block. Please wait for page reload.');
					break;
				/* Facebook Fan Box Block */
				case "facebook":
					$dataInit = ['hide_cover', 'show_facepile', 'hide_call_to', 'small_header', 'fit_inside'];
					$data = $this->reInitData($data, $dataInit);
					$tabs = implode(',',$data['setting']['facebook_tabs']);
					$content = '{{block class="MGS\Fbuilder\Block\Social\Facebook" page_url="'.$data['setting']['page_url'].'" width="'.$data['setting']['width'].'" height="'.$data['setting']['height'].'" facebook_tabs="'.$tabs.'" hide_cover="'.$data['setting']['hide_cover'].'" show_facepile="'.$data['setting']['show_facepile'].'" small_header="'.$data['setting']['small_header'].'" fit_inside="'.$data['setting']['fit_inside'].'" hide_call_to="'.$data['setting']['hide_call_to'].'" template="widget/socials/facebook_fanbox.phtml"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Facebook Fanbox block. Please wait to reload page.');
					break;
					
				/* Twitter Fan Box Block */
				case "twitter":
					$content = '{{block class="MGS\Fbuilder\Block\Social\Twitter" page_url="'.$data['setting']['page_url'].'" width="'.$data['setting']['width'].'" height="'.$data['setting']['height'].'" theme="'.$data['setting']['theme'].'" default_link_color="'.$data['setting']['default_link_color'].'" language="'.$data['setting']['language'].'" template="widget/socials/twitter_timeline.phtml"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Twitter Timeline block. Please wait to reload page.');
					break;
					
				/* Category List */	
				case "category_list":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'show_category_name', 'show_product', 'show_icon', 'font_bold', 'font_italic', 'uppercase', 'other_font_bold', 'other_font_italic', 'other_uppercase', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					
					$categories = '';
					if(isset($data['setting']['category_id'])){
						$categories = implode(',',$data['setting']['category_id']);
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Widget\CategoryList" fbuilder_title="'.$this->encodeHtml($data['setting']['title']).'" category_ids="'.$categories.'"';
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' use_slider="'.$data['setting']['use_slider'].'" perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'" show_category_name="'.$data['setting']['show_category_name'].'" show_product="'.$data['setting']['show_product'].'"';
						
						if($data['setting']['use_slider']){
							$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'" loop="'.$data['setting']['loop'].'"';
						}
						
						$data['custom_style_temp']['category-style'] = ['grid'=>[
							'font-size' => $data['setting']['font_size'],
							'other-font-size' => $data['setting']['other_font_size'],
							'primary-color' => $data['setting']['primary_color'],
							'secondary-color' => $data['setting']['secondary_color'],
							'third-color' => $data['setting']['third_color']
						]];
						
					}else{
						$content .= ' show_icon="'.$data['setting']['show_icon'].'"';
						
						$data['custom_style_temp']['category-style'] = ['list'=>[
							'font-size' => $data['setting']['font_size'],
							'other-font-size' => $data['setting']['other_font_size'],
							'primary-color' => $data['setting']['primary_color'],
							'secondary-color' => $data['setting']['secondary_color'],
							'third-color' => $data['setting']['third_color'],
							'fourth-color' => $data['setting']['fourth_color'],
							'fifth_color' => $data['setting']['fifth_color'],
						]];
					}
					
					if($data['setting']['font_bold']){
						$content .= ' font_bold="1"';
					}
					if($data['setting']['font_italic']){
						$content .= ' font_italic="1"';
					}
					if($data['setting']['uppercase']){
						$content .= ' uppercase="1"';
					}
					
					if($data['setting']['other_font_bold']){
						$content .= ' other_font_bold="1"';
					}
					if($data['setting']['other_font_italic']){
						$content .= ' other_font_italic="1"';
					}
					if($data['setting']['other_uppercase']){
						$content .= ' other_uppercase="1"';
					}
					
					$content .=  ' template="widget/category/'.$data['setting']['template'].'"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Category list block. Please wait to reload page.');
					break;
				
				
				/* Video Block*/
				case "video":
					
					$dataInit = ['full_width','autoplay','hide_info','hide_control','loop','mute'];
					$data = $this->reInitData($data, $dataInit);
					
					$content = '{{block class="MGS\Fbuilder\Block\Widget\Video" video_url="'.$data['setting']['video_url'].'" full_width="'.$data['setting']['full_width'].'" video_width="'.$data['setting']['video_width'].'" video_height="'.$data['setting']['video_height'].'" autoplay="'.$data['setting']['autoplay'].'" hide_info="'.$data['setting']['hide_info'].'" hide_control="'.$data['setting']['hide_control'].'" loop="'.$data['setting']['loop'].'" mute="'.$data['setting']['mute'].'"';
					
					$content .= ' template="widget/video.phtml"}}';
					
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Video block. Please wait for page reload.');
					
					break;
					
				/* Promo Banner Block*/
				case "promo_banner":
					$dataInit = [];
					$data = $this->reInitData($data, $dataInit);
					
					if(isset($_FILES['image']) && $_FILES['image']['name'] != '') {
						try {
							/* if(isset($data['setting']['banner_image']) && ($data['setting']['banner_image']!='')){
								$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/promobanners') . $data['setting']['banner_image'];
								if ($this->_file->isExists($filePath))  {
									$this->_file->deleteFile($filePath);
								}
							} */
							
							$uploader = $this->uploadFile('image', 'promobanners');
							$data['setting']['banner_image'] = $uploader->getUploadedFileName();
							
							
							
						} catch (\Exception $e) {
							$result['message'] = $e->getMessage();
						}
					}
					
					$content = '{{block class="MGS\Fbuilder\Block\Widget\PromoBanner" banner_image="'.$data['setting']['banner_image'].'" url="'.$data['setting']['url'].'" fbuilder_text_content="'.$this->encodeHtml($data['setting']['text_content']).'" fbuilder_button_text="'.$this->encodeHtml($data['setting']['button_text']).'" text_align="'.$data['setting']['text_align'].'" effect="'.$data['setting']['effect'].'"';
					
					$content .= ' template="widget/promobanner.phtml"}}';
					
					$data['custom_style_temp']['banner-style'] = [
						'text-color' => $data['setting']['text_color'],
						'button-background' => $data['setting']['button_background'],
						'button-color' => $data['setting']['button_color'],
						'button-border' => $data['setting']['button_border'],
						'button-hover-background' => $data['setting']['button_hover_background'],
						'button-hover-color' => $data['setting']['button_hover_color'],
						'button-hover-border' => $data['setting']['button_hover_border']
					];
					
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Promo Banner block. Please wait for page reload.');
					
					break;
				
				/* Counter Block*/
				case "counter_box":
					$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" fbuilder_text_content="'.$this->encodeHtml($data['setting']['text_content']).'" style="'.$data['setting']['style'].'" icon_font_size="'.$data['setting']['icon_font_size'].'" border="'.$data['setting']['border'].'" border_width="'.$data['setting']['border_width'].'" width="'.$data['setting']['width'].'" icon_color="'.$data['setting']['icon_color'].'" icon_color="'.$data['setting']['icon_color'].'" icon_background="'.$data['setting']['icon_background'].'" subtitle_font_size="'.$data['setting']['subtitle_font_size'].'" subtitle_font_color="'.$data['setting']['subtitle_font_color'].'" desc_font_size="'.$data['setting']['desc_font_size'].'" box_border="'.$data['setting']['box_border'].'" number_color="'.$data['setting']['number_color'].'" number_font_size="'.$data['setting']['number_font_size'].'" number_from="'.$data['setting']['number_from'].'" number_to="'.$data['setting']['number_to'].'" duration="'.$data['setting']['duration'].'" separators="'.$data['setting']['separators'].'" template="MGS_Fbuilder::widget/counter_box.phtml"}}';
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Counter Box block. Please wait for page reload.');
					
					break;
				
				/* Divider Block*/
				case "divider":
					$dataInit = ['show_text','show_icon','text_fontweight'];
					$data = $this->reInitData($data, $dataInit);
					$content = '{{block class="Magento\Framework\View\Element\Template" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" width="'.$data['setting']['width'].'" style="'.$data['setting']['style'].'" border_align="'.$data['setting']['border_align'].'" show_text="'.$data['setting']['show_text'].'" text_fontweight="'.$data['setting']['text_fontweight'].'" show_icon="'.$data['setting']['show_icon'].'" fbuilder_icon="'.$this->encodeHtml($data['setting']['icon']).'" template="MGS_Fbuilder::widget/divider.phtml"}}'; 
					
					$data['custom_style_temp']['divider-style'] = [
						'border_width' => $data['setting']['border_width'],
						'border_color' => $data['setting']['border_color'],
						'show_text' => $data['setting']['show_text'],
						'text_font_size' => $data['setting']['text_font_size'],
						'text_color' => $data['setting']['text_color'],
						'text_background' => $data['setting']['text_background'],
						'text_padding' => $data['setting']['text_padding'],
						'show_text' => $data['setting']['show_text'],
						'style' => $data['setting']['style'],
						'show_icon' => $data['setting']['show_icon'],
						'icon_font_size' => $data['setting']['icon_font_size'],
						'icon_color' => $data['setting']['icon_color'],
						'icon_background' => $data['setting']['icon_background'],
						'icon_border' => $data['setting']['icon_border'],
						'icon_padding' => $data['setting']['icon_padding']
					];
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Divider block. Please wait for page reload.');
					
					break;
				
				/* Heading Block*/
				case "heading":
					$dataInit = ['heading_fontweight', 'heading_italic', 'heading_uppercase', 'show_border'];
					$data = $this->reInitData($data, $dataInit);
					$content = '{{block class="Magento\Framework\View\Element\Template" heading="'.$data['setting']['heading'].'" fbuilder_subtitle="'.$this->encodeHtml($data['setting']['subtitle']).'" heading_align="'.$data['setting']['heading_align'].'" heading_fontweight="'.$data['setting']['heading_fontweight'].'" heading_italic="'.$data['setting']['heading_italic'].'" heading_uppercase="'.$data['setting']['heading_uppercase'].'" show_border="'.$data['setting']['show_border'].'" border_style="'.$data['setting']['border_style'].'" border_position="'.$data['setting']['border_position'].'" template="MGS_Fbuilder::widget/heading.phtml"}}'; 
					
					$data['custom_style_temp']['heading-style'] = [
						'heading_font_size' => $data['setting']['heading_font_size'],
						'heading_color' => $data['setting']['heading_color'],
						'heading_background' => $data['setting']['heading_background'],
						'show_border' => $data['setting']['show_border'],
						'border_position' => $data['setting']['border_position'],
						'border_color' => $data['setting']['border_color'],
						'border_width' => $data['setting']['border_width'],
						'border_margin' => $data['setting']['border_margin']
					];
					
					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Heading block. Please wait for page reload.');
					break;
				
				/* Lookbook Block*/
				case "lookbook":
					$content = '{{widget type="MGS\Lookbook\Block\Widget\Lookbook" lookbook_id="'.$data['setting']['lookbook_id'].'" template="MGS_Lookbook::widget/lookbook.phtml"}}'; 

					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Lookbook block. Please wait for page reload.');
					break;
				
				/* Lookbook Block*/
				case "lookbook_slider":
					$content = '{{widget type="MGS\Lookbook\Block\Widget\Slider" slider_id="'.$data['setting']['slide_id'].'" template="MGS_Lookbook::widget/slider.phtml"}}';

					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Lookbook slider block. Please wait for page reload.');
					break;
				
				/* Latest Post Block*/
				case "latest_post":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_thumbnail', 'hide_description', 'hide_create', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					
					if(isset($data['setting']['post_category'])){
						$categories = implode(',',$data['setting']['post_category']);
					}else{
						$data['setting']['post_category'] = [];
					}
					
					$content = '{{widget type="MGS\Blog\Block\Widget\Latest" limit="'.$data['setting']['limit'].'" post_category="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_thumbnail="'.$data['setting']['hide_thumbnail'].'" hide_description="'.$data['setting']['hide_description'].'" character_count="'.$data['setting']['character_count'].'" hide_create="'.$data['setting']['hide_create'].'"';
					
					if($data['setting']['template']=='list.phtml'){
						$content .= ' numbercol="'.$data['setting']['numbercol'].'" percol="'.$data['setting']['percol'].'"';
					}
					
					if($data['setting']['template']=='grid.phtml'){
						$content .= ' perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/blog/'.$data['setting']['template'].'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Latest Post block. Please wait for page reload.');
					break;
				
				/* Portfolio Block*/
				case "portfolio":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_categories', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					$categories = '';
					if(isset($data['setting']['category_ids'])){
						$categories = implode(',',$data['setting']['category_ids']);
					}else{
						$data['setting']['category_ids'] = [];
					}
					
					$content = '{{block class="MGS\Portfolio\Block\Widget" limit="'.$data['setting']['limit'].'" category_ids="'.$categories.'" use_slider="'.$data['setting']['use_slider'].'" hide_categories="'.$data['setting']['hide_categories'].'" perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';

					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/portfolio.phtml"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Portfolio block. Please wait for page reload.');
					break;
				
				/* Testimonial Block*/
				case "testimonial":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_photo', 'hide_name', 'hide_info', 'content_italic', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);

					
					$content = '{{block class="MGS\Testimonial\Block\Testimonial" testimonials_count="'.$data['setting']['limit'].'" hide_photo="'.$data['setting']['hide_photo'].'" hide_name="'.$data['setting']['hide_name'].'" hide_info="'.$data['setting']['hide_info'].'" content_italic="'.$data['setting']['content_italic'].'" use_slider="'.$data['setting']['use_slider'].'" perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';

					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/tetimonials.phtml"}}';
					
					$data['custom_style_temp']['testimonial'] = [
						'name_font_size' => $data['setting']['name_font_size'],
						'name_color' => $data['setting']['name_color'],
						'info_font_size' => $data['setting']['info_font_size'],
						'info_color' => $data['setting']['info_color'],
						'content_font_size' => $data['setting']['content_font_size'],
						'content_color' => $data['setting']['content_color']
					];
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Testimonial block. Please wait for page reload.');
					break;
					
				/* Mageplaza Banner Slider */
				case "mageplaza_slider":
					$dataInit = ['autoplay', 'stop_auto', 'navigation', 'pagination', 'loop', 'fullheight', 'rtl', 'hide_nav'];
                        
					$data = $this->reInitData($data, $dataInit);
					$content = '{{block class="Mageplaza\BannerSlider\Block\Widget" slider_id="'.$data['setting']['slider_id'].'" autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" fullheight="'.$data['setting']['fullheight'].'" pagination="'.$data['setting']['pagination'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" speed="'.$data['setting']['speed'].'" items="'.$data['setting']['items'].'" items_tablet="'.$data['setting']['items_tablet'].'" items_mobile="'.$data['setting']['items_mobile'].'" slide_margin="'.$data['setting']['slide_margin'].'" template="MGS_Fbuilder::widget/mageplaza/bannerslider.phtml"}}'; 

					$data['block_content'] = $content;
					$result['message'] = 'success';
					$sessionMessage = __('You saved the Banner Slider block. Please wait for page reload.');
					break;
					
				/* Mageplaza Blog Post */
				case "mageplaza_blog":
					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					
					
					$content = '{{widget type="Mageplaza\Blog\Block\Widget\Posts" post_count="'.$data['setting']['limit'].'" show_type="'.$data['setting']['show_type'].'" use_slider="'.$data['setting']['use_slider'].'" perrow="'.$data['setting']['perrow'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					
					if($data['setting']['show_type']=='category'){
						$content .= ' category_id="'.$data['setting']['category_id'].'"';
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/mageplaza/blog_posts.phtml"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved Blog posts block. Please wait for page reload.');
					break;
				
				/* Mageplaza Countdown Timer */
				case "mageplaza_countdown":

					$content = '{{block class="Mageplaza\CountdownTimer\Block\Widget" rule_id ="'.$data['setting']['rule_id'].'"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved Countdown Timer block. Please wait for page reload.');
					break;
					
				/* Mageplaza Daily Deal */
				case "mageplaza_deal":

					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					
					
					$content = '{{block class="Mageplaza\DailyDeal\Block\Widget" limit="'.$data['setting']['limit'].'" type="'.$data['setting']['type'].'" perrow="'.$data['setting']['perrow'].'" use_slider="'.$data['setting']['use_slider'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/mageplaza/deal.phtml"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved Countdown Timer block. Please wait for page reload.');
					break;
				
				/* Mageplaza Shop by Brand */
				case "mageplaza_brand":

					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					
					$type = $data['setting']['show_type'];
					
					$content = '{{block class="MGS\Fbuilder\Block\Mageplaza\Shopbybrand\\'.$type.'" perrow="'.$data['setting']['perrow'].'" use_slider="'.$data['setting']['use_slider'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					
					if($type=='OptionId'){
						if(isset($data['setting']['option_id']) && count($data['setting']['option_id'])>0){
							$options = implode(',',$data['setting']['option_id']);
							$content .= ' option_id="'.$options.'"';
						}else{
							$result['message'] = __('Have no brand to display.');
						}
					}elseif($type=='CategoryId'){
						if(isset($data['setting']['category_id']) && count($data['setting']['category_id'])>0){
							$categories = implode(',',$data['setting']['category_id']);
							$content .= ' category_id="'.$categories.'"';
						}else{
							$result['message'] = __('Have no brand to display.');
						}
					}
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/mageplaza/brand.phtml"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved Brand block. Please wait for page reload.');
					break;
					
				/* Mageplaza Product Slider */
				case "mageplaza_productslider":

					$dataInit = ['use_slider', 'autoplay', 'stop_auto', 'rtl', 'navigation', 'loop', 'pagination', 'hide_nav'];
					$data = $this->reInitData($data, $dataInit);
					
					$type = $data['setting']['product_type'];
					
					$content = '{{block class="MGS\Fbuilder\Block\Mageplaza\Productslider" product_type="'.$type.'" products_count="'.$data['setting']['products_count'].'" perrow="'.$data['setting']['perrow'].'" use_slider="'.$data['setting']['use_slider'].'" perrow_tablet="'.$data['setting']['perrow_tablet'].'" perrow_mobile="'.$data['setting']['perrow_mobile'].'"';
					
					if($data['setting']['use_slider']){
						$content .= ' autoplay="'.$data['setting']['autoplay'].'" stop_auto="'.$data['setting']['stop_auto'].'" navigation="'.$data['setting']['navigation'].'" hide_nav="'.$data['setting']['hide_nav'].'" nav_top="'.$data['setting']['nav_top'].'" navigation_position="'.$data['setting']['navigation_position'].'" pagination_position="'.$data['setting']['pagination_position'].'" pagination="'.$data['setting']['pagination'].'" number_row="'.$data['setting']['number_row'].'" slide_by="'.$data['setting']['slide_by'].'" loop="'.$data['setting']['loop'].'" rtl="'.$data['setting']['rtl'].'" slide_margin="'.$data['setting']['slide_margin'].'"';
					}
					
					$content .= ' template="MGS_Fbuilder::widget/mageplaza/product_slider.phtml"}}';
					
					$data['block_content'] = $content;
					
					$result['message'] = 'success';
					$sessionMessage = __('You saved Brand block. Please wait for page reload.');
					break;
			}
			if($result['message']=='success'){
				$this->saveBlockData($data, $sessionMessage);
				$this->cacheManager->clean(['full_page']);
			}else{
				return $this->getMessageHtml('danger', $result['message'], false);
			}
		}
		else{
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			return $resultRedirect;
		}
    }
	
	public function uploadFile($field, $folder){
		$uploader = $this->_fileUploaderFactory->create(['fileId' => $field]);
		$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
		$uploader->setAllowRenameFiles(true);
		$uploader->setFilesDispersion(true);
		
		$path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/'.$folder.'/');
		$uploader->save($path);
		return $uploader;
	}
	
	/* Save data to childs table */
	public function saveBlockData($data, $sessionMessage){
		$model = $this->getModel('MGS\Fbuilder\Model\Child');
		$data['setting'] = json_encode($data['setting']);
		
		if(isset($data['remove_background']) && ($data['remove_background']==1) && isset($data['old_background'])){
			$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/backgrounds') . $data['old_background'];
			if ($this->_file->isExists($filePath))  {
				$this->_file->deleteFile($filePath);
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

		if(!isset($data['child_id'])){
			$storeId = $this->_storeManager->getStore()->getId();
			$data['store_id'] = $storeId;
			$data['position'] = $this->getNewPositionOfChild($data['store_id'], $data['block_name']);
		}
		
		if(!isset($data['background_repeat'])){
			$data['background_repeat'] = 0;
		}
		if(!isset($data['background_gradient'])){
			$data['background_gradient'] = 0;
		}
		if(!isset($data['background_cover'])){
			$data['background_cover'] = 0;
		}
		
		$model->setData($data);
		if(isset($data['child_id'])){
			$id = $data['child_id'];
			unset($data['child_id']);
			$model->setId($id);
		}
		try {
			// save the data
			$model->save();
			
			$customStyle = '';
			if(isset($data['custom_style_temp']['tab-style'])){
				//print_r($data['custom_style_temp']['tab-style']); die();
				foreach($data['custom_style_temp']['tab-style'] as $tabStyle=>$styleInfo){
					if(($styleInfo['font-size']!='') && ($styleInfo['font-size']>0)){
						$customStyle .= '.block'.$model->getId().' .mgs-tab.data.items > .item.title > .switch{font-size:'.$styleInfo['font-size'].'px;}';
						
						if($tabStyle=='tab-style1'){
							$height = $styleInfo['font-size'] + 4;
							$customStyle .= '.block'.$model->getId().' .mgs-tab.data.items > .item.title > .switch{height:'.$height.'px !important; line-height:'.$height.'px !important}';
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .mgs-tab.data.items .item.title .switch::before{height: '.$height.'px; top:1px}';
						}
						
						if($tabStyle=='tab-style2' || $tabStyle=='tab-style3'){
							$borderRadius = $styleInfo['font-size'] + 10;
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch{border-radius:' . $borderRadius .'px;}';
						}
					}

					if(($tabStyle=='tab-style1') || ($tabStyle=='tab-style2') || ($tabStyle=='tab-style4') || ($tabStyle=='tab-style5') || ($tabStyle=='tab-style7')){
						if($styleInfo['third-color']!=''){
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch:before{background: '.$styleInfo['third-color'].';}';
							
							$customStyle .= '@media (max-width:767px) {.mgs-product-tab .mgs-tab.data.items > .item.title > .switch{border:1px solid '.$styleInfo['third-color'].'}}';
							
							if($tabStyle=='tab-style2'){
								$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title.active .switch{border-color: '.$styleInfo['third-color'].';}';
							}
							
							if($tabStyle=='tab-style4'){
								$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title.active .switch::after{background-color: '.$styleInfo['third-color'].';}';
							}
							
							if($tabStyle=='tab-style5'){
								$customStyle .= '.block'.$model->getId().' .mgs-product-tab .tab-style5.data.items > .item.content{border-color: '.$styleInfo['third-color'].';}';
							}
						}
						
						if($styleInfo['secondary-color']!=''){
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch{color: '.$styleInfo['secondary-color'].' !important;}';
						}
						
						if($styleInfo['primary-color']!=''){
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title.active .switch, .block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch:hover{color: '.$styleInfo['primary-color'].' !important}';
							
							if($tabStyle=='tab-style5'){
								$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title.active .switch:after{background-color: '.$styleInfo['primary-color'].';}';
							}
						}
						
						
					}
					
					if($tabStyle=='tab-style3'){
						if($styleInfo['third-color']!=''){
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch{border-color: '.$styleInfo['third-color'].'}';
						}
						
						if($styleInfo['secondary-color']!=''){
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch{color: '.$styleInfo['secondary-color'].'}';
						}
						
						if($styleInfo['primary-color']!=''){
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title.active .switch,.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch:hover{background-color: '.$styleInfo['primary-color'].' !important; border-color:'.$styleInfo['primary-color'].' !important}';
						}
					}
					
					if($tabStyle=='tab-style6'){
						if($styleInfo['third-color']!=''){
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch{border-color: '.$styleInfo['third-color'].'}';
						}
						
						if($styleInfo['secondary-color']!=''){
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch{background-color: '.$styleInfo['secondary-color'].'}';
						}
						
						if($styleInfo['primary-color']!=''){
							$customStyle .= '.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title.active .switch,.block'.$model->getId().' .mgs-product-tab .'.$tabStyle.'.data.items .item.title .switch:hover{background-color: '.$styleInfo['primary-color'].' !important;}';
						}
					}
				}
			}
			
			
			
			if(isset($data['custom_style_temp']['category-style'])){
				if(isset($data['custom_style_temp']['category-style']['grid'])){
					$savedStyle = $data['custom_style_temp']['category-style']['grid'];
					
					if($savedStyle['other-font-size']!=''){
						$customStyle .= '.block'.$model->getId().' .category-grid-block .category-item .widget-category-infor .category-product-count{font-size:'.$savedStyle['other-font-size'].'px}';
					}
					
					if($savedStyle['font-size']!=''){
						$customStyle .= '.block'.$model->getId().' .category-grid-block .category-item .widget-category-infor .category-name{font-size:'.$savedStyle['font-size'].'px}';
					}
					
					if($savedStyle['primary-color']!=''){
						$customStyle .= '.block'.$model->getId().' .category-grid-block .category-item .widget-category-infor .category-name{color:'.$savedStyle['primary-color'].'}';
					}
					
					if($savedStyle['fifth_color']!=''){
						$customStyle .= '.block'.$model->getId().' .category-grid-block .category-item .widget-category-infor .category-name:hover{color:'.$savedStyle['fifth_color'].'}';
					}
					
					if($savedStyle['secondary-color']!=''){
						$customStyle .= '.block'.$model->getId().' .category-grid-block .category-item .widget-category-infor .category-product-count{color:'.$savedStyle['secondary-color'].'}';
					}
					
					if($savedStyle['third-color']!=''){
						$customStyle .= '.block'.$model->getId().' .category-grid-block .category-item .widget-category-infor .category-product-count .number{color:'.$savedStyle['third-color'].'}';
					}
				}else{
					$savedStyle = $data['custom_style_temp']['category-style']['list'];
					
					if($savedStyle['other-font-size']!=''){
						$customStyle .= '.block'.$model->getId().' .category-list-block .list-heading h3{font-size:'.$savedStyle['other-font-size'].'px}';
					}
					
					if($savedStyle['font-size']!=''){
						$customStyle .= '.block'.$model->getId().' .category-list-block ul li a{font-size:'.$savedStyle['font-size'].'px}';
					}
					
					if($savedStyle['fifth_color']!=''){
						$customStyle .= '.block'.$model->getId().' .category-list-block ul li a:hover{color:'.$savedStyle['fifth_color'].'}';
					}
					
					if($savedStyle['secondary-color']!=''){
						$customStyle .= '.block'.$model->getId().' .category-list-block .list-heading h3{color:'.$savedStyle['secondary-color'].'}';
					}
					
					if($savedStyle['third-color']!=''){
						$customStyle .= '.block'.$model->getId().' .category-list-block .list-heading h3{background-color:'.$savedStyle['third-color'].'}';
					}
					
					if($savedStyle['fourth-color']!=''){
						$customStyle .= '.block'.$model->getId().' .category-list-block .list-heading h3,.block'.$model->getId().' .category-list-block ul li,.block'.$model->getId().' .category-list-block{border-color:'.$savedStyle['fourth-color'].'}';
					}
				}
			}
			
			if(isset($data['custom_style_temp']['banner-style'])){
				$bannerStyle = $data['custom_style_temp']['banner-style'];
				
				if($bannerStyle['text-color']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-promobanner .banner-text{color:'.$bannerStyle['text-color'].'}';
				}
				
				if($bannerStyle['button-background']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-promobanner .banner-button button.btn-promo-banner{background-color:'.$bannerStyle['button-background'].'}';
				}
				
				if($bannerStyle['button-color']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-promobanner .banner-button button.btn-promo-banner span{color:'.$bannerStyle['button-color'].'}';
				}
				
				if($bannerStyle['button-border']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-promobanner .banner-button button.btn-promo-banner{border-color:'.$bannerStyle['button-border'].'}';
				}
				
				if($bannerStyle['button-hover-background']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-promobanner .banner-button button.btn-promo-banner:hover{background-color:'.$bannerStyle['button-hover-background'].'}';
				}
				
				if($bannerStyle['button-hover-color']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-promobanner .banner-button button.btn-promo-banner:hover span{color:'.$bannerStyle['button-hover-color'].'}';
				}
				
				if($bannerStyle['button-hover-border']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-promobanner .banner-button button.btn-promo-banner:hover{border-color:'.$bannerStyle['button-hover-border'].'}';
				}
			}

			if(isset($data['custom_style_temp']['countdown-style'])){
				$countdownStyle = $data['custom_style_temp']['countdown-style'];
				if($countdownStyle['date_font_size']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .countdown span b{font-size:'.$countdownStyle['date_font_size'].'px}';
				}
				
				if($countdownStyle['date_fontweight']){
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .countdown span b{font-weight:bold}';
				}
				
				if($countdownStyle['date_color']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .countdown span b{color:'.$countdownStyle['date_color'].'}';
				}
				
				if($countdownStyle['date_background']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .countdown span b{background:'.$countdownStyle['date_background'].'}';
				}
				
				if($countdownStyle['date_border']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .countdown span b{border:1px solid '.$countdownStyle['date_border'].'}';
				}
				
				if($countdownStyle['date_border_size']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .countdown span b{border-width:'.$countdownStyle['date_border_size'].'px}';
				}
				
				if($countdownStyle['date_border_radius']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .countdown span b{border-radius:'.$countdownStyle['date_border_radius'].'px}';
				}
				
				if($countdownStyle['date_background']!='' || $countdownStyle['date_border']!=''){
					$size = 20;
					if($countdownStyle['date_border']!=''){
						$size = 22;
						if($countdownStyle['date_border_size']!=''){
							$size = 20 + $countdownStyle['date_border_size'];
						}
					}
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .countdown span b{padding:20px;}';
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown{padding:'.$size.'px 0;}';
					if($countdownStyle['position']=='top'){
						$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .time-text{margin-bottom:10px}';
					}else{
						$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .time-text{margin-top:10px}';
					}
				}
				
				if($countdownStyle['text_font_size']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .time-text{font-size:'.$countdownStyle['text_font_size'].'px}';
				}
				
				if($countdownStyle['text_color']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-countdown-block .countdown-timer .time-text{color:'.$countdownStyle['text_color'].'}';
				}
			}

			if(isset($data['custom_style_temp']['divider-style'])){
				$dividerStyle = $data['custom_style_temp']['divider-style'];
				if($dividerStyle['border_width']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-divider-block .mgs-divider hr{border-width:'.$dividerStyle['border_width'].'px}';
				}
				if($dividerStyle['border_color']!=''){
					if($dividerStyle['style']=='shadown'){
						list($r, $g, $b) = sscanf($dividerStyle['border_color'], "#%02x%02x%02x");

						$customStyle .= '.block'.$model->getId().' .mgs-divider-block .mgs-divider hr::after{background: -webkit-radial-gradient(50% -50% ellipse,rgba('.$r.','.$g.','.$b.',.5) 0,rgba(255,255,255,0) 65%);background: radial-gradient(ellipse at 50% -50%,rgba('.$r.','.$g.','.$b.',.5) 0,rgba(255,255,255,0) 65%);}';
					}else{
						$customStyle .= '.block'.$model->getId().' .mgs-divider-block .mgs-divider hr{border-color:'.$dividerStyle['border_color'].'}';
					}
				}
				
				if($dividerStyle['show_text']){
					if($dividerStyle['text_font_size']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-text span{font-size:'.$dividerStyle['text_font_size'].'px}';
						
						$marginTop = $dividerStyle['text_font_size']/2;
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-text{height:'.$dividerStyle['text_font_size'].'px;line-height:'.$dividerStyle['text_font_size'].'px;margin-top:-'.$marginTop.'px}';
					}
					if($dividerStyle['text_color']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-text span{color:'.$dividerStyle['text_color'].'}';
					}
					if($dividerStyle['text_background']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-text span{background-color:'.$dividerStyle['text_background'].'}';
					}
					if($dividerStyle['text_padding']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-text span{padding:0 '.$dividerStyle['text_padding'].'px}';
					}
				}
				
				if($dividerStyle['show_icon']){
					if($dividerStyle['icon_font_size']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-icon span, .block'.$model->getId().' .mgs-divider .text-icon-container span.icon{font-size:'.$dividerStyle['icon_font_size'].'px;}';
						
						$marginTop = $dividerStyle['icon_font_size']/2;
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-icon{height:'.$dividerStyle['icon_font_size'].'px;line-height:'.$dividerStyle['icon_font_size'].'px;margin-top:-'.$marginTop.'px; height:'.$dividerStyle['icon_font_size'].'px;}';
						
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-icon span::before,.block'.$model->getId().' .mgs-divider .text-icon-container span.icon:before{margin-top:-'.$marginTop.'px}';
					}
					
					if($dividerStyle['icon_color']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-icon span, .block'.$model->getId().' .mgs-divider .text-icon-container span.icon{color:'.$dividerStyle['icon_color'].'}';
					}
					if($dividerStyle['icon_background']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-icon span, .block'.$model->getId().' .mgs-divider .text-icon-container span.icon{background-color:'.$dividerStyle['icon_background'].'}';
					}
					if($dividerStyle['icon_padding']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-icon span, .block'.$model->getId().' .mgs-divider .text-icon-container span.icon{width:'.$dividerStyle['icon_padding'].'px; height:'.$dividerStyle['icon_padding'].'px}';

						$marginTop = $dividerStyle['icon_padding']/2;
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-icon{height:'.$dividerStyle['icon_padding'].'px; margin-top:-'.$marginTop.'px}';

					}
					if($dividerStyle['icon_border']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-icon span, .block'.$model->getId().' .mgs-divider .text-icon-container span.icon{border:1px solid '.$dividerStyle['icon_border'].';}';
					}
				}
				
				if($dividerStyle['show_icon'] && $dividerStyle['show_text']){
					if($dividerStyle['icon_font_size']==''){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .text-icon-container span.icon{font-size:15px;}';
						$customStyle .= '.block'.$model->getId().' .mgs-divider .text-icon-container span.icon::before{margin-top:-7.5px}';
					}
					
					$textHeight = 20;
					if($dividerStyle['text_font_size']!=''){
						$textHeight = $dividerStyle['text_font_size'];
					}
					$height = $textHeight;
					$iconHeight = $dividerStyle['icon_padding'];
					if($height<$iconHeight){
						$height = $iconHeight;
					}
					$marginTop = $height/2;
					$top = $marginTop/2;
					$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-text{height:'.$height.'px; line-height:'.$height.'px; margin-top:-'.$marginTop.'px}';
					
					if($dividerStyle['icon_padding']>$dividerStyle['text_font_size']){
						$customStyle .= '.block'.$model->getId().' .mgs-divider .divider-text span.text{position:relative; top:-'.$top.'px; background:transparent}';
					}
				}
				
			}
			
			if(isset($data['custom_style_temp']['heading-style'])){
				$headingStyle = $data['custom_style_temp']['heading-style'];
				if($headingStyle['heading_font_size']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-heading .heading{font-size:'.$headingStyle['heading_font_size'].'px}';
				}
				if($headingStyle['heading_color']!=''){
					$customStyle .= '.block'.$model->getId().' .mgs-heading .heading{color:'.$headingStyle['heading_color'].'}';
				}
				
				if($headingStyle['show_border']){
					if(($headingStyle['border_position']=='middle') && ($headingStyle['heading_background']!='')){
						$customStyle .= '.block'.$model->getId().' .mgs-heading.has-border.heading-middle .heading span{background:'.$headingStyle['heading_background'].'}';
					}
					if($headingStyle['border_color']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-heading.has-border .heading::after{border-color:'.$headingStyle['border_color'].'}';
					}
					if($headingStyle['border_width']!=''){
						$customStyle .= '.block'.$model->getId().' .mgs-heading.has-border .heading::after{border-width:'.$headingStyle['border_width'].'px}';
						
						if($headingStyle['border_position']=='middle'){
							$customStyle .= '.block'.$model->getId().' .mgs-heading.has-border .heading::after{margin-top:-'. $headingStyle['border_width']/2 .'px}';
						}else{
							if($headingStyle['border_margin']!=''){
								$customStyle .= '.block'.$model->getId().' .mgs-heading.has-border .heading::after{bottom:-'. $headingStyle['border_margin'] .'px}';
							}
						}
					}
				}
			}
			
			if(isset($data['custom_style_temp']['testimonial'])){
				$testimonialStyle = $data['custom_style_temp']['testimonial'];
				if($testimonialStyle['name_font_size']!=''){
					$customStyle .= '.block'.$model->getId().' .testimonial-content .content .name{font-size:'.$testimonialStyle['name_font_size'].'px}';
				}
				if($testimonialStyle['name_color']!=''){
					$customStyle .= '.block'.$model->getId().' .testimonial-content .content .name{color:'.$testimonialStyle['name_color'].'}';
				}
				if($testimonialStyle['info_font_size']!=''){
					$customStyle .= '.block'.$model->getId().' .testimonial-content .content .infomation{font-size:'.$testimonialStyle['info_font_size'].'px}';
				}
				if($testimonialStyle['info_color']!=''){
					$customStyle .= '.block'.$model->getId().' .testimonial-content .content .infomation{color:'.$testimonialStyle['info_color'].'}';
				}
				if($testimonialStyle['content_font_size']!=''){
					$customStyle .= '.block'.$model->getId().' .testimonial-content .content blockquote{font-size:'.$testimonialStyle['content_font_size'].'px}';
				}
				if($testimonialStyle['content_color']!=''){
					$customStyle .= '.block'.$model->getId().' .testimonial-content .content blockquote{color:'.$testimonialStyle['content_color'].'}';
				}
			}
			
			//if($customStyle!=''){
				$this->getModel('MGS\Fbuilder\Model\Child')->setCustomStyle($customStyle)->setId($model->getId())->save();
				$this->generateBlockCss();
			//}
			
			return $this->getMessageHtml('success', $sessionMessage, true);
		} catch (\Exception $e) {
			return $this->getMessageHtml('danger', $e->getMessage(), false);
		}
	}
	
	public function generateBlockCss(){
		$model = $this->getModel('MGS\Fbuilder\Model\Child');
		$collection = $model->getCollection();
		$customStyle = '';
		foreach($collection as $child){
			if($child->getCustomStyle() != ''){
				$customStyle .= $child->getCustomStyle();
			}
		}
		if($customStyle!=''){
			try{
				$this->builderHelper->generateFile($customStyle, 'blocks.css', $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/fbuilder/css/'));
			}catch (\Exception $e) {
				
			}
		}
	}
	
	/* Set value 0 for not exist data */
	public function reInitData($data, $dataInit){
		foreach($dataInit as $item){
			if(!isset($data['setting'][$item])){
				$data['setting'][$item] = 0;
			}
		}
		return $data;
	}
	
	/* Get position of new block for sort */
	public function getNewPositionOfChild($storeId, $blockName){
		$child = $this->getModel('MGS\Fbuilder\Model\Child')
                ->getCollection()
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('block_name', $blockName)
                ->setOrder('position', 'DESC')
                ->getFirstItem();

        if ($child->getId()) {
            $position = (int) $child->getPosition() + 1;
        } else {
            $position = 1;
        }

        return $position;
	}
	
	/* Show message after save block */
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
	
	public function removePanelImages($type,$data){
		if(isset($data['remove']) && (count($data['remove'])>0)){
			foreach($data['remove'] as $filename){
				$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('wysiwyg/'.$type.'/') . $filename;
				if ($this->_file->isExists($filePath))  {
					$this->_file->deleteFile($filePath);
				}
			}
		}
	}
	
	public function encodeHtml($html){
		$result = str_replace("<","&ltchange;",$html);
		$result = str_replace(">","&gtchange;",$result);
		$result = str_replace('"','&#34change;',$result);
		$result = str_replace("'","&#39change;",$result);
		$result = str_replace(",","&commachange;",$result);
		$result = str_replace("+","&pluschange;",$result);
		$result = str_replace("{","&leftcurlybracket;",$result);
		$result = str_replace("}","&rightcurlybracket;",$result);
		return $result;
	}
	
	public function noSpace($text){
		$result = str_replace(" ","&mgs_space;",$text);
		return $result;
	}
}
