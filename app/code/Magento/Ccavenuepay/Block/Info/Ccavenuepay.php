<?php
/*
 Payment Name      : CCAvenue MCPG
 Description		  : Extends Payment with  CCAvenue MCPG.
 CCAvenue Version  : MCPG-2.0
 Module Version    : bz-3.0
 Author			  : BlueZeal SoftNet
 Copyright         : © 2009-2017
 */
namespace Magento\Ccavenuepay\Block\Info;

/**
 * Credit card generic payment info
 */
class Ccavenuepay extends \Magento\Payment\Block\Info
{
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
     * Retrieve credit card type name
     *
     * @return string
     */
    public function getCcavenuepayTypeName()
    {
        $types = $this->_paymentConfig->getCcavenuepayTypes();
        $ccavenuepayType = $this->getInfo()->getCcavenuepayType();
        if (isset($types[$ccavenuepayType])) {
            return $types[$ccavenuepayType];
        }
        return empty($ccavenuepayType) ? __('N/A') : $ccavenuepayType;
    }

    /**
     * Whether current payment method has credit card expiration info
     *
     * @return bool
     */
    public function hasCcavenuepayExpDate()
    {
        return (int)$this->getInfo()->getCcavenuepayExpMonth() || (int)$this->getInfo()->getCcavenuepayExpYear();
    }

    /**
     * Retrieve Ccavenuepay expiration month
     *
     * @return string
     */
    public function getCcavenuepayExpMonth()
    {
        $month = $this->getInfo()->getCcavenuepayExpMonth();
        if ($month < 10) {
            $month = '0' . $month;
        }
        return $month;
    }

    /**
     * Retrieve Ccavenuepay expiration date
     *
     * @return \DateTime
     */
    public function getCcavenuepayExpDate()
    {
        $date = new \DateTime('now', new \DateTimeZone($this->_localeDate->getConfigTimezone()));
        $date->setDate($this->getInfo()->getCcavenuepayExpYear(), $this->getInfo()->getCcavenuepayExpMonth() + 1, 0);
        return $date;
    }

    /**
     * Prepare credit card related payment info
     *
     * @param \Magento\Framework\DataObject|array $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        if ($ccavenuepayType = $this->getCcavenuepayTypeName()) {
            $data[(string)__('Credit Card Type')] = $ccavenuepayType;
        }
        if ($this->getInfo()->getCcavenuepayLast4()) {
            $data[(string)__('Credit Card Number')] = sprintf('xxxx-%s', $this->getInfo()->getCcavenuepayLast4());
        }

        if (!$this->getIsSecureMode()) {
            if ($ccavenuepaySsIssue = $this->getInfo()->getCcavenuepaySsIssue()) {
                $data[(string)__('Switch/Solo/Maestro Issue Number')] = $ccavenuepaySsIssue;
            }
            $year = $this->getInfo()->getCcavenuepaySsStartYear();
            $month = $this->getInfo()->getCcavenuepaySsStartMonth();
            if ($year && $month) {
                $data[(string)__('Switch/Solo/Maestro Start Date')] = $this->_formatCardDate($year, $month);
            }
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }

    /**
     * Format year/month on the credit card
     *
     * @param string $year
     * @param string $month
     * @return string
     */
    protected function _formatCardDate($year, $month)
    {
        return sprintf('%s/%s', sprintf('%02d', $month), $year);
    }
}
