/**
 * Copyright © Scandiweb, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'uiRegistry',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator'
], function (
    $,
    urlBuilder,
    quote,
    checkoutDataResolver,
    registry,
    checkoutData,
    setShippingInformationAction,
    stepNavigator
) {
    'use strict';

    var mixin = {

        /**
         * Set shipping information handler. Redirects to Cart Page when clicking on "Next" step button, if the shipping form is validated.
         */
        setShippingInformation: function () {
            if (this.validateShippingInformation()) {
                quote.billingAddress(null);
                checkoutDataResolver.resolveBillingAddress();
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var shippingAddressData = checkoutData.getShippingAddressFromData();

                    if (shippingAddressData) {
                        checkoutProvider.set(
                            'shippingAddress',
                            $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                        );
                    }
                });
                setShippingInformationAction().done(
                    function () {
                        var cartPageUrl = urlBuilder.build('checkout/cart');
                        window.location.href = cartPageUrl;
                    }
                );
            }
        },

        /**
         * @return {Boolean}
         */
        validateShippingInformation: function () {
            return this._super();
        }
    };

    return function (target) {//target == Result that Magento_Checkout/js/view/shipping returns.
        return target.extend(mixin); //All other modules will receive this new result.
    };
});
