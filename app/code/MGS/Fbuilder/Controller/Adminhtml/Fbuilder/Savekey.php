<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Controller\Adminhtml\Fbuilder;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
class Savekey extends \MGS\Fbuilder\Controller\Adminhtml\Fbuilder
{
	/**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;
	
	/**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $cacheManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Config\Storage\WriterInterface
     */
    public function __construct(
		\Magento\Backend\App\Action\Context $context, 
		WriterInterface $configWriter,
		CacheManager $cacheManager
	)
    {
        parent::__construct($context);
		$this->configWriter = $configWriter;
		$this->cacheManager = $cacheManager;
    }

    /**
     * Edit sitemap
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
		if($data = $this->getRequest()->getPostValue()){
			$this->configWriter->save('fbuilder/license_key/name', $data['name']);
			$this->configWriter->save('fbuilder/license_key/mail', $data['mail']);
			$this->configWriter->save('fbuilder/license_key/license', $data['key']);
			$this->cacheManager->clean(['config']);
		}
    }
}
