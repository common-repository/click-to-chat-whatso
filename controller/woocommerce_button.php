<?php
if (isset($_POST['whatso_woocommerce_button']) ) {
    $legit = true;
    /* Check if our nonce is set. */
    if (! isset($_POST['whatso_woocommerce_button_form_nonce']) ) {
        $legit = false;
    }
    $nonce = sanitize_text_field(wp_unslash($_POST['whatso_woocommerce_button_form_nonce']));
    /* Verify that the nonce is valid. */
    if (! wp_verify_nonce($nonce, 'whatso_woocommerce_button_form') ) {
        $legit = false;
    }
    /**
     * Something is wrong with the nonce. Redirect it to the 
     * settings page without processing any data.
     */
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }
    $wc_button_position = isset($_POST['wc_button_position']) ? sanitize_text_field(wp_unslash($_POST['wc_button_position'])) : '';
    $wc_randomize_accounts_order = isset($_POST['wc_randomize_accounts_order']) ? 'on' : 'off';
    $wc_total_accounts_shown = isset($_POST['wc_total_accounts_shown']) ? (int) sanitize_text_field(wp_unslash($_POST['wc_total_accounts_shown'])) : 0;
    WHATSO_Utils::updateSetting('wc_button_position', $wc_button_position);
    WHATSO_Utils::updateSetting('wc_randomize_accounts_order', $wc_randomize_accounts_order);
    WHATSO_Utils::updateSetting('wc_total_accounts_shown', $wc_total_accounts_shown);
    $ids = array();
    $the_posts = isset($_POST['whatso_selected_account']) ? sanitize_post(wp_unslash($_POST['whatso_selected_account']))  : array();
    foreach ( $the_posts as $k => $v ) {
        $ids[] = (int) $v;
    }
    WHATSO_Utils::updateSetting('selected_accounts_for_woocommerce', wp_json_encode($ids));
    add_settings_error('whatso-settings', 'whatso-settings', __('WooCommerce button saved', 'whatso'), 'updated');
}
WHATSO_Utils::setView('woocommerce_button');
?>
