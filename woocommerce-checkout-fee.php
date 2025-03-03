<?php
/**
 * Plugin Name: WooCommerce Checkout Fee
 * Plugin URI: https://github.com/gdeguglielmo/woocommerce-checkout-fee
 * Description: Aggiunge una fee al carrello di WooCommerce che può essere rimossa dall'utente tramite una checkbox.
 * Version: 1.0
 * Author: [Il tuo nome]
 * Author URI: [Il tuo sito web]
 * License: GPL2
 */

// Assicurati che WooCommerce sia attivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Aggiungi la fee al carrello
function wc_add_checkout_fee() {
    // Verifica se la checkbox è stata selezionata
    if ( isset( $_POST['remove_checkout_fee'] ) ) {
        // Se la checkbox è spuntata, non aggiungere la fee
        return;
    }

    // Aggiungi la fee al carrello
    $fee = 10; // L'importo della fee (puoi modificarlo tramite opzioni nel backend)
    WC()->cart->add_fee( __( 'Checkout Fee', 'woocommerce-checkout-fee' ), $fee, true, '' );
}
add_action( 'woocommerce_cart_calculate_fees', 'wc_add_checkout_fee' );

// Aggiungi la checkbox al checkout
function wc_checkout_fee_checkbox() {
    // Aggiungi una checkbox al form di checkout
    echo '<div class="checkout-fee-checkbox">';
    echo '<label>';
    echo '<input type="checkbox" name="remove_checkout_fee" value="1" /> ';
    echo __( 'Remove checkout fee', 'woocommerce-checkout-fee' );
    echo '</label>';
    echo '</div>';
}
add_action( 'woocommerce_review_order_before_payment', 'wc_checkout_fee_checkbox' );

// Salva la scelta dell'utente nella sessione
function wc_save_checkout_fee_checkbox( $order_id ) {
    if ( isset( $_POST['remove_checkout_fee'] ) ) {
        update_post_meta( $order_id, '_remove_checkout_fee', 'yes' );
    } else {
        update_post_meta( $order_id, '_remove_checkout_fee', 'no' );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'wc_save_checkout_fee_checkbox' );

// Aggiungi una pagina delle opzioni nel backend per impostare il testo e l'importo della fee
function wc_checkout_fee_settings_page() {
    add_options_page(
        'WooCommerce Checkout Fee Settings', 
        'Checkout Fee Settings', 
        'manage_options', 
        'wc_checkout_fee', 
        'wc_checkout_fee_settings_page_content'
    );
}
add_action( 'admin_menu', 'wc_checkout_fee_settings_page' );

// Contenuto della pagina delle impostazioni
function wc_checkout_fee_settings_page_content() {
    ?>
    <div class="wrap">
        <h2><?php _e( 'WooCommerce Checkout Fee Settings', 'woocommerce-checkout-fee' ); ?></h2>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'wc_checkout_fee_settings' );
            do_settings_sections( 'wc_checkout_fee' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Fee Amount', 'woocommerce-checkout-fee' ); ?></th>
                    <td>
                        <input type="number" name="wc_checkout_fee_amount" value="<?php echo esc_attr( get_option( 'wc_checkout_fee_amount', 10 ) ); ?>" min="0" step="0.01" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Fee Label', 'woocommerce-checkout-fee' ); ?></th>
                    <td>
                        <input type="text" name="wc_checkout_fee_label" value="<?php echo esc_attr( get_option( 'wc_checkout_fee_label', 'Checkout Fee' ) ); ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Registra le opzioni nelle impostazioni
function wc_checkout_fee_register_settings() {
    register_setting( 'wc_checkout_fee_settings', 'wc_checkout_fee_amount' );
    register_setting( 'wc_checkout_fee_settings', 'wc_checkout_fee_label' );
}
add_action( 'admin_init', 'wc_checkout_fee_register_settings' );