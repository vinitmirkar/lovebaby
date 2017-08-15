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

class AddFieldsToResponseObserver implements ObserverInterface
{
    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Ccavenuepay\Model\Directpost
     */
    protected $payment;

    /**
     * @var \Magento\Ccavenuepay\Model\Ccavenuepay\Session
     */
    protected $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Ccavenuepay\Model\Ccavenuepay $payment
     * @param \Magento\Ccavenuepay\Model\Ccavenuepay\Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Ccavenuepay\Model\Ccavenuepay $payment,
        \Magento\Ccavenuepay\Model\Ccavenuepay\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->payment = $payment;
        $this->session = $session;
        $this->storeManager = $storeManager;
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
        $order = $this->coreRegistry->registry('ccavenuepay_order');

        if (!$order || !$order->getId()) {
            return $this;
        }

        $payment = $order->getPayment();

        if (!$payment || $payment->getMethod() != $this->payment->getCode()) {
            return $this;
        }

        $result = $observer->getData('result')->getData();

        if (!empty($result['error'])) {
            return $this;
        }

        // if success, then set order to session and add new fields
        $this->session->addCheckoutOrderIncrementId($order->getIncrementId());
        $this->session->setLastOrderIncrementId($order->getIncrementId());

        $requestToAuthorizenet = $payment->getMethodInstance()
            ->generateRequestFromOrder($order);
        $requestToAuthorizenet->setControllerActionName(
            $observer->getData('action')
                ->getRequest()
                ->getControllerName()
				
        );
        $requestToAuthorizenet->setIsSecure(
            (string)$this->storeManager->getStore()
                ->isCurrentlySecure()
        );

        $result[$this->payment->getCode()] = ['fields' => $requestToAuthorizenet->getData()];

        $observer->getData('result')->setData($result);

        return $this;
    }
}
