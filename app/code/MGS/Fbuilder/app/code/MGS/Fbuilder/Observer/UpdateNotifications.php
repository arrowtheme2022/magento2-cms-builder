<?php
namespace MGS\Fbuilder\Observer;

use Magento\Framework\Event\ObserverInterface;

class UpdateNotifications implements ObserverInterface
{
	protected $_feedFactory;

	/**
	 * @type \Magento\Backend\Model\Auth\Session
	 */
	protected $_backendAuthSession;

	public function __construct(
		\MGS\Fbuilder\Model\FeedFactory $feedFactory,
		\Magento\Backend\Model\Auth\Session $backendAuthSession
	)
	{
		$this->_feedFactory        = $feedFactory;
		$this->_backendAuthSession = $backendAuthSession;
	}

	/**
	 * @param \Magento\Framework\Event\Observer $observer
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if ($this->_backendAuthSession->isLoggedIn()) {
			$feedModel = $this->_feedFactory->create();
			$feedModel->checkUpdate();
		}
	}
}
