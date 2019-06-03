<?php

namespace MGS\Fbuilder\Block\Widget;

use Magento\Framework\View\Element\Template;

class Chart extends Template
{
	public function getChartJs($blockId){
		$type = $this->getChartType();
		$html = "var ctx".$blockId." = document.getElementById('mgsChart".$blockId."');";
		$labelHtml = '';
		if($type=='line' || $type=='bar' || $type=='radar'){
			$labels = explode(',',$this->decodeHtmlTag($this->getFbuilderTimelineLabel()));
			$items = json_decode($this->decodeHtmlTag($this->getFbuilderChartItem()), true);

			$labelHtml = "[";
			if(count($labels)>0){
				foreach($labels as $label){
					$labelHtml .= "'".$label."',";
				}
				$labelHtml = substr($labelHtml, 0, -1);
			}
			$labelHtml .= "]";
			

			$dataset = '[';

			if(count($items)>0){
				foreach($items as $key=>$item){
					$data = '['.implode(',',$item['point']).']';
					$dataset .= '{ 
						data: '.$data.',
						label: "'.$item['label'].'",
						borderColor: "'.$item['background'].'",';
					if($type=='radar'){
						list($r, $g, $b) = sscanf($item['background'], "#%02x%02x%02x");
						$dataset .= 'backgroundColor: "rgba('.$r.','.$g.','.$b.',.3)",
							fill: true
						},';
					}else{
						$dataset .= 'backgroundColor: "'.$item['background'].'",
							fill: false
						},';
					}
				}
			}

			$dataset .= ']';
			
			
		}else{
			$segments = json_decode($this->decodeHtmlTag($this->getFbuilderSegment()), true);
			$dataset = '[';

			$data = $background = $label = [];
			if(count($segments)>0){
				foreach($segments as $key=>$segment){
					$data[] = $segment['value'];
					$background[] = '"'.$segment['background'].'"';
					$label[] = '"'.$segment['label'].'"';
				}
				$labelHtml = '['. implode(',',$label) .']';
				$backgroundHtml = '['. implode(',',$background) .']';
				$dataHtml = '['. implode(',',$data) .']';
				
				$dataset .= '{
					"data":'.$dataHtml.',
					"backgroundColor":'.$backgroundHtml.'
				}';
			}

			$dataset .= ']';
		}
		
		$html .= "var myChart".$blockId." = new Chart(ctx".$blockId.", {
			type: '".$type."',
			data: {
				labels: ".$labelHtml.",
				datasets: ".$dataset."
			}
		});";
		
		return $html;
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
}