<?php
if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
    //Woocommerce is not active.
    return;
}

class WC_PP_PRO_Gateway extends WC_Payment_Gateway {

    protected $PAYPAL_NVP_SIG_SANDBOX	 = "https://api-3t.sandbox.paypal.com/nvp";
    protected $PAYPAL_NVP_SIG_LIVE		 = "https://api-3t.paypal.com/nvp";
    protected $PAYPAL_NVP_PAYMENTACTION	 = "Sale";
    protected $PAYPAL_NVP_METHOD		 = "DoDirectPayment";
    protected $PAYPAL_NVP_API_VERSION	 = "84.0";
    protected $order			 = null;
    protected $transactionId		 = null;
    protected $transactionErrorMessage	 = null;
    protected $usesandboxapi		 = true;
    protected $securitycodehint		 = true;
    protected $apiusername			 = '';
    protected $apipassword			 = '';
    protected $apisigniture			 = '';

    public function __construct() {
	$this->id		 = 'paypalpro'; //ID needs to be ALL lowercase or it doens't work
	$this->GATEWAYNAME	 = 'PayPal-Pro';
	$this->method_title	 = 'PayPal-Pro';
	$this->has_fields	 = true;

	$this->init_form_fields();
	$this->init_settings();

	$this->description	 = '';
	$this->usesandboxapi	 = strcmp( $this->settings[ 'debug' ], 'yes' ) == 0;
	$this->securitycodehint	 = strcmp( $this->settings[ 'securitycodehint' ], 'yes' ) == 0;
	//If the field is populated, it will grab the value from there and will not be translated.  If it is empty, it will use the default and translate that value
	$this->title		 = strlen( $this->settings[ 'title' ] ) > 0 ? $this->settings[ 'title' ] : __( 'Credit Card Payment', 'woocommerce-paypal-pro-payment-gateway' );
	$this->apiusername	 = $this->settings[ 'paypalapiusername' ];
	$this->apipassword	 = $this->settings[ 'paypalapipassword' ];
	$this->apisigniture	 = $this->settings[ 'paypalapisigniture' ];

	add_filter( 'http_request_version', array( &$this, 'use_http_1_1' ) );
	add_action( 'admin_notices', array( &$this, 'handle_admin_notice_msg' ) );
	add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
    }

    public function admin_options() {
	?>
	<h3><?php _e( 'PayPal Pro', 'woocommerce-paypal-pro-payment-gateway' ); ?></h3>
	<p><?php _e( 'Allows Credit Card Payments via the PayPal Pro gateway.', 'woocommerce-paypal-pro-payment-gateway' ); ?></p>

	<table class="form-table">
	<?php
	//Render the settings form according to what is specified in the init_form_fields() function
	$this->generate_settings_html();
	?>
	</table>
	    <?php
	}

