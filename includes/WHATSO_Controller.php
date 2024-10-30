<?php

/**
 * This class catches the admin_init hook and decide which controller 
 * file to load based on the query string.
 */
class WHATSO_Controller
{
    
    /**
     * Initialize constructor
     */    
    public function __construct()
    {
        
        if (is_admin() ) {
            add_action('admin_init', array( $this, 'getController' ));
        }
    }
        
    /**
     * Function for get controller
     */
    public function getController()
    {
        $page = isset($_GET['page']) ? strtolower(sanitize_text_field(wp_unslash($_GET['page']))) : '';
        $prefix = WHATSO_PREFIX . '_';
        $file_name = substr($page, 0, strlen($prefix)) === $prefix
        ? substr($page, strlen($prefix), strlen($page)) 
        : $page
        ;
        $files_allowed = array ( "floating_widget.php", "floating_widget_auto_display.php", "floating_widget_consent_confirmation.php", "floating_widget_display_settings.php", "floating_widget_selected_accounts.php", "settings.php","floating_quick_setup.php","floating_menu_setup.php","floating_ctc_setup.php", "woocommerce_button.php","notifications_setup.php","abandoned_cart.php","ac_setup1.php","ac_setup2.php" ,"ac_dashboard.php","ac_dashboard.php","ac_dashboard_display.php","report_display.php","admin_settings.php","broadcast_message.php","message_notification_cf7.php");
    
        $path_to_controller = WHATSO_PLUGIN_DIR . 'controller/' . $file_name . '.php';
        $filenames = basename($path_to_controller);
        if(file_exists($path_to_controller) ) {
            if(in_array($filenames, $files_allowed) ) {
                   include_once $path_to_controller;
            }
            else{
                 wp_die("File not allowed");
            }
        }
    }
}
