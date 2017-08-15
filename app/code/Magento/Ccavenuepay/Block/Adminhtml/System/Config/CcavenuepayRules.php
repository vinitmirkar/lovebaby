<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
 */
namespace Magento\Ccavenuepay\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Ccavenuepay\Helper\Data;

/**
 * Class ResolutionRules
 */
class CcavenuepayRules extends Template
{
    
	
	protected $logger;
	protected $helperCcavenuepayData;
    /**
     * Constructor
     *
     * @param Context $context
     * @param Ccavenuepaydata $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
		\Magento\Ccavenuepay\Helper\Data $helperCcavenuepayData,
        array $data = []
    ) {
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		$this->logger->info("getJson==111111111111===");
		$this->helperCcavenuepayData=$helperCcavenuepayData;
        parent::__construct($context, $data);
      
    }

    /**
     * Getting data for generating rules (JSON)
     *
     * @return string
     */
     public function getJson()
    {
		
		$this->logger->info("getJson==111111111111===");
		
		/*
		
		$this->logger->info("getJson==111111111111===");
		$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->logger->info("getJson==\Helper\Data getInstance222222222222===");		
		$helper_factory = $object_manager->get('Magento\Ccavenuepay\Helper\Data');
		$this->logger->info("getJson==getCcavenuepayParams===3333333333333");
		$ccavenuepay_params	= $helper_factory->getCcavenuepayParams();
		
		*/
		$ccavenuepay_params	= $this->helperCcavenuepayData->getCcavenuepayParams();
		$this->logger->info(print_r($ccavenuepay_params,true));
		$this->logger->info("getJson==ccavenuepay_params===");
        return json_encode($ccavenuepay_params);
    } 
	
	public function checkHideBlock()
	{
		return $this->helperCcavenuepayData->ccavenue_bz_module_validation();
	}
	public function getCcavenuepayPdfManualLink()
	{
		return $this->helperCcavenuepayData->getCcavenuepayParams('pdf_manual_link');
	}	
	public function getCcavenuepayVideoLink()
	{
		return $this->helperCcavenuepayData->getCcavenuepayParams('VideoLink');
	}
	public function getModuleCms()
	{
		return $this->helperCcavenuepayData->getCcavenuepayParams('cms');
	}
	public function getLicence()
	{
		return $this->helperCcavenuepayData->getCcavenuepayParams('license_key');
	}
	public function getModuleVer()
	{
		return $this->helperCcavenuepayData->getCcavenuepayParams('module_version');
	}
}
