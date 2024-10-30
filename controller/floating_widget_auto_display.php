<?php

// Stop immediately if accessed directly.
if (! defined('ABSPATH') ) {
    die();
}

if (isset($_POST['whatso_auto_display']) ) {
    $legit = true;
    if (! isset($_POST['whatso_auto_display_form_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_auto_display_form_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_auto_display_form_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'whatso_auto_display_form') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }
    $delay_time = isset($_POST['delay_time']) ? sanitize_text_field(wp_unslash($_POST['delay_time'])) : '';
    $inactivity_time = isset($_POST['inactivity_time']) ? sanitize_text_field(wp_unslash($_POST['inactivity_time'])) : '';
    $scroll_length = isset($_POST['scroll_length']) ? sanitize_text_field(wp_unslash($_POST['scroll_length'])) : '';
    $disable_auto_display_on_small_screen = isset($_POST['disable_auto_display_on_small_screen']) ? 'on' : 'off';
    $disable_auto_display_when_no_one_online = isset($_POST['disable_auto_display_when_no_one_online']) ? 'on' : 'off';
    WHATSO_Utils::updateSetting('delay_time', $delay_time);
    WHATSO_Utils::updateSetting('inactivity_time', $inactivity_time);
    WHATSO_Utils::updateSetting('scroll_length', $scroll_length);
    WHATSO_Utils::updateSetting('disable_auto_display_on_small_screen', $disable_auto_display_on_small_screen);
    WHATSO_Utils::updateSetting('disable_auto_display_when_no_one_online', $disable_auto_display_when_no_one_online);    
    add_settings_error('whatso-settings', 'whatso-settings', __('Auto display saved', 'whatso'), 'updated');
}
WHATSO_Utils::setView('floating_widget_auto_display');
?>
