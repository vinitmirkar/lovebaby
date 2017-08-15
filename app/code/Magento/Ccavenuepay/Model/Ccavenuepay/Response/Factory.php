<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
     */
namespace Magento\Ccavenuepay\Model\Ccavenuepay\Response;

use Magento\Ccavenuepay\Model\Response\Factory as CcavenuepayResponseFactory;

/**
 * Factory class for @see \Magento\Ccavenuepay\Model\Ccavenuepay\Response
 */
class Factory extends CcavenuepayResponseFactory
{
    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = 'Magento\Ccavenuepay\Model\Ccavenuepay\Response'
    ) {
        parent::__construct($objectManager, $instanceName);
    }
}
