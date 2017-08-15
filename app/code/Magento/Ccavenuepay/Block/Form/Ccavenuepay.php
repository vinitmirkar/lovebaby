<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
 */
namespace Magento\Ccavenuepay\Block\Form;

class Ccavenuepay extends \Magento\Payment\Block\Form
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Ccavenuepay::form/ccavenuepay.phtml';

    /**
     * Payment config model
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_paymentConfig = $paymentConfig;
    }
	
	/**
     * Retrieve direcpay configuration object
     *
     * @return Mage_Ccavenuepay_Model_Config
     */
    protected function _getCcavenuepayConfig()
    {
        return Mage::getSingleton('ccavenuepay/config');
		
    }
	
	
	/**
     * Retrieve availables credit card types
     *
     * @return array
     */
    public function getCcavenuepayServiceTypes()
    {
        $types = $this->_getCcavenuepayConfig()->getCcavenuepayServiceTypes();
        if ($method = $this->getMethod()) {
            $availableTypes = $method->getConfigData('Ccavenuepaytypes');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
		
        return $types;
    }

    /**
     * Retrieve availables credit card types
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getCcavenuepayAvailableTypes()
    {
        $types = $this->_paymentConfig->getCcavenuepayTypes();
        if ($method = $this->getMethod()) {
            $availableTypes = $method->getConfigData('ccavenuepaytypes');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code => $name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return $types;
    }

    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    public function getCcavenuepayMonths()
    {
        $months = $this->getData('ccavenuepay_months');
        if ($months === null) {
            $months[0] = __('Month');
            $months = array_merge($months, $this->_paymentConfig->getMonths());
            $this->setData('ccavenuepay_months', $months);
        }
        return $months;
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    public function getCcavenuepayYears()
    {
        $years = $this->getData('ccavenuepay_years');
        if ($years === null) {
            $years = $this->_paymentConfig->getYears();
            $years = [0 => __('Year')] + $years;
            $this->setData('ccavenuepay_years', $years);
        }
        return $years;
    }

    /**
     * Retrieve has verification configuration
     *
     * @return bool
     */
    public function hasVerification()
    {
        if ($this->getMethod()) {
            $configData = $this->getMethod()->getConfigData('useccv');
            if ($configData === null) {
                return true;
            }
            return (bool)$configData;
        }
        return true;
    }

    /**
     * Whether switch/solo card type available
     *
     * @return bool
     */
    public function hasSsCardType()
    {
        $availableTypes = explode(',', $this->getMethod()->getConfigData('ccavenuepaytypes'));
        $ssPresenations = array_intersect(['SS', 'SM', 'SO'], $availableTypes);
        if ($availableTypes && count($ssPresenations) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Solo/switch card start year
     *
     * @return array
     */
    public function getSsStartYears()
    {
        $years = [];
        $first = date("Y");

        for ($index = 5; $index >= 0; $index--) {
            $year = $first - $index;
            $years[$year] = $year;
        }
        $years = [0 => __('Year')] + $years;
        return $years;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('payment_form_block_to_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
