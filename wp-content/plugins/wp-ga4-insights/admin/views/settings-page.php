<?php
/**
 * Settings page template.
 *
 * @package GA4_Insights
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div class="wrap">
    <h1><?php esc_html_e( 'GA4 Insights - Impostazioni network', 'ga4-insights' ); ?></h1>
    <form method="post" action="<?php echo esc_url( network_admin_url( 'edit.php?action=ga4_insights_settings' ) ); ?>">
        <?php wp_nonce_field( 'ga4_insights_settings-options' ); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="ga4-insights-endpoint"><?php esc_html_e( 'Endpoint MCP', 'ga4-insights' ); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr( GA4_Insights_Settings::OPTION_KEY ); ?>[endpoint]" type="url" id="ga4-insights-endpoint" value="<?php echo esc_attr( $settings['endpoint'] ); ?>" class="regular-text" required />
                        <p class="description"><?php esc_html_e( 'URL dell\'endpoint MCP, ad esempio http://127.0.0.1:8080/chat.', 'ga4-insights' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ga4-insights-username"><?php esc_html_e( 'Username Basic Auth', 'ga4-insights' ); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr( GA4_Insights_Settings::OPTION_KEY ); ?>[username]" type="text" id="ga4-insights-username" value="<?php echo esc_attr( $settings['username'] ); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e( 'Opzionale. Lascia vuoto se l\'endpoint non richiede autenticazione Basic.', 'ga4-insights' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ga4-insights-password"><?php esc_html_e( 'Password Basic Auth', 'ga4-insights' ); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr( GA4_Insights_Settings::OPTION_KEY ); ?>[password]" type="password" id="ga4-insights-password" value="<?php echo esc_attr( $settings['password'] ); ?>" class="regular-text" autocomplete="off" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ga4-insights-token"><?php esc_html_e( 'API Token', 'ga4-insights' ); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr( GA4_Insights_Settings::OPTION_KEY ); ?>[api_token]" type="text" id="ga4-insights-token" value="<?php echo esc_attr( $settings['api_token'] ); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e( 'Opzionale. Verrà inviato nell\'header X-API-Token.', 'ga4-insights' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ga4-insights-model"><?php esc_html_e( 'Nome modello AI', 'ga4-insights' ); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr( GA4_Insights_Settings::OPTION_KEY ); ?>[model_name]" type="text" id="ga4-insights-model" value="<?php echo esc_attr( $settings['model_name'] ); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e( 'Identificativo del modello AI da utilizzare per le richieste.', 'ga4-insights' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ga4-insights-timeout"><?php esc_html_e( 'Timeout richiesta (secondi)', 'ga4-insights' ); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr( GA4_Insights_Settings::OPTION_KEY ); ?>[timeout]" type="number" min="5" step="1" id="ga4-insights-timeout" value="<?php echo esc_attr( $settings['timeout'] ); ?>" class="small-text" />
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button( __( 'Salva impostazioni', 'ga4-insights' ) ); ?>
    </form>
</div>
