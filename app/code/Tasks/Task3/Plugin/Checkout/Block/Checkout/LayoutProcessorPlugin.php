<?php
/**
 * Copyright © Scandiweb, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tasks\Task3\Plugin\Checkout\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor as CheckoutLayoutProcessor;

/**
 * Checkout Layout Processor Plugin
 */
class LayoutProcessorPlugin
{
    /**
     * Process js Layout of block is slightly modified for showing backwards field names in checkout shipping step.
     *
     * @param CheckoutLayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(CheckoutLayoutProcessor $subject, array $jsLayout): array
    {
        $shippingAddressFieldsetChildren = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        foreach ($shippingAddressFieldsetChildren as $key => $shippingAddressFieldsetChild) {
            if (isset($shippingAddressFieldsetChild['label'])) {
                $backwardsLabel = strrev($shippingAddressFieldsetChild['label']->getText());
                if ($key === 'company' || $key === 'telephone') {//Removes "Company" and "Phone Number" fields from the Shipping step.
                    unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$key]);

                    continue;
                }

                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$key]['label'] = $backwardsLabel;
            }
        }

        return $jsLayout;
    }
}
