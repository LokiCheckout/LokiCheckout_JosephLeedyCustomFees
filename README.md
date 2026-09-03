# LokiCheckout_JosephLeedyCustomFees

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/loki-checkout/magento2-joseph-leedy-custom-fees)
<!-- badges.specs.end -->


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

## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_JosephLeedyCustomFees/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_JosephLeedyCustomFees/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_JosephLeedyCustomFees/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_JosephLeedyCustomFees/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_JosephLeedyCustomFees/compile.yml?label=compile)
<!-- badges.test.end -->
