<?php
/**
 * REST endpoint proxying questions to MCP.
 *
 * @package GA4_Insights
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles REST communication with the MCP endpoint.
 */
class GA4_Insights_Chat {
    /**
     * Singleton instance.
     *
     * @var GA4_Insights_Chat|null
     */
    private static $instance = null;

    /**
     * Retrieve instance.
     *
     * @return GA4_Insights_Chat
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
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register REST routes.
     */
    public function register_routes() {
        register_rest_route(
            'ga4-insights/v1',
            '/query',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'handle_query' ),
                'permission_callback' => array( $this, 'permission_check' ),
                'args'                => array(
                    'question' => array(
                        'required' => true,
                        'type'     => 'string',
                    ),
                ),
            )
        );
    }

    /**
     * Permission check for REST endpoint.
     *
     * @return bool
     */
    public function permission_check() {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Handle REST query.
     *
     * @param WP_REST_Request $request Request instance.
     * @return WP_REST_Response|WP_Error
     */
    public function handle_query( WP_REST_Request $request ) {
        $question = trim( (string) $request->get_param( 'question' ) );

        if ( '' === $question ) {
            return new WP_Error( 'ga4_insights_empty_question', __( 'La domanda non può essere vuota.', 'ga4-insights' ), array( 'status' => 400 ) );
        }

        $settings = GA4_Insights_Settings::get_settings();

        $endpoint = $settings['endpoint'];
        if ( empty( $endpoint ) ) {
            return new WP_Error( 'ga4_insights_missing_endpoint', __( 'Endpoint MCP non configurato.', 'ga4-insights' ), array( 'status' => 500 ) );
        }

        $host = wp_parse_url( get_site_url(), PHP_URL_HOST );
        if ( empty( $host ) ) {
            $host = wp_parse_url( home_url(), PHP_URL_HOST );
        }

        $payload = array(
            'model'    => $settings['model_name'],
            'query'    => $question,
            'filters'  => array(
                'hostName' => $host,
            ),
            'metadata' => array(
                'siteUrl' => get_site_url(),
            ),
        );

        $headers = array(
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        );

        if ( ! empty( $settings['username'] ) && ! empty( $settings['password'] ) ) {
            $headers['Authorization'] = 'Basic ' . base64_encode( $settings['username'] . ':' . $settings['password'] );
        }

        if ( ! empty( $settings['api_token'] ) ) {
            $headers['X-API-Token'] = $settings['api_token'];
        }

        $response = wp_remote_post(
            $endpoint,
            array(
                'headers' => $headers,
                'body'    => wp_json_encode( $payload ),
                'timeout' => (int) $settings['timeout'],
                'user-agent' => 'GA4 Insights/' . GA4_INSIGHTS_VERSION,
            )
        );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'ga4_insights_request_error', $response->get_error_message(), array( 'status' => 500 ) );
        }

        $code    = wp_remote_retrieve_response_code( $response );
        $body    = wp_remote_retrieve_body( $response );
        $decoded = json_decode( $body, true );

        if ( 200 > $code || 299 < $code ) {
            $message = isset( $decoded['error'] ) ? $decoded['error'] : __( 'Errore sconosciuto dalla sorgente dati.', 'ga4-insights' );
            return new WP_Error( 'ga4_insights_remote_error', $message, array( 'status' => $code ? $code : 500 ) );
        }

        if ( null === $decoded ) {
            return new WP_Error( 'ga4_insights_invalid_json', __( 'Risposta non valida dal servizio MCP.', 'ga4-insights' ), array( 'status' => 500 ) );
        }

        return rest_ensure_response(
            array(
                'success' => true,
                'data'    => $decoded,
            )
        );
    }
}
