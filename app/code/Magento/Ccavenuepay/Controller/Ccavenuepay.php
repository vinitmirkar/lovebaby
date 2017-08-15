<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
 */
namespace Magento\Ccavenuepay\Controller;
use Magento\Ccavenuepay\Helper\Data;
/**
 * Payflow Checkout Controller
 */
abstract class Ccavenuepay extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
	protected $logger;

    /**
     * @var \Magento\Ccavenuepay\Model\Ccavenuepay
     */
    protected $_ccavenuepay;

    /**
     * @var \Magento\Ccavenuepay\Helper\Checkout
     */
    protected $_checkoutHelper;
	
	/**
     * @var \Magento\Ccavenuepay\Helper\data
     */
    protected $_ccavenuepayHelper;

    /**
     * Redirect block name
     * @var string
     */
    protected $_redirectBlockName = 'ccavenuepay.iframe';
	
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Ccavenuepay\Model\PayflowlinkFactory $ccavenuepay
     * @param \Magento\Ccavenuepay\Helper\Checkout $checkoutHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    
	
	
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Ccavenuepay\Model\Ccavenuepay $ccavenuepay,
        \Magento\Ccavenuepay\Helper\Checkout $checkoutHelper,
		\Psr\Log\LoggerInterface $logger
    ) {
	
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		$this->logger->info("__construct=====Ccavenuepay extends \Magento\Framework\App\Action\Action");
	
		
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $logger;
        $this->_ccavenuepay = $ccavenuepay;
        $this->_checkoutHelper = $checkoutHelper;
		//$this->_ccavenuepayHelper = $this->_ccavenuepay;
        parent::__construct($context);
    }
	
	
	
	
    /**
     * Cancel order, return quote to customer
     *
     * @param string $errorMsg
     * @return false|string
     */
    protected function _cancelPayment($errorMsg = '')
    {
        $errorMsg = trim(strip_tags($errorMsg));

        $gotoSection = false;
        $this->_checkoutHelper->cancelCurrentOrder($errorMsg);
        if ($this->_checkoutSession->restoreQuote()) {
            //Redirect to payment step
            $gotoSection = 'paymentMethod';
        }

        return $gotoSection;
    }
	
	public function errorAction()
    {
        $this->_redirect('checkout/onepage/');
    }
	
	//check_module_upload
	//newmoduleupdate_now
	/**
     * Check if email is registered at Moneybookers
     */
	
    
	
	protected function _getCheckout()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Session');
    }
	/**
     * Get session model
     *
     * @return \Magento\Ccavenuepay\Model\Ccavenuepay\Session
     */
    protected function _getCcavenuepayPostSession()
    {
        return $this->_objectManager->get('Magento\Ccavenuepay\Model\Ccavenuepay\Session');
    }
	protected function _getHelper()
    {
        return $this->_ccavenuepay->getHelper();
    }
}
