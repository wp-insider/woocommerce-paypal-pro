<?php

/**
 * Plugin Name: WooCommerce PayPal Pro
 * Plugin URI: https://wp-ecommerce.net/paypal-pro-payment-gateway-for-woocommerce
 * Description: Easily adds PayPal Pro payment gateway to the WooCommerce plugin so you can allow customers to checkout via credit card.
 * Version: 2.8
 * Author: wp.insider
 * Author URI: https://wp-ecommerce.net/
 * Requires at least: 3.0
 * License: GPL2 or Later
 * WC requires at least: 3.0
 * WC tested up to: 3.7
 */
if ( ! defined( 'ABSPATH' ) ) {
    //Exit if accessed directly
    exit;
}

//Slug - wcpprog

if ( ! class_exists( 'WC_Paypal_Pro_Gateway_Addon' ) ) {

    class WC_Paypal_Pro_Gateway_Addon {

	var $version		 = '2.8';
	var $db_version	 = '1.0';
	var $plugin_url;
	var $plugin_path;

	function __construct() {
	    $this->define_constants();
	    $this->includes();
	    $this->loader_operations();
	    //Handle any db install and upgrade task
	    add_action( 'init', array( &$this, 'plugin_init' ), 0 );

	    add_filter( 'plugin_action_links', array( &$this, 'add_link_to_settings' ), 10, 2 );
	}

	function define_constants() {
	    define( 'WC_PP_PRO_ADDON_VERSION', $this->version );
	    define( 'WC_PP_PRO_ADDON_URL', $this->plugin_url() );
	    define( 'WC_PP_PRO_ADDON_PATH', $this->plugin_path() );
	}

	function includes() {
	    include_once('woo-paypal-pro-utility-class.php');
	}

	function loader_operations() {
	    add_action( 'plugins_loaded', array( &$this, 'plugins_loaded_handler' ) ); //plugins loaded hook
	}

	function plugins_loaded_handler() {
	    //Runs when plugins_loaded action gets fired
	    include_once('woo-paypal-pro-gateway-class.php');
	    add_filter( 'woocommerce_payment_gateways', array( &$this, 'init_paypal_pro_gateway' ) );
	}

	function do_db_upgrade_check() {
	    //NOP
	}

	function plugin_url() {
	    if ( $this->plugin_url )
		return $this->plugin_url;
	    return $this->plugin_url = plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) );
	}

	function plugin_path() {
	    if ( $this->plugin_path )
		return $this->plugin_path;
	    return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	function plugin_init() {//Gets run when WP Init is fired
	    load_plugin_textdomain( 'woocommerce-paypal-pro-payment-gateway', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	function add_link_to_settings( $links, $file ) {
	    if ( $file == plugin_basename( __FILE__ ) ) {
		$settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=paypalpro">Settings</a>';
		array_unshift( $links, $settings_link );
	    }
	    return $links;
	}

	function init_paypal_pro_gateway( $methods ) {
	    array_push( $methods, 'WC_PP_PRO_Gateway' );
	    return $methods;
	}

    }

    //End of plugin class
}//End of class not exists check

$GLOBALS[ 'WC_Paypal_Pro_Gateway_Addon' ] = new WC_Paypal_Pro_Gateway_Addon();

