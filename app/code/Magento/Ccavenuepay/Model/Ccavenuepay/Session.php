<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
     */
namespace Magento\Ccavenuepay\Model\Ccavenuepay;

use Magento\Framework\Session\SessionManager;

/**
 * Authorize.net Ccavenuepay session model
 */
class Session extends SessionManager
{
    /**
     * Add order IncrementId to session
     *
     * @param string $orderIncrementId
     * @return void
     */
    public function addCheckoutOrderIncrementId($orderIncrementId)
    {
        $orderIncIds = $this->getCcavenuepayOrderIncrementIds();
        if (!$orderIncIds) {
            $orderIncIds = [];
        }
        $orderIncIds[$orderIncrementId] = 1;
        $this->setCcavenuepayOrderIncrementIds($orderIncIds);
    }

    /**
     * Remove order IncrementId from session
     *
     * @param string $orderIncrementId
     * @return void
     */
    public function removeCheckoutOrderIncrementId($orderIncrementId)
    {
        $orderIncIds = $this->getCcavenuepayOrderIncrementIds();

        if (!is_array($orderIncIds)) {
            return;
        }

        if (isset($orderIncIds[$orderIncrementId])) {
            unset($orderIncIds[$orderIncrementId]);
        }
        $this->setCcavenuepayOrderIncrementIds($orderIncIds);
    }

    /**
     * Return if order incrementId is in session.
     *
     * @param string $orderIncrementId
     * @return bool
     */
    public function isCheckoutOrderIncrementIdExist($orderIncrementId)
    {
        $orderIncIds = $this->getCcavenuepayOrderIncrementIds();
        if (is_array($orderIncIds) && isset($orderIncIds[$orderIncrementId])) {
            return true;
        }
        return false;
    }

    /**
     * Set quote id to session
     *
     * @param int|string $id
     * @return $this
     */
    public function setQuoteId($id)
    {
        $this->storage->setQuoteId($id);
        return $this;
    }
}
