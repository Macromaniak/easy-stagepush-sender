<?php
/*
Plugin Name: Easy StagePush Sender
Description: Push posts with fields and media to the production site on demand.
Version: 1.2
Requires at least: 6.3
Requires PHP: 7.2.24
Author: Anandhu Nadesh
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Text Domain: easy-stagepush-sender
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'ESPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ESPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once ESPS_PLUGIN_DIR . 'includes/esps-helpers.php';
require_once ESPS_PLUGIN_DIR . 'includes/class-esps-settings.php';
require_once ESPS_PLUGIN_DIR . 'includes/class-esps-push.php';

// Initialize settings.
new ESPS_Settings();
// Initialize Push-to-Live.
new ESPS_Push();
