<?php
$tab = isset($_GET['tab']) ? strtolower(sanitize_text_field(wp_unslash($_GET['tab']))) : '';
if($tab == "" || $tab == "selected_accounts" ) {
    include_once 'floating_widget_selected_accounts.php';
}
elseif($tab == 'display_settings' ) {
    include_once 'floating_widget_display_settings.php';
}
elseif($tab == 'auto_display' ) {
    include_once 'floating_widget_auto_display.php';
}
elseif($tab == 'consent_confirmation' ) {
    include_once 'floating_widget_consent_confirmation.php';
}
elseif($tab == "" || $tab == "dashboard" ) {
    include_once 'ac_dashboard_display.php';
}

elseif($tab == 'messages' ) {
    include_once 'messages.php';
}
elseif($tab == 'whatsapp_setting' ) {
    include_once 'whatsapp_setting.php';
}
elseif($tab == 'report_display' ) {
    include_once 'report_display.php';
}
else{
    wp_die();
}
?>
