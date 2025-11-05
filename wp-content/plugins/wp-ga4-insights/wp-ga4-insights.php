<?php
/**
 * Plugin Name:       GA4 Insights
 * Plugin URI:        https://www.gay.it
 * Description:       Provides GA4 conversational insights inside the WordPress admin for multisite networks.
 * Version:           1.0.0
 * Author:            Davide Buselli
 * Author URI:        https://www.gay.it
 * Network:           true
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ga4-insights
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'GA4_INSIGHTS_VERSION' ) ) {
    define( 'GA4_INSIGHTS_VERSION', '1.0.0' );
}

if ( ! defined( 'GA4_INSIGHTS_PLUGIN_DIR' ) ) {
    define( 'GA4_INSIGHTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'GA4_INSIGHTS_PLUGIN_URL' ) ) {
    define( 'GA4_INSIGHTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

require_once GA4_INSIGHTS_PLUGIN_DIR . 'includes/class-ga4-insights-plugin.php';
require_once GA4_INSIGHTS_PLUGIN_DIR . 'includes/class-ga4-insights-settings.php';
require_once GA4_INSIGHTS_PLUGIN_DIR . 'includes/class-ga4-insights-admin-bar.php';
require_once GA4_INSIGHTS_PLUGIN_DIR . 'includes/class-ga4-insights-chat.php';

register_activation_hook( __FILE__, array( 'GA4_Insights_Plugin', 'activate' ) );

GA4_Insights_Plugin::get_instance();