	public function init_form_fields() {
	    $this->form_fields = array(
		'enabled'		 => array(
		    'title'		 => __( 'Enable/Disable', 'woocommerce-paypal-pro-payment-gateway' ),
		    'type'		 => 'checkbox',
		    'label'		 => __( 'Enable PayPal Pro Gateway', 'woocommerce-paypal-pro-payment-gateway' ),
		    'default'	 => 'yes'
		),
		'debug'			 => array(
		    'title'		 => __( 'Sandbox Mode', 'woocommerce-paypal-pro-payment-gateway' ),
		    'type'		 => 'checkbox',
		    'label'		 => __( 'Enable Sandbox Mode', 'woocommerce-paypal-pro-payment-gateway' ),
		    'default'	 => 'no'
		),
		'title'			 => array(
		    'title'		 => __( 'Title', 'woocommerce-paypal-pro-payment-gateway' ),
		    'type'		 => 'text',
		    'description'	 => __( 'The title for this checkout option.', 'woocommerce-paypal-pro-payment-gateway' ),
		    'default'	 => __( 'Credit Card Payment', 'woocommerce-paypal-pro-payment-gateway' )
		),
		'securitycodehint'	 => array(
		    'title'		 => __( 'Show CVV Hint', 'woocommerce-paypal-pro-payment-gateway' ),
		    'type'		 => 'checkbox',
		    'label'		 => __( 'Enable this option if you want to show a hint for the CVV field on the credit card checkout form', 'woocommerce-paypal-pro-payment-gateway' ),
		    'default'	 => 'no'
		),
		'paypalapiusername'	 => array(
		    'title'		 => __( 'PayPal Pro API Username', 'woocommerce-paypal-pro-payment-gateway' ),
		    'type'		 => 'text',
		    'description'	 => __( 'Your PayPal payments pro API username.', 'woocommerce-paypal-pro-payment-gateway' ),
		    'default'	 => __( '', 'woocommerce-paypal-pro-payment-gateway' )
		),
		'paypalapipassword'	 => array(
		    'title'		 => __( 'PayPal Pro API Password', 'woocommerce-paypal-pro-payment-gateway' ),
		    'type'		 => 'text',
		    'description'	 => __( 'Your PayPal payments pro API password.', 'woocommerce-paypal-pro-payment-gateway' ),
		    'default'	 => __( '', 'woocommerce-paypal-pro-payment-gateway' )
		),
		'paypalapisigniture'	 => array(
		    'title'		 => __( 'PayPal Pro API Signature', 'woocommerce-paypal-pro-payment-gateway' ),
		    'type'		 => 'textarea',
		    'description'	 => __( 'Your PayPal payments pro API signature.', 'woocommerce-paypal-pro-payment-gateway' ),
		    'default'	 => __( '', 'woocommerce-paypal-pro-payment-gateway' )
		)
	    );
	}

	function handle_admin_notice_msg() {
	    if ( ! $this->usesandboxapi && get_option( 'woocommerce_force_ssl_checkout' ) == 'no' && $this->enabled == 'yes' ) {
		echo '<div class="error"><p>' . sprintf( __( '%s gateway requires SSL certificate for better security. The <a href="%s">force SSL option</a> is disabled on your site. Please ensure your server has a valid SSL certificate so you can enable the SSL option on your checkout page.', 'woocommerce-paypal-pro-payment-gateway' ), $this->GATEWAYNAME, admin_url( 'admin.php?page=woocommerce_settings&tab=general' ) ) . '</p></div>';
	    }
	}

	/*
	 * Validates the fields specified in the payment_fields() function.
	 */

	public function validate_fields() {
	    global $woocommerce;

	    if ( ! WC_PP_PRO_Utility::is_valid_card_number( $_POST[ 'billing_credircard' ] ) ) {
		wc_add_notice( __( 'Credit card number you entered is invalid.', 'woocommerce-paypal-pro-payment-gateway' ), 'error' );
	    }
	    if ( ! WC_PP_PRO_Utility::is_valid_card_type( $_POST[ 'billing_cardtype' ] ) ) {
		wc_add_notice( __( 'Card type is not valid.', 'woocommerce-paypal-pro-payment-gateway' ), 'error' );
	    }
	    if ( ! WC_PP_PRO_Utility::is_valid_expiry( $_POST[ 'billing_expdatemonth' ], $_POST[ 'billing_expdateyear' ] ) ) {
		wc_add_notice( __( 'Card expiration date is not valid.', 'woocommerce-paypal-pro-payment-gateway' ), 'error' );
	    }
	    if ( ! WC_PP_PRO_Utility::is_valid_cvv_number( $_POST[ 'billing_ccvnumber' ] ) ) {
		wc_add_notice( __( 'Card verification number (CVV) is not valid. You can find this number on your credit card.', 'woocommerce-paypal-pro-payment-gateway' ), 'error' );
	    }
	}

	/*
	 * Render the credit card fields on the checkout page
	 */

