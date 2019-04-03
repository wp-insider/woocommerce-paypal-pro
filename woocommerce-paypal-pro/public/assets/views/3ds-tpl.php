<?php

class WC_Paypal_Pro_3DS_tpl {

    function __construct( $args ) {
	$this->get_tpl();
	$out = $this->tpl;
	foreach ( $args as $key => $value ) {
	    $out = str_replace( '%_' . $key . '_%', $value, $out );
	}
	echo $out;
    }

    private function get_tpl() {
	ob_start();
	?>
	<!doctype html>
	<html>
	    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>%_title_%</title>
		<link rel="stylesheet" type="text/css" href="<?php echo WC_PP_PRO_ADDON_URL; ?>/public/assets/css/pure-min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo WC_PP_PRO_ADDON_URL; ?>/public/assets/css/3ds.css">
		<style>
		</style>
	    </head>
	    <body>
		%_body_%
	    </body>
	</html>
	<?php
	$this->tpl = ob_get_clean();
    }

}
