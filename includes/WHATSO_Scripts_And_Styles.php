<?php

class WHATSO_Scripts_And_Styles
{
    
    public function __construct()
    {
        
        if (is_admin() ) {
            add_action('admin_enqueue_scripts', array( $this, 'adminEnqueueScripts' ));
        }
        
    }
    
    /**
     * Enqueue scripts and styles only for our plugin.
     */
    public function adminEnqueueScripts()
    {
        
        global $pagenow;
        
        $settings_pages = array(
        WHATSO_PREFIX . '_floating_quick_setup',
        WHATSO_PREFIX . '_settings',
        WHATSO_PREFIX . '_floating_widget',
        WHATSO_PREFIX . '_woocommerce_button',
        WHATSO_PREFIX . '_notifications_setup',
        WHATSO_PREFIX . '_abandoned_cart',
        WHATSO_PREFIX . '_floating_menu_setup',
        WHATSO_PREFIX . '_floating_ctc_setup',
        WHATSO_PREFIX . '_ac_widget',
        WHATSO_PREFIX . '_ac_setup1',
        WHATSO_PREFIX . '_ac_setup2',
        WHATSO_PREFIX . '_report_display',
        WHATSO_PREFIX . '_admin_settings',
            WHATSO_PREFIX . '_broadcast_message',
            WHATSO_PREFIX . '_message_notification_cf7',
        );
        
        $plugin_data = get_file_data(WHATSO_PLUGIN_BOOTSTRAP_FILE, array( 'version' ));
        $plugin_version = isset($plugin_data[0]) ? $plugin_data[0] : false;
        if (( 'admin.php' === $pagenow && isset($_GET['page']) && in_array(strtolower(sanitize_text_field($_GET['page'])), $settings_pages) )  
            || 'whatso_accounts' === get_post_type() 
        ) {
            
            wp_enqueue_media();
            
            wp_enqueue_style('jquery-minicolors', WHATSO_PLUGIN_URL . 'assets/css/jquery-minicolors.css', array(), $plugin_version);
            wp_enqueue_style('whatso-admin', WHATSO_PLUGIN_URL . 'assets/css/admin.css', array(), $plugin_version);
            wp_enqueue_script('jquery-minicolors', WHATSO_PLUGIN_URL . 'assets/js/vendor/jquery.minicolors.min.js', array( 'jquery' ), $plugin_version, true);
            wp_enqueue_script('whatso-admin', WHATSO_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), $plugin_version, true);

            wp_localize_script('whatso-admin', 'whatso_ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php') ));
            // Css Links
            wp_enqueue_style('font-awesome', WHATSO_PLUGIN_URL . 'assets/css/font-awesome.min.css', array(), $plugin_version);
            wp_enqueue_style('bootstrap-min', WHATSO_PLUGIN_URL . 'assets/css/bootstrap.min.css', array(), $plugin_version);
            //pending from here
             wp_enqueue_style('jquery-bs5-datatable', WHATSO_PLUGIN_URL . 'assets/css/datatables.min.css', "", false, false);
            wp_enqueue_style('jquery-ui-css', WHATSO_PLUGIN_URL . 'assets/css/jquery-ui.min.css', "", false, false);
            wp_enqueue_style('custom-css', WHATSO_PLUGIN_URL . 'assets/css/custom.css');
            // JS Links
            wp_enqueue_script('jquery-min-js', includes_url('js/jquery/jquery.min.js'), "", false, false);
            wp_enqueue_script('jquery-ui-min-js', WHATSO_PLUGIN_URL . 'assets/js/jquery-ui.min.js', "", false, false);
            wp_enqueue_script('bootstrap-bundle-min-js', WHATSO_PLUGIN_URL . 'assets/js/bootstrap.bundle.min.js', "", false, false);
            wp_enqueue_script('proper-js', WHATSO_PLUGIN_URL . 'assets/js/popper.min.js', "", false, false);
            wp_enqueue_script('bootstrap-min-js', WHATSO_PLUGIN_URL . 'assets/js/bootstrap.min.js', "", false, false);
            wp_enqueue_script('daterangepicker-min-js', WHATSO_PLUGIN_URL . 'assets/js/daterangepicker.min.js', array( 'jquery' ), $plugin_version, true);
            wp_enqueue_script('bootstrap-bundle-min-js', WHATSO_PLUGIN_URL . 'assets/js/moment.min.js', array( 'jquery' ), $plugin_version, true);
            wp_enqueue_script('datatable-bs-min-js', WHATSO_PLUGIN_URL . 'assets/js/datatables.min.js', "", false, false);
    
        }
        
    }
    
}

?>
