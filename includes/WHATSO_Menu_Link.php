<?php

class WHATSO_Menu_Link
{
    
    private static $menus = array();
    
    public function __construct()
    {
        if (is_admin() ) {
            add_action('admin_menu', array( $this, 'addMenuLink' ));
            add_filter('plugin_action_links_' . WHATSO_PLUGIN_BASENAME, array( $this, 'addPluginActionLinks' ));
            add_filter('plugin_row_meta', array( $this, 'pluginRowMeta' ), 10, 4);
            add_filter('admin_footer_text', array( $this, 'adminFooterText' ));
        }
    }
    
    public function addMenuLink()
    {
        
        $parent_slug = 'whatso_floating_menu_setup';
        
        $this->addMenu(
            esc_html__('Whatso', 'whatso'),
            '',
            $parent_slug,
            '',
            plugin_dir_url(__DIR__).'assets/images/whatso-new-logo.png'
        );
        

        $this->addMenu(
            esc_html__('Dashboard', 'whatso'),
            array( $this, 'getView' ),
            'whatso_floating_menu_setup',
            $parent_slug
        );

        $this->addMenu(
            esc_html__('Click to Chat', 'whatso'),
            array( $this, 'getView' ),
            'whatso_floating_ctc_setup',
            $parent_slug
        );
        
        $this->addMenu(
            esc_html__('Order Notification Setup', 'whatso'),
            array( $this, 'getView' ),
            'whatso_notifications_setup',
            $parent_slug
        );
        $this->addMenu(
            esc_html__('Abandoned Carts', 'whatso'),
            array( $this, 'getView' ),
            'whatso_abandoned_cart',
            $parent_slug
        );
        $this->addMenu(
            esc_html__('ContactForm 7 Notification', 'whatso'),
            array( $this, 'getView' ),
            'whatso_message_notification_cf7',
            $parent_slug
        );
        $this->addMenu(
            esc_html__('Broadcast Messages', 'whatso'),
            array( $this, 'getView' ),
            'whatso_broadcast_message',
            $parent_slug
        );

        $this->addMenu(
            esc_html__('Settings', 'whatso'),
            array( $this, 'getView' ),
            'whatso_admin_settings',
            $parent_slug
        );

        $this->addMenu(
            '',
            array( $this, 'getView' ),
            'whatso_floating_quick_setup',
            $parent_slug
        );
        $this->addMenu(
            '',
            '',
            'edit.php?post_type=whatso_accounts',
            $parent_slug
        );
        
        $this->addMenu(
            '',
            array( $this, 'getView' ),
            'whatso_floating_widget',
            $parent_slug
        );
        
        $this->addMenu(
            '',
            array( $this, 'getView' ),
            'whatso_settings',
            $parent_slug
        );

        $this->addMenu(
            '',
            array( $this, 'getView' ),
            'whatso_woocommerce_button',
            $parent_slug
        );
        $this->addMenu(
            '',
            array( $this, 'getView' ),
            'whatso_ac_setup1',
            $parent_slug
        );
        $this->addMenu(
            '',
            array( $this, 'getView' ),
            'whatso_ac_setup2',
            $parent_slug
        );
        $this->addMenu(
            '',
            array( $this, 'getView' ),
            'whatso_ac_dashboard',
            $parent_slug
        );
        $this->addMenu(
            '',
            array( $this, 'getView' ),
            'whatso_report_display',
            $parent_slug
        );
    
    }
    
    private function addMenu( $title, $callback, $slug, $parent_slug = '', $icon = '' )
    {
        
        if ('' === $parent_slug ) {
            add_menu_page(
                $title,
                $title,
                'manage_options',
                $slug,
                $callback,
                $icon
            );
        }
        else {
            add_submenu_page(
                $parent_slug,
                $title,
                $title,
                'manage_options',
                $slug,
                $callback,
                null
            );
            
            self::$menus[$title] = $slug;
        }
        
    }
    
    public function getView()
    {
        WHATSO_Utils::getView();
    }
    
    public static function getMenus()
    {
        return self::$menus;
    }
    
    /**
     * Add 'Settings' link to the plugin page. 
     * This link will only displayed if the plugin is active.
     */
    public function addPluginActionLinks( $links )
    {
        $settings_link = sprintf('<a href="admin.php?page=whatso_settings">%1$s</a>', esc_html__('Settings', 'whatso'));
        array_unshift($links, $settings_link);
        return $links;
    }
    
    public function pluginRowMeta( $links)
    {
        
        return $links;
    }
    
    /**
     * Ask for some stars at the bottom of admin page
     */
    public function adminFooterText( $default )
    {
        global $pagenow;
        
        $setting_pages = array(
        WHATSO_PREFIX . '_floating_quick_setup',
        WHATSO_PREFIX . '_settings',
        WHATSO_PREFIX . '_floating_widget',
        WHATSO_PREFIX . '_woocommerce_button',
        WHATSO_PREFIX . '_notifications_setup',
        WHATSO_PREFIX . '_abandoned_cart',
        WHATSO_PREFIX . '_broadcast_message',
        WHATSO_PREFIX . '_floating_menu_setup',
        WHATSO_PREFIX . '_floating_ctc_setup',
        WHATSO_PREFIX . '_ac_widget',
        WHATSO_PREFIX . '_ac_setup1',
        WHATSO_PREFIX . '_ac_setup2',
        WHATSO_PREFIX . '_admin_settings',
        WHATSO_PREFIX . '_message_notification_cf7',
        );
        
        
        $post_type = filter_input(INPUT_GET, 'post_type');
        if (! $post_type ) {
            $post_type = get_post_type(filter_input(INPUT_GET, 'post'));
        }
        
        if ('admin.php' === $pagenow && isset($_GET['page']) && in_array(sanitize_text_field($_GET['page']), $setting_pages) 
            || 'whatso_accounts' === $post_type 
        ) {
            
            $plugin_data = get_plugin_data(WHATSO_PLUGIN_BOOTSTRAP_FILE, false, true);
            echo esc_attr('Whatso Click to Chat ') . esc_attr('Version') . ' ' . esc_attr($plugin_data['Version']);
            echo ' ' . esc_attr('by') . ' <a href="https://www.whatso.net/click-to-chat-whatsapp" target="_blank">Whatso</a>';
            
        }
        else {
            echo esc_attr($default);
        }
    }
    
}
