<?php
class WHATSO_Activation
{

    /**
     * Initialize constructor
     */
    public function __construct()
    {
        if (is_admin()) {
            register_activation_hook(WHATSO_PLUGIN_BOOTSTRAP_FILE, array($this, 'activation'));
            register_deactivation_hook(WHATSO_PLUGIN_BOOTSTRAP_FILE, array($this, 'plugin_deactivation'));
        }
        add_action('plugins_loaded', array($this, 'loadTextDomain'));
    }

    /**
     * Function for activation
     */

    public function activation()
    {
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/whatso';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0700);
        }

        /* Add options to WordPress specific for WHATSO */
        if (!get_option('ac_counter')) {
            $update_notifications_arr = "false";
            $result = update_option('ac_counter',  $update_notifications_arr);
        }
        if (!get_option('whatso_user_settings')) {
            $data = get_option('whatso_user_settings');
            $update_user_settings = array(
            'isOrderNotificationToAdmin' => "true",
            'isCustomizeMessageToAdmin' => "true",
            'isOrderNotificationToCustomer' => "true",
            'isCustomizMessageToCustomer' => "true",
            'isCustomizMessageOfAbandoned' => "true",
            'multiple_messages' => '5',
            'isMessageFromAdminNumber' => "false",
            'official_number' => '918141001180',
            'isDisplayReport' => "true",
            );
            $result = update_option('whatso_user_settings', wp_json_encode($update_user_settings));
        }

        if (!get_option(WHATSO_SETTINGS_NAME)) {
            WHATSO_Utils::prepeareSettings();
            WHATSO_Utils::updateSetting('toggle_text', esc_html__('Chat with us', 'whatso'));
            WHATSO_Utils::updateSetting('toggle_text_color', 'rgba(255, 255, 255, 1)');
            WHATSO_Utils::updateSetting('toggle_background_color', '#34aa91');
            WHATSO_Utils::updateSetting('description', esc_html__('Hi, We are ready to help. Start a conversation by selecting a user below.', 'whatso'));
            WHATSO_Utils::updateSetting('mobile_close_button_text', esc_html__('Close and go back to page', 'whatso'));
            WHATSO_Utils::updateSetting('container_text_color', 'rgba(85, 85, 85, 1)');
            WHATSO_Utils::updateSetting('container_background_color', 'rgba(255, 255, 255, 1)');
            WHATSO_Utils::updateSetting('account_hover_background_color', 'rgba(245, 245, 245, 1)');
            WHATSO_Utils::updateSetting('account_hover_text_color', 'rgba(85, 85, 85, 1)');
            WHATSO_Utils::updateSetting('border_color_between_accounts', '#f5f5f5');
            WHATSO_Utils::updateSetting('box_position', 'left');

            WHATSO_Utils::updateSetting('consent_alert_background_color', 'rgba(255, 0, 0, 1)');

            WHATSO_Utils::updateSetting('button_label', 'We are happy to help! Chat with us now.');
            WHATSO_Utils::updateSetting('button_background_color', '#34aa91');
            WHATSO_Utils::updateSetting('button_text_color', '#ffffff');
            WHATSO_Utils::updateSetting('button_background_color_on_hover', '#34aa91');
            WHATSO_Utils::updateSetting('button_text_color_on_hover', '#ffffff');

            WHATSO_Utils::updateSetting('button_background_color_offline', '#a0a0a0');
            WHATSO_Utils::updateSetting('button_text_color_offline', '#ffffff');

            WHATSO_Utils::updateSetting('hide_on_large_screen', 'off');
            WHATSO_Utils::updateSetting('hide_on_small_screen', 'off');

            WHATSO_Utils::updateSetting('delay_time', '0');
            WHATSO_Utils::updateSetting('inactivity_time', '0');
            WHATSO_Utils::updateSetting('scroll_length', '0');

            WHATSO_Utils::updateSetting('total_accounts_shown', '0');
            WHATSO_Utils::generateCustomCSS();
        } else {
            WHATSO_Utils::generateCustomCSS();
        }

