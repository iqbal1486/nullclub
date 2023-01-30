<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that integrate form.
 * $cf7 variable return current form data.
 */
if ( ! function_exists( 'geek_cf7_hubspot_connector' ) ) {
    add_action( 'wpcf7_before_send_mail', 'geek_cf7_hubspot_connector', 20, 1 );
    function geek_cf7_hubspot_connector( $cf7 ) {
        
        $submission = WPCF7_Submission::get_instance();
        if ( $submission ) {
          $request = $submission->get_posted_data();     
        }
        
        $form_id = 0;
        if ( isset( $request['_wpcf7'] ) ) {
            $form_id = intval( $request['_wpcf7'] );
        } else if ( isset( $_POST['_wpcf7'] ) ) {
            $form_id = intval( $_POST['_wpcf7'] );
        } else {
            //
        }
        
        if ( $form_id ) {
            $cf7_tl = get_post_meta( $form_id, 'geek_cf7_hubspot_api_status', true );
            if ( $cf7_tl ) {
                $geek_cf7_hubspot_fields = get_post_meta( $form_id, 'geek_cf7_hubspot_fields', true );
                if ( $geek_cf7_hubspot_fields != null ) {
                    $data = array();
                    $email = '';
                    foreach ( $geek_cf7_hubspot_fields as $geek_cf7_hubspot_field_key => $geek_cf7_hubspot_field ) {
                        if ( isset( $geek_cf7_hubspot_field['key'] ) && $geek_cf7_hubspot_field['key'] ) {
                            if ( is_array( $request[$geek_cf7_hubspot_field_key] ) ) {
                                $request[$geek_cf7_hubspot_field_key] = implode( ', ', $request[$geek_cf7_hubspot_field_key] );
                            }
                            
                            if ( strpos( $geek_cf7_hubspot_field['key'], '###' ) !== false ) {
                                $geek_cf7_hubspot_field_data = explode( '###', $geek_cf7_hubspot_field['key'] );
                                if ( $geek_cf7_hubspot_field_data[0] == 'emails' ) {
                                    $data['emails'][] = array(
                                        'email' => strip_tags( $request[$geek_cf7_hubspot_field_key] ),
                                        'type'  => $geek_cf7_hubspot_field_data[1],
                                    );
                                    $email = strip_tags( $request[$geek_cf7_hubspot_field_key] );
                                } else if ( $geek_cf7_hubspot_field_data[0] == 'telephones' ) {
                                    $data['telephones'][] = array(
                                        'number'    => strip_tags( $request[$geek_cf7_hubspot_field_key] ),
                                        'type'      => $geek_cf7_hubspot_field_data[1],
                                    );
                                } else if ( $geek_cf7_hubspot_field_data[0] == 'addresses' ) {
                                    $data['addresses'][$geek_cf7_hubspot_field_data[1]]['type'] = $geek_cf7_hubspot_field_data[1];
                                    $data['addresses'][$geek_cf7_hubspot_field_data[1]]['address'][$geek_cf7_hubspot_field_data[2]] = strip_tags( $request[$geek_cf7_hubspot_field_key] );
                                } else if ( $geek_cf7_hubspot_field_data[0] == 'custom_fields' ) {
                                    $data['custom_fields'][] = array(
                                        'value' => strip_tags( $request[$geek_cf7_hubspot_field_key] ),
                                        'id'    => $geek_cf7_hubspot_field_data[1],
                                    );
                                }
                            } else {
                                $data[$geek_cf7_hubspot_field['key']] = strip_tags( $request[$geek_cf7_hubspot_field_key] );
                            }
                        }
                    }
                    
                    if ( isset( $data['addresses'] ) ) {
                        $addresses_data = array();
                        foreach ( $data['addresses'] as $addresses ) {
                            $addresses_data[] = $addresses;
                        }

                        $data['addresses'] = $addresses_data;
                    }
                    
                    if ( isset( $data['tags'] ) && $data['tags'] ) {
                        $tags = explode( ',', $data['tags'] );
                        if ( $tags != null ) {
                            $data['tags'] = $tags;
                        }
                    }
                    
                    if ( $data != null ) {
                        $client_id = get_option( 'geek_cf7_hubspot_client_id' );
                        $client_secret = get_option( 'geek_cf7_hubspot_client_secret' );
                        $hubspot = new GEEK_CF7_HUBSPOT_API( 'https://app.hubspot.eu', $client_id, $client_secret );
                        $token = get_option( 'geek_cf7_hubspot_api_manager' );
                        $hubspot->getRefreshToken( $token );
                        $token = get_option( 'geek_cf7_hubspot_api_manager' );
                        $module = get_post_meta( $form_id, 'geek_cf7_hubspot_module', true );
                        $action = get_option( 'geek_cf7_hubspot_action_'.$form_id );
                        if ( ! $action ) {
                            $action = 'create';
                        }
                        
                        if ( $action == 'create' ) {
                            $hubspot->addRecord( $token->access_token, $module, $data, $form_id );
                        } else if ( $action == 'create_or_update' ) {
                            if ( $email ) {
                                $records = $hubspot->getRecords( $token->access_token, $module, $email );
                                if ( isset( $records->data ) && $records->data != null ) {
                                    foreach ( $records->data as $record ) {
                                        $record_id = $record->id;
                                        $hubspot->updateRecord( $token->access_token, $module, $data, $record_id, $form_id );
                                    }
                                } else {
                                    $hubspot->addRecord( $token->access_token, $module, $data, $form_id );
                                }
                            } else {
                                $hubspot->addRecord( $token->access_token, $module, $data, $form_id );
                            }
                        } else {
                            // nothing
                        }
                    }
                }
            }
        }
    }
}