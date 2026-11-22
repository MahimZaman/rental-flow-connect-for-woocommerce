<?php
if (!defined('ABSPATH')) exit;

class RFWC_Meta_Boxes
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'register_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes']);
    }

    public function register_meta_boxes()
    {
        add_meta_box(
            'rfwc_contract_details',
            'Contract Details',
            [$this, 'contract_meta_box'],
            'rfwc_contract',
            'normal',
            'high'
        );

        add_meta_box(
            'rfwc_dispute_details',
            'Dispute Details',
            [$this, 'dispute_meta_box'],
            'rfwc_dispute',
            'normal',
            'high'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CONTRACT META BOX
    |--------------------------------------------------------------------------
    */

    public function contract_meta_box($post)
    {
        wp_nonce_field('rfwc_save_meta', 'rfwc_meta_nonce');

        $fields = [
            'order_id',
            'product_id',
            'tenant_id',
            'landlord_id',
            'rental_start',
            'rental_end',
            'signed_at'
        ];

        foreach ($fields as $field) {
            $$field = get_post_meta($post->ID, "_rfwc_$field", true);
        }

?>
        <table class="widefat striped">
            <tr>
                <td>Order ID</td>
                <td><input type="number" name="rfwc_order_id" value="<?php echo esc_attr($order_id); ?>"></td>
            </tr>
            <tr>
                <td>Product ID</td>
                <td><input type="number" name="rfwc_product_id" value="<?php echo esc_attr($product_id); ?>"></td>
            </tr>
            <tr>
                <td>Tenant ID</td>
                <td><input type="number" name="rfwc_tenant_id" value="<?php echo esc_attr($tenant_id); ?>"></td>
            </tr>
            <tr>
                <td>Landlord ID</td>
                <td><input type="number" name="rfwc_landlord_id" value="<?php echo esc_attr($landlord_id); ?>"></td>
            </tr>
            <tr>
                <td>Rental Start</td>
                <td><input type="date" name="rfwc_rental_start" value="<?php echo esc_attr($rental_start); ?>"></td>
            </tr>
            <tr>
                <td>Rental End</td>
                <td><input type="date" name="rfwc_rental_end" value="<?php echo esc_attr($rental_end); ?>"></td>
            </tr>
            <tr>
                <td>Signed At</td>
                <td><input type="datetime-local" name="rfwc_signed_at" value="<?php echo esc_attr($signed_at ? date('Y-m-d\TH:i', $signed_at) : ''); ?>"></td>
            </tr>
        </table>

        <p><strong>Contract Preview:</strong></p>
        <div style="max-height:200px;overflow:auto;background:#fff;padding:10px;border:1px solid #ddd;">
            <?php echo wp_kses_post(get_post_meta($post->ID, '_rfwc_contract_html', true)); ?>
        </div>
    <?php
    }

    /*
    |--------------------------------------------------------------------------
    | DISPUTE META BOX
    |--------------------------------------------------------------------------
    */

    public function dispute_meta_box($post)
    {
        wp_nonce_field('rfwc_save_meta', 'rfwc_meta_nonce');

        $meta = function ($key) use ($post) {
            return get_post_meta($post->ID, "_rfwc_$key", true);
        };
    ?>

        <table class="widefat striped">
            <tr>
                <td>Order ID</td>
                <td><input type="number" name="rfwc_order_id" value="<?php echo esc_attr($meta('order_id')); ?>"></td>
            </tr>
            <tr>
                <td>Tenant ID</td>
                <td><input type="number" name="rfwc_tenant_id" value="<?php echo esc_attr($meta('tenant_id')); ?>"></td>
            </tr>
            <tr>
                <td>Landlord ID</td>
                <td><input type="number" name="rfwc_landlord_id" value="<?php echo esc_attr($meta('landlord_id')); ?>"></td>
            </tr>

            <tr>
                <td>Requested (€)</td>
                <td><input type="number" step="0.01" name="rfwc_requested_amount" value="<?php echo esc_attr($meta('requested_amount')); ?>"></td>
            </tr>
            <tr>
                <td>Approved (€)</td>
                <td><input type="number" step="0.01" name="rfwc_approved_amount" value="<?php echo esc_attr($meta('approved_amount')); ?>"></td>
            </tr>

            <tr>
                <td>Status</td>
                <td>
                    <select name="rfwc_status">
                        <?php
                        $statuses = ['open', 'reviewing', 'resolved'];
                        foreach ($statuses as $status) {
                            echo '<option value="' . $status . '" ' . selected($meta('status'), $status, false) . '>' . ucfirst($status) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Resolution</td>
                <td>
                    <select name="rfwc_resolution_type">
                        <option value="">--</option>
                        <option value="full_return" <?php selected($meta('resolution_type'), 'full_return'); ?>>Full Return</option>
                        <option value="partial" <?php selected($meta('resolution_type'), 'partial'); ?>>Partial</option>
                        <option value="full_charge" <?php selected($meta('resolution_type'), 'full_charge'); ?>>Full Charge</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Admin Notes</td>
                <td><textarea name="rfwc_admin_notes" rows="4"><?php echo esc_textarea($meta('admin_notes')); ?></textarea></td>
            </tr>
        </table>
<?php
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE HANDLER
    |--------------------------------------------------------------------------
    */

    public function save_meta_boxes($post_id)
    {
        if (!isset($_POST['rfwc_meta_nonce']) || !wp_verify_nonce($_POST['rfwc_meta_nonce'], 'rfwc_save_meta')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        $fields = [
            'order_id',
            'product_id',
            'tenant_id',
            'landlord_id',
            'rental_start',
            'rental_end',
            'signed_at',
            'requested_amount',
            'approved_amount',
            'status',
            'resolution_type',
            'admin_notes'
        ];

        foreach ($fields as $field) {
            if (isset($_POST["rfwc_$field"])) {
                update_post_meta($post_id, "_rfwc_$field", sanitize_text_field($_POST["rfwc_$field"]));
            }
        }

        // Convert datetime-local to timestamp
        if (!empty($_POST['rfwc_signed_at'])) {
            update_post_meta($post_id, '_rfwc_signed_at', strtotime($_POST['rfwc_signed_at']));
        }
    }
}
