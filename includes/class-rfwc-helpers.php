<?php
if (!defined('ABSPATH')) exit;

class RFWC_Helpers
{
    /*
    |--------------------------------------------------------------------------
    | CONTRACT
    |--------------------------------------------------------------------------
    */

    public static function get_contract_by_order($order_id)
    {
        $posts = get_posts([
            'post_type' => 'rfwc_contract',
            'meta_key' => '_rfwc_order_id',
            'meta_value' => $order_id,
            'numberposts' => 1
        ]);

        return $posts ? $posts[0] : null;
    }

    public static function create_contract($data)
    {
        $post_id = wp_insert_post([
            'post_type' => 'rfwc_contract',
            'post_title' => 'Contract #' . $data['order_id'],
            'post_status' => 'publish'
        ]);

        foreach ($data as $key => $value) {
            update_post_meta($post_id, "_rfwc_$key", $value);
        }

        return $post_id;
    }

    /*
    |--------------------------------------------------------------------------
    | DISPUTE
    |--------------------------------------------------------------------------
    */

    public static function create_dispute($data)
    {
        $post_id = wp_insert_post([
            'post_type' => 'rfwc_dispute',
            'post_title' => 'Dispute #' . $data['order_id'],
            'post_status' => 'publish'
        ]);

        foreach ($data as $key => $value) {
            update_post_meta($post_id, "_rfwc_$key", $value);
        }

        return $post_id;
    }

    /*
    |--------------------------------------------------------------------------
    | LOG SYSTEM
    |--------------------------------------------------------------------------
    */

    public static function log($type, $message, $order_id = 0, $user_id = 0, $extra = [])
    {
        $post_id = wp_insert_post([
            'post_type' => 'rfwc_log',
            'post_title' => strtoupper($type),
            'post_status' => 'publish'
        ]);

        update_post_meta($post_id, '_rfwc_log_type', $type);
        update_post_meta($post_id, '_rfwc_log_message', $message);
        update_post_meta($post_id, '_rfwc_order_id', $order_id);
        update_post_meta($post_id, '_rfwc_user_id', $user_id);
        update_post_meta($post_id, '_rfwc_created_at', current_time('timestamp'));

        if (!empty($extra)) {
            update_post_meta($post_id, '_rfwc_extra', $extra);
        }

        return $post_id;
    }
}
