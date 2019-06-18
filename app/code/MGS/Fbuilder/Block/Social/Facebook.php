<?php

namespace MGS\Fbuilder\Block\Social;

use Magento\Framework\View\Element\Template;

class Facebook extends Template{

	public function getFacebookFanBox() {
        $pageUrl = $this->getPageUrl();
        $width = $this->getWidth();
        $height = $this->getHeight();
		$pageTab = $this->getFacebookTabs();
		
        if ($this->getSmallHeader()) {
            $useSmallHeader = 'true';
        } else {
            $useSmallHeader = 'false';
        }
		
		if ($this->getFitInside()) {
			$dataAdaptContainerWidth = 'true';
		}else {
            $dataAdaptContainerWidth = 'false';
        }
		
        if ($this->getHideCover()) {
            $dataHideCover = 'true';
        } else {
            $dataHideCover = 'false';
        }
		
        if ($this->getShowFacepile()) {
            $dataShowFacepile = 'true';
        } else {
            $dataShowFacepile = 'false';
        }
		
        if ($this->getShowPosts()) {
            $dataShowPosts = 'true';
        } else {
            $dataShowPosts = 'false';
        }
		
		if ($this->getHideCallTo()) {
            $dataHideCallTo = 'true';
        } else {
            $dataHideCallTo = 'false';
        }
		
        if ($pageUrl != '' && $width != '' && $height != '') {
            return '<iframe src="https://www.facebook.com/plugins/page.php?href='.$pageUrl.'&tabs='.$pageTab.'&width=' . $width . '&height=' . $height . '&small_header=' . $useSmallHeader . '&adapt_container_width=' . $dataAdaptContainerWidth . '&hide_cover=' . $dataHideCover . '&show_facepile=' . $dataShowFacepile . '" width="' . $width . '" height="' . $height . '" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe>';
        } else {
            return null;
        }
    }
}
?>