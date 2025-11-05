<?php
/**
 * Admin bar integration and UI rendering.
 *
 * @package GA4_Insights
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Adds admin bar node and floating panel.
 */
class GA4_Insights_Admin_Bar {
    /**
     * Singleton instance.
     *
     * @var GA4_Insights_Admin_Bar|null
     */
    private static $instance = null;

    /**
     * Retrieve instance.
     *
     * @return GA4_Insights_Admin_Bar
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
        add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_node' ), 100 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'network_admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_footer', array( $this, 'render_panel_markup' ) );
        add_action( 'network_admin_footer', array( $this, 'render_panel_markup' ) );
    }

    /**
     * Add admin bar node when user has access.
     *
     * @param WP_Admin_Bar $admin_bar Admin bar instance.
     */
    public function add_admin_bar_node( $admin_bar ) {
        if ( ( ! is_admin() && ! is_network_admin() ) || ! current_user_can( 'edit_posts' ) || ! is_admin_bar_showing() ) {
            return;
        }

        $admin_bar->add_node(
            array(
                'id'    => 'ga4-insights-toggle',
                'title' => __( 'GA4 Insights', 'ga4-insights' ),
                'href'  => '#ga4-insights-panel',
                'meta'  => array(
                    'title' => __( 'Apri GA4 Insights', 'ga4-insights' ),
                ),
            )
        );
    }

    /**
     * Enqueue assets.
     */
    public function enqueue_assets() {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        $css_file = GA4_INSIGHTS_PLUGIN_DIR . 'assets/css/chat-panel' . $suffix . '.css';
        $css_url  = GA4_INSIGHTS_PLUGIN_URL . 'assets/css/chat-panel' . $suffix . '.css';

        if ( ! file_exists( $css_file ) ) {
            $css_file = GA4_INSIGHTS_PLUGIN_DIR . 'assets/css/chat-panel.css';
            $css_url  = GA4_INSIGHTS_PLUGIN_URL . 'assets/css/chat-panel.css';
        }

        wp_enqueue_style(
            'ga4-insights-chat-panel',
            $css_url,
            array(),
            file_exists( $css_file ) ? filemtime( $css_file ) : GA4_INSIGHTS_VERSION
        );

        $js_file = GA4_INSIGHTS_PLUGIN_DIR . 'assets/js/chat-panel' . $suffix . '.js';
        $js_url  = GA4_INSIGHTS_PLUGIN_URL . 'assets/js/chat-panel' . $suffix . '.js';

        if ( ! file_exists( $js_file ) ) {
            $js_file = GA4_INSIGHTS_PLUGIN_DIR . 'assets/js/chat-panel.js';
            $js_url  = GA4_INSIGHTS_PLUGIN_URL . 'assets/js/chat-panel.js';
        }

        wp_enqueue_script(
            'ga4-insights-chat-panel',
            $js_url,
            array(),
            file_exists( $js_file ) ? filemtime( $js_file ) : GA4_INSIGHTS_VERSION,
            true
        );

        wp_localize_script(
            'ga4-insights-chat-panel',
            'GA4InsightsConfig',
            array(
                'restUrl' => esc_url_raw( rest_url( 'ga4-insights/v1/query' ) ),
                'nonce'   => wp_create_nonce( 'wp_rest' ),
                'i18n'    => array(
                    'panelTitle'   => __( 'GA4 Insights', 'ga4-insights' ),
                    'placeholder'  => __( 'Fai una domanda sui dati GA4…', 'ga4-insights' ),
                    'send'         => __( 'Invia', 'ga4-insights' ),
                    'sending'      => __( 'Invio in corso…', 'ga4-insights' ),
                    'emptyMessage' => __( 'Inserisci una domanda prima di inviare.', 'ga4-insights' ),
                    'error'        => __( 'Si è verificato un errore durante il recupero dei dati.', 'ga4-insights' ),
                ),
            )
        );
    }

    /**
     * Render floating panel markup.
     */
    public function render_panel_markup() {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }
        ?>
        <div id="ga4-insights-panel" class="ga4-insights-panel" aria-hidden="true">
            <div class="ga4-insights-panel__dialog" role="dialog" aria-modal="true" aria-labelledby="ga4-insights-panel-title">
                <div class="ga4-insights-panel__header">
                    <h2 id="ga4-insights-panel-title" class="ga4-insights-panel__title"><?php esc_html_e( 'GA4 Insights', 'ga4-insights' ); ?></h2>
                    <button type="button" class="ga4-insights-panel__close" aria-label="<?php esc_attr_e( 'Chiudi GA4 Insights', 'ga4-insights' ); ?>">&times;</button>
                </div>
                <div class="ga4-insights-panel__messages" role="log" aria-live="polite"></div>
                <form class="ga4-insights-panel__form" novalidate>
                    <label class="screen-reader-text" for="ga4-insights-question"><?php esc_html_e( 'Domanda', 'ga4-insights' ); ?></label>
                    <textarea id="ga4-insights-question" name="question" class="ga4-insights-panel__textarea" rows="3" placeholder="<?php esc_attr_e( 'Fai una domanda sui dati GA4…', 'ga4-insights' ); ?>" required></textarea>
                    <div class="ga4-insights-panel__actions">
                        <button type="submit" class="button button-primary ga4-insights-panel__submit"><?php esc_html_e( 'Invia', 'ga4-insights' ); ?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
}
