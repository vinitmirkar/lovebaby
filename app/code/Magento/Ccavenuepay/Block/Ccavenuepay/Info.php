<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
 */

/**
 * Payflow link infoblock
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Ccavenuepay\Block\Ccavenuepay;

class Info extends \Magento\Ccavenuepay\Block\Info\Ccavenuepay
{
    /**
     * Don't show CC type
     *
     * @return false
     */
    public function getCcavenuepayTypeName()
    {
        return false;
    }
}
