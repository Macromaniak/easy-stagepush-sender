<?php

/**
 * Easy StagePush Sender - Push to Live Logic
 * Handles the Push to Live button and AJAX
 */

if (! defined('ABSPATH')) exit;

class ESPS_Push
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'esps_enqueue_admin_assets']);
        add_action('add_meta_boxes', array($this, 'esps_register_meta_box'));
        add_action('wp_ajax_esps_push_to_live', [$this, 'esps_ajax_push_to_live']);
    }

    public function esps_enqueue_admin_assets($hook)
    {
        global $post;
        if (in_array($hook, ['post.php', 'post-new.php'], true) && isset($post->post_type)) {
            $post_types = ESPS_Settings::get_option('post_types', []);
            if (in_array($post->post_type, $post_types, true)) {
                wp_enqueue_script(
                    'esps-admin-js',
                    ESPS_PLUGIN_URL . 'assets/js/esps-admin.js',
                    ['jquery'],
                    '1.1',
                    true
                );
                wp_localize_script(
                    'esps-admin-js',
                    'esps_ajax_object',
                    [
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'nonce'    => wp_create_nonce('esps_push_to_live'),
                        'post_id'  => $post->ID,
                    ]
                );
            }
        }
    }

    public function esps_register_meta_box()
    {
        $post_types = ESPS_Settings::get_option('post_types', []);
        foreach ($post_types as $post_type) {
            add_meta_box(
                'esps-push-to-live',
                __('Easy StagePush', 'easy-stagepush-sender'),
                array($this, 'esps_add_push_button'),
                $post_type,
                'side', // 'normal', 'side', or 'advanced'
                'high'
            );
        }
    }

    public function esps_add_push_button()
    {
        global $post;
        error_log(print_r($post, true));
        if (! $post) return;
        $post_types = ESPS_Settings::get_option('post_types', []);
        if (in_array($post->post_type, $post_types, true)) {
            echo '<div id="esps-push-to-live-container" style="margin-top:15px;">';
            echo '<button type="button" class="button button-primary" id="esps-push-to-live-btn">' . esc_html__('Push to Live', 'easy-stagepush-sender') . '</button>';
            echo '<p id="esps-push-to-live-msg"></p>';
            echo '</div>';
        }
    }

    public function esps_ajax_push_to_live()
    {
        error_log('handler runs');
        check_ajax_referer('esps_push_to_live', 'security');
        if (! current_user_can('edit_post', intval($_POST['post_id']))) {
            wp_send_json_error(['message' => __('You do not have permission.', 'easy-stagepush-sender')]);
        }
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);
        if (! $post) {
            wp_send_json_error(['message' => __('Post not found.', 'easy-stagepush-sender')]);
        }

        $dev_url  = ESPS_Settings::get_option('dev_url');
        if (empty($dev_url)) {
            $dev_url = get_site_url();
        }
        $prod_url = ESPS_Settings::get_option('prod_url');

        if (empty($prod_url)) {
            wp_send_json_error(['message' => __('Production URL is not set.', 'easy-stagepush-sender')]);
        }

        $acf_fields = function_exists('get_fields') ? esps_prepare_acf_fields_for_transfer(get_fields($post_id)) : [];
        $acf_fields = esps_replace_dev_urls($acf_fields, $dev_url, $prod_url);

        $thumbnail_id = get_post_thumbnail_id($post_id);
        $thumbnail_url = $thumbnail_id ? esps_get_original_image_url($thumbnail_id) : null;
        $template = get_post_meta($post_id, '_wp_page_template', true);

        $parent_id = $post->post_parent;
        $parent = $parent_id ? get_post($parent_id) : null;
        $parent_data = $parent ? ['path' => get_page_uri($parent), 'post_type' => $parent->post_type] : [];

        $post_data = [
            'post_title'        => $post->post_title,
            'post_content'      => $post->post_content,
            'post_status'       => $post->post_status,
            'post_name'         => $post->post_name,
            'path'              => get_page_uri($post_id),
            'post_type'         => $post->post_type,
            '_wp_page_template' => $template,
            'parent_lookup'     => $parent_data,
            'featured_image_url' => $thumbnail_url,
            'post_date'         => $post->post_date,      // Add this!
            'post_date_gmt'     => $post->post_date_gmt,  // And this!
        ];

        $payload = [
            'post'       => $post_data,
            'acf_fields' => $acf_fields,
            'taxonomies' => esps_get_post_taxonomies($post_id, $post->post_type),
            'yoast_meta' => esps_get_yoast_meta($post_id),
        ];

        error_log('payload');
        error_log(print_r($payload, true));

        $response = wp_remote_post(
            trailingslashit($prod_url) . 'wp-json/esps-sync/v1/import-post',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => wp_json_encode($payload),
                'timeout' => 20,
            ]
        );

        if (is_wp_error($response)) {
            error_log('Push to live failed: ' . $response->get_error_message());
            wp_send_json_error(['message' => $response->get_error_message()]);
        }

        $body = wp_remote_retrieve_body($response);
        error_log('Push to live response: ' . $body);

        wp_send_json_success(['message' => __('Successfully pushed to live.', 'easy-stagepush-sender')]);
    }
}
