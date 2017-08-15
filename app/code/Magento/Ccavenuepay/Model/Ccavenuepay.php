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

use Magento\Ccavenuepay\Model\TransactionService;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
 
class Ccavenuepay extends \Magento\Payment\Model\Method\Cc
{
    /**
     * AIM gateway url
     */
	 
    const CGI_URL = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

    const REQUEST_METHOD_CC = 'CC';

    const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';

    const REQUEST_TYPE_AUTH_ONLY = 'AUTH_ONLY';

    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';

    const REQUEST_TYPE_CREDIT = 'CREDIT';

    const REQUEST_TYPE_VOID = 'VOID';

    const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';

    const RESPONSE_DELIM_CHAR = '(~)';

    const RESPONSE_CODE_APPROVED = 1;

    const RESPONSE_CODE_DECLINED = 2;

    const RESPONSE_CODE_ERROR = 3;

    const RESPONSE_CODE_HELD = 4;

    const RESPONSE_REASON_CODE_APPROVED = 1;

    const RESPONSE_REASON_CODE_PENDING_REVIEW_AUTHORIZED = 252;

    const RESPONSE_REASON_CODE_PENDING_REVIEW = 253;

    const RESPONSE_REASON_CODE_PENDING_REVIEW_DECLINED = 254;
	
	const METHOD_CODE= 'ccavenuepay';
	
	var $_code=self::METHOD_CODE;
    /**
     * Transaction fraud state key
     */
    const TRANSACTION_FRAUD_STATE_KEY = 'is_transaction_fraud';
	
    /**
     * Real transaction id key
     */
    const REAL_TRANSACTION_ID_KEY = 'real_transaction_id';

    /**
     * Gateway actions locked state key
     */
    const GATEWAY_ACTIONS_LOCKED_STATE_KEY = 'is_gateway_actions_locked';

    /**
     * @var \Magento\Ccavenuepay\Helper\Data
     */
    protected $dataHelper;

    /**
     * Request factory
     *
     * @var \Magento\Ccavenuepay\Model\RequestFactory
     */
    protected $requestFactory;

    /**
     * Response factory
     *
     * @var \Magento\Ccavenuepay\Model\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Ccavenuepay\Model\TransactionService;
     */
    protected $transactionService;
	
