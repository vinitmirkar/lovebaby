<?php
/*
 Payment Name      : CCAvenue MCPG
 Description		  : Extends Payment with  CCAvenue MCPG.
 CCAvenue Version  : MCPG-2.0
 Module Version    : bz-3.0
 Author			  : BlueZeal SoftNet
 Copyright         : © 2009-2017
 */
namespace Magento\Payment\Block\Info;

/**
 * Substitution payment info
 */
class Substitution extends \Magento\Payment\Block\Info
{
    /**
     * Add additional info block
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $parentBlock = $this->getParentBlock();
        if (!$parentBlock) {
            return $this;
        }

        $container = $parentBlock->getParentBlock();
        if ($container) {
            $block = $this->_layout->createBlock(
                'Magento\Framework\View\Element\Template',
                '',
                ['data' => ['method' => $this->getMethod(), 'template' => 'Magento_Payment::info/substitution.phtml']]
            );
            $container->setChild('order_payment_additional', $block);
        }
        return $this;
    }
}
