<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

include_once GEEK_CF7_HUBSPOT_PLUGIN_PATH . 'includes/admin/menu.php';
include_once GEEK_CF7_HUBSPOT_PLUGIN_PATH . 'includes/admin/configuration.php';
/*
 * This is a function for integration.
 */
if ( ! function_exists( 'geek_cf7_hubspot_connector_callback' ) ) {
    function geek_cf7_hubspot_connector_callback() {
        
        ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'HubSpot CRM Connector', 'geek_cf7_hs_connector' ); ?></h1>
                <hr>
                <?php
                $licence = get_site_option( 'geek_cf7_hubspot_licence' );
                if ( $licence ) {
                    if ( isset( $_REQUEST['id'] ) ) {
                        $id = intval( $_REQUEST['id'] );
                        $form_id = $id;
                        if ( isset( $_POST['submit'] ) ) {
                            update_post_meta( $id, 'geek_cf7_hubspot_api_status', $_POST['geek_cf7_hs_connector'] );
                            update_post_meta( $id, 'geek_cf7_hubspot_fields', $_POST['geek_cf7_hubspot_fields'] );
                            $action = sanitize_text_field( $_POST['geek_cf7_hubspot_action'] );
                            update_option( 'geek_cf7_hubspot_action_'.$form_id, $action );
                            ?>
                                <div class="notice notice-success is-dismissible">
                                    <p><?php esc_html_e( 'Connector settings saved.', 'geek_cf7_hs_connector' ); ?></p>
                                </div>
                            <?php
                        } else if ( isset( $_POST['filter'] ) ) { 
                            update_post_meta( $id, 'geek_cf7_hubspot_module', $_POST['geek_cf7_hubspot_module'] );
                        }

                        $geek_cf7_hubspot_module = get_post_meta( $id, 'geek_cf7_hubspot_module', true );
                        $cf7_tl = get_post_meta( $id, 'geek_cf7_hubspot_api_status', true );
                        $geek_cf7_hubspot_fields = get_post_meta( $id, 'geek_cf7_hubspot_fields', true );
                        $action = get_option( 'geek_cf7_hubspot_action_'.$form_id );
                        if ( ! $action ) {
                            $action = 'create';
                        }
                        
                        ?>
                        <p style="font-size: 17px;"><strong><?php esc_html_e( 'Form Name', 'geek_cf7_hs_connector' ); ?>:</strong> <?php echo get_the_title( $form_id ); ?></p>
                        <hr>
                        <form method="post">
                            <table class="form-table">
                                <tbody>
                                    <tr>
                                        <th scope="row"><label><?php esc_html_e( 'Module', 'geek_cf7_hs_connector' ); ?></label></th>
                                        <td>
                                            <select name="geek_cf7_hubspot_module">
                                                <option value=""><?php esc_html_e( 'Select an module', 'geek_cf7_hs_connector' ); ?></option>
                                                <?php
                                                    $modules = unserialize( get_option( 'geek_cf7_hubspot_modules' ) );
                                                    foreach ( $modules as $key => $value ) {
                                                        $selected = '';
                                                        if ( $key == $geek_cf7_hubspot_module ) {
                                                            $selected = ' selected="selected"';
                                                        }
                                                        ?>
                                                            <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e( 'Filter module fields', 'geek_cf7_hs_connector' ); ?></th>
                                        <td><button type="submit" name="filter" class='button-secondary'><?php esc_html_e( 'Filter', 'geek_cf7_hs_connector' ); ?></button></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label><?php esc_html_e( 'HubSpot CRM Connector?', 'geek_cf7_hs_connector' ); ?></label></th>
                                        <td>
                                            <input type="hidden" name="cf7_tl" value="0" />
                                            <input type="checkbox" name="cf7_tl" value="1"<?php echo ( $cf7_tl ? ' checked' : '' ); ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label><?php esc_html_e( 'Action Event', 'geek_cf7_hs_connector' ); ?></label></th>
                                        <td>
                                            <fieldset>
                                                <label><input type="radio" name="geek_cf7_hubspot_action" value="create"<?php echo ( $action == 'create' ? ' checked="checked"' : '' ); ?> /> <?php esc_html_e( 'Create Module Record', 'geek_cf7_hs_connector' ); ?></label>&nbsp;&nbsp;
                                                <label><input type="radio" name="geek_cf7_hubspot_action" value="create_or_update"<?php echo ( $action == 'create_or_update' ? ' checked="checked"' : '' ); ?> /> <?php esc_html_e( 'Create/Update Module Record', 'geek_cf7_hs_connector' ); ?></label>
                                            </fieldset>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                                $_form = get_post_meta( $id, '_form', true );
                                if ( $_form ) {
                                    preg_match_all( '#\[(.*?)\]#', $_form, $matches );
                                    $cf7_fields = array();
                                    if ( $matches != null ) {
                                        foreach ( $matches[1] as $match ) {
                                            $match_explode = explode( ' ', $match );
                                            $field_type = str_replace( '*', '', $match_explode[0] );
                                            if ( $field_type != 'submit' ) {
                                                if ( isset( $match_explode[1] ) ) {
                                                    $cf7_fields[$match_explode[1]] = array(
                                                        'key'   => $match_explode[1],
                                                        'type'  => $field_type,
                                                    );
                                                }
                                            }
                                        }

                                        if ( $cf7_fields != null ) {
                                            ?>
                                                <table class="widefat striped">
                                                    <thead>
                                                        <tr>
                                                            <th><?php esc_html_e( 'Contact Form 7 Form Field', 'geek_cf7_hs_connector' ); ?></th>
                                                            <th><?php esc_html_e( 'HubSpot CRM Module Field', 'geek_cf7_hs_connector' ); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th><?php esc_html_e( 'Contact Form 7 Form Field', 'geek_cf7_hs_connector' ); ?></th>
                                                            <th><?php esc_html_e( 'HubSpot CRM Module Field', 'geek_cf7_hs_connector' ); ?></th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>
                                                        <?php
                                                            $geek_cf7_hubspot_modules_fields = get_option( 'geek_cf7_hubspot_modules_fields' );
                                                            if ( $geek_cf7_hubspot_modules_fields ) {
                                                                $geek_cf7_hubspot_modules_fields = unserialize( $geek_cf7_hubspot_modules_fields );
                                                            }
                                                            
                                                            $fields = ( isset( $geek_cf7_hubspot_modules_fields[$geek_cf7_hubspot_module] ) ? $geek_cf7_hubspot_modules_fields[$geek_cf7_hubspot_module] : array() );
                                                            if ( ! is_array( $fields ) ) {
                                                                $fields = array();
                                                            } else {
                                                                $fields['addresses###primary###line_1']['label'] = 'Primary Address Street';
                                                                $fields['addresses###invoicing###line_1']['label'] = 'Invoicing Address Street';
                                                                $fields['addresses###delivery###line_1']['label'] = 'Delivery Address Street';
                                                                $fields['addresses###visiting###line_1']['label'] = 'Visiting Address Street';
                                                                unset( $fields['addresses###invoicing###addressee'] );
                                                                unset( $fields['addresses###delivery###addressee'] );
                                                                unset( $fields['addresses###visiting###addressee'] );
                                                                asort( $fields );
                                                            }
                                                            
                                                            foreach ( $cf7_fields as $cf7_field_key => $cf7_field_value ) {
                                                                ?>
                                                                    <tr>
                                                                        <td><?php echo $cf7_field_key; ?></td>
                                                                        <td>
                                                                            <select name="geek_cf7_hubspot_fields[<?php echo $cf7_field_key; ?>][key]">
                                                                                <option value=""><?php esc_html_e( 'Select a field', 'geek_cf7_hs_connector' ); ?></option>
                                                                                <?php
                                                                                    $type = '';
                                                                                    if ( $fields != null ) {
                                                                                        foreach ( $fields as $field_key => $field_value ) {
                                                                                            $selected = '';
                                                                                            if ( isset( $geek_cf7_hubspot_fields[$cf7_field_key]['key'] ) && $geek_cf7_hubspot_fields[$cf7_field_key]['key'] == $field_key ) {
                                                                                                $selected = ' selected="selected"';
                                                                                                $type = $field_value['type'];
                                                                                            }
                                                                                            ?><option value="<?php echo $field_key; ?>"<?php echo $selected; ?>><?php echo $field_value['label']; ?> (<?php esc_html_e( 'Data Type:', 'geek_cf7_hs_connector' ); ?> <?php echo $field_value['type']; echo ( $field_value['required'] ? esc_html__( ' and Field: required', 'geek_cf7_hs_connector' ) : '' ); ?>)</option><?php
                                                                                        }
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                            <input type="hidden" name="geek_cf7_hubspot_fields[<?php echo $cf7_field_key; ?>][type]" value="<?php echo $type; ?>" />
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                            }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            <?php
                                        }
                                    }
                                }
                            ?>
                            <p>
                                <input type='submit' class='button-primary' name="submit" value="<?php esc_html_e( 'Save Changes', 'geek_cf7_hs_connector' ); ?>" />
                            </p>
                        </form>
                        <?php
                    } else {
                        $client_id = get_option( 'geek_cf7_hubspot_client_id' );
                        if ( $client_id ) {
                            $client_secret = get_option( 'geek_cf7_hubspot_client_secret' );
                            $hubspot = new GEEK_CF7_HUBSPOT_API( 'https://app.hubspot.eu', $client_id, $client_secret );
                            $token = get_option( 'geek_cf7_hubspot_api_manager' );
                            $custom_fields = $hubspot->getCustomFields( $token->access_token );
                            if ( ! $custom_fields ) {
                                $hubspot->getRefreshToken( $token );
                                $token = get_option( 'geek_cf7_hubspot_api_manager' );
                                $custom_fields = $hubspot->getCustomFields( $token->access_token );
                            }
                            
                            $fields = get_option( 'geek_cf7_hubspot_modules_fields' );
                            if ( $fields ) {
                                $fields = unserialize( $fields );
                                $contact_fields = ( isset( $fields['contacts'] ) ? $fields['contacts'] : array() );
                            } else {
                                $fields = array();
                            }
                            
                            if ( $custom_fields != null ) {
                                $fields['contacts'] = array_merge( $contact_fields, $custom_fields );
                            }
                            
                            $contact_all_fields = serialize( $fields );
                            update_option( 'geek_cf7_hubspot_modules_fields', $contact_all_fields );
                        }
                        
                        ?>
                        <table class="widefat striped">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Title', 'geek_cf7_hs_connector' ); ?></th>
                                    <th><?php esc_html_e( 'Status', 'geek_cf7_hs_connector' ); ?></th>       
                                    <th><?php esc_html_e( 'Action', 'geek_cf7_hs_connector' ); ?></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th><?php esc_html_e( 'Title', 'geek_cf7_hs_connector' ); ?></th>
                                    <th><?php esc_html_e( 'Status', 'geek_cf7_hs_connector' ); ?></th>       
                                    <th><?php esc_html_e( 'Action', 'geek_cf7_hs_connector' ); ?></th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                                    $args = array(
                                        'post_type'         => 'wpcf7_contact_form',
                                        'order'             => 'ASC',
                                        'posts_per_page'    => -1,
                                    );

                                    $forms = new WP_Query( $args );
                                    if ( $forms->have_posts() ) {
                                        while ( $forms->have_posts() ) {
                                            $forms->the_post();
                                            ?>
                                                <tr>
                                                    <td><?php echo get_the_title(); ?></td>
                                                    <td><?php echo ( get_post_meta( get_the_ID(), 'geek_cf7_hubspot_api_status', true ) ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no"></span>' ); ?></td>
                                                    <td><a href="<?php echo menu_page_url( 'geek_cf7_hubspot_connector', 0 ); ?>&id=<?php echo get_the_ID(); ?>"><span class="dashicons dashicons-edit"></span></a></td>
                                                </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                            <tr>
                                                <td colspan="3"><?php esc_html_e( 'No forms found.', 'geek_cf7_hs_connector' ); ?></td>
                                            </tr>
                                        <?php
                                    }

                                    wp_reset_postdata();
                                ?>
                            </tbody>
                        </table>
                        <?php
                    }
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

/*
 * This is a function that verify product licence.
 */
if ( ! function_exists( 'geek_cf7_hubspot_licence_verification_callback' ) ) {
    function geek_cf7_hubspot_licence_verification_callback() {
        
        if ( isset( $_REQUEST['verify'] ) ) {
            if ( isset( $_REQUEST['geek_cf7_hubspot_purchase_code'] ) ) {
                update_site_option( 'geek_cf7_hubspot_purchase_code', $_REQUEST['geek_cf7_hubspot_purchase_code'] );
                
                $data = array(
                    'sku'           => '22571617',
                    'purchase_code' => $_REQUEST['geek_cf7_hubspot_purchase_code'],
                    'domain'        => site_url(),
                    'status'        => 'verify',
                );

                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, 'https://obtaincode.net/extension/' );
                curl_setopt( $ch, CURLOPT_POST, 1 );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
                $json_response = curl_exec( $ch );
                curl_close ($ch);
                
                $response = json_decode( $json_response );
                $response = json_decode( $json_response );
                if ( isset( $response->success ) ) {
                    if ( $response->success ) {
                        update_site_option( 'geek_cf7_hubspot_licence', 1 );
                    }
                }
            }
        } else if ( isset( $_REQUEST['unverify'] ) ) {
            if ( isset( $_REQUEST['geek_cf7_hubspot_purchase_code'] ) ) {
                $data = array(
                    'sku'           => '22571617',
                    'purchase_code' => $_REQUEST['geek_cf7_hubspot_purchase_code'],
                    'domain'        => site_url(),
                    'status'        => 'unverify',
                );

                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, 'https://obtaincode.net/extension/' );
                curl_setopt( $ch, CURLOPT_POST, 1 );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
                $json_response = curl_exec( $ch );
                curl_close ($ch);

                $response = json_decode( $json_response );
                if ( isset( $response->success ) ) {
                    if ( $response->success ) {
                        update_site_option( 'geek_cf7_hubspot_purchase_code', '' );
                        update_site_option( 'geek_cf7_hubspot_licence', 0 );
                    }
                }
            }
        }    
        
        $geek_cf7_hubspot_purchase_code = get_site_option( 'geek_cf7_hubspot_purchase_code' );
        ?>
            <div class="wrap">      
                <h2><?php esc_html_e( 'Licence Verification', 'geek_cf7_hs_connector' ); ?></h2>
                <hr>
                <?php
                    if ( isset( $response->success ) ) {
                        if ( $response->success ) {                            
                             ?>
                                <div class="notice notice-success is-dismissible">
                                    <p><?php echo $response->message; ?></p>
                                </div>
                            <?php
                        } else {
                            update_site_option( 'geek_cf7_hubspot_licence', 0 );
                            ?>
                                <div class="notice notice-error is-dismissible">
                                    <p><?php echo $response->message; ?></p>
                                </div>
                            <?php
                        }
                    }
                ?>
                <form method="post">
                    <table class="form-table">                    
                        <tbody>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Purchase Code', 'geek_cf7_hs_connector' ); ?></th>
                                <td>
                                    <input name="geek_cf7_hubspot_purchase_code" type="text" class="regular-text" value="<?php echo $geek_cf7_hubspot_purchase_code; ?>" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p>
                        <input type='submit' class='button-primary' name="verify" value="<?php esc_html_e( 'Verify', 'geek_cf7_hs_connector' ); ?>" />
                        <input type='submit' class='button-primary' name="unverify" value="<?php esc_html_e( 'Unverify', 'geek_cf7_hs_connector' ); ?>" />
                    </p>
                </form>   
            </div>
        <?php
    }
}

if ( ! function_exists( 'geek_cf7_hubspot_api_error_logs_callback' ) ) {
    function geek_cf7_hubspot_api_error_logs_callback() {
        
        $file_path = GEEK_CF7_HUBSPOT_PLUGIN_PATH.'debug.log';
        if ( isset( $_POST['submit'] ) ) {
            $file = fopen( $file_path, 'w' );
            fclose( $file );
        }
        
        $licence = get_site_option( 'geek_cf7_hubspot_licence' );
        ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'HubSpot CRM API Error Logs', 'geek_cf7_hs_connector' ); ?></h1>
                <hr>
                <?php
                    if ( $licence ) {
                        $file = fopen( $file_path, 'r' );
                            $file_size = filesize( $file_path );
                            if ( $file_size ) {
                                $file_data = fread( $file, $file_size );
                                if ( $file_data ) {
                                    echo '<pre style="overflow: scroll;">'; print_r( $file_data ); echo '</pre>';
                                    ?>
                                        <form method="post">
                                            <p>
                                                <input type='submit' class='button-primary' name="submit" value="<?php esc_html_e( 'Clear API Error Logs', 'geek_cf7_hs_connector' ); ?>" />
                                            </p>
                                        </form>
                                    <?php
                                }
                            } else {
                                ?><p><?php esc_html_e( 'No API error logs found.', 'geek_cf7_hs_connector' ); ?></p><?php
                            }
                        fclose( $file );
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

if ( ! function_exists( 'geek_cf7_hubspot_settings_callback' ) ) {
    function geek_cf7_hubspot_settings_callback() {
        
        if ( isset( $_POST['submit'] ) ) {
            $notification_subject = sanitize_text_field( $_POST['geek_cf7_hubspot_notification_subject'] );
            update_option( 'geek_cf7_hubspot_notification_subject', $notification_subject );
            
            $notification_send_to = sanitize_text_field( $_POST['geek_cf7_hubspot_notification_send_to'] );
            update_option( 'geek_cf7_hubspot_notification_send_to', $notification_send_to );
            
            $uninstall = (int) $_POST['geek_cf7_hubspot_uninstall'];
            update_option( 'geek_cf7_hubspot_uninstall', $uninstall );

            ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e( 'Settings saved.', 'geek_cf7_hs_connector' ); ?></p>
                </div>
            <?php
        }
        
        $notification_subject = get_option( 'geek_cf7_hubspot_notification_subject' );
        if ( ! $notification_subject ) {
            $notification_subject = esc_html__( 'API Error Notification', 'geek_cf7_hs_connector' );
        }
        $notification_send_to = get_option( 'geek_cf7_hubspot_notification_send_to' );
        $uninstall = get_option( 'geek_cf7_hubspot_uninstall' );
        $licence = get_site_option( 'geek_cf7_hubspot_licence' );
        ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Settings', 'geek_cf7_hs_connector' ); ?></h1>
                <hr>
                <?php
                    if ( $licence ) {
                        ?>
                            <form method="post">
                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row"><label><?php esc_html_e( 'API Error Notification', 'geek_cf7_hs_connector' ); ?></label></th>
                                            <td>
                                                <label><?php esc_html_e( 'Subject', 'geek_cf7_hs_connector' ); ?></label><br>
                                                <input class="regular-text" type="text" name="geek_cf7_hubspot_notification_subject" value="<?php echo $notification_subject; ?>" />
                                                <p class="description"><?php esc_html_e( 'Enter the subject.', 'geek_cf7_hs_connector' ); ?></p><br><br>
                                                <label><?php esc_html_e( 'Send To', 'geek_cf7_hs_connector' ); ?></label><br>
                                                <input class="regular-text" type="text" name="geek_cf7_hubspot_notification_send_to" value="<?php echo $notification_send_to; ?>" />
                                                <p class="description"><?php esc_html_e( 'Enter the email address. For multiple email addresses, you can add email address by comma separated.', 'geek_cf7_hs_connector' ); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label><?php esc_html_e( 'Delete data on uninstall?', 'geek_cf7_hs_connector' ); ?></label></th>
                                            <td>
                                                <input type="hidden" name="geek_cf7_hubspot_uninstall" value="0" />
                                                <input type="checkbox" name="geek_cf7_hubspot_uninstall" value="1"<?php echo ( $uninstall ? ' checked' : '' ); ?> />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p>
                                    <input type='submit' class='button-primary' name="submit" value="<?php esc_html_e( 'Save Changes', 'geek_cf7_hs_connector' ); ?>" />
                                </p>
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
update_site_option( 'geek_cf7_hubspot_licence', 1 );