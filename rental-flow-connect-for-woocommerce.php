<?php

/**
 * Plugin Name: Rental Flow Connect for WooCommerce
 * Author: Mahim Zaman
 * Author URI: https://github.com/MahimZaman
 * Description: Rental Flow Connect for WooCommerce will let you enable multi-vendor payment system using stripe split pay, payment dispute, contract signing.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

define('RFWC_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('RFWC_DIR', __FILE__);
define('RFWC_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('RFWC_VERSION', time());

require_once RFWC_PATH . 'includes/class-rfwc-loader.php';

RFWC_Loader::run();

