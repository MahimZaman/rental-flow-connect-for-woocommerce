<?php

defined('ABSPATH') || exit;

class RFWC_Loader
{
    public static function run()
    {
        self::load_core();
        self::load_ins();
        self::load_hooks();
    }

    private static function load_core()
    {
        require_once RFWC_PATH . 'includes/class-rfwc-assets.php';
        require_once RFWC_PATH . 'includes/class-rfwc-post-types.php';
        require_once RFWC_PATH . 'includes/class-rfwc-meta-boxes.php';
        require_once RFWC_PATH . 'includes/class-rfwc-helpers.php';
    }

    private static function load_ins()
    {
        new RFWC_Assets();
        new RFWC_Post_Types();
        new RFWC_Meta_Boxes();
        new RFWC_Helpers();
    }

    private static function load_hooks()
    {
        add_action('plugins_loaded', [__CLASS__, 'rfwc_deposit_and_payment']);
    }

    public static function rfwc_deposit_and_payment()
    {
        if (!class_exists('WC_Payment_Gateway')) return;
        require_once RFWC_PATH . 'includes/class-rfwc-stripe-gateway.php';
        require_once RFWC_PATH . 'includes/class-rfwc-deposit-manager.php';
        new RFWC_Stripe_Gateway();
        new RFWC_Deposit_Manager();
    }
}
