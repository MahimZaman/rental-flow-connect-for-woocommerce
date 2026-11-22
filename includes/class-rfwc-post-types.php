<?php
if (!defined('ABSPATH')) exit;

class RFWC_Post_Types
{
    public function __construct()
    {
        add_action('init', [$this, 'register_post_types']);
        add_filter('manage_rfwc_contract_posts_columns', [$this, 'contract_columns']);
        add_action('manage_rfwc_contract_posts_custom_column', [$this, 'contract_column_content'], 10, 2);

        add_filter('manage_rfwc_dispute_posts_columns', [$this, 'dispute_columns']);
        add_action('manage_rfwc_dispute_posts_custom_column', [$this, 'dispute_column_content'], 10, 2);

        add_filter('manage_rfwc_log_posts_columns', [$this, 'log_columns']);
        add_action('manage_rfwc_log_posts_custom_column', [$this, 'log_column_content'], 10, 2);
    }

    public function register_post_types()
    {
        // CONTRACT CPT
        register_post_type('rfwc_contract', [
            'labels' => [
                'name' => 'Contracts',
                'singular_name' => 'Contract',
            ],
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-media-document',
            'supports' => ['title'],
        ]);

        // DISPUTE CPT
        register_post_type('rfwc_dispute', [
            'labels' => [
                'name' => 'Disputes',
                'singular_name' => 'Dispute',
            ],
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-warning',
            'supports' => ['title'],
        ]);

        // LOG CPT (optional but recommended)
        register_post_type('rfwc_log', [
            'labels' => [
                'name' => 'Logs',
                'singular_name' => 'Log',
            ],
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-list-view',
            'supports' => ['title'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CONTRACT COLUMNS
    |--------------------------------------------------------------------------
    */

    public function contract_columns($columns)
    {
        return [
            'cb' => $columns['cb'],
            'title' => 'Contract ID',
            'order' => 'Order',
            'tenant' => 'Tenant',
            'landlord' => 'Landlord',
            'product' => 'Product',
            'rental' => 'Rental Period',
            'signed' => 'Signed At',
        ];
    }

    public function contract_column_content($column, $post_id)
    {
        switch ($column) {
            case 'order':
                echo '#' . get_post_meta($post_id, '_rfwc_order_id', true);
                break;

            case 'tenant':
                $user = get_userdata(get_post_meta($post_id, '_rfwc_tenant_id', true));
                echo $user ? $user->user_login : '-';
                break;

            case 'landlord':
                $user = get_userdata(get_post_meta($post_id, '_rfwc_landlord_id', true));
                echo $user ? $user->user_login : '-';
                break;

            case 'product':
                $product_id = get_post_meta($post_id, '_rfwc_product_id', true);
                echo $product_id ? get_the_title($product_id) : '-';
                break;

            case 'rental':
                $start = get_post_meta($post_id, '_rfwc_rental_start', true);
                $end = get_post_meta($post_id, '_rfwc_rental_end', true);
                echo ($start && $end) ? "$start → $end" : '-';
                break;

            case 'signed':
                $time = get_post_meta($post_id, '_rfwc_signed_at', true);
                echo $time ? date('Y-m-d H:i', $time) : '-';
                break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DISPUTE COLUMNS
    |--------------------------------------------------------------------------
    */

    public function dispute_columns($columns)
    {
        return [
            'cb' => $columns['cb'],
            'title' => 'Dispute ID',
            'order' => 'Order',
            'tenant' => 'Tenant',
            'landlord' => 'Landlord',
            'requested' => 'Requested (€)',
            'approved' => 'Approved (€)',
            'status' => 'Status',
            'resolution' => 'Resolution',
            'date' => 'Date',
        ];
    }

    public function dispute_column_content($column, $post_id)
    {
        switch ($column) {
            case 'order':
                echo '#' . get_post_meta($post_id, '_rfwc_order_id', true);
                break;

            case 'tenant':
                $user = get_userdata(get_post_meta($post_id, '_rfwc_tenant_id', true));
                echo $user ? $user->user_login : '-';
                break;

            case 'landlord':
                $user = get_userdata(get_post_meta($post_id, '_rfwc_landlord_id', true));
                echo $user ? $user->user_login : '-';
                break;

            case 'requested':
                echo get_post_meta($post_id, '_rfwc_requested_amount', true) ?: '-';
                break;

            case 'approved':
                echo get_post_meta($post_id, '_rfwc_approved_amount', true) ?: '-';
                break;

            case 'status':
                echo ucfirst(get_post_meta($post_id, '_rfwc_status', true));
                break;

            case 'resolution':
                echo ucfirst(get_post_meta($post_id, '_rfwc_resolution_type', true));
                break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LOG COLUMNS
    |--------------------------------------------------------------------------
    */

    public function log_columns($columns)
    {
        return [
            'cb' => $columns['cb'],
            'title' => 'Log ID',
            'order' => 'Order',
            'user' => 'User',
            'type' => 'Type',
            'message' => 'Message',
            'date' => 'Date',
        ];
    }

    public function log_column_content($column, $post_id)
    {
        switch ($column) {
            case 'order':
                echo '#' . get_post_meta($post_id, '_rfwc_order_id', true);
                break;

            case 'user':
                $user = get_userdata(get_post_meta($post_id, '_rfwc_user_id', true));
                echo $user ? $user->user_login : '-';
                break;

            case 'type':
                echo get_post_meta($post_id, '_rfwc_log_type', true);
                break;

            case 'message':
                echo get_post_meta($post_id, '_rfwc_log_message', true);
                break;
        }
    }
}
