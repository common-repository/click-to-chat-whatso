<?php

// Stop immediately if accessed directly.
if (! defined('ABSPATH') ) {
    die();
}
if (isset($_POST['whatso_selected_accounts']) ) {
    $legit = true;
    // Check if our nonce is set.
    if (! isset($_POST['whatso_selected_accounts_form_nonce']) ) {
        
        $legit = false;
    }
    $nonce = isset($_POST['whatso_selected_accounts_form_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_selected_accounts_form_nonce'])): '';
    // Verify that the nonce is valid.
    if (! wp_verify_nonce($nonce, 'whatso_selected_accounts_form') ) {
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
    $ids = array();
    $the_posts = isset($_POST['whatso_selected_account']) ?  sanitize_post(wp_unslash($_POST['whatso_selected_account'])) : array();
        
    foreach ( $the_posts as $k => $v ) {
        $ids[] = (int) $v;
        
    }
    
    WHATSO_Utils::updateSetting('selected_accounts_for_widget', wp_json_encode($ids));
    
    add_settings_error('whatso-settings', 'whatso-settings', __('Selected accounts saved', 'whatso'), 'updated');
}
WHATSO_Utils::setView('floating_widget_selected_accounts');
?>
