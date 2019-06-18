<?php

namespace MGS\Fbuilder\Block\Social;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;


class Twitter extends Template{
	
	public function getTwitterTimeline() {
        $pageUrl = $this->getPageUrl();
        $width = $this->getWidth();
        $height = $this->getHeight();
		$theme = $this->getTheme();
		$color = $this->getDefaultLinkColor();
		$language = $this->getLanguage();
		
        if ($pageUrl != '' && $width != '' && $height != '') {
            return '<a class="twitter-timeline" data-lang="'.$language.'" data-width="'.$width.'" data-height="'.$height.'" data-dnt="true" data-theme="'.$theme.'" data-link-color="'.$color.'" href="'.$pageUrl.'?ref_src=twsrc%5Etfw"></a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script> ';
        } else {
            return null;
        }
    }
}
?>