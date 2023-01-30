<?php
/**
 * Plugin Name:       Geek CF7 to Hubspot Connector
 * Plugin URI:        https://profiles.wordpress.org/iqbal1486/
 * Description:       WP Smart Zoho help you to manage and synch possible WordPress data like customers, orders, products to the Zoho modules as per your settings options.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Geekerhub
 * Author URI:        https://profiles.wordpress.org/iqbal1486/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       geek-cf7-hubspot
 * Domain Path:       /languages
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}


define( 'GEEK_CF7_HUBSPOT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

include_once GEEK_CF7_HUBSPOT_PLUGIN_PATH . 'includes/class-hubspot-api.php';
include_once GEEK_CF7_HUBSPOT_PLUGIN_PATH . 'includes/admin/init.php';
include_once GEEK_CF7_HUBSPOT_PLUGIN_PATH . 'includes/functions.php';

/*
 * This is a function that run when plugin activation.
 */
if ( ! function_exists( 'geek_cf7_hubspot_activation' ) ) {
    register_activation_hook( __FILE__, 'geek_cf7_hubspot_activation' );
    function geek_cf7_hubspot_activation() {
        
        update_option( 'geek_cf7_hubspot_modules', 'a:1:{s:8:"contacts";s:8:"Contacts";}' );
        $fields = get_option( 'geek_cf7_hubspot_modules_fields' );
        if ( ! $fields ) {
            update_option( 'geek_cf7_hubspot_modules_fields', 'a:1:{s:8:"contacts";a:36:{s:10:"first_name";a:3:{s:5:"label";s:10:"First Name";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:9:"last_name";a:3:{s:5:"label";s:9:"Last Name";s:4:"type";s:16:"Single Line Text";s:8:"required";i:1;}s:16:"emails###primary";a:3:{s:5:"label";s:13:"Primary Email";s:4:"type";s:13:"Email Address";s:8:"required";i:0;}s:18:"emails###invoicing";a:3:{s:5:"label";s:15:"Invoicing Email";s:4:"type";s:13:"Email Address";s:8:"required";i:0;}s:10:"salutation";a:3:{s:5:"label";s:10:"Salutation";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:18:"telephones###phone";a:3:{s:5:"label";s:5:"Phone";s:4:"type";s:6:"Number";s:8:"required";i:0;}s:19:"telephones###mobile";a:3:{s:5:"label";s:6:"Mobile";s:4:"type";s:6:"Number";s:8:"required";i:0;}s:16:"telephones###fax";a:3:{s:5:"label";s:3:"Fax";s:4:"type";s:6:"Number";s:8:"required";i:0;}s:7:"website";a:3:{s:5:"label";s:7:"Website";s:4:"type";s:3:"URL";s:8:"required";i:0;}s:8:"language";a:3:{s:5:"label";s:8:"Language";s:4:"type";s:16:"Single Selection";s:8:"required";i:0;}s:6:"gender";a:3:{s:5:"label";s:6:"Gender";s:4:"type";s:16:"Single Selection";s:8:"required";i:0;}s:9:"birthdate";a:3:{s:5:"label";s:9:"Birthdate";s:4:"type";s:4:"Date";s:8:"required";i:0;}s:4:"iban";a:3:{s:5:"label";s:4:"IBAN";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:3:"bic";a:3:{s:5:"label";s:11:"BIC - SWIFT";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:7:"remarks";a:3:{s:5:"label";s:7:"Remarks";s:4:"type";s:14:"Multiline Text";s:8:"required";i:0;}s:4:"tags";a:3:{s:5:"label";s:4:"Tags";s:4:"type";s:18:"Multiple Selection";s:8:"required";i:0;}s:23:"marketing_mails_consent";a:3:{s:5:"label";s:22:"Opt-in Marketing Mails";s:4:"type";s:6:"Yes/No";s:8:"required";i:0;}s:28:"addresses###primary###line_1";a:3:{s:5:"label";s:22:"Primary Address Line 2";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:33:"addresses###primary###postal_code";a:3:{s:5:"label";s:24:"Primary Address Zip Code";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:26:"addresses###primary###city";a:3:{s:5:"label";s:20:"Primary Address City";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:29:"addresses###primary###country";a:3:{s:5:"label";s:23:"Primary Address Country";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:33:"addresses###invoicing###addressee";a:3:{s:5:"label";s:24:"Invoicing Address Line 1";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:30:"addresses###invoicing###line_1";a:3:{s:5:"label";s:24:"Invoicing Address Line 2";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:35:"addresses###invoicing###postal_code";a:3:{s:5:"label";s:26:"Invoicing Address Zip Code";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:28:"addresses###invoicing###city";a:3:{s:5:"label";s:22:"Invoicing Address City";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:31:"addresses###invoicing###country";a:3:{s:5:"label";s:25:"Invoicing Address Country";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:32:"addresses###delivery###addressee";a:3:{s:5:"label";s:23:"Delivery Address Line 1";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:29:"addresses###delivery###line_1";a:3:{s:5:"label";s:23:"Delivery Address Line 2";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:34:"addresses###delivery###postal_code";a:3:{s:5:"label";s:25:"Delivery Address Zip Code";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:27:"addresses###delivery###city";a:3:{s:5:"label";s:21:"Delivery Address City";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:30:"addresses###delivery###country";a:3:{s:5:"label";s:24:"Delivery Address Country";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:32:"addresses###visiting###addressee";a:3:{s:5:"label";s:23:"Visiting Address Line 1";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:29:"addresses###visiting###line_1";a:3:{s:5:"label";s:23:"Visiting Address Line 2";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:34:"addresses###visiting###postal_code";a:3:{s:5:"label";s:25:"Visiting Address Zip Code";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:27:"addresses###visiting###city";a:3:{s:5:"label";s:21:"Visiting Address City";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}s:30:"addresses###visiting###country";a:3:{s:5:"label";s:24:"Visiting Address Country";s:4:"type";s:16:"Single Line Text";s:8:"required";i:0;}}}' );
        }
    }
}