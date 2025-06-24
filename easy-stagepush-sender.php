<?php
/*
Plugin Name: Easy StagePush Sender
Description: Push posts with fields and media to the production site when publishing.
Version: 1.0
Requires at least: 6.3
Requires PHP: 7.2.24
Author: Anandhu Nadesh
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Text Domain: easy-stagepush-sender
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-esps-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-esps-meta-box.php';
// add_action('acf/save_post', 'esps_push_to_live_site', 20);
add_action('save_post', 'esps_push_to_live_site', 20, 2);


function esps_push_to_live_site($post_id)
{
    // if (strpos($post_id, 'option') !== false || wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
    //     return;
    // }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) return;

    // $allowed_types = ['post', 'page', 'press', 'people', 'testimonial', 'author'];

    // if (!in_array($post->post_type, $allowed_types)) return;

    $meta_key = ESPS_Settings::get_option('meta_key', '_sync_to_prod');
    if (!get_post_meta($post_id, $meta_key, true)) {
        return;
    }

    $post = get_post($post_id);

    $dev_url = ESPS_Settings::get_option('dev_url');
    if (empty($dev_url)) {
        $dev_url = get_site_url();
    }
    $prod_url = ESPS_Settings::get_option('prod_url');

    $acf_fields = esps_prepare_acf_fields_for_transfer(get_fields($post_id));
    $acf_fields = esps_replace_dev_urls($acf_fields, $dev_url, $prod_url);

    $thumbnail_id = get_post_thumbnail_id($post_id);
    $thumbnail_url = $thumbnail_id ? esps_get_original_image_url($thumbnail_id) : null;
    $template = get_post_meta($post_id, '_wp_page_template', true);

    $parent_id = $post->post_parent;
    $parent = $parent_id ? get_post($parent_id) : null;
    $parent_data = $parent ? ['path' => get_page_uri($parent), 'post_type' => $parent->post_type] : [];

    $post_data = [
        'post_title'   => $post->post_title,
        'post_content' => $post->post_content,
        'post_status'  => 'publish',
        'post_name'    => $post->post_name,
        'path'         => get_page_uri($post_id),
        'post_type'    => $post->post_type,
        '_wp_page_template' => $template,
        'parent_lookup' => $parent_data,
        'featured_image_url' => $thumbnail_url
    ];

    $payload = [
        'post'        => $post_data,
        'acf_fields'  => $acf_fields,
        'taxonomies'  => esps_get_post_taxonomies($post_id, $post->post_type),
        'yoast_meta'  => esps_get_yoast_meta($post_id),
    ];

    $response = wp_remote_post(trailingslashit($prod_url) . 'wp-json/esps-sync/v1/import-post', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($payload),
        'timeout' => 20
    ]);

    if (is_wp_error($response)) {
        error_log('Push to live failed: ' . $response->get_error_message());
    } else {
        error_log('Push to live response: ' . wp_remote_retrieve_body($response));
    }
}

function esps_prepare_acf_fields_for_transfer($fields)
{
    foreach ($fields as $key => &$value) {
        if (is_array($value) && isset($value['ID']) && get_post_mime_type($value['ID'])) {
            $file_path = get_post_meta($value['ID'], '_wp_attached_file', true);
            $uploads = wp_upload_dir();
            $value = $file_path ? trailingslashit($uploads['baseurl']) . $file_path : null;
        } elseif (is_array($value) && !empty($value) && is_numeric(reset($value))) {
            $value = array_map(function ($id) {
                $post = get_post($id);
                return $post ? ['post_type' => $post->post_type, 'slug' => $post->post_name] : null;
            }, $value);
        } elseif (is_int($value)) {
            $mime = get_post_mime_type($value);
            if ($mime) {
                $file_path = get_post_meta($value, '_wp_attached_file', true);
                $uploads = wp_upload_dir();
                $value = $file_path ? trailingslashit($uploads['baseurl']) . $file_path : null;
            } else {
                $post = get_post($value);
                $value = $post ? ['post_type' => $post->post_type, 'slug' => $post->post_name] : null;
            }
        } elseif (is_array($value)) {
            $value = esps_prepare_acf_fields_for_transfer($value);
        }
    }
    return $fields;
}

function esps_get_original_image_url($image_id)
{
    $meta = wp_get_attachment_metadata($image_id);
    $uploads = wp_upload_dir();
    return $meta && isset($meta['file']) ? trailingslashit($uploads['baseurl']) . $meta['file'] : null;
}

function esps_get_post_taxonomies($post_id, $post_type)
{
    $taxonomies = get_object_taxonomies($post_type);
    $term_data = [];
    foreach ($taxonomies as $taxonomy) {
        $terms = wp_get_post_terms($post_id, $taxonomy);
        if (!is_wp_error($terms) && !empty($terms)) {
            $term_data[$taxonomy] = wp_list_pluck($terms, 'slug');
        }
    }
    return $term_data;
}

function esps_get_yoast_meta($post_id)
{
    $yoast_keys = [
        '_yoast_wpseo_title',
        '_yoast_wpseo_metadesc',
        '_yoast_wpseo_focuskw',
        '_yoast_wpseo_canonical',
        '_yoast_wpseo_opengraph-title',
        '_yoast_wpseo_opengraph-description',
        '_yoast_wpseo_twitter-title',
        '_yoast_wpseo_twitter-description',
    ];
    $meta = [];
    foreach ($yoast_keys as $key) {
        $value = get_post_meta($post_id, $key, true);
        if (!empty($value)) {
            $meta[$key] = $value;
        }
    }
    return $meta;
}

function esps_replace_dev_urls($data, $dev_url, $prod_url)
{
    foreach ($data as $key => &$value) {
        if (is_string($value) && strpos($value, $dev_url) !== false) {
            $value = str_replace($dev_url, $prod_url, $value);
        } elseif (is_array($value)) {
            $value = esps_replace_dev_urls($value, $dev_url, $prod_url);
        }
    }
    return $data;
}

function esps_admin_notice_missing_prod_url()
{
    if (!current_user_can('manage_options')) return;
    $prod_url = ESPS_Settings::get_option('prod_url');
    if (empty($prod_url)) {
        echo '<div class="notice notice-error"><p><strong>Easy StagePush:</strong> Production URL is not set. Content syncing will not work.</p></div>';
    }
}
