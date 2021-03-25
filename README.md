<img src="https://user-images.githubusercontent.com/15148391/110596748-1ce9dd00-8199-11eb-87b9-18bb32f7eff4.png" width="420">

# Thawani Pay v2 for WooCommerce

Accepts payments with the **Thawani Pay v2** Gateway for WooCommerce

This is the unofficial **Thawani Pay v2** payment gateway plugin for WooCommerce. Allows you to accept credit cards, debit cards payments with the WooCommerce plugin. It uses a seamless integration, allowing the customer to pay on your website quickly.
Thawani Checkout API Documentation: [Thawani API Documentation](https://developer.thawani.om/)

> **Note:** Thawani Pay is currently available for businesses with account in Thawani company.

For detailed information and signup please visit: [Thawani Pay](https://thawani.om/)

# Requirements
* Requires at least: woocommerce  >= 4.0.0, WordPress >= 4.0.0
* Tested up to: 5.6.2
* Requires PHP >= 5.6
* WC requires at least: 4.0.0
* WC tested up to: 5.0.0


## Features
* Compatible with Thawani Pay version 2
* Easy configuration in WooCommerce - only API secret key and Publishable key need to be copied from Thawani Merchant Portal
* Easy customization - payment method **title**, **description**, **environment: UAT/Production**, **cancel url**, **success url**, **client reference id Prefix** and more can be changed easily
* Compatible with UAT and production API.
* Developer logs feature: receive new emails when something happens with the payment API
* WC tested up to 5.0.0

**To-Do**
- [x] Support UAT api
- [x] Support PRODUCTION api
- [x] Create new payment
- [x] Success and Cancel callbacks
- [ ] Webhook
- [ ] Payment card tokenization - Saved cards
- [ ] Refund - intgrate Thawani refund API with woocomerce refund feature 
- [ ] Transaction details - View payment transaction details on on order edit.

## Screenshots
<img width="1664" alt="Screen Shot 2021-03-10 at 11 18 05 AM" src="https://user-images.githubusercontent.com/15148391/110591421-7c90ba00-8192-11eb-8d9e-6da8c3358738.png">

## Installation
1. Upload `thawani-pay-woocommerce` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce - Settings - Payments - Thawani Pay - setup
4. Thawani payment gateway will be available in "Checkout" page.

## Test with UAT
You can test if the payment works correctly by enableing the UAT mode <img width="156" alt="Screen Shot 2021-03-10 at 11 36 51 AM" src="https://user-images.githubusercontent.com/15148391/110593372-ead67c00-8194-11eb-9fce-bab7d442ace1.png">

UAT Secret Key: `rRQ26GcsZzoEhbrP2HZvLYDbn9C9et`

UAT Publishable Key: `HGvTMLDssJghr9tlN9gr4DVYt0qyBy`

You can use the test cards provided by Thawani Team:

**Success Card**

<img src="https://user-images.githubusercontent.com/15148391/110593723-5fa9b600-8195-11eb-978a-129f257792e5.png" width="420">

**Decline Card**

<img src="https://user-images.githubusercontent.com/15148391/110593730-620c1000-8195-11eb-87c9-479082e9c39b.png" width="420">

## Changelog

# 1.1.0 #
* fix product name must be a string with a maximum length of 40 issue.
* Add billing phone to request metadata.

# 1.0.0 #
* first public version

## License

[gpl-3.0 License](LICENSE)

## Contributing
<a href="https://www.buymeacoffee.com/mahmood" target="_blank "><img src="https://user-images.githubusercontent.com/15148391/110588953-2c642880-818f-11eb-81ef-34e36a608d2f.png" width="220" height="50"></a>

All feedback/bug reports/pull requests are welcome.
