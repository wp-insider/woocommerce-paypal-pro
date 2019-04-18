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
	    add_action( 'woocommerce_after_register_post_type', array( $this, 'three_d_secure_do_auth' ) );
	}
    }

    function set_order_id( $order_id ) {
	$this->order_id = $order_id;
    }

    function get_trans_data() {
	$trans_name		 = sprintf( 'wp_pp_pro_%d', $this->order_id );
	$this->trans_data	 = json_decode( get_transient( $trans_name ), true );
    }

    function delete_trans_data() {
	$trans_name = sprintf( 'wp_pp_pro_%d', $this->order_id );
	delete_transient( $trans_name );
    }

    private function get_order_id_from_get() {
	$order_id = filter_input( INPUT_GET, 'wc_pp_pro_order', FILTER_SANITIZE_NUMBER_INT );
	$this->set_order_id( $order_id );
    }

    function three_d_secure_check() {

	$this->get_order_id_from_get();
	$this->get_trans_data();

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
		<link rel="stylesheet" type="text/css" href="<?php echo WC_PP_PRO_ADDON_URL; ?>/public/assets/css/pure-min.css">
		<style>
		    html, body {
			height: 100%;
		    }
		    body {
			margin:0px;
			padding:0px;
			overflow:hidden;
		    }
		    #iframe {
			width: 100%;
			height: 100%;
			margin-top:20px;
			padding: 5px;
			overflow: scroll;
		    }
		    #header {
			padding: 5px;
			text-align: center;
			background-color: #f0f0f0;
			border-bottom: 1px solid #d0d0d0;
		    }
		    #description {
			margin-bottom: 10px;
		    }
		    #spinner{
			margin:0 auto;
			width:97px;
		    }
		    #spinner {
			display: none;
			margin-bottom: 5px;
		    }
		    .spinner i{
			width: 19px;
			height: 19px;
			display: inline-block;
			border-radius: 50%;
			background: rgb(17,91,219);
		    }
		    .spinner i:first-child{
			opacity: 0;
			animation:spinner-ani2 0.58s linear infinite;
			-o-animation:spinner-ani2 0.58s linear infinite;
			-ms-animation:spinner-ani2 0.58s linear infinite;
			-webkit-animation:spinner-ani2 0.58s linear infinite;
			-moz-animation:spinner-ani2 0.58s linear infinite;
			transform:translate(-19px);
			-o-transform:translate(-19px);
			-ms-transform:translate(-19px);
			-webkit-transform:translate(-19px);
			-moz-transform:translate(-19px);
		    }
		    .spinner i:nth-child(2),
		    .spinner i:nth-child(3){
			animation:spinner-ani3 0.58s linear infinite;
			-o-animation:spinner-ani3 0.58s linear infinite;
			-ms-animation:spinner-ani3 0.58s linear infinite;
			-webkit-animation:spinner-ani3 0.58s linear infinite;
			-moz-animation:spinner-ani3 0.58s linear infinite;
		    }
		    .spinner i:last-child{
			animation:spinner-ani1 0.58s linear infinite;
			-o-animation:spinner-ani1 0.58s linear infinite;
			-ms-animation:spinner-ani1 0.58s linear infinite;
			-webkit-animation:spinner-ani1 0.58s linear infinite;
			-moz-animation:spinner-ani1 0.58s linear infinite;
		    }

		    @keyframes spinner-ani1{
			100%{
			    transform:translate(39px);
			    opacity: 0;
			}
		    }

		    @-o-keyframes spinner-ani1{
			100%{
			    -o-transform:translate(39px);
			    opacity: 0;
			}
		    }

		    @-ms-keyframes spinner-ani1{
			100%{
			    -ms-transform:translate(39px);
			    opacity: 0;
			}
		    }

		    @-webkit-keyframes spinner-ani1{
			100%{
			    -webkit-transform:translate(39px);
			    opacity: 0;
			}
		    }

		    @-moz-keyframes spinner-ani1{
			100%{
			    -moz-transform:translate(39px);
			    opacity: 0;
			}
		    }

		    @keyframes spinner-ani2{
			100%{
			    transform:translate(19px);
			    opacity: 1;
			}
		    }

		    @-o-keyframes spinner-ani2{
			100%{
			    -o-transform:translate(19px);
			    opacity: 1;
			}
		    }

		    @-ms-keyframes spinner-ani2{
			100%{
			    -ms-transform:translate(19px);
			    opacity: 1;
			}
		    }

		    @-webkit-keyframes spinner-ani2{
			100%{
			    -webkit-transform:translate(19px);
			    opacity: 1;
			}
		    }

		    @-moz-keyframes spinner-ani2{
			100%{
			    -moz-transform:translate(19px);
			    opacity: 1;
			}
		    }

		    @keyframes spinner-ani3{
			100%{
			    transform:translate(19px);
			}
		    }

		    @-o-keyframes spinner-ani3{
			100%{
			    -o-transform:translate(19px);
			}
		    }

		    @-ms-keyframes spinner-ani3{
			100%{
			    -ms-transform:translate(19px);
			}
		    }

		    @-webkit-keyframes spinner-ani3{
			100%{
			    -webkit-transform:translate(19px);
			}
		    }

		    @-moz-keyframes spinner-ani3{
			100%{
			    -moz-transform:translate(19px);
			}
		    }
		</style>
		<style>
		</style>
	    </head>
	    <body>
		<div id="header">
		    <h3>3D Secure Check</h3>
		    <div id="description">For your security, please fill out the form below to complete your order.
			Do not click the refresh or back button or this transaction may be interrupted or cancelled.
		    </div>
		    <div id="spinner">
			<div class="spinner"><i></i><i></i><i></i><i></i></div>
		    </div>
		</div>
		<iframe onload="content_finished_loading(this);" id="iframe" frameborder="0" height="100%" width="100%" src='<?php echo $iframe_url; ?>'>
		    Frames are currently disabled or not supported by your browser.  Please click <a href="<?php echo $iframe_url; ?>">here</a> to continue processing your transaction.
		</iframe>

		<script>
		    var iframeChangeCount = 0;
		    function content_finished_loading(iframe) {
			try {
			    var url = iframe.contentWindow.location.href;
			    if (url === '<?php echo esc_js( $this->trans_data[ 'TermUrl' ] ); ?>') {
				iframe.style.display = "none";
				document.getElementById("description").style.display = "none";
				document.getElementById("spinner").style.display = "block";
			    }
			} catch (error) {
			    iframeChangeCount++;
			    if (iframeChangeCount >= 2) {
				document.getElementById("iframe").style.display = "none";
				document.getElementById("description").style.display = "none";
				document.getElementById("spinner").style.display = "block";
			    }
			}
		    }
		</script>
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
		<form name="wpPPProLaunchACS" method="post" action="<?php echo $this->trans_data[ 'url' ]; ?>">
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
	    </body>
	</html>
	<?php
	exit;
    }

    function three_d_secure_do_auth() {
	$PaRes		 = $_POST[ 'PaRes' ];
	$order_id	 = filter_input( INPUT_POST, 'MD', FILTER_SANITIZE_NUMBER_INT );
	$this->order_id	 = $order_id;
	//TODO: add tests for empty values

	$this->get_trans_data();
	require_once(WC_PP_PRO_ADDON_PATH . '/lib/centinel/CentinelClient.php');
	require_once(WC_PP_PRO_ADDON_PATH . '/lib/centinel/CentinelConfig.php');
	require_once(WC_PP_PRO_ADDON_PATH . '/lib/centinel/CentinelUtility.php');

	$centinelClient = new CentinelClient;

	$centinelClient->add( 'MsgType', 'cmpi_authenticate' );
	$centinelClient->add( 'Version', CENTINEL_MSG_VERSION );
	$centinelClient->add( 'MerchantId', CENTINEL_MERCHANT_ID );
	$centinelClient->add( 'ProcessorId', CENTINEL_PROCESSOR_ID );
	$centinelClient->add( 'TransactionPwd', CENTINEL_TRANSACTION_PWD );
	$centinelClient->add( 'TransactionType', 'C' );
	$centinelClient->add( 'OrderId', $this->trans_data[ 'OrderId' ] );
	$centinelClient->add( 'TransactionId', $this->trans_data[ 'TransactionId' ] );
	$centinelClient->add( 'PAResPayload', $PaRes );

	//var_dump( $centinelClient->request );
	//wp_die();

	$centinelClient->sendHttp( CENTINEL_MAPS_URL, CENTINEL_TIMEOUT_CONNECT, CENTINEL_TIMEOUT_READ );

//	var_dump( $centinelClient->response );
//	exit;
	//$this->delete_trans_data();

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

	ob_start();

	if ( empty( $errMsg ) ) {
	    //auth successful
	    $_POST	 = $this->trans_data[ "_POST" ];
	    //set additional fields for PP transaction
//	    $order		 = wc_get_order( $this->order_id );
//	    $order_id	 = $order->get_id();
	    $ppgw	 = new WC_PP_PRO_Gateway();
	    $cavv	 = $centinelClient->getValue( "Cavv" );
	    $eciflag = $centinelClient->getValue( "EciFlag" );
	    $xid	 = $centinelClient->getValue( "Xid" );
	    $fields	 = array(
		'VERSION'	 => '59.0',
		'AUTHSTATUS3DS'	 => $PaResStatus,
		'MPIVENDOR3DS'	 => $this->trans_data[ 'enrolled' ],
		'CAVV'		 => $cavv,
		'ECI3DS'	 => $eciflag,
		'XID'		 => $xid,
	    );
	    $ppgw->set_additional_paypal_req_fields( $fields );
	    $res	 = $ppgw->process_payment( $this->order_id, true );
	    if ( isset( $res[ 'redirect' ] ) ) {
		$redir_url = $res[ 'redirect' ];
	    }
	} else {
	    //auth failed
	    wc_add_notice( $errMsg, 'error' );
	}
	ob_end_clean();
	?>
	<!doctype html>
	<html>
	    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>3D Secure Check</title>
		<script>
		    (function () {
			window.top.location = '<?php echo $redir_url; ?>';
		    })();
		</script>
	    </head>
	    <body>
		Performing 3D Secure check...
	    </body>
	</html>
	<?php
	exit;
    }

}
