<?php

/*
 * Class with some utility functions for this addon
 */

class WC_PP_PRO_Utility {

    static $CURRENCIES		 = array(
	'AFN'	 => array( 'numericCode' => 971, 'minorUnit' => 2 ),
	'EUR'	 => array( 'numericCode' => 978, 'minorUnit' => 2 ),
	'ALL'	 => array( 'numericCode' => 8, 'minorUnit' => 2 ),
	'DZD'	 => array( 'numericCode' => 12, 'minorUnit' => 2 ),
	'USD'	 => array( 'numericCode' => 840, 'minorUnit' => 2 ),
	'AOA'	 => array( 'numericCode' => 973, 'minorUnit' => 2 ),
	'XCD'	 => array( 'numericCode' => 951, 'minorUnit' => 2 ),
	'ARS'	 => array( 'numericCode' => 32, 'minorUnit' => 2 ),
	'AMD'	 => array( 'numericCode' => 51, 'minorUnit' => 2 ),
	'AWG'	 => array( 'numericCode' => 533, 'minorUnit' => 2 ),
	'AUD'	 => array( 'numericCode' => 36, 'minorUnit' => 2 ),
	'AZN'	 => array( 'numericCode' => 944, 'minorUnit' => 2 ),
	'BSD'	 => array( 'numericCode' => 44, 'minorUnit' => 2 ),
	'BHD'	 => array( 'numericCode' => 48, 'minorUnit' => 3 ),
	'BDT'	 => array( 'numericCode' => 50, 'minorUnit' => 2 ),
	'BBD'	 => array( 'numericCode' => 52, 'minorUnit' => 2 ),
	'BYN'	 => array( 'numericCode' => 933, 'minorUnit' => 2 ),
	'BZD'	 => array( 'numericCode' => 84, 'minorUnit' => 2 ),
	'XOF'	 => array( 'numericCode' => 952, 'minorUnit' => 0 ),
	'BMD'	 => array( 'numericCode' => 60, 'minorUnit' => 2 ),
	'INR'	 => array( 'numericCode' => 356, 'minorUnit' => 2 ),
	'BTN'	 => array( 'numericCode' => 64, 'minorUnit' => 2 ),
	'BOB'	 => array( 'numericCode' => 68, 'minorUnit' => 2 ),
	'BOV'	 => array( 'numericCode' => 984, 'minorUnit' => 2 ),
	'BAM'	 => array( 'numericCode' => 977, 'minorUnit' => 2 ),
	'BWP'	 => array( 'numericCode' => 72, 'minorUnit' => 2 ),
	'NOK'	 => array( 'numericCode' => 578, 'minorUnit' => 2 ),
	'BRL'	 => array( 'numericCode' => 986, 'minorUnit' => 2 ),
	'BND'	 => array( 'numericCode' => 96, 'minorUnit' => 2 ),
	'BGN'	 => array( 'numericCode' => 975, 'minorUnit' => 2 ),
	'BIF'	 => array( 'numericCode' => 108, 'minorUnit' => 0 ),
	'CVE'	 => array( 'numericCode' => 132, 'minorUnit' => 2 ),
	'KHR'	 => array( 'numericCode' => 116, 'minorUnit' => 2 ),
	'XAF'	 => array( 'numericCode' => 950, 'minorUnit' => 0 ),
	'CAD'	 => array( 'numericCode' => 124, 'minorUnit' => 2 ),
	'KYD'	 => array( 'numericCode' => 136, 'minorUnit' => 2 ),
	'CLP'	 => array( 'numericCode' => 152, 'minorUnit' => 0 ),
	'CLF'	 => array( 'numericCode' => 990, 'minorUnit' => 4 ),
	'CNY'	 => array( 'numericCode' => 156, 'minorUnit' => 2 ),
	'COP'	 => array( 'numericCode' => 170, 'minorUnit' => 2 ),
	'COU'	 => array( 'numericCode' => 970, 'minorUnit' => 2 ),
	'KMF'	 => array( 'numericCode' => 174, 'minorUnit' => 0 ),
	'CDF'	 => array( 'numericCode' => 976, 'minorUnit' => 2 ),
	'NZD'	 => array( 'numericCode' => 554, 'minorUnit' => 2 ),
	'CRC'	 => array( 'numericCode' => 188, 'minorUnit' => 2 ),
	'HRK'	 => array( 'numericCode' => 191, 'minorUnit' => 2 ),
	'CUP'	 => array( 'numericCode' => 192, 'minorUnit' => 2 ),
	'CUC'	 => array( 'numericCode' => 931, 'minorUnit' => 2 ),
	'ANG'	 => array( 'numericCode' => 532, 'minorUnit' => 2 ),
	'CZK'	 => array( 'numericCode' => 203, 'minorUnit' => 2 ),
	'DKK'	 => array( 'numericCode' => 208, 'minorUnit' => 2 ),
	'DJF'	 => array( 'numericCode' => 262, 'minorUnit' => 0 ),
	'DOP'	 => array( 'numericCode' => 214, 'minorUnit' => 2 ),
	'EGP'	 => array( 'numericCode' => 818, 'minorUnit' => 2 ),
	'SVC'	 => array( 'numericCode' => 222, 'minorUnit' => 2 ),
	'ERN'	 => array( 'numericCode' => 232, 'minorUnit' => 2 ),
	'ETB'	 => array( 'numericCode' => 230, 'minorUnit' => 2 ),
	'FKP'	 => array( 'numericCode' => 238, 'minorUnit' => 2 ),
	'FJD'	 => array( 'numericCode' => 242, 'minorUnit' => 2 ),
	'XPF'	 => array( 'numericCode' => 953, 'minorUnit' => 0 ),
	'GMD'	 => array( 'numericCode' => 270, 'minorUnit' => 2 ),
	'GEL'	 => array( 'numericCode' => 981, 'minorUnit' => 2 ),
	'GHS'	 => array( 'numericCode' => 936, 'minorUnit' => 2 ),
	'GIP'	 => array( 'numericCode' => 292, 'minorUnit' => 2 ),
	'GTQ'	 => array( 'numericCode' => 320, 'minorUnit' => 2 ),
	'GBP'	 => array( 'numericCode' => 826, 'minorUnit' => 2 ),
	'GNF'	 => array( 'numericCode' => 324, 'minorUnit' => 0 ),
	'GYD'	 => array( 'numericCode' => 328, 'minorUnit' => 2 ),
	'HTG'	 => array( 'numericCode' => 332, 'minorUnit' => 2 ),
	'HNL'	 => array( 'numericCode' => 340, 'minorUnit' => 2 ),
	'HKD'	 => array( 'numericCode' => 344, 'minorUnit' => 2 ),
	'HUF'	 => array( 'numericCode' => 348, 'minorUnit' => 2 ),
	'ISK'	 => array( 'numericCode' => 352, 'minorUnit' => 0 ),
	'IDR'	 => array( 'numericCode' => 360, 'minorUnit' => 2 ),
	'XDR'	 => array( 'numericCode' => 960, 'minorUnit' => 0 ),
	'IRR'	 => array( 'numericCode' => 364, 'minorUnit' => 2 ),
	'IQD'	 => array( 'numericCode' => 368, 'minorUnit' => 3 ),
	'ILS'	 => array( 'numericCode' => 376, 'minorUnit' => 2 ),
	'JMD'	 => array( 'numericCode' => 388, 'minorUnit' => 2 ),
	'JPY'	 => array( 'numericCode' => 392, 'minorUnit' => 0 ),
	'JOD'	 => array( 'numericCode' => 400, 'minorUnit' => 3 ),
	'KZT'	 => array( 'numericCode' => 398, 'minorUnit' => 2 ),
	'KES'	 => array( 'numericCode' => 404, 'minorUnit' => 2 ),
	'KPW'	 => array( 'numericCode' => 408, 'minorUnit' => 2 ),
	'KRW'	 => array( 'numericCode' => 410, 'minorUnit' => 0 ),
	'KWD'	 => array( 'numericCode' => 414, 'minorUnit' => 3 ),
	'KGS'	 => array( 'numericCode' => 417, 'minorUnit' => 2 ),
	'LAK'	 => array( 'numericCode' => 418, 'minorUnit' => 2 ),
	'LBP'	 => array( 'numericCode' => 422, 'minorUnit' => 2 ),
	'LSL'	 => array( 'numericCode' => 426, 'minorUnit' => 2 ),
	'ZAR'	 => array( 'numericCode' => 710, 'minorUnit' => 2 ),
	'LRD'	 => array( 'numericCode' => 430, 'minorUnit' => 2 ),
	'LYD'	 => array( 'numericCode' => 434, 'minorUnit' => 3 ),
	'CHF'	 => array( 'numericCode' => 756, 'minorUnit' => 2 ),
	'MOP'	 => array( 'numericCode' => 446, 'minorUnit' => 2 ),
	'MKD'	 => array( 'numericCode' => 807, 'minorUnit' => 2 ),
	'MGA'	 => array( 'numericCode' => 969, 'minorUnit' => 2 ),
	'MWK'	 => array( 'numericCode' => 454, 'minorUnit' => 2 ),
	'MYR'	 => array( 'numericCode' => 458, 'minorUnit' => 2 ),
	'MVR'	 => array( 'numericCode' => 462, 'minorUnit' => 2 ),
	'MRO'	 => array( 'numericCode' => 478, 'minorUnit' => 2 ),
	'MUR'	 => array( 'numericCode' => 480, 'minorUnit' => 2 ),
	'XUA'	 => array( 'numericCode' => 965, 'minorUnit' => 0 ),
	'MXN'	 => array( 'numericCode' => 484, 'minorUnit' => 2 ),
	'MXV'	 => array( 'numericCode' => 979, 'minorUnit' => 2 ),
	'MDL'	 => array( 'numericCode' => 498, 'minorUnit' => 2 ),
	'MNT'	 => array( 'numericCode' => 496, 'minorUnit' => 2 ),
	'MAD'	 => array( 'numericCode' => 504, 'minorUnit' => 2 ),
	'MZN'	 => array( 'numericCode' => 943, 'minorUnit' => 2 ),
	'MMK'	 => array( 'numericCode' => 104, 'minorUnit' => 2 ),
	'NAD'	 => array( 'numericCode' => 516, 'minorUnit' => 2 ),
	'NPR'	 => array( 'numericCode' => 524, 'minorUnit' => 2 ),
	'NIO'	 => array( 'numericCode' => 558, 'minorUnit' => 2 ),
	'NGN'	 => array( 'numericCode' => 566, 'minorUnit' => 2 ),
	'OMR'	 => array( 'numericCode' => 512, 'minorUnit' => 3 ),
	'PKR'	 => array( 'numericCode' => 586, 'minorUnit' => 2 ),
	'PAB'	 => array( 'numericCode' => 590, 'minorUnit' => 2 ),
	'PGK'	 => array( 'numericCode' => 598, 'minorUnit' => 2 ),
	'PYG'	 => array( 'numericCode' => 600, 'minorUnit' => 0 ),
	'PEN'	 => array( 'numericCode' => 604, 'minorUnit' => 2 ),
	'PHP'	 => array( 'numericCode' => 608, 'minorUnit' => 2 ),
	'PLN'	 => array( 'numericCode' => 985, 'minorUnit' => 2 ),
	'QAR'	 => array( 'numericCode' => 634, 'minorUnit' => 2 ),
	'RON'	 => array( 'numericCode' => 946, 'minorUnit' => 2 ),
	'RUB'	 => array( 'numericCode' => 643, 'minorUnit' => 2 ),
	'RWF'	 => array( 'numericCode' => 646, 'minorUnit' => 0 ),
	'SHP'	 => array( 'numericCode' => 654, 'minorUnit' => 2 ),
	'WST'	 => array( 'numericCode' => 882, 'minorUnit' => 2 ),
	'STD'	 => array( 'numericCode' => 678, 'minorUnit' => 2 ),
	'SAR'	 => array( 'numericCode' => 682, 'minorUnit' => 2 ),
	'RSD'	 => array( 'numericCode' => 941, 'minorUnit' => 2 ),
	'SCR'	 => array( 'numericCode' => 690, 'minorUnit' => 2 ),
	'SLL'	 => array( 'numericCode' => 694, 'minorUnit' => 2 ),
	'SGD'	 => array( 'numericCode' => 702, 'minorUnit' => 2 ),
	'XSU'	 => array( 'numericCode' => 994, 'minorUnit' => 0 ),
	'SBD'	 => array( 'numericCode' => 90, 'minorUnit' => 2 ),
	'SOS'	 => array( 'numericCode' => 706, 'minorUnit' => 2 ),
	'SSP'	 => array( 'numericCode' => 728, 'minorUnit' => 2 ),
	'LKR'	 => array( 'numericCode' => 144, 'minorUnit' => 2 ),
	'SDG'	 => array( 'numericCode' => 938, 'minorUnit' => 2 ),
	'SRD'	 => array( 'numericCode' => 968, 'minorUnit' => 2 ),
	'SZL'	 => array( 'numericCode' => 748, 'minorUnit' => 2 ),
	'SEK'	 => array( 'numericCode' => 752, 'minorUnit' => 2 ),
	'CHE'	 => array( 'numericCode' => 947, 'minorUnit' => 2 ),
	'CHW'	 => array( 'numericCode' => 948, 'minorUnit' => 2 ),
	'SYP'	 => array( 'numericCode' => 760, 'minorUnit' => 2 ),
	'TWD'	 => array( 'numericCode' => 901, 'minorUnit' => 2 ),
	'TJS'	 => array( 'numericCode' => 972, 'minorUnit' => 2 ),
	'TZS'	 => array( 'numericCode' => 834, 'minorUnit' => 2 ),
	'THB'	 => array( 'numericCode' => 764, 'minorUnit' => 2 ),
	'TOP'	 => array( 'numericCode' => 776, 'minorUnit' => 2 ),
	'TTD'	 => array( 'numericCode' => 780, 'minorUnit' => 2 ),
	'TND'	 => array( 'numericCode' => 788, 'minorUnit' => 3 ),
	'TRY'	 => array( 'numericCode' => 949, 'minorUnit' => 2 ),
	'TMT'	 => array( 'numericCode' => 934, 'minorUnit' => 2 ),
	'UGX'	 => array( 'numericCode' => 800, 'minorUnit' => 0 ),
	'UAH'	 => array( 'numericCode' => 980, 'minorUnit' => 2 ),
	'AED'	 => array( 'numericCode' => 784, 'minorUnit' => 2 ),
	'USN'	 => array( 'numericCode' => 997, 'minorUnit' => 2 ),
	'UYU'	 => array( 'numericCode' => 858, 'minorUnit' => 2 ),
	'UYI'	 => array( 'numericCode' => 940, 'minorUnit' => 0 ),
	'UZS'	 => array( 'numericCode' => 860, 'minorUnit' => 2 ),
	'VUV'	 => array( 'numericCode' => 548, 'minorUnit' => 0 ),
	'VEF'	 => array( 'numericCode' => 937, 'minorUnit' => 2 ),
	'VND'	 => array( 'numericCode' => 704, 'minorUnit' => 0 ),
	'YER'	 => array( 'numericCode' => 886, 'minorUnit' => 2 ),
	'ZMW'	 => array( 'numericCode' => 967, 'minorUnit' => 2 ),
	'ZWL'	 => array( 'numericCode' => 932, 'minorUnit' => 2 ),
	'XBA'	 => array( 'numericCode' => 955, 'minorUnit' => 0 ),
	'XBB'	 => array( 'numericCode' => 956, 'minorUnit' => 0 ),
	'XBC'	 => array( 'numericCode' => 957, 'minorUnit' => 0 ),
	'XBD'	 => array( 'numericCode' => 958, 'minorUnit' => 0 ),
	'XTS'	 => array( 'numericCode' => 963, 'minorUnit' => 0 ),
	'XXX'	 => array( 'numericCode' => 999, 'minorUnit' => 0 ),
	'XAU'	 => array( 'numericCode' => 959, 'minorUnit' => 0 ),
	'XPD'	 => array( 'numericCode' => 964, 'minorUnit' => 0 ),
	'XPT'	 => array( 'numericCode' => 962, 'minorUnit' => 0 ),
	'XAG'	 => array( 'numericCode' => 961, 'minorUnit' => 0 ),
    );
    public static $acceptable_cards	 = array(
	"Visa",
	"MasterCard",
	"Discover",
	"Amex"
    );

