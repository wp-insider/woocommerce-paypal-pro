=== WooCommerce PayPal Pro Payment Gateway ===
Contributors: wp.insider, wpecommerce
Donate link: https://wp-ecommerce.net/paypal-pro-payment-gateway-for-woocommerce
Tags: paypal, paypal pro, woocommerce, payment gateway, card, credit card, ecommerce, gateway, PayPal payment, paypal pro, payments pro, PayPal Pro Credit Card, dodirectpayment, express checkout,
Requires at least: 5.0
Tested up to: 5.8
Stable tag: 2.9.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add PayPal Pro payment gateway to the WooCommerce plugin so you can allow customers to checkout using credit cards on-site.

== Description ==

This extension adds on-site credit card checkout functionality on your WooCommerce site. Your customers will enter the credit card on your checkout page (they never leave the site to do the transaction).

The credit card checkout experience offered by this addon is very smooth. The following video shows the credit card checkout experience.

https://www.youtube.com/watch?v=PAZRba8Tp74

Configuring this addon is very easy. Simply go to the following WooCommerce settings area to enable the PayPal Pro gateway and enter your PayPal Pro API details:

WooCommerce Settings -> Checkout -> PayPal-Pro

You can find detailed usage instruction with screenshots on the [WooCommerce PayPal Pro Gateway](https://wp-ecommerce.net/paypal-pro-payment-gateway-for-woocommerce) extension page.

After that, your customers will be able to select the credit card checkout option on the WooCommere checkout page.

Post a question on the forum if you have any issue using the addon.

== Installation ==

Do the following to install the addon:

1. Upload the 'woocommerce-paypal-pro.zip' file from the Plugins->Add New page in the WordPress administration panel.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

1. Will my customers be able to checkout using their credit cards after I install this plugin? 
Yes

2. Will it offer on-site checkout so my customers doesn't have to leave the site? 
Yes

== Screenshots ==

Please visit the PayPal Pro Payment Gateway for WooCommerce plugin page to view screenshots:
https://wp-ecommerce.net/paypal-pro-payment-gateway-for-woocommerce

== Changelog ==

= 2.9.9 =
- Reversing the temporary fix that was added for the PayPal's "Duplicate invoice ID supplied" error.

= 2.9.8 =
- Temporarily Ignore the "Duplicate invoice ID supplied" error. Thanks to @thaissamendes for sharing the code tweak.
- Added a new action hook "wcpprog_paypal_api_error_response" when the API response is an error response.

= 2.9.7 =
- Added a new filter hook (wcpprog_get_user_ip) for the IP address that gets submitted to the PayPal API. This hook can be used to override and apply your own customization for the IP Address.

= 2.9.6 =
- The "wcpprog_request_txn_description" filter can be used to override and customize the Transaction Description value.
- The description field of the API is now populated with a value like the following:
  WooCommerce Order ID: XXXX
- The last 4 digits of the card is saved in the order post meta (for the transaction).

= 2.9.5 =
- Added the email address value in the query parameter of the API. The billing email address will now be sent to the PayPal API.

= 2.9.4 =
- Removed (commented out) the individual item amount passing to the PayPal API (this was recently added after a request from a user). A few sites are having issues with it when dynamic pricing is used. It will still pass the item name.

= 2.9.3 =
- Variable product checkout error fixed. The dynamic pricing was causing an error with the additional amount parameter that the plugin now sends to PayPal.

= 2.9.2 =
- Added additional item info to the request parameter that is sent to PayPal. It will show more info about the item when you view the transaction in your PayPal account. Thanks to @kingpg

= 2.9.1 =
- Added a credit card icon next to the paypal checkout selection radio button.
- Added a filter (wcpprog_checkout_icon) to allow updating/tweaking of the credit card icon image.

= 2.9 =
- Updated a call to a function for the new WooCommerce version.

= 2.8 =
- Fixed WooCommerce settings URL in force SSL notice for the new version of WooCommerce.

= 2.7 =
- The shipping address is also sent to the PayPal API during the checkout.

= 2.6 =
- Added language translation POT file so the addon can be translated.

= 2.5 =
- The WooCommerce order number gets sent to the PayPal Pro API (so it is available in your merchant account)

= 2.4 =
- Credit Card number and CVV fields now show a placeholder text (the same as the field label).
- Added filters so the placeholder text can be customized using custom code.
- Added a filter that can be used to fully customize the output of the credit card fields on the checkout form.

= 2.3 =
- The credit card expiry year value has been increased.

= 2.2 =
- Added a class_exists check to make sure the 'WC_Payment_Gateway' class is available before trying to use it in the code.
- Added an empty index file in the plugin folder so it cannot be browsed.

= 2.1 =
- Replaced the "get_option('woocommerce_currency')" function call with get_woocommerce_currency()

= 2.0 =
- Plugin translation related improvements.
- Tested on WordPress 4.6 so it is compatible.

= 1.9 =
- Added translation string for the credit card form.

= 1.8 =
- Fix: settings interface not showing. Updated the addon to be compatible with the latest version of WooCommerce.
- Note: Make sure to go to the settings interface of this addon and save your API details.
- Added a link to the settings menu in the plugins listing interface.

= 1.7 =
- The credit card number will stay in the form when there is a validation error.

= 1.6 =
- Added a new filter so the CVV hint image can be customized.
- WP4.4 compatibility.

= 1.5 =
- Added a new option to show a hint for the credit card security code (verification number) on the checkout form.

= 1.4 =
- Some more code refactoring changes.

= 1.3 =
- Minor code refactoring to make it more readable.

= 1.1 =
- First commit to WordPress repository.

== Upgrade Notice ==
Make sure to go to the settings interface of this addon after the upgrade and save your API details (if the API details are missing).

== Arbitrary section ==
None
