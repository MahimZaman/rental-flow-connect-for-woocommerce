<?php

defined('ABSPATH') || exit;

class RFWC_Assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'public_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
    }

    public function public_scripts()
    {
        wp_enqueue_style('rfwc-remixicon', 'https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css', [], RFWC_VERSION);

        wp_enqueue_style('rfwc-public', RFWC_URL . 'assets/public.css', [], RFWC_VERSION);
        wp_enqueue_style('rfwc-public', RFWC_URL . 'assets/public.js', [], RFWC_VERSION, true);
    }

    public function admin_scripts()
    {
        wp_enqueue_style('rfwc-remixicon', 'https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css', [], RFWC_VERSION);

        wp_enqueue_style('rfwc-admin', RFWC_URL . 'assets/admin.css', [], RFWC_VERSION);
        wp_enqueue_style('rfwc-admin', RFWC_URL . 'assets/admin.js', [], RFWC_VERSION, true);
    }
}
