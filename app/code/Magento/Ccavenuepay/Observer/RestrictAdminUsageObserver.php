<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
     */
namespace Magento\Ccavenuepay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Ccavenuepay\Helper\Data as CcavenuepaydataHelper;
use Magento\Framework\App\ObjectManager;

class RestrictAdminUsageObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;
	/**
     * @var \Magento\Ccavenuepay\Helper\Data
     */
    protected $_ccavenuepayhelperdata;

    /**
     * @param \Magento\Framework\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization,
		\Magento\Ccavenuepay\Helper\Data $data
    ) {
        $this->_authorization = $authorization;
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		$this->logger->info("EventObserver==__construct11===");
		$this->_authorization 			= $data;
		$this->logger->info("data==data===");
    }

    /**
     * Block admin ability to use customer billing agreements
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $event = $observer->getEvent();
        $methodInstance = $event->getMethodInstance();
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		$this->logger->info("EventObserver==aaaaaaaaa===");
		$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->logger->info("EventObserver==ddddddd===");
		$helper_factory = $object_manager->create('Magento\Ccavenuepay\Helper\Data');
		$this->logger->info("EventObserver==__construct11333===");
		
		$helper_factory1 = $object_manager->create('Magento\Ccavenuepay\Model\cbdom_main');
		//$this->_ccavenuepayhelperdata = $ccavenuepayhelperdata;
		//$helper_factory->test();
		$key='FREE';
		$helper_factory->loadDefaults();
		//$helper_factory->newModuleUpdateNow();
		//$helper_factory->checkModuleUpload();
	//	$helper_factory1->install();
		
		//$helper_factory1->curl_request($post='FREE');
		//$helper_factory->installbzCc($license_key='FREE');
		$this->logger->info("EventObserver==loadDefaults===");
		//$this->ccavenueBzModuleValidation();
    }
	public function ccavenue_bz_module_validation()
	{		
		$_token			= "magento";
		$check_dom 		= Mage::helper('ccavenuepay')->getCcavenueApi($_token);
		
		if($check_dom == false)
		{
			return false;
		}		
		Mage::helper('ccavenuepay')->loadDefaults();
		$payment_module_validate	= base64_decode('aHR0cHM6Ly9ibHVlemVhbC5pbi9tb2R1bGVfdmFsaWRhdGUvc3VjY2Vzcy5waHA=');
		$poststring	= "server_address=".$_SERVER['SERVER_ADDR']."&domain_url=".$_SERVER['HTTP_HOST']."&module_code=CCAVEN_N_M";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$payment_module_validate);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$poststring);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		return true;
	}
	
	
}
