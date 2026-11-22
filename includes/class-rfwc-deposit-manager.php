<?php

defined('ABSPATH') || exit;

class RFWC_Deposit_Manager
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('admin_post_rfwc_capture_deposit', [$this, 'capture']);
        add_action('admin_post_rfwc_release_deposit', [$this, 'release']);
    }

    public function add_meta_box()
    {
        add_meta_box(
            'rfwc_deposit_box',
            'Deposit Manager',
            [$this, 'render_box'],
            'shop_order',
            'side'
        );
    }

    public function render_box($post)
    {
        $order = wc_get_order($post->ID);

        $deposit = $order->get_meta('_rfwc_deposit_amount');
        $intent_id = $order->get_meta('_rfwc_payment_intent');

        echo "<p><strong>Deposit:</strong> {$deposit}</p>";
        echo "<p><strong>Intent:</strong> {$intent_id}</p>";

        echo '<a href="' . admin_url('admin-post.php?action=rfwc_capture_deposit&order_id=' . $post->ID) . '" class="button button-primary">Capture</a>';

        echo '<a href="' . admin_url('admin-post.php?action=rfwc_release_deposit&order_id=' . $post->ID) . '" class="button">Release</a>';
    }

    public function capture()
    {
        $order_id = $_GET['order_id'];
        $order = wc_get_order($order_id);

        $intent_id = $order->get_meta('_rfwc_payment_intent');

        \Stripe\Stripe::setApiKey(get_option('woocommerce_rfwc_stripe_connect_settings')['secret_key']);

        $intent = \Stripe\PaymentIntent::retrieve($intent_id);
        $intent->capture();

        $order->add_order_note('Deposit captured');

        wp_redirect(admin_get_referer());
        exit;
    }

    public function release()
    {
        $order_id = $_GET['order_id'];
        $order = wc_get_order($order_id);

        $intent_id = $order->get_meta('_rfwc_payment_intent');

        \Stripe\Stripe::setApiKey(get_option('woocommerce_rfwc_stripe_connect_settings')['secret_key']);

        \Stripe\Refund::create([
            'payment_intent' => $intent_id
        ]);

        $order->add_order_note('Deposit refunded');

        wp_redirect(admin_get_referer());
        exit;
    }
}
