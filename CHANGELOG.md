# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 27 November 2026
### Added
- Initial release: integrates the JosephLeedy CustomFees module into the Loki Checkout
- Group all individual custom fee totals into a single "Custom Fees" segment, shown in the Loki Checkout sidebar totals before the grand total
- Display custom fees excluding tax, including tax or both, following the `tax/cart_display/custom_fees` setting
- Support for PHP 8.2 up to 8.5

### Fixed
- Declare missing dependencies on Magento_Quote and Magento_Store
