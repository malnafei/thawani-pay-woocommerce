=== Thawani pay woocommerce ===
Contributors: malnafei
Donate link: https://www.buymeacoffee.com/mahmood
Tags: online payment, woocommerce, thawani pay, oman payment gateway
Requires at least: 4.7
Tested up to: 5.4
Stable tag: 4.0
Requires PHP: 5.2.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==

Accepts payments with the **Thawani Pay v2** Gateway for WooCommerce

This is the unofficial **Thawani Pay v2** payment gateway plugin for WooCommerce. Allows you to accept credit cards, debit cards payments with the WooCommerce plugin. It uses a seamless integration, allowing the customer to pay on your website quickly.

**Features**

1. Compatible with Thawani Pay version 2
2. Easy configuration in WooCommerce - only API secret key and Publishable key need to be copied from Thawani Merchant Portal
3. Easy customization - payment method title, description, environment: UAT/Production, cancel url, success url, client reference id Prefix and more can be changed easily
4. Compatible with UAT and production API.
5. Developer logs feature: receive new emails when something happens with the payment API
6. WC tested up to 5.0.0

== Frequently Asked Questions ==

= What is the plugin Requirements? =

* Requires at least: woocommerce  >= 4.0.0, WordPress >= 4.0.0
* Tested up to: 5.6.2
* Requires PHP >= 5.6
* WC requires at least: 4.0.0
* WC tested up to: 5.0.0

= From where i can get Thwani Api key? =
Please go to https://developer.thawani.om/ for more information

= How i can install Thawani payment plugin on my website? =
1. Upload thawani-pay-woocommerce folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3.Go to WooCommerce - Settings - Payments - Thawani Pay - setup
4.Thawani payment gateway will be available in "Checkout" page.


== Changelog ==

= 1.0.0 =
* first public version

= 1.1.0 =
* fix product name must be a string with a maximum length of 40 issue.
* Add billing phone to request metadata.

= 1.2.0 =
* Arabic products supported now.
