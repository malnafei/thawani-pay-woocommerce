<?php
/*
Plugin Name: Thawani Pay v2
Description: Thawani V2 Payment Gateway for WooCommerce
Version: 1.2.0
Author: Mahmoud Alnafei
Author URI: https://twitter.com/magic_coding
Tags: payment, online payment, woocommerce, thawani pay, oman payment gateway
Text Domain: woocommerce-extension
Requires at least: 4.0.0
Tested up to: 5.6.2
Requires PHP: 5.6
Stable tag: 5.6.2
WC requires at least: 4.0.0
WC tested up to: 5.0.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

defined('ABSPATH') or wp_die( 'No script kiddies please!' );


add_filter( 'woocommerce_payment_gateways', 'thawani_add_gateway_class' );
  function thawani_add_gateway_class( $gateways ) {
  $gateways[] = 'WC_Thawani_Gateway';
  return $gateways;
}

add_action( 'plugins_loaded', 'thawani_init_gateway_class' );
function thawani_init_gateway_class() {

  class WC_Thawani_Gateway extends WC_Payment_Gateway {
    public function __construct() {
      $plugin_dir = plugin_dir_url(__FILE__);
      $this->id = 'thawani';
	    $this->icon = "";
      //un-comment below line to use payment icon behind the payment title on the checkout page
      // $this->icon = $plugin_dir . "/img/logo.png";
      $this->has_fields = false;
      $this->method_title = 'Thawani';
      $this->method_description = 'Accepts payments with the <b>Thawani Pay v2</b> Gateway for WooCommerce <img src="https://i.postimg.cc/tJcqVHGC/Thawani-logo.png" width="100px" />';
      $this->supports = array('products');
      $this->init_form_fields();
      $this->init_settings();
      $this->enabled = $this->get_option( 'enabled' );
      $this->title = $this->get_option( 'title' );
      $this->description = $this->get_option( 'description' );
      $this->secret_key = $this->get_option( 'secret_key' );
      $this->publishable_key = $this->get_option( 'publishable_key' );
      $this->cancel_url = $this->get_option( 'cancel_url' );
      $this->success_url = $this->get_option( 'success_url' );
      $this->client_prefix = $this->get_option( 'client_prefix' );
      $this->environment = $this->get_option('environment');
			if ($this->get_option('environment') == 'yes') {
        //UAT
				$this->posturl = 'https://uatcheckout.thawani.om';
        $this->paymentmode = 0;
			} else {
        //production
				$this->posturl = 'https://checkout.thawani.om';
        $this->paymentmode = 1;
			}

      add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

    }

    //init plugin required fields
    public function init_form_fields(){
      $domain = parse_url(get_site_url(), PHP_URL_HOST);
      $domain_parts = explode('.', $domain);
      $website_name = $domain_parts[0];
      $success_url_api = wc_get_checkout_url() . "?success";
      $cancel_url_api = wc_get_checkout_url() . "?cancel";
      $admin_email = get_option('admin_email');
      $this->form_fields = array(
        'enabled' => array(
          'title'       => 'Enable/Disable',
          'label'       => 'Enable Thawani Pay payments',
          'type'        => 'checkbox',
          'description' => '',
          'default'     => 'no',
          'desc_tip'    => true
        ),
        'title' => array(
          'title'       => 'Title',
          'type'        => 'text',
          'description' => 'Controls the name of this payment method as displayed to the customer during checkout.',
          'default'     => 'Thawani Pay',
          'desc_tip'    => true
        ),
        'description' => array(
          'title'       => 'Description',
          'type'        => 'textarea',
          'description' => 'This controls the description which the user sees during checkout.',
          'default'     => 'Payment Methods Accepted: VisaCard, Credit Card/Debit Card'
        ),
        'secret_key' => array(
          'title'       => 'Secret Key',
          'type'        => 'password',
          'description'       => 'Enter your Thawani Secret Key',
          'desc_tip'    => true
        ),
        'publishable_key' => array(
          'title'       => 'Publishable key',
          'type'        => 'password',
          'description'       => 'Enter your Thawani Publishable key',
          'desc_tip'    => true
        ),
        'cancel_url' => array(
          'title'       => 'Cancel url',
          'type' 			=> 'select',
		      'options' 		=> $this->thawani_get_pages('Select Cancel Page'),
          'description'       => 'Select Thawani cancel page. Default url <span style="background: #e2e2e2;padding: 1.5px;">' . $cancel_url_api . '</span>',
          'desc_tip'    => false
        ),
        'success_url' => array(
          'title'       => 'Success url',
          'type' 			=> 'select',
		      'options' 		=> $this->thawani_get_pages('Select Success Page'),
          'description'       => 'Select Thawani success page. Default url <span style="background: #e2e2e2;padding: 1.5px;">' . $success_url_api . '</span>',
          'desc_tip'    => false
        ),
        'client_prefix' => array(
          'title'       => 'Client Reference Prefix',
          'type'        => 'text',
          'default'     => $website_name,
          'description'       => 'Plugin will use this prefix with order id <strong>[prefix][orderid]</strong> as client_reference_id . ex: mahmoud123321',
          'desc_tip'    => false
        ),
		'success_status' => array(
          'title'       => 'Order status on payment Success',
          'type' 			=> 'select',
		      'options' 		=> array(
            'Processing' => 'Processing',
            'On hold' => 'On hold',
            'Processing' => 'Processing',
            'Completed' => 'Completed',
            'Cancelled' => 'Cancelled',
            'Failed' => 'Failed',
            'Pending payment' => 'Pending payment'),
          'description'       => 'Select order status to be used on payment success. default status <code>Processing</code>',
          'desc_tip'    => false
        ),
		'Cancel_status' => array(
          'title'       => 'Cancel status',
          'type' 			=> 'select',
		      'options' 		=> array(
            'Pending payment' => 'Pending payment',
            'Processing' => 'Processing',
            'On hold' => 'On hold',
            'Processing' => 'Processing',
            'Completed' => 'Completed',
            'Cancelled' => 'Cancelled',
            'Failed' => 'Failed'),
          'description'       => 'Select order status to be used on payment cancel. default status <code>Pending payment</code>',
          'desc_tip'    => false
        ),
        'environment' => array(
          'title' => 'Environment',
          'label'		=> 'Enable UAT Mode',
          'type' => 'checkbox',
          'description' => 'If Test mode is enabled you should use the UAT Secret and Publishable Keys available on <a href="https://developer.thawani.om/#product1category1" target="_blank">Thawani Checkout API Documentation</a>',
          'desc_tip'    => false,
          'default'	=> 'no',
        ),
		  'developer_log' => array(
          'title'       => 'Error Notification',
          'type'        => 'text',
          'default'     => $admin_email,
          'description' => 'Enter your email to receive notification when something happens with the payment api. This is more for developer to track Thawani Api response when the payment failed like <code>Unauthorized</code> requests.',
          'desc_tip'    => false
        ),
      );
    }

    //Process Thawani Payment
    public function process_payment( $order_id ) {
      global $woocommerce;
      $order = wc_get_order($order_id);

      //access Thawani settings
      $secret_key	= $this->get_option('secret_key');
      $publishable_key	= $this->get_option('publishable_key');
      $cancel_url = get_permalink($this->success_url) . '?mode=' . $this->paymentmode . '&oid=' . $order_id . '&status=cancel';
      $success_url = get_permalink($this->success_url) . '?mode=' . $this->paymentmode . '&oid=' . $order_id . '&status=success';
      $client_prefix = $this->get_option('client_prefix');
      $client_reference_id = $client_prefix .''.$order_id;
      $thawani_api = $this->posturl;
      $developer_email	= $this->get_option('developer_log');

      //access customer order data
      $amount = $order->get_total();
      $order_items = $order->get_items();
      $shipping_total = $order->get_shipping_total();
      $products_list = array();

      // Get and Loop Over Order Items and add it as array to be used on "products": [] with the Thawani checkout api
      foreach ( $order_items as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $product_name = $item->get_name();
        $quantity = $item->get_quantity();
        $subtotal = $item->get_subtotal();
        $total_price = $item->get_total();
        array_push($products_list, array('name'=> substr($product_name, 0, 40), 'unit_amount' => $total_price * 1000, 'quantity' => $quantity));
        
      }

      //check for shipping cost to be added as product item on Thawani checkout api.
      if($shipping_total == 0) {
        //shipping cost is 0 .
      } else {
        array_push($products_list, array('name'=> 'Shipping Fee', 'unit_amount' => $shipping_total * 1000, 'quantity' => 1));
      }

      $billing_email = $order->get_billing_email();
      $billing_phone = $order->get_billing_phone();
      $billing_email = preg_replace("/[+]/", "00", $billing_email);
      $fname = $order->get_billing_first_name();
      $lname = $order->get_billing_last_name();
      $full_name = $fname . ' ' . $lname;
      $user_id = $order->get_user_id();
      $products = [array()];
      
      $payment_json = array (
        'client_reference_id' => $client_reference_id,
        'products' => $products_list,
        'success_url' => $success_url,
        'cancel_url' => $cancel_url,
        'metadata' => 
        array (
          'customer_name' => $full_name,
          'customer_id' => $user_id,
          'order_id' => $order_id,
          'customer_email' => $billing_email,
          'customer_phone' => $billing_phone
        ),
      );
      
      $data = json_encode($payment_json);

      try {
        //Send Post request to get payment session details
        $curl = curl_init();
        curl_setopt_array($curl, [
          CURLOPT_URL => $thawani_api . '/api/v1/checkout/session',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data,
          CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Thawani-Api-Key: " . $secret_key
          ],
        ]);
        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
          wc_add_notice("Error: " . $err, 'error' );
          //Send email to admin with error message
          if(empty($developer_email)) {
            wc_mail( get_option('admin_email'), "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $err, $headers = "Content-Type: text/htmlrn", $attachments = "" );
          } else {
            wc_mail( $developer_email, "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $err, $headers = "Content-Type: text/htmlrn", $attachments = "" );
          }
        } else {
          $response = json_decode($result, true);
          $code = $response['code'];
          if($code == 2004 || $code == '2004') {
            $session_id = $response['data']['session_id'];
            update_post_meta( $order_id, 'session_id', $session_id);
            $redirect_url = $thawani_api . "/pay/" . $session_id. '?key=' . $publishable_key;
            return array(
              'result'   => 'success',
              'redirect' => $redirect_url
            );
          } else {
            wc_add_notice($code . ": " . $response['description'], 'error' );
            if(empty($developer_email)) {
              wc_mail( get_option('admin_email'), "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $result, $headers = "Content-Type: text/htmlrn", $attachments = "" );
            } else {
              wc_mail( $developer_email, "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $result, $headers = "Content-Type: text/htmlrn", $attachments = "" );
            }
            return;
          }
        }
      } catch (\Throwable $th) {
        if(empty($developer_email)) {
          wc_mail( get_option('admin_email'), "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $th, $headers = "Content-Type: text/htmlrn", $attachments = "" );
        } else {
          wc_mail( $developer_email, "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $th, $headers = "Content-Type: text/htmlrn", $attachments = "" );
        }
        throw $th;
      }
      
    }
    
	// get website all pages
	function thawani_get_pages($title = false, $indent = true) {
		$wp_pages = get_pages('sort_column=menu_order');
		$page_list = array();
		if ($title) $page_list[] = $title;
		foreach ($wp_pages as $page) {
			$prefix = '';
			// show indented child pages?
			if ($indent) {
            	$has_parent = $page->post_parent;
            	while($has_parent) {
                	$prefix .=  ' - ';
                	$next_page = get_post($has_parent);
                	$has_parent = $next_page->post_parent;
            	}
        	}
        	// add to page list array array
        	$page_list[$page->ID] = $prefix . $page->post_title;
    	}
    	return $page_list;
		}
		

  }
}

//Payment callback to check transaction status
add_action( 'init', 'woocommerce_process_thawani_payment' );
function woocommerce_process_thawani_payment() {
  global $woocommerce;
  //check for paramter passed on the url. This will access the success and cancel payment callback
  if ( isset( $_GET['mode'] ) && isset( $_GET['oid'] ) && isset( $_GET['status'] ) ) {
    $mode = $_GET['mode'];
    $order_id = $_GET['oid'];
    $order_status = $_GET['status'];
    $session_id = get_post_meta($order_id, "session_id", true);
    $options = get_option('woocommerce_thawani_settings');
    $secret_key = str_replace('"', '', json_encode($options['secret_key']));
    $developer_email = str_replace('"', '', json_encode($options['developer_log']));

    $order = wc_get_order($order_id);

    if ($mode == 0) {
      //UAT
      $posturl = 'https://uatcheckout.thawani.om/api/v1/checkout/session/' . $session_id;
    } else {
      //production
      $posturl = 'https://checkout.thawani.om/api/v1/checkout/session/' . $session_id;
    }
    
    if(empty($secret_key)) {
      wc_add_notice("Error: No key found", 'error' );
      //Send email to admin with error message
      if(empty($developer_email)) {
        wc_mail( get_option('admin_email'), "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />Secret Key not found on the Thawany Payment settings, please make sure to add your Thawani api secret key.", $headers = "Content-Type: text/htmlrn", $attachments = "" );
      } else {
        wc_mail( $developer_email, "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />Secret Key not found on the Thawany Payment settings, please make sure to add your Thawani api secret key.", $headers = "Content-Type: text/htmlrn", $attachments = "" );
      }

    } else {
      //Send GET request to get payment session details
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $posturl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Thawani-Api-Key: ' . $secret_key
        ),
      ));
      
      $result = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      
      if ($err) {
        wc_add_notice("Error: " . $err, 'error' );
        //Send email to admin with error message
        if(empty($developer_email)) {
          wc_mail( get_option('admin_email'), "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $err, $headers = "Content-Type: text/htmlrn", $attachments = "" );
        } else {
          wc_mail( $developer_email, "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $err, $headers = "Content-Type: text/htmlrn", $attachments = "" );
        }
      } else {
        $response = json_decode($result, true);
        $code = $response['code'];
        $success = $response['success'];
        $description = $response['description'];
        //Check if call success
        if($code == 2000 && $success == true) {
          $payment_status = $response['data']['payment_status'];
          //Check payment status
          if($payment_status == 'paid') {
            $order->payment_complete();
            $order->add_order_note('Thawani payment successful.');
            $woocommerce -> cart -> empty_cart();
            wc_add_notice( __('Thank you for shopping with us.', 'woothemes') . "order placed successfully", 'success' );
          } else if($payment_status == 'unpaid') {
            $order->add_order_note('The Thawani transaction has been declined.');
            wc_add_notice( __('Thank you for shopping with us.', 'woothemes') . "However, the transaction has been declined.", 'error' );
          } else {
            wc_add_notice( __('Thank you for shopping with us.', 'woothemes') . "However, the transaction has been declined.", 'error' );
          }
        } else {
          //Return error if payment request not success and notify admin via email
          wc_add_notice($code . ": " . $response['description'], 'error' );
          if(empty($developer_email)) {
            wc_mail( get_option('admin_email'), "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $result, $headers = "Content-Type: text/htmlrn", $attachments = "" );
          } else {
            wc_mail( $developer_email, "[ERROR] Thawani Pay - Order #" . $order_id, "Howdy!<br />We caught an error with payment requests.<br /><br />Error Details<br />===========<br />" . $result, $headers = "Content-Type: text/htmlrn", $attachments = "" );
          }
        }
      }
    }

  }
}