	public function payment_fields() {
	    $payment_fields_overriden = apply_filters( 'wcpprog_before_rendering_payment_fields', '' );
	    if ( ! empty( $payment_fields_overriden ) ) {
		//The fields output has been overriden using custom code. So we don't need to display it anymore.
		return;
	    }

	    $billing_credircard		 = isset( $_REQUEST[ 'billing_credircard' ] ) ? esc_attr( $_REQUEST[ 'billing_credircard' ] ) : '';
	    ?>
	<p class="form-row validate-required">
	    <?php
	    $card_number_field_placeholder	 = __( 'Card Number', 'woocommerce-paypal-pro-payment-gateway' );
	    $card_number_field_placeholder	 = apply_filters( 'wcpprog_card_number_field_placeholder', $card_number_field_placeholder );
	    ?>            
	    <label><?php _e( 'Card Number', 'woocommerce-paypal-pro-payment-gateway' ); ?> <span class="required">*</span></label>
	    <input class="input-text" type="text" size="19" maxlength="19" name="billing_credircard" value="<?php echo $billing_credircard; ?>" placeholder="<?php echo $card_number_field_placeholder; ?>" />
	</p>         
	<p class="form-row form-row-first">
	    <label><?php _e( 'Card Type', 'woocommerce-paypal-pro-payment-gateway' ); ?> <span class="required">*</span></label>
	    <select name="billing_cardtype" >
		<option value="Visa" selected="selected">Visa</option>
		<option value="MasterCard">MasterCard</option>
		<option value="Discover">Discover</option>
		<option value="Amex">American Express</option>
	    </select>
	</p>       
	<div class="clear"></div>
	<p class="form-row form-row-first">
	    <label><?php _e( 'Expiration Date', 'woocommerce-paypal-pro-payment-gateway' ); ?> <span class="required">*</span></label>
	    <select name="billing_expdatemonth">
		<option value=1>01</option>
		<option value=2>02</option>
		<option value=3>03</option>
		<option value=4>04</option>
		<option value=5>05</option>
		<option value=6>06</option>
		<option value=7>07</option>
		<option value=8>08</option>
		<option value=9>09</option>
		<option value=10>10</option>
		<option value=11>11</option>
		<option value=12>12</option>
	    </select>
	    <select name="billing_expdateyear">
		<?php
		$today				 = (int) date( 'Y', time() );
		for ( $i = 0; $i < 12; $i ++  ) {
		    ?>
	    	<option value="<?php echo $today; ?>"><?php echo $today; ?></option>
		    <?php
		    $today ++;
		}
		?>
	    </select>            
	</p>
	<div class="clear"></div>
	<p class="form-row form-row-first validate-required">
	<?php
	$cvv_field_placeholder	 = __( 'Card Verification Number (CVV)', 'woocommerce-paypal-pro-payment-gateway' );
	$cvv_field_placeholder	 = apply_filters( 'wcpprog_cvv_field_placeholder', $cvv_field_placeholder );
	?>
	    <label><?php _e( 'Card Verification Number (CVV)', 'woocommerce-paypal-pro-payment-gateway' ); ?> <span class="required">*</span></label>
	    <input class="input-text" type="text" size="4" maxlength="4" name="billing_ccvnumber" value="" placeholder="<?php echo $cvv_field_placeholder; ?>" />
	</p>
	<?php
	if ( $this->securitycodehint ) {
	    $cvv_hint_img	 = WC_PP_PRO_ADDON_URL . '/images/card-security-code-hint.png';
	    $cvv_hint_img	 = apply_filters( 'wcpprog_cvv_image_hint_src', $cvv_hint_img );
	    echo '<div class="wcppro-security-code-hint-section">';
	    echo '<img src="' . $cvv_hint_img . '" />';
	    echo '</div>';
	}
	?>
	<div class="clear"></div>

	<?php
    }

    public function process_payment( $order_id ) {
	global $woocommerce;
	$this->order		 = new WC_Order( $order_id );
	$gatewayRequestData	 = $this->create_paypal_request();

	if ( $gatewayRequestData AND $this->verify_paypal_payment( $gatewayRequestData ) ) {
	    $this->do_order_complete_tasks();

	    return array(
		'result'	 => 'success',
		'redirect'	 => $this->get_return_url( $this->order )
	    );
	} else {
	    $this->mark_as_failed_payment();
	    wc_add_notice( __( '(Transaction Error) something is wrong.', 'woocommerce-paypal-pro-payment-gateway' ), 'error' );
	}
    }

    /*
     * Set the HTTP version for the remote posts
     * https://developer.wordpress.org/reference/hooks/http_request_version/
     */

    public function use_http_1_1( $httpversion ) {
	return '1.1';
    }

    protected function mark_as_failed_payment() {
	$this->order->add_order_note( sprintf( "Paypal Credit Card Payment Failed with message: '%s'", $this->transactionErrorMessage ) );
    }

