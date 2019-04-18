<?php

class WC_Paypal_Pro_3DS {

    var $trans_data	 = false;
    var $order_id	 = 0;

    function __construct( $wc_pp_pro_action = false ) {
	if ( $wc_pp_pro_action === 'iframe' ) {
	    $this->three_d_secure_iframe();
	}
	if ( $wc_pp_pro_action === '3ds_check' ) {
	    $this->three_d_secure_check();
	}
	if ( $wc_pp_pro_action === 'do_auth' ) {
	    add_action( 'woocommerce_init', array( $this, 'three_d_secure_do_auth' ) );
	}
    }

    function set_order_id( $order_id ) {
	$this->order_id = $order_id;
    }

    function get_trans_data() {
	$trans_name		 = sprintf( 'wp_pp_pro_%d', $this->order_id );
	$this->trans_data	 = get_transient( $trans_name, false );
    }

    private function get_order_id_from_get() {
	$order_id = filter_input( INPUT_GET, 'wc_pp_pro_order', FILTER_SANITIZE_NUMBER_INT );
	$this->set_order_id( $order_id );
    }

    function three_d_secure_check() {

	$this->get_order_id_from_get();

	if ( empty( $this->order_id ) ) {
	    wp_die( 'No order id passed, cannot proceed.' );
	}
	$iframe_url = add_query_arg( array(
	    'wc_pp_pro_action'	 => 'iframe',
	    'wc_pp_pro_order'	 => $this->order_id,
	), get_site_url( null, '/' ) );
	?>
	<!doctype html>
	<html>
	    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>3D Secure Check</title>
	    </head>
	    <body style="margin:0px;padding:0px;overflow:hidden">
	    <center>
		<iframe frameborder="0" style="overflow:hidden;height:100%;width:100%" height="100%" width="100%" src='<?php echo $iframe_url; ?>'>
		    Frames are currently disabled or not supported by your browser.  Please click <A HREF="<?php echo $iframe_url; ?>">here</A> to continue processing your transaction.
		</iframe>
	    </center>
	</body>
	</html>
	<?php
	exit;
    }

    function three_d_secure_iframe() {

	$this->get_order_id_from_get();

	if ( empty( $this->order_id ) ) {
	    wp_die( 'No order id passed, cannot proceed.' );
	}

	$this->get_trans_data();

	if ( empty( $this->trans_data ) ) {
	    wp_die( 'No data found' );
	}
	?>
	<!doctype html>
	<html>
	    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Redirecting...</title>
		<script language="javascript">
		    function onLoadHandler() {
			document.wpPPProLaunchACS.submit();
		    }
		</script>
	    </head>
	    <body onLoad="onLoadHandler();">
		<br/><br/><br/><br/>
	    <center>
		<form name="wpPPProLaunchACS" method="Post" action="<?php echo $this->trans_data[ 'url' ]; ?>">
		    <input type=hidden name="PaReq" value="<?php echo $this->trans_data[ 'PaReq' ]; ?>">
		    <input type=hidden name="TermUrl" value="<?php echo $this->trans_data[ 'TermUrl' ]; ?>">
		    <input type=hidden name="MD" value="<?php echo $this->trans_data[ 'MD' ]; ?>">
		    <noscript> 
		    <center> 
			<font color="red"> 
			<h2>Processing your Payer Authentication Transaction</h2> 
			<h3>JavaScript is currently disabled or is not supported by your browser.<br></h3> 
			<h4>Please click Submit to continue the processing of your transaction.</h4> 
			</font> 
			<input type="submit" value="Submit"> 
		    </center> 
		    </noscript> 
		</form>
	    </center>
	</body>
	</html>
	<?php
	exit;
    }

