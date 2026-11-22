<?php

defined('ABSPATH') || exit;

class RFWC_Stripe_Gateway extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'rfwc_stripe_connect';
        $this->method_title = 'RFWC Stripe Connect';
        $this->method_description = 'Stripe Connect with deposit split';
        $this->has_fields = true;

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->secret_key = $this->get_option('secret_key');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
    }

    public function init_form_fields()
    {
        $this->form_fields = [
            'enabled' => [
                'title' => 'Enable',
                'type' => 'checkbox',
                'default' => 'yes'
            ],
            'title' => [
                'title' => 'Title',
                'type' => 'text',
                'default' => 'Pay with Card'
            ],
            'secret_key' => [
                'title' => 'Stripe Secret Key',
                'type' => 'password'
            ]
        ];
    }

    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);

        $deposit = $order->get_meta('_rfwc_deposit_amount');
        $rent = $order->get_total() - $deposit;

        $customer_fee = $order->get_total() * 0.03;
        $landlord_fee = $rent * 0.03;

        $application_fee = $customer_fee + $landlord_fee;

        $landlord_account = $order->get_meta('_rfwc_landlord_stripe_account');

        \Stripe\Stripe::setApiKey($this->secret_key);

        $intent = \Stripe\PaymentIntent::create([
            'amount' => intval($order->get_total() * 100),
            'currency' => strtolower(get_woocommerce_currency()),
            'payment_method_types' => ['card'],
            'capture_method' => 'automatic',
            'application_fee_amount' => intval($application_fee * 100),
            'transfer_data' => [
                'destination' => $landlord_account,
            ],
            'metadata' => [
                'order_id' => $order_id
            ]
        ]);

        $order->update_meta_data('_rfwc_payment_intent', $intent->id);
        $order->save();

        return [
            'result' => 'success',
            'redirect' => $this->get_return_url($order)
        ];
    }
}
