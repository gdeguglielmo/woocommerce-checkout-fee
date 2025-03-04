<?php
/**
 * Plugin Name: WooCommerce Checkout Fee
 * Plugin URI:  https://wordpress.org/plugins/woocommerce-checkout-fee/
 * Description: Adds a customizable checkout fee to WooCommerce.
 * Version:     1.0
 * Author:      Giuseppina De Guglielmo D'Andrea
 * Author URI:  https://pixylabs.com
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woocommerce-checkout-fee
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add a custom checkout fee
function add_checkout_fee() {
    // Get the fee value and label from the plugin settings
    $fee_label = get_option( 'checkout_fee_label', 'Custom Fee' );
    $fee_amount = get_option( 'checkout_fee_amount', 5 );

    // Add the fee to the cart
    WC()->cart->add_fee( $fee_label, $fee_amount, true, '' );
}
add_action( 'woocommerce_cart_calculate_fees', 'add_checkout_fee' );

// Create the settings page in the WordPress admin
function checkout_fee_plugin_menu() {
    add_options_page( 
        'Checkout Fee Settings', // Page title
        'Checkout Fee',          // Menu title
        'manage_options',        // Capability
        'checkout-fee-settings', // Menu slug
        'checkout_fee_settings_page' // Function to display settings page
    );
}
add_action( 'admin_menu', 'checkout_fee_plugin_menu' );

// Display the plugin settings page
function checkout_fee_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form method="post" action="options.php">
            <?php
            // Output nonce and other settings fields
            settings_fields( 'checkout_fee_settings_group' );
            do_settings_sections( 'checkout-fee-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings for the fee label and amount
function checkout_fee_settings_init() {
    register_setting(
        'checkout_fee_settings_group', // Settings group
        'checkout_fee_label'           // Option name for the label
    );
    register_setting(
        'checkout_fee_settings_group', // Settings group
        'checkout_fee_amount'          // Option name for the fee amount
    );

    add_settings_section(
        'checkout_fee_section',          // Section ID
        'Checkout Fee Settings',         // Section title
        '',                              // Callback function for description
        'checkout-fee-settings'          // Settings page slug
    );

    add_settings_field(
        'checkout_fee_label_field',      // Field ID
        'Fee Label',                     // Field title
        'checkout_fee_label_field_cb',   // Callback function to display field
        'checkout-fee-settings',         // Settings page slug
        'checkout_fee_section'           // Section ID
    );

    add_settings_field(
        'checkout_fee_amount_field',     // Field ID
        'Fee Amount',                    // Field title
        'checkout_fee_amount_field_cb',  // Callback function to display field
        'checkout-fee-settings',         // Settings page slug
        'checkout_fee_section'           // Section ID
    );
}
add_action( 'admin_init', 'checkout_fee_settings_init' );

// Callback function to display the fee label input
function checkout_fee_label_field_cb() {
    $label = get_option( 'checkout_fee_label', 'Custom Fee' );
    echo '<input type="text" name="checkout_fee_label" value="' . esc_attr( $label ) . '" />';
}

// Callback function to display the fee amount input
function checkout_fee_amount_field_cb() {
    $amount = get_option( 'checkout_fee_amount', 5 );
    echo '<input type="number" name="checkout_fee_amount" value="' . esc_attr( $amount ) . '" />';
}

// Add settings link on the plugins page
function checkout_fee_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=checkout-fee-settings">Settings</a>';
    array_push( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'checkout_fee_add_settings_link' );