    protected function do_order_complete_tasks() {
	global $woocommerce;

	if ( $this->order->status == 'completed' )
	    return;

	$this->order->payment_complete();
	$woocommerce->cart->empty_cart();

	$this->order->add_order_note(
	sprintf( "Paypal Credit Card payment completed with Transaction Id of '%s'", $this->transactionId )
	);

	unset( $_SESSION[ 'order_awaiting_payment' ] );
    }

    protected function verify_paypal_payment( $gatewayRequestData ) {
	global $woocommerce;

	$erroMessage	 = "";
	$api_url	 = $this->usesandboxapi ? $this->PAYPAL_NVP_SIG_SANDBOX : $this->PAYPAL_NVP_SIG_LIVE;
	$request	 = array(
	    'method'	 => 'POST',
	    'timeout'	 => 45,
	    'blocking'	 => true,
	    'sslverify'	 => $this->usesandboxapi ? false : true,
	    'body'		 => $gatewayRequestData
	);

	$response = wp_remote_post( $api_url, $request );
	if ( ! is_wp_error( $response ) ) {
	    $parsedResponse = $this->parse_paypal_response( $response );

	    if ( array_key_exists( 'ACK', $parsedResponse ) ) {
		switch ( $parsedResponse[ 'ACK' ] ) {
		    case 'Success':
		    case 'SuccessWithWarning':
			$this->transactionId = $parsedResponse[ 'TRANSACTIONID' ];
			return true;
			break;

		    default:
			$this->transactionErrorMessage	 = $erroMessage			 = $parsedResponse[ 'L_LONGMESSAGE0' ];
			break;
		}
	    }
	} else {
	    // Uncomment to view the http error
	    //$erroMessage = print_r($response->errors, true);
	    $erroMessage = 'Something went wrong while performing your request. Please contact website administrator to report this problem.';
	}

	wc_add_notice( $erroMessage, 'error' );
	return false;
    }

    protected function parse_paypal_response( $response ) {
	$result		 = array();
	$enteries	 = explode( '&', $response[ 'body' ] );

	foreach ( $enteries as $nvp ) {
	    $pair				 = explode( '=', $nvp );
	    if ( count( $pair ) > 1 )
		$result[ urldecode( $pair[ 0 ] ) ]	 = urldecode( $pair[ 1 ] );
	}

	return $result;
    }

    protected function create_paypal_request() {
	if ( $this->order AND $this->order != null ) {
	    return array(
		'PAYMENTACTION'	 => $this->PAYPAL_NVP_PAYMENTACTION,
		'VERSION'	 => $this->PAYPAL_NVP_API_VERSION,
		'METHOD'	 => $this->PAYPAL_NVP_METHOD,
		'PWD'		 => $this->apipassword,
		'USER'		 => $this->apiusername,
		'SIGNATURE'	 => $this->apisigniture,
		'AMT'		 => $this->order->get_total(),
		'FIRSTNAME'	 => $this->order->billing_first_name,
		'LASTNAME'	 => $this->order->billing_last_name,
		'CITY'		 => $this->order->billing_city,
		'STATE'		 => $this->order->billing_state,
		'ZIP'		 => $this->order->billing_postcode,
		'COUNTRYCODE'	 => $this->order->billing_country,
		'IPADDRESS'	 => $_SERVER[ 'REMOTE_ADDR' ],
		'CREDITCARDTYPE' => $_POST[ 'billing_cardtype' ],
		'ACCT'		 => $_POST[ 'billing_credircard' ],
		'CVV2'		 => $_POST[ 'billing_ccvnumber' ],
		'EXPDATE'	 => sprintf( '%s%s', $_POST[ 'billing_expdatemonth' ], $_POST[ 'billing_expdateyear' ] ),
		'STREET'	 => sprintf( '%s, %s', $_POST[ 'billing_address_1' ], $_POST[ 'billing_address_2' ] ),
		'CURRENCYCODE'	 => get_woocommerce_currency(),
		'INVNUM'	 => $this->order->get_order_number(),
		'BUTTONSOURCE'	 => 'TipsandTricks_SP',
	    );
	}
	return false;
    }

}

//End of class