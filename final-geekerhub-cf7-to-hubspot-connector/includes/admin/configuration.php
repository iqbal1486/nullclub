<?php
/*
 * This is a function for configuration.
 */
if ( ! function_exists( 'geek_cf7_hubspot_configuration_callback' ) ) {
    function geek_cf7_hubspot_configuration_callback() {
        
        if ( isset( $_REQUEST['submit'] ) ) {
            $client_id = $_REQUEST['geek_cf7_hubspot_client_id'];
            $client_secret = $_REQUEST['geek_cf7_hubspot_client_secret'];
            
            update_option( 'geek_cf7_hubspot_client_id', $client_id );
            update_option( 'geek_cf7_hubspot_client_secret', $client_secret );
            
            if ( $client_id && $client_secret ) {
                $redirect_uri = urlencode( menu_page_url( 'geek_cf7_hubspot_configuration', 0 ) );
                $url = "https://app.hubspot.eu/oauth2/authorize?client_id=$client_id&redirect_uri=$redirect_uri&response_type=code";
                ?>
                    <script type="text/javascript">
                        jQuery( document ).ready( function( $ ) {
                            window.location.replace( '<?php echo $url; ?>' );
                        });
                    </script>
                <?php
            }
        } else if ( isset( $_REQUEST['code'] ) ) {
            $client_id = get_option( 'geek_cf7_hubspot_client_id' );
            $client_secret = get_option( 'geek_cf7_hubspot_client_secret' );
            $code = $_REQUEST['code'];
            $redirect_uri = menu_page_url( 'geek_cf7_hubspot_configuration', 0 );
            $hubspot = new GEEK_CF7_HUBSPOT_API( 'https://app.hubspot.eu', $client_id, $client_secret );
            $token = $hubspot->getToken( $code, $redirect_uri );
            if ( isset( $token->errors ) ) {
                ?>
                    <div class="notice notice-error is-dismissible">
                        <p><strong><?php esc_html_e( 'Error', 'geek_cf7_hs_connector' ); ?></strong>: <?php echo json_encode( $token->errors ); ?></p>
                    </div>
                <?php
            } else {
                update_option( 'geek_cf7_hubspot_api_manager', $token );
                $redirect_uri = menu_page_url( 'geek_cf7_hubspot_connector', 0 );
                ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php esc_html_e( 'Configuration successful.', 'geek_cf7_hs_connector' ); ?></p>
                    </div>
                    <script type="text/javascript">
                        jQuery( document ).ready( function( $ ) {
                            window.setTimeout(function(){
                                window.location.replace( '<?php echo $redirect_uri; ?>' );
                            }, 3000);
                        });
                    </script>
                <?php
            }
        }
        
        $client_id = get_option( 'geek_cf7_hubspot_client_id' );
        $client_secret = get_option( 'geek_cf7_hubspot_client_secret' );
        ?>
        <div class="wrap">                
            <h1><?php esc_html_e( 'HubSpot CRM Configuration', 'geek_cf7_hs_connector' ); ?></h1>
            <hr>
            <?php
            $licence = get_site_option( 'geek_cf7_hubspot_licence' );
            if ( $licence ) {
            ?>
            <form method="post">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Client ID', 'geek_cf7_hs_connector' ); ?> <span class="description">(required)</span></label></th>
                            <td>
                                <input class="regular-text" type="text" name="geek_cf7_hubspot_client_id" value="<?php echo $client_id; ?>" required />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Client Secret', 'geek_cf7_hs_connector' ); ?> <span class="description">(required)</span></label></th>
                            <td>
                                <input class="regular-text" type="text" name="geek_cf7_hubspot_client_secret" value="<?php echo $client_secret; ?>" required />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p><input type='submit' class='button-primary' name="submit" value="<?php esc_html_e( 'Authorize', 'geek_cf7_hs_connector' ); ?>" /></p>
            </form>
            <?php
            } else {
                ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php esc_html_e( 'Please verify purchase code.', 'geek_cf7_hs_connector' ); ?></p>
                    </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
}
?>