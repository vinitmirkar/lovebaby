<?php
/*
 Payment Name      : CCAvenue MCPG
 Description		  : Extends Payment with  CCAvenue MCPG.
 CCAvenue Version  : MCPG-2.0
 Module Version    : bz-3.0
 Author			  : BlueZeal SoftNet
 Copyright         : © 2009-2017
 */
namespace Magento\Ccavenuepay\Block\Adminhtml\Order\View\Info;

use Magento\Ccavenuepay\Model\Ccavenuepay;

class FraudDetails extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order\Payment
     */
    public function getPayment()
    {
        $order = $this->registry->registry('current_order');
        return $order->getPayment();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return ($this->getPayment()->getMethod() === Ccavenuepay::METHOD_CODE) ? parent::_toHtml() : '';
    }
}
