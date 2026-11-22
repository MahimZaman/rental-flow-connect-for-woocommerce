# Rental Flow Connect for WooCommerce

Adds contract, dispute, and log records for rental transactions. The Stripe Connect gateway class calculates a platform fee from rent and the order total. A Deposit Manager box exposes capture and release actions on the order screen.

## Installation

Copy the repository contents into `wp-content/plugins/rental-flow-connect-for-woocommerce/` on a WordPress installation with WooCommerce active. Activate **Rental Flow Connect for WooCommerce** from the Plugins screen.

## Configuration and usage

Activate WooCommerce first. The integration expects the Stripe PHP SDK to be available and order metadata for the deposit amount and landlord's Stripe account. The loader instantiates the gateway but does not register it with the WooCommerce gateway list. The payment class requests automatic capture while the deposit controls expect a capturable intent. Treat this checkout integration as a prototype.

## Author

[Mahim Zaman](https://github.com/MahimZaman)
