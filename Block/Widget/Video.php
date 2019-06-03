<?php

namespace MGS\Fbuilder\Block\Widget;

use Magento\Framework\View\Element\Template;

class Video extends Template
{
	public function identifyService($url)
	{
		if (preg_match('%youtube|youtu\.be%i', $url)) {
			return 'youtube';
		}
		elseif (preg_match('%vimeo%i', $url)) {
			return 'vimeo';
		}
		return null;
	}
	/**
	 * Determines which cloud video provider is being used based on the passed url,
	 * and extracts the video id from the url.
	 *
	 * @param string $url The url
	 * @return null|string Null on failure, the video's id on success
	 */
	public function getUrlId($url)
	{
		$service = $this->identifyService($url);
		if ($service == 'youtube') {
			return $this->getYoutubeId($url);
		}
		elseif ($service == 'vimeo') {
			return $this->getVimeoId($url);
		}
		return null;
	}
	/**
	 * Determines which cloud video provider is being used based on the passed url,
	 * extracts the video id from the url, and builds an embed url.
	 *
	 * @param string $url The url
	 * @return null|string Null on failure, the video's embed url on success
	 */
	public function getUrlEmbed($url)
	{
		$service = $this->identifyService($url);
		$id = $this->getUrlId($url);
		if ($service == 'youtube') {
			return $this->getYoutubeEmbed($id);
		}
		elseif ($service == 'vimeo') {
			return $this->getVimeoEmbed($id);
		}
		return null;
	}
	/**
	 * Parses various youtube urls and returns video identifier.
	 *
	 * @param string $url The url
	 * @return string the url's id
	 */
	public function getYoutubeId($url)
	{
		$youtube_url_keys = array('v','vi');
		// Try to get ID from url parameters
		$key_from_params = $this->parseUrlForParams($url, $youtube_url_keys);
		if ($key_from_params) return $key_from_params;
		// Try to get ID from last portion of url
		return $this->parseUrlForLastElement($url);
	}
	/**
	 * Builds a Youtube embed url from a video id.
	 *
	 * @param string $youtube_video_id The video's id
	 * @return string the embed url
	 */
	public function getYoutubeEmbed($youtube_video_id)
	{
		$autoPlay = $this->getAutoplay() ? '1' : '0';
		$autoHide = $this->getHideInfo() ? '1' : '0';
		$showInfo = $this->getHideInfo() ? '0' : '1';
		$control = $this->getHideControl() ? '0' : '1';
		$loop = $this->getLoop() ? '1' : '0';
		$mute = $this->getMute() ? '1' : '0';
		$embed = "http://youtube.com/embed/$youtube_video_id?autoplay=$autoPlay&autohide=$autoHide&showinfo=$showInfo&controls=$control&loop=$loop&mute=$mute";
		return $embed;
	}
	/**
	 * Parses various vimeo urls and returns video identifier.
	 *
	 * @param string $url The url
	 * @return string The url's id
	 */
	public function getVimeoId($url)
	{
		// Try to get ID from last portion of url
		return $this->parseUrlForLastElement($url);
	}
	/**
	 * Builds a Vimeo embed url from a video id.
	 *
	 * @param string $vimeo_video_id The video's id
	 * @return string the embed url
	 */
	public function getVimeoEmbed($vimeo_video_id)
	{
		$autoPlay = $this->getAutoplay() ? '1' : '0';
		$showInfo = $this->getHideInfo() ? '0' : '1';
		$control = $this->getHideControl() ? '0' : '1';
		$loop = $this->getLoop() ? '1' : '0';
		$embed = "http://player.vimeo.com/video/$vimeo_video_id?byline=0&portrait=0&transparent=0&autoplay=$autoPlay&loop=$loop&title=$showInfo&sidedock=$control&mute=1&api=1";
		return $embed;
	}
	/**
	 * Find the first matching parameter value in a url from the passed params array.
	 *
	 * @access private
	 *
	 * @param string $url The url
	 * @param array $target_params Any parameter keys that may contain the id
	 * @return null|string Null on failure to match a target param, the url's id on success
	 */
	private function parseUrlForParams($url, $target_params)
	{
		parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_params );
		foreach ($target_params as $target) {
			if (array_key_exists ($target, $my_array_of_params)) {
				return $my_array_of_params[$target];
			}
		}
		return null;
	}
	/**
	 * Find the last element in a url, without any trailing parameters
	 *
	 * @access private
	 *
	 * @param string $url The url
	 * @return string The last element of the url
	 */
	private function parseUrlForLastElement($url)
	{
		$url_parts = explode("/", $url);
		$prospect = end($url_parts);
		$prospect_and_params = preg_split("/(\?|\=|\&)/", $prospect);
		if ($prospect_and_params) {
			return $prospect_and_params[0];
		} else {
			return $prospect;
		}
		return $url;
	}
}