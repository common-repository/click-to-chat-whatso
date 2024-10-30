<?php

/**
 * Plugin Name: Whatso - Abandoned Cart Recovery- Order Success Notifications - Click to Chat
 * Plugin URI:  https://www.whatso.net/click-to-chat-whatsapp
 * Description: Whatso is an Abandoned Cart, Order Notification, and Click to Chat plugin for your e-commerce business.
 * Version:     6.0
 * Author:      Whatso
 * Author URI:  https://www.whatso.net
 * License:     GPLv2 or later
 * Text Domain: whatso
 */

/* Stop immediately if accessed directly. */
if (! defined('ABSPATH') ) {
    die();
}

/* All constants should be defined in this file. */
if (! defined('WHATSO_PREFIX') ) {
    define('WHATSO_PREFIX', 'whatso');
}
if (! defined('WHATSO_PLUGIN_DIR') ) {
    define('WHATSO_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (! defined('WHATSO_PLUGIN_BASENAME') ) {
    define('WHATSO_PLUGIN_BASENAME', plugin_basename(__FILE__));
}
if (! defined('WHATSO_PLUGIN_URL') ) {
    define('WHATSO_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (! defined('WHATSO_SETTINGS_NAME') ) {
    define('WHATSO_SETTINGS_NAME', 'whatso_settings');
}
if (! defined('WHATSO_PLUGIN_BOOTSTRAP_FILE') ) {
    define('WHATSO_PLUGIN_BOOTSTRAP_FILE', __FILE__);
}

/* Auto-load all the necessary classes. */
if(! function_exists('whatso_class_auto_loader') ) {
    
    function whatso_class_auto_loader( $class )
    {
        
        $includes = WHATSO_PLUGIN_DIR . 'includes/' . $class . '.php';   
        
        if(is_file($includes) && ! class_exists($class) ) {
            include_once $includes;
        }
    }
}
spl_autoload_register('whatso_class_auto_loader');

/* Initialize all modules now. */
new WHATSO_Display();
new WHATSO_Shortcode();
new WHATSO_Activation();
new WHATSO_Scripts_And_Styles();
new WHATSO_Menu_Link();
new WHATSO_Accounts();
new WHATSO_Controller();
new WHATSO_Ajax();
new WHATSO_WooCommerce();
