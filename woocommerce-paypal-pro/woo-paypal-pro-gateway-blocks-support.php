<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

class WC_PP_PRO_Gateway_Blocks_Support extends AbstractPaymentMethodType
{

	private $gateway;

	protected $name = 'paypalpro'; // payment gateway id

	public function initialize()
	{
		// get gateway class
		$gateways      = WC()->payment_gateways->payment_gateways();
		$this->gateway = $gateways[$this->name];

		// get payment gateway settings
		$this->settings = get_option("woocommerce_{$this->name}_settings", array());
	}

	public function is_active()
	{
		return !empty($this->settings['enabled']) && 'yes' === $this->settings['enabled'];
	}

	public function get_payment_method_script_handles()
	{
		$asset_path   = WC_PP_PRO_ADDON_PATH . '/block-integration/index.asset.php';
		$version      = null;
		$dependencies = array();
		if (file_exists($asset_path)) {
			$asset        = require $asset_path;
			$version      = isset($asset['version']) ? $asset['version'] : $version;
			$dependencies = isset($asset['dependencies']) ? $asset['dependencies'] : $dependencies;
		}

		wp_enqueue_style(
			'wcpprog-block-support-styles',
			plugins_url('', WC_PP_PRO_ADDON_FILE) . '/block-integration/index.css',
			null,
			$version
		);

		wp_register_script(
			'wcpprog-block-support-script',
			plugins_url('', WC_PP_PRO_ADDON_FILE) . '/block-integration/index.js',
			$dependencies,
			$version,
			true
		);

		// Return the script handler(s), so woocommerce can handle enqueueing of them.
		return array('wcpprog-block-support-script');
	}

	public function get_payment_method_data()
	{
		return array(
			'title'                         => $this->get_setting('title'),
			'description'                   => $this->get_setting('description'),
			'securitycodehint'              => $this->get_setting('securitycodehint') == 'yes',
			'icon'                          => apply_filters('wcpprog_checkout_icon', plugins_url('images/credit-cards.png', __FILE__)),
			'cardIcons'                     => apply_filters('wcpprog_checkout_card_icons', $this->get_credit_card_icons()),
			'card_number_field_placeholder' => apply_filters('wcpprog_card_number_field_placeholder', __('Card Number', 'woocommerce-paypal-pro-payment-gateway')),
			'cvv_field_placeholder'         => apply_filters('wcpprog_cvv_field_placeholder', __('Card Verification Number (CVV)', 'woocommerce-paypal-pro-payment-gateway')),
			'cvv_hint_img'                  => apply_filters('wcpprog_cvv_image_hint_src', WC_PP_PRO_ADDON_URL . '/images/card-security-code-hint.png'),
			'supports'                      => array('products'),
		);
	}

	public function get_credit_card_icons()
	{
		return array(
			array(
				"id" => "wcpprog-wc-payment-method-visa",
				"alt" => "PayPal Pro Visa Card Icon",
				"src" => plugins_url('images/cards/visa.png', __FILE__),
			),
			array(
				"id" => "wcpprog-wc-payment-method-mastercard",
				"alt" => "PayPal Pro Master Card Icon",
				"src" => plugins_url('images/cards/mastercard.png', __FILE__),
			),
			array(
				"id" => "wcpprog-wc-payment-method-discover",
				"alt" => "PayPal Pro Discover Card Icon",
				"src" => plugins_url('images/cards/discover.png', __FILE__),
			),
			array(
				"id" => "wcpprog-wc-payment-method-Amex",
				"alt" => "PayPal Pro American Express Card Icon",
				"src" => plugins_url('images/cards/amex.png', __FILE__),
			),
		);
	}
}
