# LokiCheckout_JosephLeedyCustomFees

**This is an add-on package to the LokiCheckout. It is allowing the JosephLeedy CustomFees module to integrate into the LokiCheckout.**

## Installation
Install this package via composer (assuming you have setup the `composer.yireo.com` repository correctly already):
```bash
composer require loki-checkout/magento2-joseph-leedy-custom-fees
```

Next, enable this module:
```bash
bin/magento module:enable LokiCheckout_JosephLeedyCustomFees JosephLeedy_CustomFees
```

## How it works
The JosephLeedy CustomFees module adds a separate total segment per custom fee to the quote totals. In the Hyva Checkout, these segments are grouped into a single `custom_fees` segment. This module duplicates that behaviour for the Loki Checkout: a plugin upon `Magento\Quote\Api\Data\TotalsInterface::getTotalSegments()` groups the individual fee segments into a `custom_fees` segment, which is then rendered in the sidebar totals via the child block `loki-checkout.sidebar.totals.custom_fees`.

The display type (excluding tax, including tax or both) follows the `tax/cart_display/custom_fees` configuration setting of the JosephLeedy CustomFees module.
