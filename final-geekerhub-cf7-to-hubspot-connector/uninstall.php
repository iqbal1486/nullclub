<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

/*
 * Deleted options when plugin uninstall.
 */
delete_option( 'geek_cf7_hubspot_api_manager' );
delete_option( 'geek_cf7_hubspot_client_id' );
delete_option( 'geek_cf7_hubspot_client_secret' );
delete_option( 'geek_cf7_hubspot_modules' );
delete_option( 'geek_cf7_hubspot_modules_fields' );