/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList,
		setPaymentInformationAction, 
		additionalValidators, 
		fullScreenLoader
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'ccavenuepay',
                component: 'Magento_Ccavenuepay/js/view/payment/method-renderer/ccavenuepay'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);


