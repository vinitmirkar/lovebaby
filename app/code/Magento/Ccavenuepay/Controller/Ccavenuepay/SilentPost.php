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

class SilentPost extends \Magento\Ccavenuepay\Controller\Ccavenuepay
{
    /**
     * Get response from ccavenuepay by silent post method
     *
     * @return void
     */
    public function execute()
    {
		$quoteId=$this->_getCheckout()->getLastRealOrder()->getQuoteId();
		$this->_getCcavenuepayPostSession()->setQuoteId($quoteId);
		//print_r($this->_checkoutSession->getQuote());
		$data = $this->getRequest()->getPostValue();
		//$this->_getCcavenuepayPostSession()->setQuoteId('11');
		//echo "getQuoteId=".$this->_getCheckout()->getQuote()->getId();
		$ccavenuepay = $this->_objectManager->get('Magento\Ccavenuepay\Model\Ccavenuepay');
		$order =$this->_getCheckout()->getLastRealOrder();
		$order->setStatus($ccavenuepay->getConfigData('new_order_status'));
		$order->save();
		$encrypted_data= $ccavenuepay->getEncryptedData($order);
		$this->logger->info("===SlientPost encryption data===".$encrypted_data);
		$ccavenueTransactionUrl= $ccavenuepay->getCcavenueTransactionUrl();
		$this->logger->info("===SlientPost Transaction Url===".$ccavenueTransactionUrl);
		$access_code=$ccavenuepay->getConfigData('access_code') ;
		$this->logger->info("===SlientPost Access Code===".$access_code);
		$layout = $this->_view->getLayout();
		$block = $layout->createBlock('Magento\Ccavenuepay\Block\Ccavenuepay\Form');
		$img = $block->getViewFileUrl('Magento_Ccavenuepay::image/ccavenue_bz.jpg');
    	$gif = $block->getViewFileUrl('Magento_Ccavenuepay::image/ajax-loader.gif');
    
		echo '<center>You will be redirected to Ccavenuepay in a few seconds.<br><img src="'.$img.'" border="1" alt="Logo" width="185px" height="70px"><br><br><img src="'.$gif.'" alt="ajax-loader" align="center" width="128px" height="15px"><br>Copyright bluezeal.in</center><form method="post" name="redirect" action="'.$ccavenueTransactionUrl.'"> 
		<input type=hidden name=encRequest value="'.$encrypted_data.'">
		<input type=hidden name=access_code value="'.$access_code.'">
		</form>
		</center>
		<script language="javascript">document.redirect.submit();</script>';
		exit(0);
    }
}
