<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
     */
namespace Magento\Ccavenuepay\Model\Ccavenuepay\Request;

use Magento\Ccavenuepay\Model\Request\Factory as CcavenuepayRequestFactory;

/**
 * Factory class for @see \Magento\Ccavenuepay\Model\Ccavenuepay\Request
 */
class Factory extends CcavenuepayRequestFactory
{
    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = 'Magento\Ccavenuepay\Model\Ccavenuepay\Request'
    ) {
        parent::__construct($objectManager, $instanceName);
    }
}
