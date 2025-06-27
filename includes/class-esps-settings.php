<?php

/**
 * Easy StagePush Sender - Settings Module
 * Prefix: esps_
 */

if (! defined('ABSPATH')) {
    exit;
}

class ESPS_Settings
{

    private $option_key = 'esps_settings';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'esps_add_settings_page']);
        add_action('admin_init', [$this, 'esps_register_settings']);
        add_action('admin_notices', [$this, 'esps_admin_notice_receiver_plugin']);
        add_action('admin_notices', [$this, 'esps_admin_notice_missing_prod_url']);
    }

    public function esps_add_settings_page()
    {
        add_options_page(
            esc_html__('Easy StagePush Settings', 'easy-stagepush-sender'),
            esc_html__('StagePush Sender', 'easy-stagepush-sender'),
            'manage_options',
            'esps-settings',
            [$this, 'render_settings_page']
        );
    }

    public function esps_register_settings()
    {
        register_setting('esps_settings_group', $this->option_key, [$this, 'sanitize_settings']);

        add_settings_section('esps_main_section', __('Configuration', 'easy-stagepush-sender'), null, 'esps-settings');

        add_settings_field(
            'prod_url',
            __('Production Site URL', 'easy-stagepush-sender'),
            [$this, 'render_text_field'],
            'esps-settings',
            'esps_main_section',
            ['label_for' => 'prod_url', 'placeholder' => '']
        );
        add_settings_field(
            'dev_url',
            __('Dev Site URL (optional)', 'easy-stagepush-sender'),
            [$this, 'render_text_field'],
            'esps-settings',
            'esps_main_section',
            ['label_for' => 'dev_url', 'placeholder' => '']
        );
        add_settings_field(
            'post_types',
            __('Post Types to Allow Push', 'easy-stagepush-sender'),
            [$this, 'render_post_types_multiselect'],
            'esps-settings',
            'esps_main_section',
            ['label_for' => 'post_types']
        );
    }

    public function sanitize_settings($input)
    {
        return [
            'prod_url'   => esc_url_raw($input['prod_url'] ?? ''),
            'dev_url'    => esc_url_raw($input['dev_url'] ?? ''),
            'post_types' => array_filter(array_map('sanitize_key', $input['post_types'] ?? [])),
        ];
    }

    public function render_settings_page()
    {
?>
        <div class="wrap">
            <h1><?php esc_html_e('Easy StagePush Sender Settings', 'easy-stagepush-sender'); ?></h1>
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

    public function render_post_types_multiselect($args)
    {
        $options = get_option($this->option_key);
        $selected = $options['post_types'] ?? [];

        $post_types = get_post_types(['show_ui' => true], 'objects');
        echo "<select id='" . esc_attr('post_types') . "' class='esps-chosen' name='" . esc_attr($this->option_key) . "[post_types][]' multiple='multiple' style='width: 300px; max-width: 100%;'>";
        foreach ($post_types as $type) {
            $is_selected = in_array($type->name, $selected) ? 'selected' : '';
            echo "<option value='" . esc_attr($type->name) . "' {$is_selected}>" . esc_html($type->label) . "</option>";
        }
        echo "</select>";
    }

    public static function get_option($key, $default = null)
    {
        $options = get_option('esps_settings', []);
        return isset($options[$key]) && ! empty($options[$key]) ? $options[$key] : $default;
    }

    public function esps_admin_notice_receiver_plugin()
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Easy StagePush:</strong> ' . esc_html__('Make sure the Easy StagePush Receiver plugin is installed and active on the production site for this to work.', 'easy-stagepush-sender') . '</p>
        </div>';
    }

    public function esps_admin_notice_missing_prod_url()
    {
        if (! current_user_can('manage_options')) return;
        $prod_url = ESPS_Settings::get_option('prod_url');
        if (empty($prod_url)) {
            echo '<div class="notice notice-error is-dismissible"><p><strong>Easy StagePush:</strong> ' . esc_html__('Production URL is not set. Content syncing will not work.', 'easy-stagepush-sender') . '</p></div>';
        }
    }
}
