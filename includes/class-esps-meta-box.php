<?php
/**
 * Easy StagePush Sender - Meta Box Module
 * Prefix: esps_
 */

if (!defined('ABSPATH')) exit;

class ESPS_Meta_Box {

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'esps_add_sync_meta_box']);
        add_action('save_post', [$this, 'esps_save_sync_meta_box']);
    }

    public function esps_add_sync_meta_box() {
        if (!ESPS_Settings::get_option('show_meta')) return;

        $post_types = ESPS_Settings::get_option('post_types', []);
        $meta_key = ESPS_Settings::get_option('meta_key', '_sync_to_prod');
        foreach ($post_types as $post_type) {
            add_meta_box(
                $meta_key,
                esc_html('Publish to Production', 'easy-stagepush-sender'),
                [$this, 'esps_render_sync_meta_box'],
                $post_type,
                'side',
                'high'
            );
        }
    }

    public function esps_render_sync_meta_box($post) {
        $meta_key = ESPS_Settings::get_option('meta_key', '_sync_to_prod');
        $value = get_post_meta($post->ID, $meta_key, true);
        
        wp_nonce_field('esps_sync_meta_nonce', 'esps_sync_meta_nonce_field');
        echo '<label><input type="checkbox" name="esps_sync_meta_checkbox" value="1"' . esc_attr($value === '1' ? 'checked' : '') . '> ' . esc_html__('Publish to production', 'easy-stagepush-sender') . '</label>';

    }

    public function esps_save_sync_meta_box($post_id) {
        if (!isset($_POST['esps_sync_meta_nonce_field']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['esps_sync_meta_nonce_field'])), 'esps_sync_meta_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $meta_key = ESPS_Settings::get_option('meta_key', '_sync_to_prod');

        if (isset($_POST['esps_sync_meta_checkbox'])) {
            update_post_meta($post_id, $meta_key, 1);
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    }
}

new ESPS_Meta_Box();
