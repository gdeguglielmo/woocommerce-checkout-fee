<?php
/**
 * Plugin Name: WooCommerce Checkout Fee
 * Plugin URI: https://github.com/gdeguglielmo/woocommerce-checkout-fee
 * Description: Aggiungi una fee personalizzabile al carrello in WooCommerce che può essere rimossa tramite una checkbox.
 * Version: 1.0
 * Author: Il tuo nome
 * Author URI: https://github.com/gdeguglielmo
 * License: GPL2
 */

// Aggiungi la fee al carrello
function wccf_add_checkout_fee() {
    // Verifica se la checkbox è selezionata
    if ( isset( $_POST['wccf_checkout_fee'] ) && $_POST['wccf_checkout_fee'] == '1' ) {
        $fee = 10; // La fee, modificabile da backend
        WC()->cart->add_fee( 'Fee Personalizzata', $fee );
    }
}
add_action( 'woocommerce_cart_calculate_fees', 'wccf_add_checkout_fee' );

// Aggiungi una checkbox nella pagina del checkout per rimuovere la fee
function wccf_checkout_fee_checkbox() {
    echo '<div class="wccf-checkout-fee">';
    echo '<input type="checkbox" name="wccf_checkout_fee" value="1" checked> Aggiungi una fee di 10€';
    echo '</div>';
}
add_action( 'woocommerce_review_order_before_submit', 'wccf_checkout_fee_checkbox' );

// Aggiungi una pagina delle opzioni nel backend del plugin
function wccf_add_plugin_options_page() {
    add_menu_page( 
        'WooCommerce Checkout Fee', 
        'WooCommerce Checkout Fee', 
        'manage_options', 
        'wccf-settings', 
        'wccf_plugin_options_page',
        'dashicons-cart'
    );
}
add_action( 'admin_menu', 'wccf_add_plugin_options_page' );

// Contenuto della pagina delle opzioni
function wccf_plugin_options_page() {
    ?>
    <div class="wrap">
        <h1>Impostazioni Fee WooCommerce Checkout</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'wccf_options_group' );
            do_settings_sections( 'wccf-settings' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Testo della Fee</th>
                    <td><input type="text" name="wccf_fee_text" value="<?php echo esc_attr( get_option('wccf_fee_text') ); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Importo della Fee (€)</th>
                    <td><input type="number" name="wccf_fee_amount" value="<?php echo esc_attr( get_option('wccf_fee_amount') ); ?>" /></td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Registra le opzioni nel database
function wccf_register_settings() {
    register_setting( 'wccf_options_group', 'wccf_fee_text' );
    register_setting( 'wccf_options_group', 'wccf_fee_amount' );
}
add_action( 'admin_init', 'wccf_register_settings' );

// Funzione per applicare il testo e l'importo personalizzati della fee
function wccf_custom_fee_text( $cart ) {
    $fee_text = get_option( 'wccf_fee_text', 'Fee personalizzata' );
    $fee_amount = get_option( 'wccf_fee_amount', 10 );
    
    if ( isset( $_POST['wccf_checkout_fee'] ) && $_POST['wccf_checkout_fee'] == '1' ) {
        $cart->add_fee( $fee_text, $fee_amount );
    }
}
add_action( 'woocommerce_cart_calculate_fees', 'wccf_custom_fee_text' );
?>