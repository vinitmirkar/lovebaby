<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
     */
namespace Magento\Ccavenuepay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;

class IframeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCodes = [
        Config::METHOD_CODE
    ];

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
	protected $logger;
	protected $paymentMethod;

    /**
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->urlBuilder = $urlBuilder;
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		$this->logger->info("__construct methodCodes===11111111");

        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $this->paymentHelper->getMethodInstance($code);
			$this->logger->info("=======Methodcode=======");
			$this->logger->info([$code,'ll']);
			$this->logger->info("merchant_id===".$this->getConfigData('merchant_id'));
			$paymentMethod=$this->methods[$code];			
        }
		  
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'ccavenuepayIframe' => [],
            ],
        ];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment']['ccavenuepayIframe']['actionUrl'][$code] = $this->getFrameActionUrl($code);
				
            }
        }
		
		$paymentcode = Config::METHOD_CODE;
		//$config['payment']['ccavenuepayIframe']['merchant_id'][$paymentcode] = $this->getConfigData('merchant_id');
		return $config;
    }

    /**
     * Get frame action URL
     *
     * @param string $code
     * @return string
     */
    protected function getFrameActionUrl($code)
    {
		
        $url = '';
        switch($code) {
            case Config::METHOD_CODE:
                $url = $this->urlBuilder->getUrl('ccavenuepay/ccavenuepay/redirect', ['_secure' => true]);
                break;
            
        }

        return $url;
    }
	protected function getConfigData($key)
    {
	
		$merchant_id=2;
		$encryption_key='B2FC8D837D36EF00B163975053A6D23D';
		$access_code='E3YJNHW79AAY2FWF';
		if($key=='merchant_id')
		{
			return $merchant_id;
		}
		elseif($key=='encryption_key')
		{
			return $encryption_key;
		}
		elseif($key=='access_code')
		{
			return $access_code;
		}
	}
	
	
	/**
     * Retrieve gateway url
     *
     * @return string
     */
    protected function getCgiUrl()
    {
        return (bool)$this->getMethodConfigData('sandbox_flag')
            ? $this->getMethodConfigData('cgi_url_test_mode')
            : $this->getMethodConfigData('cgi_url'); 
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("getCgiUrl=====");
		$logger->info($this->getMethodConfigData('cgi_url'));
		$logger->info("merchant_id-----");
		$logger->info($this->getMethodConfigData('merchant_id'));
		$logger->info("getCgiUrl----");
		return $this->getMethodConfigData('cgi_url');
    }
}