    function three_d_secure_do_auth() {
	$PaRes		 = filter_input( INPUT_POST, 'PaRes', FILTER_SANITIZE_STRING );
	$order_id	 = filter_input( INPUT_POST, 'MD', FILTER_SANITIZE_NUMBER_INT );
	$this->order_id	 = $order_id;
	//TODO: add tests for empty values

	$this->get_trans_data();
	require(WC_PP_PRO_ADDON_PATH . '/lib/centinel/CentinelClient.php');
	require(WC_PP_PRO_ADDON_PATH . '/lib/centinel/CentinelConfig.php');
	require(WC_PP_PRO_ADDON_PATH . '/lib/centinel/CentinelUtility.php');

	$centinelClient = new CentinelClient;

	$centinelClient->add( 'MsgType', 'cmpi_authenticate' );
	$centinelClient->add( 'Version', CENTINEL_MSG_VERSION );
	$centinelClient->add( 'MerchantId', CENTINEL_MERCHANT_ID );
	$centinelClient->add( 'ProcessorId', CENTINEL_PROCESSOR_ID );
	$centinelClient->add( 'TransactionPwd', CENTINEL_TRANSACTION_PWD );
	$centinelClient->add( 'TransactionType', 'C' );
	$centinelClient->add( 'OrderId', $this->order_id );
	$centinelClient->add( 'TransactionId', $this->trans_data[ 'TransactionId' ] );
	$centinelClient->add( 'PAResPayload', $PaRes );

	$centinelClient->sendHttp( CENTINEL_MAPS_URL, CENTINEL_TIMEOUT_CONNECT, CENTINEL_TIMEOUT_READ );

	var_dump( $centinelClient->response );

	$PaResStatus	 = $centinelClient->getValue( "PAResStatus" );
	$sign_verify	 = $centinelClient->getValue( "SignatureVerification" );
	$errorNo	 = $centinelClient->getValue( "ErrorNo" );
	$errorDesc	 = $centinelClient->getValue( "ErrorDesc" );
	$errMsg		 = '';

	if ( empty( $PaResStatus ) || empty( $sign_verify ) ) {
	    $errMsg = "ERROR: " . $errorDesc;
	} else if ( (strcasecmp( 'Y', $PaResStatus ) == 0 || strcasecmp( 'A', $PaResStatus ) == 0) && (strcasecmp( 'Y', $sign_verify ) == 0) && (strcasecmp( '0', $errorNo ) == 0 || strcasecmp( '1140', $errorNo ) == 0) ) {
	    // Transaction completed successfully. 
	    //$errMsg = "Transaction completed successfully. (ErrorNo: [{$errorNo}], ErrorDesc: [{$errorDesc}])";
	} else if ( (strcasecmp( 'N', $PaResStatus ) == 0) && (strcasecmp( 'Y', $sign_verify ) == 0) && (strcasecmp( '0', $errorNo ) == 0 || strcasecmp( '1140', $errorNo ) == 0) ) {
	    // Unable to authenticate. Provide another form of payment. 
	    $errMsg = "Unable to authenticate. Provide another form of payment. (ErrorNo: [{$errorNo}], ErrorDesc: [{$errorDesc}])";
	} else {
	    // Transaction complete however is pending review. Order will be shipped once payment is verified. 
//	    $errMsg		 = "Transaction complete however is pending review. Order will be shipped once payment is verified. (ErrorNo: [{$errorNo}], ErrorDesc: [{$errorDesc}])";
//	    $redirectPage	 = 'ccResults.php';
	} // end processing logic

	$redir_url = $this->trans_data[ 'checkout_url' ];

	if ( empty( $errMsg ) ) {
	    //auth successful
	    //set POST values
	    $_POST	 = $this->trans_data[ '_POST' ];
	    $ppgw	 = new WC_PP_PRO_Gateway();
	    $res	 = $ppgw->process_payment( $this->order_id, true );
	    if ( isset( $res[ 'redirect' ] ) ) {
		$redir_url = $res[ 'redirect' ];
	    }
	} else {
	    //auth failed
	    wc_add_notice( $errMsg, 'error' );
	}
	?>
	<!doctype html>
	<html>
	    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>3D Secure Check</title>
		<script>
		    (function () {
			//window.top.location = '<?php echo $redir_url; ?>';
		    })();
		</script>
	    </head>
	    <body>
	    </body>
	</html>
	<?php
	exit;
    }

}
