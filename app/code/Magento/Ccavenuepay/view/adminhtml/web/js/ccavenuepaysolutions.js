/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'uiClass',
    'Magento_Ccavenuepay/js/ccavenuepaysolution',
    'underscore'
], function ($, Class, Ccavenuepaysolution, _) {
    'use strict';

    return Class.extend({
        defaults: {

            /**
             * Initialized ccavenuepaysolutions
             */
            ccavenuepaysolutions: {},

            /**
             * The elements of created ccavenuepaysolutions
             */
            ccavenuepaysolutionsElements: {},

            /**
             * The selector element responsible for configuration of payment method (CSS class)
             */
            buttonConfiguration: '.button.action-configure'
        },

        /**
         * Constructor
         *
         * @param {Object} config
         * @returns {exports.initialize}
         */
        initialize: function (config) {
            this.initConfig(config)
                .initSolutions();

            return this;
        },

        /**
         * Initialization and configuration ccavenuepaysolutions
         *
         * @returns {exports.initSolutions}
         */
        initSolutions: function () {
            _.each(this.config.ccavenuepaysolutions, this.addSolution, this);
            this.initializeSolutions()
                .wipeButtonsConfiguration();
            _.each(this.ccavenuepaysolutions, this.applicationRules);
			
            return this;
        },

        /**
         * The creation and addition of the ccavenuepaysolution according to the configuration
         *
         * @param {Object} ccavenuepaysolution
         * @param {String} identifier
         */
        addSolution: function (ccavenuepaysolution, identifier) {
            this.ccavenuepaysolutions[identifier] = new Ccavenuepaysolution({
                config: ccavenuepaysolution,
                buttonConfiguration: this.buttonConfiguration
            }, identifier);
            this.ccavenuepaysolutionsElements[identifier] = this.ccavenuepaysolutions[identifier].$self;
        },

        /**
         * Wiping buttons configuration of the payment method
         */
        wipeButtonsConfiguration: function () {
            $(this.buttonConfiguration).removeClass('disabled')
                .removeAttr('disabled');
        },

        /**
         * Application of the rules
         *
         * @param {Object} ccavenuepaysolution
         */
        applicationRules: function (ccavenuepaysolution) {
            _.each(ccavenuepaysolution.afterLoadRules, function (rule) {
                rule.apply();
            });
        },

        /**
         * Initialize ccavenuepaysolutions
         *
         * @returns {exports.initializeSolutions}
         */
        initializeSolutions: function () {
            _.each(this.ccavenuepaysolutions, function (solution) {
                solution.setSolutionsElements(this.ccavenuepaysolutionsElements)
                    .initEvents()
                    .addListeners();
            }, this);

            return this;
        }
    });
});
