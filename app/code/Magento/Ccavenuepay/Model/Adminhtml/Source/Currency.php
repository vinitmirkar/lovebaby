<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
     */
namespace Magento\Ccavenuepay\Model\Adminhtml\Source;

use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class PaymentAction
 */
class Currency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => "INR",
                'label' => __('Indian Rupee')
            ],
             [
                'value' => "USD",
                'label' => __('United States Dollar')
            ],
             [
                'value' => "SGD",
                'label' => __('Singapore Dollar')
            ],
             [
                'value' => "GBP",
                'label' => __('Pound Sterling')
            ],
             [
                'value' => "EUR",
                'label' => __('Euro')
            ]
        ];
    }
}
