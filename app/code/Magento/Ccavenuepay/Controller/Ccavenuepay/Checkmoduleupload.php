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

class Checkmoduleupload extends \Magento\Ccavenuepay\Controller\Ccavenuepay
{
    /**
     * Get response from Ccavenuepay by silent post method
     *
     * @return void
     */
    public function execute()
    {
		
		try {
            $params = $this->getRequest()->getParams();
			
			 if (empty($params['license_key'])) {
				throw new \Magento\Framework\Exception\LocalizedException(
                        __('Error11: No parameters specified')
                    );
                //Mage::throwException('Error: No parameters specified');
            } 
			 if (empty($params['module_version'])) {
				throw new \Magento\Framework\Exception\LocalizedException(
                        __('Error22: No parameters specified')
                    );
               // Mage::throwException('Error: No parameters specified');
            } 
			  if (empty($params['module_name'])) {
				throw new \Magento\Framework\Exception\LocalizedException(
                        __('Error33: No parameters specified')
                    );
               // Mage::throwException('Error: No parameters specified');
            }  
            $response =  $this->_getHelper()->checkModuleUpload($params);
            if (empty($response)) {
				throw new \Magento\Framework\Exception\LocalizedException(
                        __('Error: Connection to ccavnue  domain validation failed')
                    );
               // Mage::throwException('Error: Connection to ccavnue  domain validation failed');
            }
            $this->getResponse()->setBody($response);
            return;
        } catch (Mage_Core_Exception $e) {
            $response = $e->getMessage();
        } catch (Exception $e) {
            $response = 'Error: System error during request';
        }
        $this->getResponse()->setBody($response);
		
    }
}
