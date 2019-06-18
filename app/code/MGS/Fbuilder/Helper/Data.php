<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Fbuilder\Helper;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Contact base helper
 */
class Data extends \MGS\Core\Helper\Data
{
	protected $_storeManager;
	
	protected $_date;
	
	protected $_url;
	
	protected $_filesystem;
	
	protected $_request;
	
	protected $_acceptToUsePanel = false;
	
	protected $_useBuilder = false;
	
	protected $_customer;
	
	/**
	 * @var \Magento\Framework\Xml\Parser
	 */
	private $_parser;
	
	/**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;
	
    protected $filterManager;
	
	/**
     * Block factory
     *
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;
	/**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;
	
	protected $_file;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    protected $_fullActionName;
	
    protected $_currentCategory;
	
    protected $_currentProduct;
	
    protected $scopeConfig;
	
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
	
	
	/**
	 * Escaper
	 *
	 * @var \Magento\Framework\Escaper
	 */
	protected $_escaper;

	
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Url $url,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\Element\Context $context,
		\Magento\Cms\Model\BlockFactory $blockFactory,
		\Magento\Cms\Model\PageFactory $pageFactory,
		\Magento\Framework\Filesystem\Driver\File $file,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Framework\Escaper $_escaper,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		CustomerSession $customerSession
	) {
		$this->scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $storeManager;
		$this->_date = $date;
		$this->_url = $url;
		$this->_filesystem = $filesystem;
		$this->customerSession = $customerSession;
		$this->_objectManager = $objectManager;
		$this->_request = $request;
		$this->filterManager = $context->getFilterManager();
		$this->_assetRepo = $context->getAssetRepository();
		$this->_blockFactory = $blockFactory;
		$this->_pageFactory = $pageFactory;
		$this->_file = $file;
		$this->_parser = $parser;
		$this->_escaper = $_escaper;
		$this->_filterProvider = $filterProvider;
		
		$this->_fullActionName = $this->_request->getFullActionName();
	}
	
	public function getModel($model){
		return $this->_objectManager->create($model);
	}
	
	/**
     * Retrieve current url in base64 encoding
     *
     * @return string
     */
	public function getCurrentBase64Url()
    {
		return strtr(base64_encode($this->_url->getCurrentUrl()), '+/=', '-_,');
    }
	
	/**
     * base64_decode() for URLs decoding
     *
     * @param    string $url
     * @return   string
     */
    public function decode($url)
    {
        $url = base64_decode(strtr($url, '-_,', '+/='));
        return $this->_url->sessionUrlVar($url);
    }

    /**
     * Returns customer id from session
     *
     * @return int|null
     */
    public function getCustomerId()
    {
		$customerInSession = $this->_objectManager->create('Magento\Customer\Model\Session');
        return $customerInSession->getCustomerId();
    }
	
	/* Get current customer */
	public function getCustomer(){
		if(!$this->_customer){
			$this->_customer = $this->getModel('Magento\Customer\Model\Customer')->load($this->getCustomerId());
		}
		return $this->_customer;
	}
	
	public function getStore(){
		return $this->_storeManager->getStore();
	}
	
	public function getFullActionName(){
		return $this->_request->getFullActionName();
	}
	
	/* Get system store config */
	public function getStoreConfig($node, $storeId = NULL){
		if($storeId != NULL){
			return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		}
		return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
	}
	
	// Check to accept to use builder panel
    public function acceptToUsePanel() {
		if($this->_acceptToUsePanel){
			return true;
		}else{
			if ($this->showButton() && ($this->customerSession->getUseFrontendBuilder() == 1)) {
				$this->_acceptToUsePanel = true;
				return true;
			}
			$this->_acceptToUsePanel = false;
			return false;
		}
        
    }

	/* Check to visible panel button */
    public function showButton() {
        if ($this->getStoreConfig('fbuilder/general/is_enabled')) {
            $customer = $this->getCustomer();
			if($customer->getIsFbuilderAccount() == 1){
				return true;
			}
			return false;
        }

        return false;
    }
	
	public function getCurrentDateTime(){
		$now = $this->_date->gmtDate();
		return $now;
	}
	
	public function getUrlBuilder(){
		return $this->_url;
	}
	
	public function getPanelCssUrl(){
		return $this->_url->getUrl('fbuilder/index/panelstyle');
	}
	
	/* Get css content of panel */
	public function getPanelStyle(){
		$dir = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/MGS/Mpanel/view/frontend/web/css/panel.css');
		$content = file_get_contents($dir);
		return $content;
	}
	
	/* Check current page is homepage or not */
	public function isHomepage(){
		if ($this->_fullActionName == 'cms_index_index') {
			return true;
		}
		return false;
	}
	
	/* Check current page is homepage or not */
	public function isCmsPage(){
		if ($this->_fullActionName == 'cms_page_view') {
			return true;
		}
		return false;
	}
	
	/* Get Animation Effect */
	public function getAnimationEffect(){
		return [
			'bounce' => 'Bounce',
			'flash' => 'Flash',
			'pulse' => 'Pulse',
			'rubberBand' => 'Rubber Band',
			'shake' => 'Shake',
			'swing' => 'Swing',
			'tada' => 'Tada',
			'wobble' => 'Wobble',
			'bounceIn' => 'Bounce In',
			'fadeIn' => 'Fade In',
			'fadeInDown' => 'Fade In Down',
			'fadeInDownBig' => 'Fade In Down Big',
			'fadeInLeft' => 'Fade In Left',
			'fadeInLeftBig' => 'Fade In Left Big',
			'fadeInRight' => 'Fade In Right',
			'fadeInRightBig' => 'Fade In Right Big',
			'fadeInUp' => 'Fade In Up',
			'fadeInUpBig' => 'Fade In Up Big',
			'flip' => 'Flip',
			'flipInX' => 'Flip In X',
			'flipInY' => 'Flip In Y',
			'lightSpeedIn' => 'Light Speed In',
			'rotateIn' => 'Rotate In',
			'rotateInDownLeft' => 'Rotate In Down Left',
			'rotateInDownRight' => 'Rotate In Down Right',
			'rotateInUpLeft' => 'Rotate In Up Left',
			'rotateInUpRight' => 'Rotate In Up Right',
			'rollIn' => 'Roll In',
			'zoomIn' => 'Zoom In',
			'zoomInDown' => 'Zoom In Down',
			'zoomInLeft' => 'Zoom In Left',
			'zoomInRight' => 'Zoom In Right',
			'zoomInUp' => 'Zoom In Up',
		];
	}
	
	public function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->_request->isSecure()], $params);
            return $this->_assetRepo->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);
            return $this->_getNotFoundUrl();
        }
    }
	
	public function getColorAccept($type, $color = NULL) {
		$dir = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/MGS/Fbuilder/view/frontend/web/images/panel/colour/');
        $html = '';
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                $html .= '<ul>';

                while ($files[] = readdir($dh));
                sort($files);
                foreach ($files as $file) {
                    $file_parts = pathinfo($dir . $file);
                    if (isset($file_parts['extension']) && $file_parts['extension'] == 'png') {
                        $colour = str_replace('.png', '', $file);
                        $wrapper = str_replace('_', '-', $type);
						$_color = explode('.', $colour);
                        $colour = $wrapper . '-' . strtolower(end($_color));
                        $html .= '<li>';
                        $html .= '<a href="#" onclick="changeInputColor(\'' . $colour . '\', \'' . $type . '\', this, \'' . $wrapper . '-content\'); return false"';
                        if ($color != NULL && $color == $colour) {
                            $html .= ' class="active"';
                        }
                        $html .= '>';
                         $html .= '<img src="' . $this->getViewFileUrl('MGS_Fbuilder::images/panel/colour/'.$file) . '" alt=""/>';
                        $html .= '</a>';
                        $html .= '</li>';
                    }
                }
                $html .= '</ul>';
            }
        }
        return $html;
    }
	
	public function getRootCategory(){
		$store = $this->getStore();
		$categoryId = $store->getRootCategoryId();
		$category = $this->getModel('Magento\Catalog\Model\Category')->load($categoryId);
		return $category;
	}
	
	public function getTreeCategory($category, $parent, $ids = array(), $checkedCat){
		$rootCategoryId = $this->getRootCategory()->getId();
		$children = $category->getChildrenCategories();
		$childrenCount = count($children);
		//$checkedCat = explode(',',$checkedIds);
		$htmlLi = '<li lang="'.$category->getId().'">';
		$html[] = $htmlLi;
		//if($this->isCategoryActive($category)){
		$ids[] = $category->getId();
		//$this->_ids = implode(",", $ids);
		//}
		
		$html[] = '<a id="node'.$category->getId().'">';

		if($category->getId() != $rootCategoryId){
			$html[] = '<input lang="'.$category->getId().'" type="checkbox" id="radio'.$category->getId().'" name="setting[category_id][]" value="'.$category->getId().'" class="checkbox'.$parent.'"';
			if(in_array($category->getId(), $checkedCat)){
				$html[] = ' checked="checked"';
			}
			$html[] = '/>';
		}
		

		$html[] = '<label for="radio'.$category->getId().'">' . $category->getName() . '</label>';

		$html[] = '</a>';
		
		$htmlChildren = '';
		if($childrenCount>0){
			foreach ($children as $child) {
				$_child = $this->getModel('Magento\Catalog\Model\Category')->load($child->getId());
				$htmlChildren .= $this->getTreeCategory($_child, $category->getId(), $ids, $checkedCat);
			}
		}
		if (!empty($htmlChildren)) {
            $html[] = '<ul id="container'.$category->getId().'">';
            $html[] = $htmlChildren;
            $html[] = '</ul>';
        }

        $html[] = '</li>';
        $html = implode("\n", $html);
        return $html;
	}
	
	public function truncate($content, $length){
		return $this->filterManager->truncate($content, ['length' => $length, 'etc' => '']);
	}
	
	public function convertToLayoutUpdateXml($child){
		$settings = json_decode($child->getSetting(), true);
		$content = $child->getBlockContent();
		$content = preg_replace('/(fbuilder_address_title="")/i', '', $content);
		$content = preg_replace('/(fbuilder_address_title=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_button_text="")/i', '', $content);
		$content = preg_replace('/(fbuilder_button_text=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_text_content="")/i', '', $content);
		$content = preg_replace('/(fbuilder_text_content=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_title="")/i', '', $content);
		$content = preg_replace('/(fbuilder_title=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_note="")/i', '', $content);
		$content = preg_replace('/(fbuilder_note=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_coundown_date="")/i', '', $content);
		$content = preg_replace('/(fbuilder_coundown_date=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_days="")/i', '', $content);
		$content = preg_replace('/(fbuilder_days=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_hours="")/i', '', $content);
		$content = preg_replace('/(fbuilder_hours=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_minutes="")/i', '', $content);
		$content = preg_replace('/(fbuilder_minutes=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_seconds="")/i', '', $content);
		$content = preg_replace('/(fbuilder_seconds=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_saved_text="")/i', '', $content);
		$content = preg_replace('/(fbuilder_saved_text=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(accordion_content="")/i', '', $content);
		$content = preg_replace('/(accordion_content=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(accordion_label="")/i', '', $content);
		$content = preg_replace('/(accordion_label=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_address="")/i', '', $content);
		$content = preg_replace('/(fbuilder_address=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_line_one="")/i', '', $content);
		$content = preg_replace('/(fbuilder_line_one=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_line_two="")/i', '', $content);
		$content = preg_replace('/(fbuilder_line_two=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_line_three="")/i', '', $content);
		$content = preg_replace('/(fbuilder_line_three=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_line_four="")/i', '', $content);
		$content = preg_replace('/(fbuilder_line_four=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_line_five="")/i', '', $content);
		$content = preg_replace('/(fbuilder_line_five=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_profile_name="")/i', '', $content);
		$content = preg_replace('/(fbuilder_profile_name=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_subtitle="")/i', '', $content);
		$content = preg_replace('/(fbuilder_subtitle=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(fbuilder_icon="")/i', '', $content);
		$content = preg_replace('/(fbuilder_icon=".+?)+(")/i', '', $content);
		
		$content = preg_replace('/(labels=".+?)+(")/i', '', $content);
		
		//return $content;
		$arrContent = explode(' ',$content);
		$arrContent = array_filter($arrContent);
		
		$class = $arrContent[1];
		$class = str_replace('type=','class=',$class);
		unset($arrContent[0], $arrContent[1]);
		
		$lastData = end($arrContent);
		array_pop($arrContent);
		
		$arrContent = array_values($arrContent);

		$argumentString = '&nbsp;&nbsp;&nbsp;&nbsp;&lt;arguments&gt;<br/>';
		
		if(isset($settings['address_title']) && ($settings['address_title']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_address_title" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['address_title'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['title']) && ($settings['title']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_title" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['title'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['text_content']) && ($settings['text_content']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_text_content" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['text_content'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['button_text']) && ($settings['button_text']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_button_text" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['button_text'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['additional_content']) && ($settings['additional_content']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_note" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['additional_content'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['coundown_date']) && ($settings['coundown_date']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_coundown_date" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['coundown_date'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['days']) && ($settings['days']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_days" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['days'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['hours']) && ($settings['hours']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_hours" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['hours'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['minutes']) && ($settings['minutes']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_minutes" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['minutes'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['seconds']) && ($settings['seconds']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_seconds" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['seconds'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['saved_text']) && ($settings['saved_text']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_saved_text" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['saved_text'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['address']) && ($settings['address']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_address" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['address'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['line_one']) && ($settings['line_one']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_line_one" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['address'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['line_two']) && ($settings['line_two']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_line_two" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['line_two'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['line_three']) && ($settings['line_three']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_line_three" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['line_three'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['line_four']) && ($settings['line_four']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_line_four" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['line_four'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['line_five']) && ($settings['line_five']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_line_five" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['line_five'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['profile_name']) && ($settings['profile_name']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_profile_name" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['profile_name'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['subtitle']) && ($settings['subtitle']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_subtitle" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['subtitle'])).'&lt;/argument&gt;<br/>';
		}
		
		if(isset($settings['icon']) && ($settings['icon']!='')){
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="fbuilder_icon" xsi:type="string"&gt;'.htmlspecialchars($this->encodeHtml($settings['icon'])).'&lt;/argument&gt;<br/>';
		}

		
		if(isset($settings['tabs']) && ($settings['tabs']!='')){
			usort($settings['tabs'], function ($item1, $item2) {
				if ($item1['position'] == $item2['position']) return 0;
				return $item1['position'] < $item2['position'] ? -1 : 1;
			});
			$tabType = $tabLabel = [];
			foreach($settings['tabs'] as $tab){
				$tabLabel[] = $tab['label'];
			}
			$labels = implode(',',$tabLabel);
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="labels" xsi:type="string"&gt;'.$this->_escaper->escapeHtml($labels).'&lt;/argument&gt;<br/>';
		}
		
		
		if(isset($settings['accordion']) && ($settings['accordion']!='')){
			if(isset($settings['accordion']['position'])){
				usort($settings['accordion'], function ($item1, $item2) {
					if ($item1['position'] == $item2['position']) return 0;
					return $item1['position'] < $item2['position'] ? -1 : 1;
				});
			}
			
			$accordionContent = $accordionLabel = [];
			foreach($settings['accordion'] as $accordion){
				if(isset($accordion['label'])){
					$accordionLabel[] = $this->encodeHtml($accordion['label']);
				}
				$accordionContent[] = $this->encodeHtml($accordion['content']);
			}
			
			if(isset($settings['accordion']['label'])){
				$labels = implode(',',$accordionLabel);
				$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="accordion_label" xsi:type="string"&gt;'.$this->_escaper->escapeHtml($labels).'&lt;/argument&gt;<br/>';
			}
			
			$accordionData = implode(',',$accordionContent);
			$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="accordion_content" xsi:type="string"&gt;'.$this->_escaper->escapeHtml($accordionData).'&lt;/argument&gt;<br/>';
		}
		
		
		$template = '';

		foreach($arrContent as $argument){
			$argumentData = explode('=',$argument);
			if($argumentData[0]!='template' && isset($argumentData[0]) && isset($argumentData[1])){
				$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="'.$argumentData[0].'" xsi:type="string"&gt;'.str_replace('"','',$argumentData[1]).'&lt;/argument&gt;<br/>';
			}else{
				$template = $argumentData[1];
			}
			
		}
		
		$html = '&lt;block '.$class;
		
		$lastDataArr = explode('=',$lastData);
		if(isset($lastDataArr[0]) && isset($lastDataArr[1])){
			if($lastDataArr[0]=='template'){
				$template = str_replace('}}','',$lastDataArr[1]);
			}else{
				$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name="'.$lastDataArr[0].'" xsi:type="string"&gt;'.str_replace('"','',str_replace('}}','',$lastDataArr[1])).'&lt;/argument&gt;<br/>';
			}
		}
		
		$html .= ' template='.$template;
		$argumentString .= '&nbsp;&nbsp;&nbsp;&nbsp;&lt;/arguments&gt;';
		$html .= '&gt;<br/>';
		$html .= $argumentString;
		$html .= '<br/>&lt;/block&gt;';
		
		return $html;
	}
	
	/* Get all images from pub/media/wysiwyg/$type folder */
	public function getPanelUploadImages($type){
		$path = 'wysiwyg/'.$type.'/';
		$dir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path);
		$result = [];
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while ($files[] = readdir($dh));
                sort($files);
                foreach ($files as $file) {
                    $file_parts = pathinfo($dir . $file);
                    if (isset($file_parts['extension']) && in_array(strtolower($file_parts['extension']), ['jpg', 'jpeg', 'png', 'gif'])) {
                        $result[] = $file;
                    }
                }
            }
        }
        return $result;
	}
	
	/* Convert short code to insert image */
	public function convertImageWidgetCode($type, $image){
		return '&lt;img src="{{media url="wysiwyg/'.$type.'/'.$image.'"}}" alt=""/&gt;';
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
	
	public function decodeHtmlTag($content){
		$result = str_replace("&ltchange;","<",$content);
		$result = str_replace("&gtchange;",">",$result);
		$result = str_replace('&#34change;','"',$result);
		$result = str_replace("&#39change;","'",$result);
		$result = str_replace("&commachange;",",",$result);
		$result = str_replace("&pluschange;","+",$result);
		$result = str_replace("&leftcurlybracket;","{",$result);
		$result = str_replace("&rightcurlybracket;","}",$result);
		$result = str_replace("&mgs_space;"," ",$result);
		return $result;
	}
	
	public function getCmsBlockByIdentifier($identifier){
		$block = $this->_blockFactory->create();
		$block->setStoreId($this->getStore()->getId())->load($identifier);
		return $block;
	}
	
	public function getPageById($id){
		$page = $this->_pageFactory->create();
		$page->setStoreId($this->getStore()->getId())->load($id, 'identifier');
		return $page;
	}
	
	public function isFile($path, $type, $fileName){
		$path = str_replace('Mgs/','',$path);
		$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/'.$path.'/'.$type.'s/') . $fileName.'.png';
		if ($this->_file->isExists($filePath))  {
			return $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'mgs/'.$path.'/'.$type.'s/' . $fileName.'.png';
		}
		return false;
	}
	
	public function getBackgroundImageUrl($backgroundImageName){
		return $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).'mgs/fbuilder/backgrounds/'.$backgroundImageName;
	}
	
	public function isPopup(){
		if (
			$this->_fullActionName == 'fbuilder_edit_section' || 
			$this->_fullActionName == 'fbuilder_create_block' || 
			$this->_fullActionName == 'fbuilder_create_element' || 
			$this->_fullActionName == 'fbuilder_edit_footer' || 
			$this->_fullActionName == 'fbuilder_edit_header' || 
			$this->_fullActionName == 'fbuilder_edit_staticblock'
		) {
			return true;
		}
		return false;
	}
	
	public function getMediaUrl(){
		return $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
	}
	
	public function convertContent($layoutContent, $builderContent=NULL){
		return $layoutContent;
	}
	
	public function convertColClass($perrowDefault, $perrowTablet=NULL, $perrowDefaultMobile=NULL){
		$class = '';
		switch($perrowDefault){
			case 1:
				$class .= 'col-des-12';
				break;
			case 2:
				$class .= 'col-des-6';
				break;
			case 3:
				$class .= 'col-des-4';
				break;
			case 4:
				$class .= 'col-des-3';
				break;
			case 6:
				$class .= 'col-des-2';
				break;
			default:
				$class .= 'col';
				break;
		}
		
		if($perrowTablet!=NULL){
			switch($perrowTablet){
				case 1:
					$class .= ' col-tb-12';
					break;
				case 2:
					$class .= ' col-tb-6';
					break;
				case 3:
					$class .= ' col-tb-4';
					break;
				case 4:
					$class .= ' col-tb-3';
					break;
				case 6:
					$class .= ' col-tb-2';
					break;
				default:
					$class .= ' col-tb';
					break;
			}
		}
		
		if($perrowDefaultMobile!=NULL){
			switch($perrowDefaultMobile){
				case 1:
					$class .= ' col-mb-12';
					break;
				case 2:
					$class .= ' col-mb-6';
					break;
				case 3:
					$class .= ' col-mb-4';
					break;
				case 4:
					$class .= ' col-mb-3';
					break;
				case 6:
					$class .= ' col-mb-2';
					break;
				default:
					$class .= ' col-mb';
					break;
			}
		}
		
		return $class;
	}
	
	/* Get class clear left */
	public function getClearClass($perrow = NULL, $nb_item){
		if(!$perrow){
			$settings = $this->getThemeSettings();
			$perrow = $settings['catalog']['per_row'];
		}
		$clearClass = '';
		switch($perrow){
			case 2:
				if($nb_item % 2 == 1){
					$clearClass.= " first-row-item first-tb-item first-mb-item";
				}
				return $clearClass;
				break;
			case 3:
				if($nb_item % 3 == 1){
					$clearClass.= " first-row-item first-tb-item";
				}
				if($nb_item % 2 == 1){
					$clearClass.= " first-mb-item";
				}
				return $clearClass;
				break;
			case 4:
				if($nb_item % 4 == 1){
					$clearClass.= " first-row-item";
				}
				if($nb_item % 3 == 1){
					$clearClass.= " first-tb-item";
				}
				if($nb_item % 2 == 1){
					$clearClass.= " first-mb-item";
				}
				return $clearClass;
				break;
			case 5:
				if($nb_item % 5 == 1){
					$clearClass.= " first-row-item";
				}
				if($nb_item % 3 == 1){
					$clearClass.= " first-tb-item";
				}
				if($nb_item % 2 == 1){
					$clearClass.= " first-mb-item";
				}
				return $clearClass;
				break;
			case 6:
				if($nb_item % 6 == 1){
					$clearClass.= " first-row-item";
				}
				if($nb_item % 3 == 1){
					$clearClass.= " first-tb-item";
				}
				if($nb_item % 2 == 1){
					$clearClass.= " first-mb-item";
				}
				return $clearClass;
				break;
			case 7:
				if($nb_item % 7 == 1){
					$clearClass.= " first-row-item";
				}
				if($nb_item % 2 == 1){
					$clearClass.= " first-tb-item first-mb-item";
				}
				return $clearClass;
				break;
			case 8:
				if($nb_item % 8 == 1){
					$clearClass.= " first-row-item";
				}
				if($nb_item % 3 == 1){
					$clearClass.= " first-tb-item";
				}
				if($nb_item % 2 == 1){
					$clearClass.= " first-mb-item";
				}
				return $clearClass;
				break;
		}
		return $clearClass;
	}
	
	public function truncateString($string, $length){
		return $this->filterManager->truncate($string, ['length' => $length+3]);
	}
	
	public function getDirectUrl($var){
		if (filter_var($var, FILTER_VALIDATE_URL)) { 
			return $var;
		}else{
			return $this->_url->getUrl($var);
		}
	}
	
	public function getContentByShortcode($content){
		return $this->_filterProvider->getBlockFilter()->setStoreId($this->_storeManager->getStore()->getId())->filter($content);
	}
}