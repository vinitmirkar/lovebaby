<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
     */
namespace Magento\Ccavenuepay\Helper;

use Magento\Payment\Model\Method\Substitution;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;
use Magento\Payment\Block\Form;
use Magento\Payment\Model\InfoInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\MethodInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Ccavenuepay\Model\Cbdom_main;


 
/**
 * Ccavenuepay Data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const HTML_TRANSACTION_ID =
        '<a target="_blank" href="https://www.%1$s.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%2$s">%2$s</a>';
 
	const METHOD_CODE = 'ccavenuepay';
	

    /**
     * Cache for shouldAskToCreateBillingAgreement()
     *
     * @var bool
     */
    protected static $_shouldAskToCreateBillingAgreement = null;

    /**
     * @var \Magento\ccavenuepay\Helper\Data
     */
    protected $_paymentData;

    /**
     * @var \Magento\Ccavenuepay\Model\Billing\AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @var array
     */
    private $methodCodes;

	
	protected $Cbdom;
	 
    /**
     * @var \Magento\Ccavenuepay\Model\ConfigFactory
     */
    private $configFactory;

	protected	$_pgmod_ver		= "3.0";				//==> Module Version
	protected	$_pgcat			= "CCAvenue";			//==>Category
	protected	$_pgcat_ver  	= "MCPG-2.0";			//==>Category Version
	protected 	$_pgcms 		= "Magento";			//==>CMS
	protected	$_pgcms_ver 	= "2.1.1";				//==>CMS Version
	protected	$_pg_lic_key 	= 'FREE';				//Payment module license key
	protected 	$_token			= "magento";
	protected 	$_ccavenuepay_pdf_manual_link			= "";
	protected 	$_ccavenuepay_video_link			= "";
	protected 	$_ccavenuepay_alert_message		= "";	
	protected   $_Cbdom ;
	protected   $logger;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Ccavenuepay\Model\Billing\AgreementFactory $agreementFactory
     * @param \Magento\Ccavenuepay\Model\ConfigFactory $configFactory
     * @param array $methodCodes
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        LayoutFactory $layoutFactory,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Framework\App\Config\Initial $initialConfig,
		\Magento\Ccavenuepay\Model\Cbdom_main $Cbdom_main 
    ) {
		
       
		
		//$this->_Cbdom = Magento\Ccavenuepay\Model\Cbdom_main->create();
		$this->_Cbdom = $Cbdom_main;
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		$this->logger->info("Helper Data extends \Magento\Framework\App\Helper\AbstractHelper");
		 parent::__construct($context);
        $this->_layout = $layoutFactory->create();
        $this->_methodFactory = $paymentMethodFactory;
        $this->_appEmulation = $appEmulation;
        $this->_paymentConfig = $paymentConfig;
        $this->_initialConfig = $initialConfig;
		$this->logger->info("Helper Data extends \Magento\Framework\App\Helper\AbstractHelper2");
    }

    /**
     * Check whether customer should be asked confirmation whether to sign a billing agreement
     *
     * @param \Magento\Ccavenuepay\Model\Config $config
     * @param int $customerId
     * @return bool
     */
    
	
	/**
     * @param string $code
     * @return string
     */
    protected function getMethodModelConfigName($code)
    {
		$this->logger->info("getMethodModelConfigName");
        return sprintf('%s/%s/model', self::METHOD_CODE, $code);
    }

    /**
     * Retrieve method model object
     *
     * @param string $code
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return MethodInterface
     */
    public function getMethodInstance($code)
    {
			$this->logger->info("getMethodInstance");
        $class = $this->scopeConfig->getValue(
            $this->getMethodModelConfigName($code),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$class) {
            throw new \UnexpectedValueException('Payment model name is not provided in config!');
        }

        return $this->_methodFactory->create($class);
    }
 

    /**
     * Get HTML representation of transaction id
     *
     * @param string $methodCode
     * @param string $txnId
     * @return string
     */
	 
    public function getHtmlTransactionId($methodCode, $txnId)
    {
        if (in_array($methodCode, $this->methodCodes)) {
            /** @var \Magento\Ccavenuepay\Model\Config $config */
            $config = $this->configFactory->create()->setMethod($methodCode);
            $sandboxFlag = ($config->getValue('sandboxFlag') ? 'sandbox' : '');
            return sprintf(self::HTML_TRANSACTION_ID, $sandboxFlag, $txnId);
        }
        return $txnId;
    }
	public function getCcavneueReturnUrl(array $params)
    {
		$this->logger->info("getCcavneueReturnUrl");
	   $this->logger->info('getCcavneueReturnUrl');
       return $this->_getUrl('ccavenuepay/ccavenuepay/returnurl', $params);
		 
    }
	/**
     * Check if email is registered at Moneybookers
     *
     * @param array $params
     * @return array
     */
    public function checkModuleUpload($params) {
		$this->logger->info('checkModuleUpload');
		/* $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		$this->logger->info("EventObserver==checkModuleUpload===");
		return true; */
		//$params=[];
		$this->logger->info("====Params array=====");
		$this->logger->info(print_r($params,true));
        $response = null;
        try {
			//$cbdom        	= $this->_Cbdom;
			$classPath=$this->_Cbdom->getCbdomClassPath();
			$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
			$this->logger->info("EventObserver==__construct112===");
			$cbdom = $object_manager->create($classPath);
			$license_key   = $params['license_key'];
            $module_version= $params['module_version'];
			$module_name   = $params['module_name'];
			$response = $cbdom->check_module_uploadapi($license_key,$module_version,$module_name);
			$this->logger->info($license_key);
        } catch (Exception $e) {
            //Mage::log($e->getMessage());
            return null;
        }
		
        return $response;
    }
	
	public function newModuleUpdateNow($params) {
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		$this->logger->info('newModuleUpdateNow');
		//$params=[];
        $response = null;
        try {
		
			
           //$cbdom         	= new Cbdom();
			$classPath=$this->_Cbdom->getCbdomClassPath();
			$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
			$this->logger->info("EventObserver==__construct112===");
			$cbdom = $object_manager->create($classPath);
			$license_key   		= $params['license_key'];
			$module_name		= $params['module_name'];
            $module_ver   		= $params['module_version'];
            $newmodule_version  = $params['newmodule_version'];
            $new_cms_ver   		= $params['new_cms_ver'];
            $new_cat_ver   		= $params['new_cat_ver'];
			$response = $cbdom->updatemodule_newversionnow($license_key,$module_name,$module_ver,$newmodule_version,$new_cms_ver,$new_cat_ver);
			
		} catch (Exception $e) {
            //Mage::log($e->getMessage());
            return null;
        }
        return $response;
    }
	public function loadDefaults()
	{
		$this->logger->info('loadDefaults1111');
		
		if($this->_Cbdom->checkDomExist() == false)
		{
			$this->logger->info('checkDomExist');
			return false;
		}
		//$cbdom        = $this->_Cbdom;
		//$cbdom        = new Cbdom();
		$classPath=$this->_Cbdom->getCbdomClassPath();
		$this->logger->info("getCbdomClassPath====Class===Path");
		$this->logger->info($classPath);
		//echo "====classPath key===".$classPath;
		$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->logger->info("EventObserver==__construct112===");
		
		$cbdom = $object_manager->create($classPath);
		return true;
		$this->logger->info("EventObserver==BZCCPG_LICENCE_KEY===");
		$this->logger->info(BZCCPG_LICENCE_KEY);
		$ccavenuepay_license_key = BZCCPG_LICENCE_KEY;
		$this->logger->info("getCbdomClassPath====Class===Path272");
		if(!empty($ccavenuepay_license_key))
		{
			$license_key = $ccavenuepay_license_key; 
			$this->logger->info("getCbdomClassPath====Class===Path2721111");
			$checked  = $cbdom->check_license($license_key);  // This will call the communication file function check_license
			$this->logger->info($checked );
			$getres = json_decode($checked,true);
			$this->logger->info("getres");
			$this->logger->info($getres );
			///Store License key into the data arry from Databse
			$_ccavenuepay_pdf_manual_link 	= '';
			$_ccavenuepay_video_link 		= '';
			$_ccavenuepay_alert_message 	= '';
			$this->logger->info("getCbdomClassPath====Class===Path2722222222222");
			if(isset($getres['success'])){	
				$this->_ccavenuepay_pdf_manual_link = $getres['module_pdf_link'] ;
				$this->_ccavenuepay_video_link 		= $getres['module_video_link'] ;
				$this->_ccavenuepay_alert_message 	= $getres['module_alert_message'] ;
			}
			$this->logger->info("getCbdomClassPath====Class===Path2723333333333");
			if(!is_array($getres) || array_key_exists('error',$getres))
			{
				$this->logger->info("getCbdomClassPath====Class===Path27244444444444");
				$_POST['ccavenuepay_status'] = 0;
			    $errortxt = "Not installed!!! Error:".$getres['error'];
			    $settings['ccavenuepay_status'] = 0;
			    $cbdom->send_error_mail($errortxt);
			}
			else
			{
				$this->logger->info("getCbdomClassPath====Class===Path272555555555555");
				$settings['ccavenuepay_license_key'] = BZCCPG_LICENCE_KEY;//$this->_pg_lic_key;
				$settings['ccavenuepay_status'] = 0;
				$this->installbzCc($license_key);

				$success = $sucesstxt = 'You need to set configuration!!';
				$settings['ccavenuepay_license_key'] = BZCCPG_LICENCE_KEY;//$this->_pg_lic_key;
				if(isset($_POST['ajax']) && $_POST['ajax'] == 'true')
				{
					$this->logger->info("getCbdomClassPath====Class===Path27266666666666");
					$this->logger->info("ajax==json_encode===");
					echo json_encode(array('error'=>$errortxt,'success'=>$success));
					//exit;					
				}
		   	}
		}
		else 
		{
			// Get the license key from databse
			$license_key = BZCCPG_LICENCE_KEY;//$this->_ccavenuepay_license_key;
			$license_key = $ccavenuepay_license_key;
			if(empty($license_key))
			{
				$_POST['ccavenuepay_status'] = 0;
				$sucesstxt = 'You need to set license key for complete installation!!';
			}
			// API code for checking the inputed license key is in setting array.
			$settings['ccavenuepay_license_key'] = $this->_pg_lic_key;
			if(!empty($settings['ccavenuepay_license_key']))
			{
				$data['ccavenuepay_license_key'] = $settings['ccavenuepay_license_key'];
			}
			$data['ccavenuepay_license_key'] =  $ccavenuepay_license_key;
		}
		$data['ccavenuepay_license_key'] = $this->_pg_lic_key;
		//unset($cbdom);	
		$file = DOM_BZ_PATH_PG_201."Cbdom.php";
		
		return true;
	}
	public function installbzCc($license_key='FREE')
	{
		$object_manager = \Magento\Framework\App\ObjectManager::getInstance();
		$classPath=$this->_Cbdom->getCbdomClassPath();
		$this->logger->info("EventObserver==__construct112===");
		$cbdom = $object_manager->create($classPath);
		$prefix =''; //Mage::getConfig()->getTablePrefix();
		$cbdom->setBZCCLicenceApiTNPrefix($prefix);
		$this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
			->get('Magento\Framework\App\ResourceConnection');
		$connection= $this->_resources->getConnection();
		$this->logger->info("connection==__constr222222222555uct112===");
		$prefix =''; //Mage::getConfig()->getTablePrefix();
		$cbdom->setBZCCLicenceApiTNPrefix($prefix);
		$this->logger->info("connection==connection=dddd222222222==");
		$query_array = $cbdom->installMainApi($license_key='FREE');
		foreach($query_array as $key=>$tmp_query)
		{
			$sql_license_id = $connection->query($tmp_query);//$db->execute();
			if($key == 1)
			{
				$select_lic_sql=$tmp_query;
				$sql_license_id = $connection->fetchOne($select_lic_sql);//$db->loadAssocList();
			}
		}
		if($sql_license_id == false) 
		{
			$count_key = 0;
		}
		else 
		{
			$count_key = count($sql_license_id);
		}
		$res = $cbdom->setRegisterMainApi($count_key,$license_key);
		if(isset($res['sql_update']))
		{
			$connection->query($res['sql_update']);
		}
		unset($res['sql_update']);
		return json_encode($res);
	}
	public function ccavenue_bz_module_validation()
	{		
		$this->logger->info("helper ccavenue_bz_module_validation");
		$_token			= "magento";
		$check_dom 		= $this->getCcavenueApi($_token);
		 if($check_dom == false)
		{
			return false;
		} 
		$this->loadDefaults();
		return true;
	}
	
	 public function getCcavenuepayParams($key='')
	 {
			$CcavenuepayParams =[];
			$CcavenuepayParams = array('module_version'	=>	$this->_pgmod_ver,				//==> Module Version
								'category'				=>	$this->_pgcat,				//==>Category
								'category_version'		=>	$this->_pgcat_ver ,		//==>Category Version
								'cms'					=>	$this->_pgcms 	,			//==>CMS
								'cms_version'			=>	$this->_pgcms_ver ,				//==>CMS Version
								'license_key'			=>	$this->_pg_lic_key,				//Payment module license key
								'token'					=>	$this->_token ,
								'pdf_manual_link'		=>	$this->_ccavenuepay_pdf_manual_link,
								'VideoLink'			=>	$this->_ccavenuepay_video_link,
								'alert_message'			=>	$this->_ccavenuepay_alert_message	);
			$this->logger->info("CcavenuepayParams");
			$this->logger->info($CcavenuepayParams);
			if($key!='')
			{
				if(isset($CcavenuepayParams[$key]))
				{
					return $CcavenuepayParams[$key];
				}
				return '';
			}
			return $CcavenuepayParams;
	 }
	
	public function getCcavenueApi($_token)
	{	
		$this->logger->info("helper getCcavenueApi");
		$key_dom="qdfd1i@uj9";
		$cbdom =$this->_Cbdom ;
		$this->logger->info("helper getCcavenueApi111111111111");
		$dombz= $cbdom->getDomBz($this->_pgmod_ver,$this->_pgcat,$this->_pgcat_ver,$this->_pgcms,$this->_pgcms_ver,$this->_pg_lic_key,$_token,$key_dom);		
		$this->logger->info("helper getCcavenueApi aaaaaaaaaaaaaaaa");
		$this->logger->info(print_r($dombz,true));
		if(count($cbdom->getErrors())>0)
		{
			foreach ($cbdom->getErrors() as $err)
			{
				//Mage::log($err);
				$this->logger->info(print_r($err,true));
				//Mage::getSingleton('core/session')->addError($err);
			}
		}
		$this->logger->info("helper getCcavenueApi 222222222222");
		//unset($cbdom);	
		return $dombz;
	}
	 
}
