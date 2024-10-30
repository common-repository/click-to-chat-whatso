<?php
// Stop immediately if accessed directly.
if (! defined('ABSPATH') ) {
    die();
}
if (isset($_POST['whatso_consent_confirmation']) ) {
    $legit = true;
    if (! isset($_POST['whatso_consent_confirmation_form_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_consent_confirmation_form_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_consent_confirmation_form_nonce'])): '';
    if (! wp_verify_nonce($nonce, 'whatso_consent_confirmation_form') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }
    $consent_description = isset($_POST['consent_description']) ? sanitize_text_field(wp_unslash($_POST['consent_description'])) : '';
    $consent_checkbox_text_label = isset($_POST['consent_checkbox_text_label']) ? sanitize_text_field(wp_unslash($_POST['consent_checkbox_text_label'])) : '';
    $consent_alert_background_color = isset($_POST['consent_alert_background_color']) ? sanitize_text_field(wp_unslash($_POST['consent_alert_background_color'])) : '';
    WHATSO_Utils::updateSetting('consent_description', $consent_description);
    WHATSO_Utils::updateSetting('consent_checkbox_text_label', $consent_checkbox_text_label);
    WHATSO_Utils::updateSetting('consent_alert_background_color', $consent_alert_background_color);
    /* WPML if installed and active */
    do_action('wpml_register_single_string', 'WhatsApp Click to Chat', 'Consent Description', $consent_description);
    do_action('wpml_register_single_string', 'WhatsApp Click to Chat', 'Consent Checkbox Text Label', $consent_checkbox_text_label);
    /* Recreate CSS file */
    WHATSO_Utils::generateCustomCSS();
    add_settings_error('whatso-settings', 'whatso-settings', __('Consent confirmation saved', 'whatso'), 'updated');
}
WHATSO_Utils::setView('floating_widget_consent_confirmation');
?>