        /**
         * Create table on plugin activation
         */
        $this->plugin_activation();
    }
    /**
     * Function for load text domain
     */
    public function loadTextDomain()
    {
        load_plugin_textdomain('whatso', false, plugin_basename(WHATSO_PLUGIN_DIR) . '/languages');
    }

    /**
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     *
     * @static
     */
    public static function plugin_activation()
    {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $tblname = 'whatso_abandoned_cart';
        $tblname1='whatso_order_notification';
        $wp_chat_table = $table_prefix . "$tblname";
        $wp_order_table = $table_prefix . "$tblname1";
        $charset_collate = $wpdb->get_charset_collate();
        //Check to see if the table exists already, if not, then create it
        $db_result = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wp_chat_table));
        if (strtolower($db_result) !== strtolower($wp_chat_table)) {

            $tbl = "CREATE TABLE $wp_chat_table (
			`id`                  BIGINT(20) NOT NULL auto_increment,
			`customer_id`         VARCHAR(100) NULL DEFAULT NULL,
			`customer_email`      VARCHAR(100) NULL DEFAULT NULL,
			`customer_mobile_no`  VARCHAR(100) NULL DEFAULT NULL,
			`customer_first_name` VARCHAR(100) NULL DEFAULT NULL,
			`customer_last_name`  VARCHAR(100) NULL DEFAULT NULL,
			`customer_type`       VARCHAR(50) NULL DEFAULT NULL,
			`create_date_time`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`cart_json`           LONGTEXT NULL DEFAULT NULL,
			`cart_total_json`     LONGTEXT NULL DEFAULT NULL,
			`cart_total`          FLOAT NOT NULL,
			`cart_currency`       VARCHAR(50) NOT NULL,
			`abandoned_date_time` DATETIME NOT NULL default '0000-00-00 00:00:00',
			`message_sent`        INT NOT NULL DEFAULT '0',
			`status`              INT NOT NULL DEFAULT '0',
			`last_access_time`    DATETIME NOT NULL default '0000-00-00 00:00:00',
			`message_api_response` LONGTEXT NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
			)$charset_collate;";
            include_once ABSPATH . '/wp-admin/includes/upgrade.php';
            dbDelta($tbl);
        }
        //Check to see if the table exists already, if not, then create it
        $db_result1 = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wp_order_table));
        if (strtolower($db_result1) !== strtolower($wp_order_table)) {

            $tbl1 = "CREATE TABLE $wp_order_table (
			`id`              		BIGINT(20) NOT NULL auto_increment,
			`user_type`       		VARCHAR(50) NULL DEFAULT NULL,
			`create_date_time`  	DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`message_api_request`	LONGTEXT NULL DEFAULT NULL,
			`message_api_response`  LONGTEXT NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
			)$charset_collate;";
            include_once ABSPATH . '/wp-admin/includes/upgrade.php';
            dbDelta($tbl1);
        }

        
		
		$tblname3 = 'message_notification_details';
		$wp_message_cf7_table = $table_prefix . "$tblname3";
		$charset_collate = $wpdb->get_charset_collate();

		$db_result1 = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wp_order_table));
		if (strtolower($db_result1) !== strtolower($wp_message_cf7_table)) {

			$tbl3 = "CREATE TABLE $wp_message_cf7_table (
			`id`              		BIGINT(20) NOT NULL auto_increment,
			`user_type`       		VARCHAR(50) NULL DEFAULT NULL,
			`create_date_time`  	DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`message_api_request`	LONGTEXT NULL DEFAULT NULL,
			`message_api_response`  LONGTEXT NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
			)$charset_collate;";
			require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			dbDelta($tbl3);
		}
    }

    /**
     * Removes all connection options
     *
     * @static
     */
    public static function plugin_deactivation()
    {
        // For future use
        wp_clear_scheduled_hook('whatso_send_hook');
        wp_clear_scheduled_hook('whatso_clear_table_hook');
    }
}
