<?php
/*
 Payment Name      : CCAvenue MCPG
 Description		  : Extends Payment with  CCAvenue MCPG.
 CCAvenue Version  : MCPG-2.0
 Module Version    : bz-3.0
 Author			  : BlueZeal SoftNet
 Copyright         : © 2009-2017
 */
namespace Magento\Ccavenuepay\Controller\Ccavenuepay;

use Magento\Payment\Block\Transparent\Iframe;

use Magento\Ccavenuepay\Controller\Ccavenuepay;
use Magento\Ccavenuepay\Model\Config;
use Magento\Sales\Model\Order;

class Redirect extends  Ccavenuepay
{
    /**
     * Retrieve params and put javascript into iframe
     *
     * @return void
     */
    public function execute()
    {
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Informational message Redirect');
		$helper = $this->dataFactory->create('frontend');

        $redirectParams = $this->getRequest()->getParams();
        $params = [];
        if (!empty($redirectParams['success'])
            && isset($redirectParams['x_invoice_num'])
            && isset($redirectParams['controller_action_name'])
        ) {
            $this->_getDirectPostSession()->unsetData('quote_id');
            $params['redirect_parent'] = $helper->getSuccessOrderUrl([]);
        }
        if (!empty($redirectParams['error_msg'])) {
            $cancelOrder = empty($redirectParams['x_invoice_num']);
            $this->_returnCustomerQuote($cancelOrder, $redirectParams['error_msg']);
        }

        if (isset($redirectParams['controller_action_name'])
            && strpos($redirectParams['controller_action_name'], 'sales_order_') !== false
        ) {
            unset($redirectParams['controller_action_name']);
            unset($params['redirect_parent']);
        }

        $this->_coreRegistry->register(Iframe::REGISTRY_KEY, array_merge($params, $redirectParams));
        $this->_view->addPageLayoutHandles();
        $this->_view->loadLayout(false)->renderLayout();
    }
}