	/**
     * @var OrderSender
     */
    protected $orderSender;
    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = ['merchantAuthentication', 'x_login'];

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;
	 protected $registry;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Ccavenuepay\Helper\Data $dataHelper
     * @param \Magento\Ccavenuepay\Model\Request\Factory $requestFactory
     * @param \Magento\Ccavenuepay\Model\Response\Factory $responseFactory
     * @param \Magento\Ccavenuepay\Model\TransactionService $transactionService
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
	
	
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Ccavenuepay\Helper\Data $dataHelper,
        \Magento\Ccavenuepay\Model\Request\Factory $requestFactory,
        \Magento\Ccavenuepay\Model\Response\Factory $responseFactory,
        TransactionService $transactionService,
        ZendClientFactory $httpClientFactory,
		\Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->transactionService = $transactionService;
        $this->httpClientFactory = $httpClientFactory;
		$this->orderSender = $orderSender;
		$this->registry = $registry;
		
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		$this->logger->info("__construct model");
		
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes())) {
            return false;
        }
        return true;
    }

    /**
     * Return array of currency codes supplied by Payment Gateway
     *
     * @return array
     */
    public function getAcceptedCurrencyCodes()
    {
        if (!$this->hasData('_accepted_currency')) {
            $acceptedCurrencyCodes = $this->dataHelper->getAllowedCurrencyCodes();
            $acceptedCurrencyCodes[] = $this->getConfigData('currency');
            $this->setData('_accepted_currency', $acceptedCurrencyCodes);
        }
        return $this->_getData('_accepted_currency');
    }

    /**
     * Cancel the payment through gateway
     *
     * @param  \Magento\Payment\Model\InfoInterface $payment
     * @return $this
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        return $this->void($payment);
    }

    /**
     * Fetch fraud details
     *
     * @param string $transactionId
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function fetchTransactionFraudDetails($transactionId)
    {
        $responseXmlDocument = $this->transactionService->getTransactionDetails($this, $transactionId);
        $response = new \Magento\Framework\DataObject();

        if (empty($responseXmlDocument->transaction->FDSFilters->FDSFilter)) {
            return $response;
        }

        $response->setFdsFilterAction(
            $this->dataHelper->getFdsFilterActionLabel((string)$responseXmlDocument->transaction->FDSFilterAction)
        );
        $response->setAvsResponse((string)$responseXmlDocument->transaction->AVSResponse);
        $response->setCardCodeResponse((string)$responseXmlDocument->transaction->cardCodeResponse);
        $response->setCavvResponse((string)$responseXmlDocument->transaction->CAVVResponse);
        $response->setFraudFilters($this->getFraudFilters($responseXmlDocument->transaction->FDSFilters));

        return $response;
    }

    /**
     * Get fraud filters
     *
     * @param \Magento\Framework\Simplexml\Element $fraudFilters
     * @return array
     */
    protected function getFraudFilters($fraudFilters)
    {
        $result = [];

        foreach ($fraudFilters->FDSFilter as $filer) {
            $result[] = [
                'name' => (string)$filer->name,
                'action' => $this->dataHelper->getFdsFilterActionLabel((string)$filer->action)
            ];
        }

        return $result;
    }
   
	public function getEncryptedData(\Magento\Sales\Model\Order $order)
    {
		/** @var \Magento\Sales\Model\Order $order */
     $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test1.log');
					$logger = new \Zend\Log\Logger();
					$logger->addWriter($writer);
        $request = $this->requestFactory->create()
            ->setConstantData($this);
		$get_data		=	array();
	    $get_data['merchant_id']		=	$this->getConfigData('merchant_id');
	    if (!empty($order)) {
            $billing = $order->getBillingAddress();
			if (!empty($billing)) {
				$request->setBillingName($billing->getFirstname())//." ".$billing->getLastname())
                    ->setCompany($billing->getCompany())
                    ->setBillingAddress($billing->getStreetLine(1))
                    ->setBillingCity($billing->getCity())
                    ->setBillingState($billing->getRegion())
                    ->setBillingZip($billing->getPostcode())
                    ->setBillingCountry($billing->getCountry())
                    ->setBillingTel($billing->getTelephone())
                    ->setFax($billing->getFax())
                    ->setCustId($order->getCustomerId())
                    ->setCustomerIp($order->getRemoteIp())
                    ->setCustomerTaxId($billing->getTaxId())
                    ->setBillingEmail($order->getCustomerEmail())
                    ->setEmailCustomer($this->getConfigData('email_customer'))
                    ->setMerchantEmail($this->getConfigData('merchant_email'));
					
					$get_data['billing_name']		=	$request->getBillingName();
					$get_data['billing_address']	=	$request->getBillingAddress();
					$get_data['billing_city']		=	$request->getBillingCity();
					$get_data['billing_state']		=	$request->getBillingState();
					$get_data['billing_zip']		=	$request->getBillingZip();
					$get_data['billing_country']	=	'India';
					$get_data['billing_tel']		=	$request->getBillingTel();
					$get_data['billing_email']		=	$request->getBillingEmail();
					
		
            }

            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
			$request->setDeliveryName($shipping->getFirstname())//." ".$shipping->getLastname())
					->setShipToCompany($shipping->getCompany())
                    ->setDeliveryAddress($shipping->getStreetLine(1))
                    ->setDeliveryCity($shipping->getCity())
                    ->setDeliveryState($shipping->getRegion())
                    ->setDeliveryZip($shipping->getPostcode())
                    ->setDeliveryCountry($shipping->getCountry());
					
					$get_data['delivery_name']		=	$request->getDeliveryName();
					$get_data['delivery_address']	=	$request->getDeliveryAddress();
					$get_data['delivery_city']		=	$request->getDeliveryCity();
					$get_data['delivery_state']		=	$request->getDeliveryState();
					$get_data['delivery_zip']		=	$request->getDeliveryZip();
					$get_data['delivery_country']	=	'India';
					//$get_data['delivery_tel']		=	request->getDeliveryName();
            }
/*
 $orderIncrementId = $order->getIncrementId();
        $api->setCurrencyCode($order->getBaseCurrencyCode())
            ->setInvNum($orderIncrementId)
            ->setCustref($orderIncrementId)
            ->setPonum($order->getId());
*/
			$get_data['order_id']			=	$order->getIncrementId();
			$get_data['amount']				=	$order->getGrandTotal();
			$get_data['currency']			=	$order->getOrderCurrencyCode();
			$get_data['redirect_url']		=	$this->getCcavneueReturnUrl();
			$get_data['cancel_url']			=	$this->getCcavneueReturnUrl();
			$get_data['language']			=	'EN';
			$get_data['merchant_param1']	=	$order->getIncrementId();
			$get_data['merchant_param2']	=	$order->getId();
		}
		
					$logger->info("====get_data====");
					$logger->info($get_data);
	 
	 
	$merchant_data='';
	foreach ($get_data as $key => $value){
		$merchant_data.=$key.'='.urlencode($value).'&';
	}
	$encryption_key =$this->getConfigData('encryption_key'); 
	$access_code=$this->getConfigData('access_code') ;
	$encrypted_data=$this->encrypt($merchant_data,$encryption_key); 
	
         return $encrypted_data;
	}
	public function getCcavneueReturnUrl()
    {
       $params = [];
	   return $this->dataHelper->getCcavneueReturnUrl($params);
		 
    }
	 /**
     * Return authorize payment request
     *
     * @return \Magento\Ccavenuepay\Model\Request
     * @return \Magento\Ccavenuepay\Model\Request
     */
    protected function getRequest()
    {
		$request = $this->requestFactory->create()
            ->setXVersion(3.1)
            ->setXDelimData('True')
            ->setXRelayResponse('False')
            ->setXTestRequest($this->getConfigData('test') ? 'TRUE' : 'FALSE')
            ->setXLogin($this->getConfigData('login'))
            ->setXTranKey($this->getConfigData('trans_key'));
        return $request;
    }

    /**
     * Prepare request to gateway
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface $payment
     * @return \Magento\Authorizenet\Model\Request
     * @link http://www.authorize.net/support/AIM_guide.pdf
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function buildRequest(\Magento\Framework\DataObject $payment)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        $this->setStore($order->getStoreId());
        $request = $this->getRequest()
            ->setXType($payment->getAnetTransType())
            ->setXMethod(self::REQUEST_METHOD_CC);

        if ($order && $order->getIncrementId()) {
            $request->setXInvoiceNum($order->getIncrementId());
        }

        if ($payment->getAmount()) {
            $request->setXAmount($payment->getAmount(), 2);
            $request->setXCurrencyCode($order->getOrderCurrencyCode());
        }

        switch ($payment->getAnetTransType()) {
            case self::REQUEST_TYPE_AUTH_CAPTURE:
                $request->setXAllowPartialAuth($this->getConfigData('allow_partial_authorization') ? 'True' : 'False');
                break;
            case self::REQUEST_TYPE_AUTH_ONLY:
                $request->setXAllowPartialAuth($this->getConfigData('allow_partial_authorization') ? 'True' : 'False');
                break;
            case self::REQUEST_TYPE_CREDIT:
                /**
                 * Send last 4 digits of credit card number to authorize.net
                 * otherwise it will give an error
                 */
                $request->setXCardNum($payment->getCcLast4());
                $request->setXTransId($payment->getXTransId());
                break;
            case self::REQUEST_TYPE_VOID:
                $request->setXTransId($payment->getXTransId());
                break;
            case self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE:
                $request->setXTransId($payment->getXTransId());
                break;
            case self::REQUEST_TYPE_CAPTURE_ONLY:
                $request->setXAuthCode($payment->getCcAuthCode());
                break;
        }

        if (!empty($order)) {
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setXFirstName($billing->getFirstname())
                    ->setXLastName($billing->getLastname())
                    ->setXCompany($billing->getCompany())
                    ->setXAddress($billing->getStreetLine(1))
                    ->setXCity($billing->getCity())
                    ->setXState($billing->getRegion())
                    ->setXZip($billing->getPostcode())
                    ->setXCountry($billing->getCountry())
                    ->setXPhone($billing->getTelephone())
                    ->setXFax($billing->getFax())
                    ->setXCustId($order->getCustomerId())
                    ->setXCustomerIp($order->getRemoteIp())
                    ->setXCustomerTaxId($billing->getTaxId())
                    ->setXEmail($order->getCustomerEmail())
                    ->setXEmailCustomer($this->getConfigData('email_customer'))
                    ->setXMerchantEmail($this->getConfigData('merchant_email'));
            }

            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $request->setXShipToFirstName($shipping->getFirstname())
                    ->setXShipToLastName($shipping->getLastname())
                    ->setXShipToCompany($shipping->getCompany())
                    ->setXShipToAddress($shipping->getStreetLine(1))
                    ->setXShipToCity($shipping->getCity())
                    ->setXShipToState($shipping->getRegion())
                    ->setXShipToZip($shipping->getPostcode())
                    ->setXShipToCountry($shipping->getCountry());
            }

            $request->setXPoNum($payment->getPoNumber())
                ->setXTax($order->getBaseTaxAmount())
                ->setXFreight($order->getBaseShippingAmount());
        }

        if ($payment->getCcNumber()) {
            $request->setXCardNum($payment->getCcNumber())
                ->setXExpDate(sprintf('%02d-%04d', $payment->getCcExpMonth(), $payment->getCcExpYear()))
                ->setXCardCode($payment->getCcCid());
        }

        return $request;
    }

    /**
     * Post request to gateway and return response
     *
     * @param \Magento\Authorizenet\Model\Request $request
     * @return \Magento\Authorizenet\Model\Response
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function postRequest(\Magento\Ccavenuepay\Model\Request $request)
    {
        $result = $this->responseFactory->create();
        /** @var \Magento\Framework\HTTP\ZendClient $client */
        $client = $this->httpClientFactory->create();
        $url = $this->getConfigData('cgi_url') ?: self::CGI_URL;
        $debugData = ['url' => $url, 'request' => $request->getData()];
        $client->setUri($url);
        $client->setConfig(['maxredirects' => 0, 'timeout' => 30]);

        foreach ($request->getData() as $key => $value) {
            $request->setData($key, str_replace(self::RESPONSE_DELIM_CHAR, '', $value));
        }

        $request->setXDelimChar(self::RESPONSE_DELIM_CHAR);
        $client->setParameterPost($request->getData());
        $client->setMethod(\Zend_Http_Client::POST);

        try {
            $response = $client->request();
            $responseBody = $response->getBody();
            $debugData['response'] = $responseBody;
        } catch (\Exception $e) {
            $result->setXResponseCode(-1)
                ->setXResponseReasonCode($e->getCode())
                ->setXResponseReasonText($e->getMessage());

            throw new \Magento\Framework\Exception\LocalizedException(
                $this->dataHelper->wrapGatewayError($e->getMessage())
            );
        } finally {
            $this->_debug($debugData);
        }

        $r = explode(self::RESPONSE_DELIM_CHAR, $responseBody);
        if ($r) {
            $result->setXResponseCode((int)str_replace('"', '', $r[0]))
                ->setXResponseReasonCode((int)str_replace('"', '', $r[2]))
                ->setXResponseReasonText($r[3])
                ->setXAvsCode($r[5])
                ->setXTransId($r[6])
                ->setXInvoiceNum($r[7])
                ->setXAmount($r[9])
                ->setXMethod($r[10])
                ->setXType($r[11])
                ->setData('x_MD5_Hash', $r[37])
                ->setXAccountNumber($r[50]);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong in the payment gateway.')
            );
        }
        return $result;
    }

    /**
     * If gateway actions are locked return true
     *
     * @param  \Magento\Payment\Model\InfoInterface $payment
     * @return bool
     */
    protected function isGatewayActionsLocked($payment)
    {
        return $payment->getAdditionalInformation(self::GATEWAY_ACTIONS_LOCKED_STATE_KEY);
    }
	
	public function encrypt($plainText,$key)
	{
		$secretKey = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
	  	$openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
	  	$blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
		$plainPad = $this->pkcs5_pad($plainText, $blockSize);
	  	if (mcrypt_generic_init($openMode, $secretKey, $initVector) != -1) 
		{
		      $encryptedText = mcrypt_generic($openMode, $plainPad);
	      	      mcrypt_generic_deinit($openMode);
		      			
		} 
		return bin2hex($encryptedText);
	}

	public function decrypt($encryptedText,$key)
	{
		$secretKey = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$encryptedText=$this->hextobin($encryptedText);
	  	$openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
		mcrypt_generic_init($openMode, $secretKey, $initVector);
		$decryptedText = mdecrypt_generic($openMode, $encryptedText);
		$decryptedText = rtrim($decryptedText, "\0");
	 	mcrypt_generic_deinit($openMode);
		return $decryptedText;
		
	}
	//*********** Padding Function *********************

	public function pkcs5_pad ($plainText, $blockSize)
	{
	    $pad = $blockSize - (strlen($plainText) % $blockSize);
	    return $plainText . str_repeat(chr($pad), $pad);
	}

	//********** Hexadecimal to Binary function for php 4.0 version ********

	public function hextobin($hexString) 
   	 { 
        	$length = strlen($hexString); 
        	$binString="";   
        	$count=0; 
        	while($count<$length) 
        	{       
        	    $subString =substr($hexString,$count,2);           
        	    $packedString = pack("H*",$subString); 
        	    if ($count==0)
		    {
				$binString=$packedString;
		    } 
        	    
		    else 
		    {
				$binString.=$packedString;
		    } 
        	    
		    $count+=2; 
        	} 
  	        return $binString; 
    } 
	public function getCcavenueTransactionUrl()
	{
		return 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
	}
	
	public function getCcavenueOrderMailSender($order)
	{
		$this->orderSender->send($order);
	}
	public function getHelper()
    {
        return $this->dataHelper;
    }
	public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote=null)
    {
		$check_dom = $this->dataHelper->ccavenue_bz_module_validation();
		if($check_dom == false)
		{
			return false;
		}
        if (is_null($quote)) {
           return false;
        }
		$return = parent::isAvailable($quote);
		if($return==false)return false;
		
		return true;
	}
	
}
