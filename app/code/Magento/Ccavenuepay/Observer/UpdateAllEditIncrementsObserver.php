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
use Magento\Sales\Model\Order;

class UpdateAllEditIncrementsObserver implements ObserverInterface
{
    /**
     *
     * @var \Magento\Ccavenuepay\Helper\Data
     */
    protected $ccavenuepayData;

    /**
     * @param \Magento\Ccavenuepay\Helper\Data $ccavenuepayData
     */
    public function __construct(
        \Magento\Ccavenuepay\Helper\Data $ccavenuepayData
    ) {
        $this->ccavenuepayData = $ccavenuepayData;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order Order */
        $order = $observer->getEvent()->getData('order');
        $this->ccavenuepay->updateOrderEditIncrements($order);

        return $this;
    }
}
