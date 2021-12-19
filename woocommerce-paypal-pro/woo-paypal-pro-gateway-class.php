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
    protected $cc_last_digits = '';

    public function __construct() {
	$this->id		 = 'paypalpro'; //ID needs to be ALL lowercase or it doens't work
	$this->GATEWAYNAME	 = 'PayPal-Pro';
	$this->method_title	 = 'PayPal-Pro';
        $this->icon		 = apply_filters( 'wcpprog_checkout_icon', plugins_url( 'images/credit-cards.png', __FILE__ ) );
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
	    $greater_than_33 = version_compare( '3.3', WC_VERSION );
	    $wc_settings_url = admin_url( sprintf( 'admin.php?page=wc-settings&tab=%s', $greater_than_33 ? 'advanced' : 'checkout' ) );
	    echo '<div class="error"><p>' . sprintf( __( '%s gateway requires SSL certificate for better security. The <a href="%s">Secure checkout</a> option is disabled on your site. Please ensure your server has a valid SSL certificate so you can enable the SSL option on your checkout page.', 'woocommerce-paypal-pro-payment-gateway' ), $this->GATEWAYNAME, $wc_settings_url ) . '</p></div>';
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
		for ( $i = 0; $i < 12; $i ++ ) {
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

        if ( isset ( $gatewayRequestData['ACCT'] ) && !empty( $gatewayRequestData['ACCT'] ) ){
            $this->cc_last_digits = $this->get_cc_last_digits( $gatewayRequestData['ACCT'] );
        }

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

	if ( $this->order->get_status() == 'completed' ){
	    return;
        }

        if ( isset ( $this->cc_last_digits ) && !empty ( $this->cc_last_digits )) {
            //The value exists. Save the last 4 digits to order post meta
            $last_digits = apply_filters( 'wcpprog_cc_last_digits_for_post_meta', $this->cc_last_digits );
            update_post_meta( $this->order->get_id(), '_cc_last_digits', $last_digits );
        }

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
			$this->transactionErrorMessage = $erroMessage = $parsedResponse[ 'L_LONGMESSAGE0' ];
			break;
		}
	    }
	} else {
	    // Uncomment to view the http error
	    //$erroMessage = print_r($response->errors, true);
	    $erroMessage = 'Something went wrong while performing your request. Please contact website administrator to report this problem.';
	}

        //Trigger an action hook with the entire response. Can be helpful for troubleshooting by usign this hook to write data to a file.
        do_action( 'wcpprog_paypal_api_error_response', $response );

        /*
        //Temporary fix for the error code: "10536" (duplicate invoice ID). PayPal occassionaly gives this error code incorrectly.
        if ( isset($parsedResponse['L_ERRORCODE0']) && '10536' === $parsedResponse['L_ERRORCODE0'] ) {
            //Temporarily ignore the error: duplicate invoice ID supplied.
            $this->transactionId = isset($parsedResponse[ 'CORRELATIONID' ]) ? $parsedResponse[ 'CORRELATIONID' ] : '';
            return true;
        }
        */

	wc_add_notice( $erroMessage, 'error' );
	return false;
    }

    protected function parse_paypal_response( $response ) {
	$result		 = array();
	$enteries	 = explode( '&', $response[ 'body' ] );

	foreach ( $enteries as $nvp ) {
	    $pair					 = explode( '=', $nvp );
	    if ( count( $pair ) > 1 )
		$result[ urldecode( $pair[ 0 ] ) ]	 = urldecode( $pair[ 1 ] );
	}

	return $result;
    }

    protected function create_paypal_request() {
	//API Reference - https://developer.paypal.com/docs/nvp-soap-api/do-direct-payment-nvp/#

	if ( $this->order AND $this->order != null ) {
            $txn_description = 'WooCommerce Order ID: ' . $this->order->get_order_number();//Used as a description for the transaction.
            $txn_description = apply_filters( 'wcpprog_request_txn_description', $txn_description );

	    $query_args = array(
		'PAYMENTACTION'	 => $this->PAYPAL_NVP_PAYMENTACTION,
		'VERSION'	 => $this->PAYPAL_NVP_API_VERSION,
		'METHOD'	 => $this->PAYPAL_NVP_METHOD,
		'PWD'		 => $this->apipassword,
		'USER'		 => $this->apiusername,
		'SIGNATURE'	 => $this->apisigniture,
		'AMT'		 => $this->order->get_total(),
                'DESC'           => $txn_description,
		'FIRSTNAME'	 => $this->order->get_billing_first_name(),
		'LASTNAME'	 => $this->order->get_billing_last_name(),
                'EMAIL'          => $this->order->get_billing_email(),
		'STREET'	 => $this->order->get_billing_address_1(),
		'CITY'		 => $this->order->get_billing_city(),
		'STATE'		 => $this->order->get_billing_state(),
		'ZIP'		 => $this->order->get_billing_postcode(),
		'COUNTRYCODE'	 => $this->order->get_billing_country(),
		'SHIPTONAME'	 => $this->order->get_shipping_first_name() . " " . $this->order->get_shipping_last_name(),
		'SHIPTOSTREET'	 => $this->order->get_shipping_address_1(),
		'SHIPTOSTREET2'	 => $this->order->get_shipping_address_2(),
		'SHIPTOCITY'	 => $this->order->get_shipping_city(),
		'SHIPTOSTATE'	 => $this->order->get_shipping_state(),
		'SHIPTOZIP'	 => $this->order->get_shipping_postcode(),
		'SHIPTOCOUNTRY'	 => $this->order->get_shipping_country(),
		'IPADDRESS'	 => WC_PP_PRO_Utility::get_user_ip(),
		'CREDITCARDTYPE' => $_POST[ 'billing_cardtype' ],
		'ACCT'		 => $_POST[ 'billing_credircard' ],
		'CVV2'		 => $_POST[ 'billing_ccvnumber' ],
		'EXPDATE'	 => sprintf( '%s%s', $_POST[ 'billing_expdatemonth' ], $_POST[ 'billing_expdateyear' ] ),
		'STREET'	 => sprintf( '%s, %s', $_POST[ 'billing_address_1' ], $_POST[ 'billing_address_2' ] ),
		'CURRENCYCODE'	 => get_woocommerce_currency(),
		'INVNUM'	 => apply_filters( 'wcpprog_invnum_woo_order_number', $this->order->get_order_number() ),
		'BUTTONSOURCE'	 => 'TipsandTricks_SP',
	    );

            //Add some optional item info to the query param. This is not required for the transaction to be successful.
	    //$query_args = $this->get_additional_item_info_for_request($query_args);

            //Return the query args array.
	    return $query_args;
	}
	return false;
    }

    /*
     * Adds the additional individual item info to the query parameters
     */
    protected function get_additional_item_info_for_request($query_args) {
        global $woocommerce;
        $payment_info_params = $query_args;
        $items = $woocommerce->cart->get_cart();

        $c = 0;
        foreach($items as $item => $item_values) {
            $title_key = 'L_NAME' . $c;
            $amt_key = 'L_AMT' . $c;
            $qty_key = 'L_QTY' . $c;

            $product = wc_get_product( $item_values['data']->get_id() );

            //Get the product's name. Reference - https://docs.woocommerce.com/wc-apidocs/class-WC_Product.html#_get_name
            //The WC_Product->get_name() will retrieve the name with variation name (if any). The WC_Product->get_title() retrieves the product's post title.
            $prod_title = $product->get_name();

            //Get the item quantity values
            $prod_quantity = $item_values['quantity'];
            if( empty( $prod_quantity ) ){
                $prod_quantity = 1;// If it couldn't read the product quantity value then set it to a valid value of 1.
            }

            $prod_price = $item_values['data']->get_price();//Get the price from the cart (includes variation price).
            $prod_price = round( $prod_price, 2 );//Round it up to 2 decimal places.

            $payment_info_params[$title_key] = $prod_title;

            /* Not passing the individual item price and quantity since on some sites the dynamic pricing with other addons cuases an error. */
            /* You can uncomment the follwoing two lines to pass this data for your site. */
            //$payment_info_params[$qty_key] = $prod_quantity;
            //$payment_info_params[$amt_key] = $prod_price;

            $c++;
        }
        return $payment_info_params;
    }

    private function get_cc_last_digits( $cc_number ) {
        return substr( $cc_number, -4 );
    }
}
//End of class
