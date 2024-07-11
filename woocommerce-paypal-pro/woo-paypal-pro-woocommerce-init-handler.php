<?php

class WCPPROG_WooCommerce_Init_handler {
	public function __construct() {
		add_action( 'before_woocommerce_init', array( $this, 'wcpprog_handle_before_woocommerce_init' ) );
		add_action( 'woocommerce_blocks_payment_method_type_registration', array( $this, 'wcpprog_register_wc_blocks_payment_method_type' ) );
	}

	public function wcpprog_handle_before_woocommerce_init() {
		// handle woocommerce checkout blocks compatibility
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
				'cart_checkout_blocks',
				WC_PP_PRO_ADDON_FILE,
				true // true (compatible, default) or false (not compatible)
			);
		}
	}

	public function wcpprog_register_wc_blocks_payment_method_type( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
		require_once WC_PP_PRO_ADDON_PATH . '/woo-paypal-pro-gateway-blocks-support.php';

		$payment_method_registry->register( new WC_PP_PRO_Gateway_Blocks_Support );
	}
}
