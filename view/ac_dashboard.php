<?php

function whatso_is_active( $tab )
{
    $get = isset($_GET['tab']) ? strtolower(sanitize_text_field(wp_unslash($_GET['tab']))) : '';
    if ($get === $tab || ( '' === $get && 'dashboard' === $tab ) ) {
        echo esc_attr('nav-tab-active ');
    }
}
?>
<div class="container">

    <ul class="breadcrumb">
        <li class="breadRow"><a href="admin.php?page=whatso_floating_menu_setup"><strong>Whatso</strong></a></li>
        <li class="breadcrum2"><strong><?php esc_html_e('Abandoned cart recovery','whatso');?></strong></li>
    </ul>
    <?php settings_errors(); ?>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li><a href="?page=whatso_floating_widget&tab=dashboard"
                        class="nav-tab <?php whatso_is_active('dashboard'); ?>"><?php esc_html_e('Dashboard', 'whatso'); ?></a>
                </li>

                <li><a href="?page=whatso_floating_widget&tab=messages"
                        class="nav-tab <?php whatso_is_active('messages'); ?>"><?php esc_html_e('Messages', 'whatso'); ?></a>
                </li>
                
                <li><a href="?page=whatso_floating_widget&tab=report_display" class="nav-tab <?php whatso_is_active('report_display'); ?>"><?php esc_html_e('Report', 'whatso'); ?></a></li>
                <li><a href="?page=whatso_floating_widget&tab=whatsapp_setting"
                        class="nav-tab <?php whatso_is_active('whatsapp_setting'); ?>"
                        style="color:orange;font-weight:bold;"><i class="fa fa-star"></i>&nbsp;
                        <?php esc_html_e('Premium', 'whatso'); ?>&nbsp;<i class="fa fa-star"></i></a>
                </li>   
            </ul>
        </div>
    </div>
</div>
