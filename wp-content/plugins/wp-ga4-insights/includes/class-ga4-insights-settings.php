<?php
/**
 * Network settings handler.
 *
 * @package GA4_Insights
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Settings manager for GA4 Insights.
 */
class GA4_Insights_Settings {
    const OPTION_KEY = 'ga4_insights_settings';

    /**
     * Singleton instance.
     *
     * @var GA4_Insights_Settings|null
     */
    private static $instance = null;

    /**
     * Get instance.
     *
     * @return GA4_Insights_Settings
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
        add_action( 'network_admin_menu', array( $this, 'register_menu' ) );
        add_action( 'network_admin_edit_ga4_insights_settings', array( $this, 'save_settings' ) );
    }

    /**
     * Register network admin menu.
     */
    public function register_menu() {
        add_menu_page(
            __( 'GA4 Insights', 'ga4-insights' ),
            __( 'GA4 Insights', 'ga4-insights' ),
            'manage_network_options',
            'ga4-insights',
            array( $this, 'render_settings_page' ),
            'dashicons-chart-area',
            80
        );
    }

    /**
     * Render settings page.
     */
    public function render_settings_page() {
        if ( isset( $_GET['updated'] ) && 'true' === $_GET['updated'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            add_settings_error( 'ga4_insights_messages', 'ga4_insights_message', __( 'Impostazioni salvate.', 'ga4-insights' ), 'updated' );
        }

        settings_errors( 'ga4_insights_messages' );

        $settings = self::get_settings();

        require GA4_INSIGHTS_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    /**
     * Handle saving settings.
     */
    public function save_settings() {
        if ( ! current_user_can( 'manage_network_options' ) ) {
            wp_die( esc_html__( 'Non hai i permessi sufficienti per questa operazione.', 'ga4-insights' ) );
        }

        check_admin_referer( 'ga4_insights_settings-options' );

        $settings  = isset( $_POST[ self::OPTION_KEY ] ) ? wp_unslash( $_POST[ self::OPTION_KEY ] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $sanitized = $this->sanitize_settings( $settings );

        update_site_option( self::OPTION_KEY, $sanitized );

        wp_safe_redirect( add_query_arg( 'updated', 'true', network_admin_url( 'admin.php?page=ga4-insights' ) ) );
        exit;
    }

    /**
     * Sanitize settings.
     *
     * @param array $settings Raw settings.
     * @return array
     */
    private function sanitize_settings( $settings ) {
        $defaults = self::get_default_settings();

        $sanitized = array();
        $sanitized['endpoint']   = isset( $settings['endpoint'] ) ? esc_url_raw( $settings['endpoint'] ) : $defaults['endpoint'];
        $sanitized['username']   = isset( $settings['username'] ) ? sanitize_text_field( $settings['username'] ) : '';
        $sanitized['password']   = isset( $settings['password'] ) ? sanitize_text_field( $settings['password'] ) : '';
        $sanitized['api_token']  = isset( $settings['api_token'] ) ? sanitize_text_field( $settings['api_token'] ) : '';
        $sanitized['model_name'] = isset( $settings['model_name'] ) ? sanitize_text_field( $settings['model_name'] ) : $defaults['model_name'];
        $sanitized['timeout']    = isset( $settings['timeout'] ) ? absint( $settings['timeout'] ) : $defaults['timeout'];

        if ( empty( $sanitized['endpoint'] ) ) {
            $sanitized['endpoint'] = $defaults['endpoint'];
        }

        if ( $sanitized['timeout'] <= 0 ) {
            $sanitized['timeout'] = $defaults['timeout'];
        }

        return $sanitized;
    }

    /**
     * Get plugin settings merged with defaults.
     *
     * @return array
     */
    public static function get_settings() {
        if ( is_multisite() ) {
            $settings = get_site_option( self::OPTION_KEY, array() );
        } else {
            $settings = get_option( self::OPTION_KEY, array() );
        }

        return wp_parse_args( $settings, self::get_default_settings() );
    }

    /**
     * Retrieve defaults.
     *
     * @return array
     */
    public static function get_default_settings() {
        return array(
            'endpoint'   => 'http://127.0.0.1:8080/chat',
            'username'   => '',
            'password'   => '',
            'api_token'  => '',
            'model_name' => 'gpt-4.1-mini',
            'timeout'    => 30,
        );
    }
}
