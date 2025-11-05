<?php
/**
 * Core plugin bootstrap.
 *
 * @package GA4_Insights
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * GA4 Insights main plugin class.
 */
class GA4_Insights_Plugin {
    /**
     * Singleton instance.
     *
     * @var GA4_Insights_Plugin|null
     */
    private static $instance = null;

    /**
     * Retrieve instance.
     *
     * @return GA4_Insights_Plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    /**
     * Handle network activation.
     *
     * @param bool $network_wide Whether plugin is activated network wide.
     */
    public static function activate( $network_wide ) {
        if ( $network_wide ) {
            self::maybe_add_network_defaults();
            return;
        }

        self::maybe_add_network_defaults( get_current_blog_id() );
    }

    /**
     * Ensure default settings exist.
     *
     * @param int|null $blog_id Optional blog ID for single-site activation.
     */
    private static function maybe_add_network_defaults( $blog_id = null ) {
        $defaults = GA4_Insights_Settings::get_default_settings();

        if ( is_multisite() && null === $blog_id ) {
            $current = get_site_option( GA4_Insights_Settings::OPTION_KEY, array() );
            if ( empty( $current ) ) {
                update_site_option( GA4_Insights_Settings::OPTION_KEY, $defaults );
            }
            return;
        }

        $current = get_option( GA4_Insights_Settings::OPTION_KEY, array() );
        if ( empty( $current ) ) {
            update_option( GA4_Insights_Settings::OPTION_KEY, $defaults );
        }
    }

    /**
     * Initialize plugin features.
     */
    public function init_plugin() {
        add_action( 'init', array( $this, 'load_textdomain' ) );

        GA4_Insights_Settings::get_instance();

        if ( is_admin() ) {
            GA4_Insights_Admin_Bar::get_instance();
        }

        GA4_Insights_Chat::get_instance();
    }

    /**
     * Load plugin text domain.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'ga4-insights', false, dirname( plugin_basename( GA4_INSIGHTS_PLUGIN_DIR . 'wp-ga4-insights.php' ) ) . '/languages' );
    }
}
