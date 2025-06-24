<?php

/**
 * Easy StagePush Sender - Settings Module
 * Prefix: esps_
 */

if (!defined('ABSPATH')) exit;

class ESPS_Settings
{

    private $option_key = 'esps_settings';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        // add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_notices', [$this, 'admin_notice_receiver_plugin']);
    }

    public function add_settings_page()
    {
        add_options_page(
            esc_html('Easy StagePush Settings', 'easy-stagepush-sender'),
            esc_html('StagePush Sender', 'easy-stagepush-sender'),
            'manage_options',
            'esps-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings()
    {
        register_setting('esps_settings_group', $this->option_key, [$this, 'sanitize_settings']);

        add_settings_section('esps_main_section', 'Configuration', null, 'esps-settings');

        add_settings_field('prod_url', 'Production Site URL', [$this, 'render_text_field'], 'esps-settings', 'esps_main_section', ['label_for' => 'prod_url', 'placeholder' => '']);
        add_settings_field('dev_url', 'Dev Site URL (optional)', [$this, 'render_text_field'], 'esps-settings', 'esps_main_section', ['label_for' => 'dev_url', 'placeholder' => '']);
        add_settings_field('meta_key', 'Meta Key to Trigger Sync', [$this, 'render_text_field'], 'esps-settings', 'esps_main_section', ['label_for' => 'meta_key', 'placeholder' => '_sync_to_prod']);
        add_settings_field('show_meta', 'Show Meta Field in Admin', [$this, 'render_checkbox_field'], 'esps-settings', 'esps_main_section', ['label_for' => 'show_meta']);
        add_settings_field('post_types', 'Post Types to Show Meta Field', [$this, 'render_post_types_multiselect'], 'esps-settings', 'esps_main_section', ['label_for' => 'post_types']);
    }

    public function sanitize_settings($input)
    {
        return [
            'prod_url'   => esc_url_raw($input['prod_url'] ?? ''),
            'dev_url'    => esc_url_raw($input['dev_url'] ?? ''),
            'meta_key'   => sanitize_key($input['meta_key'] ?? '_sync_to_prod'),
            'show_meta'  => isset($input['show_meta']) ? (bool) $input['show_meta'] : false,
            'post_types' => array_filter(array_map('sanitize_key', $input['post_types'] ?? []))
        ];
    }

    public function render_settings_page()
    {
?>
        <div class="wrap">
            <h1>Easy StagePush Sender Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('esps_settings_group');
                do_settings_sections('esps-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    public function render_text_field($args)
    {
        $options = get_option($this->option_key);
        $id = $args['label_for'];
        $value = esc_attr($options[$id] ?? '');
        echo "<input type='text' id='" . esc_attr($id) . "' name='" . esc_attr($this->option_key . '[' . $id . ']') . "' value='" . esc_attr($value) . "' placeholder='" . esc_attr($args['placeholder']) . "' class='regular-text' />";
    }

    public function render_checkbox_field($args)
    {
        $options = get_option($this->option_key);
        $id = $args['label_for'];
        $checked = !empty($options[$id]) ? 'checked' : '';
        echo "<label style='display: inline-flex; align-items: center; gap: 8px;'>
            <input type='checkbox' 
                id='" . esc_attr($id) . "' 
                name='" . esc_attr($this->option_key . '[' . $id . ']') . "' 
                value='1' " . esc_attr($checked) . " />
            <span>" . esc_html__('Show this field in the post editor', 'easy-stagepush-sender') . "</span>
        </label>";
    }

    public function render_post_types_multiselect($args)
    {
        $options = get_option($this->option_key);
        $selected = $options['post_types'] ?? [];

        $post_types = get_post_types(['public' => true], 'objects');
        echo "<select id='" . esc_attr('post_types') . "' class='esps-chosen' name='" . esc_attr($this->option_key) . "[post_types][]' multiple='multiple' style='width: 300px; max-width: 100%;'>";
        foreach ($post_types as $type) {
            $is_selected = in_array($type->name, $selected) ? 'selected' : '';
            echo "<option value='" . esc_attr($type->name) . "' {$is_selected}>" . esc_html($type->label) . "</option>";
        }
        echo "</select>";
    }

    // public function enqueue_admin_assets($hook)
    // {
    //     if ($hook !== 'settings_page_esps-settings') return;
    //     wp_enqueue_style('esps-chosen-css', plugin_dir_url(__FILE__) . '../assets/chosen/chosen.min.css');
    //     wp_enqueue_script('esps-chosen-js', plugin_dir_url(__FILE__) . '../assets/chosen/chosen.jquery.min.js', ['jquery'], null, true);
    //     wp_add_inline_script('esps-chosen-js', "jQuery(document).ready(function($) { $('.esps-chosen').chosen(); });");
    // }

    public static function get_option($key, $default = null)
    {
        $options = get_option('esps_settings', []);
        return $options[$key] && !empty($options[$key]) ? $options[$key] : $default;
    }

    public function admin_notice_receiver_plugin()
    {
        if (!current_user_can('manage_options')) return;
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Easy StagePush:</strong> Make sure the <em>Easy StagePush Receiver</em> plugin is installed and active on the production site for this to work.</p>
        </div>';
    }
}


new ESPS_Settings();
