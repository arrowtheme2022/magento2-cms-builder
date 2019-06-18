<?php

namespace MGS\Fbuilder\Model;

class Feed extends \Magento\AdminNotification\Model\Feed
{
    /**
     * @inheritdoc
     */
    const FEED_URL = 'www.magesolution.com/notifications/cms_page_builder.xml';

    /**
     * @inheritdoc
     */
    public function getFeedUrl()
    {
        $httpPath = $this->_backendConfig->isSetFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://';
        if ($this->_feedUrl === null) {
            $this->_feedUrl = $httpPath . self::FEED_URL;
        }
        return $this->_feedUrl;
    }

    /**
     * @inheritdoc
     */
    public function getLastUpdate()
    {
        return $this->_cacheManager->load('builder_notifications_lastcheck');
    }

    /**
     * @inheritdoc
     */
    public function setLastUpdate()
    {
        $this->_cacheManager->save(time(), 'builder_notifications_lastcheck');
        return $this;
    }

}
