<?php

// Stop immediately if accessed directly.
if (! defined('ABSPATH') ) {
    die();
}
if (isset($_POST['whatso_display_settings']) ) {
    $legit = true;
    /* Check if our nonce is set. */
    if (! isset($_POST['whatso_display_settings_form_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_display_settings_form_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_display_settings_form_nonce'])): '';
    // Verify that the nonce is valid.
    if (! wp_verify_nonce($nonce, 'whatso_display_settings_form') ) {
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
    $toggle_text = isset($_POST['toggle_text']) ? sanitize_text_field(wp_unslash($_POST['toggle_text'])) : '';
    $toggle_text_color = isset($_POST['toggle_text_color']) ? sanitize_text_field(wp_unslash($_POST['toggle_text_color'])) : '';
    $toggle_background_color = isset($_POST['toggle_background_color']) ? sanitize_text_field(wp_unslash($_POST['toggle_background_color'])) : '';
    $toggle_round_on_desktop = isset($_POST['toggle_round_on_desktop']) ? 'on' : 'off';
    $toggle_round_on_mobile = isset($_POST['toggle_round_on_mobile']) ? 'on' : 'off';
    $description = isset($_POST['description']) ? wp_kses_post(sanitize_text_field($_POST['description'])) : '';
    $container_text_color = isset($_POST['container_text_color']) ? sanitize_text_field(wp_unslash($_POST['container_text_color'])) : '';
    $container_background_color = isset($_POST['container_background_color']) ? sanitize_text_field(wp_unslash($_POST['container_background_color'])) : '';
    $account_hover_background_color = isset($_POST['account_hover_background_color']) ? sanitize_text_field(wp_unslash($_POST['account_hover_background_color'])) : '';
    $account_hover_text_color = isset($_POST['account_hover_text_color']) ? sanitize_text_field(wp_unslash($_POST['account_hover_text_color'])) : '';
    $border_color_between_accounts = isset($_POST['border_color_between_accounts']) ? sanitize_text_field(wp_unslash($_POST['border_color_between_accounts'])) : '';
    $box_position = isset($_POST['box_position']) ? sanitize_text_field(wp_unslash($_POST['box_position'])) : '';
    $toggle_center_on_mobile = isset($_POST['toggle_center_on_mobile']) ? 'on' : 'off';
    $randomize_accounts_order = isset($_POST['randomize_accounts_order']) ? 'on' : 'off';
    $total_accounts_shown = isset($_POST['total_accounts_shown']) ? (int) sanitize_text_field(wp_unslash($_POST['total_accounts_shown'])) : 0;
    $cache_time = isset($_POST['cache_time']) ? (int) sanitize_text_field(wp_unslash($_POST['cache_time'])) : 0;
    WHATSO_Utils::updateSetting('toggle_text', $toggle_text);
    WHATSO_Utils::updateSetting('toggle_text_color', $toggle_text_color);
    WHATSO_Utils::updateSetting('toggle_background_color', $toggle_background_color);
    WHATSO_Utils::updateSetting('toggle_round_on_desktop', $toggle_round_on_desktop);
    WHATSO_Utils::updateSetting('toggle_round_on_mobile', $toggle_round_on_mobile);
    WHATSO_Utils::updateSetting('description', $description);
    WHATSO_Utils::updateSetting('container_text_color', $container_text_color);
    WHATSO_Utils::updateSetting('container_background_color', $container_background_color);
    WHATSO_Utils::updateSetting('account_hover_background_color', $account_hover_background_color);
    WHATSO_Utils::updateSetting('account_hover_text_color', $account_hover_text_color);
    WHATSO_Utils::updateSetting('border_color_between_accounts', $border_color_between_accounts);
    WHATSO_Utils::updateSetting('box_position', $box_position);
    WHATSO_Utils::updateSetting('toggle_center_on_mobile', $toggle_center_on_mobile);
    WHATSO_Utils::updateSetting('randomize_accounts_order', $randomize_accounts_order);
    WHATSO_Utils::updateSetting('total_accounts_shown', $total_accounts_shown);
    WHATSO_Utils::updateSetting('cache_time', $cache_time);
    /* WPML if installed and active */
    do_action('wpml_register_single_string', 'WhatsApp Click to Chat', 'Toggle Text', $toggle_text);
    do_action('wpml_register_single_string', 'WhatsApp Click to Chat', 'Description', $description);
    /* Recreate CSS file */
    WHATSO_Utils::generateCustomCSS();
    add_settings_error('whatso-settings', 'whatso-settings', __('Display settings saved', 'whatso'), 'updated');
}
WHATSO_Utils::setView('floating_widget_display_settings');
?>
