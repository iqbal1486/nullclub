<?php
function geek_cf7_hubspot_main_menu() {
    add_menu_page( 
		esc_html__( 'Contact Form 7 - HubSpot CRM Connector', 'geek_cf7_hs_connector' ), 
		esc_html__( 'CF7 - HubSpot Connector', 'geek_cf7_hs_connector' ), 
		'manage_options',
		'geek_cf7_hubspot_connector',
		'geek_cf7_hubspot_connector_callback',
		'dashicons-rest-api'
	);

    add_submenu_page( 
    	'geek_cf7_hubspot_connector', 
    	esc_html__( 'CF7 - HubSpot: Connector', 'geek_cf7_hs_connector' ), 
    	esc_html__( 'Connector', 'geek_cf7_hs_connector' ), 
    	'manage_options', 
    	'geek_cf7_hubspot_connector', 
    	'geek_cf7_hubspot_connector_callback' 
    );
    

    add_submenu_page( 
    	'geek_cf7_hubspot_connector', 
    	esc_html__( 'CF7 - HubSpot: Settings', 'geek_cf7_hs_connector' ), 
    	esc_html__( 'Settings', 'geek_cf7_hs_connector' ), 
    	'manage_options', 
    	'geek_cf7_hubspot_settings', 
    	'geek_cf7_hubspot_settings_callback' 
    );


    add_submenu_page( 
    	'geek_cf7_hubspot_connector', 
    	esc_html__( 'CF7 - HubSpot: Configuration', 'geek_cf7_hs_connector' ), 
    	esc_html__( 'Configuration', 'geek_cf7_hs_connector' ), 
    	'manage_options', 
    	'geek_cf7_hubspot_configuration', 
    	'geek_cf7_hubspot_configuration_callback' 
    );


    add_submenu_page( 
    	'geek_cf7_hubspot_connector', 
    	esc_html__( 'CF7 - HubSpot: Licence Verification', 'geek_cf7_hs_connector' ), 
    	esc_html__( 'Licence Verification', 'geek_cf7_hs_connector' ), 
    	'manage_options', 
    	'geek_cf7_hubspot_licence_verification', 
    	'geek_cf7_hubspot_licence_verification_callback' 
    );

    add_submenu_page( 
    	'geek_cf7_hubspot_connector', 
    	esc_html__( 'CF7 - HubSpot: API Error Logs', 'geek_cf7_hs_connector' ), 
    	esc_html__( 'API Error Logs', 'geek_cf7_hs_connector' ), 
    	'manage_options', 
    	'geek_cf7_hubspot_api_error_logs', 
    	'geek_cf7_hubspot_api_error_logs_callback' 
    );
}
add_action( 'admin_menu', 'geek_cf7_hubspot_main_menu' );
?>