    function __construct() {
	//NOP
    }

    static function is_valid_card_number( $toCheck ) {
	if ( ! is_numeric( $toCheck ) )
	    return false;

	$number	 = preg_replace( '/[^0-9]+/', '', $toCheck );
	$strlen	 = strlen( $number );
	$sum	 = 0;

	if ( $strlen < 13 )
	    return false;

	for ( $i = 0; $i < $strlen; $i ++ ) {
	    $digit = substr( $number, $strlen - $i - 1, 1 );
	    if ( $i % 2 == 1 ) {
		$sub_total = $digit * 2;
		if ( $sub_total > 9 ) {
		    $sub_total = 1 + ($sub_total - 10);
		}
	    } else {
		$sub_total = $digit;
	    }
	    $sum += $sub_total;
	}

	if ( $sum > 0 AND $sum % 10 == 0 )
	    return true;

	return false;
    }

    static function is_valid_card_type( $toCheck ) {
	return $toCheck AND in_array( $toCheck, self::$acceptable_cards );
    }

    static function is_valid_expiry( $month, $year ) {
	$now		 = time();
	$thisYear	 = (int) date( 'Y', $now );
	$thisMonth	 = (int) date( 'm', $now );

	if ( is_numeric( $year ) && is_numeric( $month ) ) {
	    $thisDate	 = mktime( 0, 0, 0, $thisMonth, 1, $thisYear );
	    $expireDate	 = mktime( 0, 0, 0, $month, 1, $year );

	    return $thisDate <= $expireDate;
	}

	return false;
    }

    static function is_valid_cvv_number( $toCheck ) {
	$length = strlen( $toCheck );
	return is_numeric( $toCheck ) AND $length > 2 AND $length < 5;
    }

    static function get_currency_code_numeric( $code ) {
	$curr	 = self::$CURRENCIES;
	$code	 = strtoupper( $code );

	if ( $curr[ $code ] ) {
	    return $curr[ $code ][ 'numericCode' ];
	}
	return false;
    }

    static function get_amount_in_cents( $amount, $code ) {
	$curr	 = self::$CURRENCIES;
	$code	 = strtoupper( $code );
	$mul	 = 100;
	if ( isset( $curr[ $code ] ) ) {
	    $decimals	 = $curr[ $code ][ 'minorUnit' ];
	    $mul		 = '1';
	    for ( $i = 0; $i < $decimals; $i ++ ) {
		$mul .= '0';
	    }
	    $mul = intval( $mul );
	}
	$amount_cents = intval( $amount * $mul );
	return $amount_cents;
    }

}
