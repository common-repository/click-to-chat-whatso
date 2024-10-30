<?php

/**
 * Function for check who is activated
 */
function whatso_is_active( $tab )
{
    $get = isset($_GET['tab']) ? strtolower(sanitize_text_field(wp_unslash($_GET['tab']))) : '';
    if ($get === $tab || ( '' === $get && 'selected_accounts' === $tab ) ) {
        echo esc_attr('nav-tab-active');
    }
}

$img_url = plugin_dir_url(__DIR__);
$logo = $img_url . 'assets/images/whatsoLogoNew_black2.webp';



?>
<h1 style="font-size: 40px;"><img src="<?php echo esc_url($logo);?>" height="35px" width="35px" style="margin-top: -10px;" alt="" /> Whatso Click to Chat</h1>
<ul class="breadcrumb">
    <li><a href="admin.php?page=whatso_floating_menu_setup"><strong>Whatso</strong></a></li>
    <li><a href="admin.php?page=whatso_floating_ctc_setup"><strong>Click to Chat</strong></a></li>
    <li><strong>Display Settings</strong></li>
</ul>
<?php settings_errors(); ?>
<h2 class="nav-tab-wrapper mb-3">
    <a href="?page=whatso_floating_widget&tab=selected_accounts" class="nav-tab <?php whatso_is_active('selected_accounts'); ?>"><?php esc_html_e('Selected Accounts', 'whatso'); ?></a>
    <a href="?page=whatso_floating_widget&tab=display_settings" class="nav-tab <?php whatso_is_active('display_settings'); ?>"><?php esc_html_e('Appearance', 'whatso'); ?></a>
    <a href="?page=whatso_floating_widget&tab=auto_display" class="nav-tab <?php whatso_is_active('auto_display'); ?>"><?php esc_html_e('Auto Display', 'whatso'); ?></a>
    <a href="?page=whatso_floating_widget&tab=consent_confirmation" class="nav-tab <?php whatso_is_active('consent_confirmation'); ?>"><?php esc_html_e('Consent Confirmation', 'whatso'); ?></a>
</h2>
