<?php


class WHATSO_WooCommerce
{

    public function __construct()
    {

        if (is_admin()) {
            add_action('add_meta_boxes', array($this, 'addMetaBoxes'));
            add_action('save_post', array($this, 'saveMetaBoxes'));
            add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
        } else {
            add_action('woocommerce_before_add_to_cart_form', array($this, 'showBeforeATC'));
            add_action('woocommerce_after_add_to_cart_form', array($this, 'showAfterATC'));

            if ('after_long_description' === WHATSO_Utils::getSetting('wc_button_position')) {
                add_filter('the_content', array($this, 'showAfterLongDescription'));
            }
            if ('after_short_description' === WHATSO_Utils::getSetting('wc_button_position')) {
                add_filter('woocommerce_short_description', array($this, 'showAfterShortDescription'), 10, 1);
            }
        }

        add_action('woocommerce_before_checkout_form', array($this, 'whatso_enqueue_checkout_script'));

        add_action('woocommerce_checkout_order_processed', array($this, 'order_processed'), 99, 4);

        add_action('woocommerce_checkout_order_processed', array($this, 'recover_order'), 999, 4);
        add_action('template_redirect', array($this, 'get_checkout_cart_link'));
       
        add_action('woocommerce_add_to_cart ', array($this, 'add_whatso_session_key'));
        add_action('woocommerce_cart_actions', array($this, 'add_whatso_session_key'));
        add_action('woocommerce_cart_item_removed', array($this, 'add_whatso_session_key'));
        
        add_action('init', array($this, 'whatso_schedulers'));
        add_action('admin_head', array($this, 'whatso_schedulers'));
        add_action('cron_schedules', array($this, 'whatso_cron_intervals'));
        add_action('whatso_send_hook', array($this, 'whatso_move_sessions_to_whatso_abandoned_table'));
        add_action('whatso_user_credentials', array($this, 'whatso_get_user_credentials'));
        add_action('whatso_user_plan', array($this, 'whatso_get_user_plan'));
        add_action('whatso_user_settings', array($this, 'whatso_update_user_settings'));
        add_action('whatso_test_message_setting', array($this, 'whatso_test_message'));
        add_action('whatso_get_login_plan', array($this, 'whatso_get_login'));
        add_action('whatso_save_contact_to', array($this, 'whatso_save_contact'));

        add_action('wp_ajax_nopriv_whatso_abandoned_save', array($this, 'whatso_abandoned_save'));
        add_action('wp_ajax_whatso_abandoned_save', array($this, 'whatso_abandoned_save'));
        
    }
    public function get_checkout_cart_link()
    {
        global $woocommerce;
        global $wpdb;
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
        {
            $url = "https://";   
        }
        else  
        {
            $url = "http://";   
            // Append the host(domain name, ip) to the URL.   
            $url.= $_SERVER['HTTP_HOST'];   
            // Append the requested resource location to the URL   
            $url.= $_SERVER['REQUEST_URI'];    
            $url_components = parse_url($url);
            parse_str($url_components['query'], $params);
            $id =$params['id'];
          
            $customernumber =  $params['num'];
        }
        if(is_checkout()){
            if ( WC()->cart->get_cart_contents_count() == 0 ) {
            
                $cart_table = $wpdb->prefix . 'whatso_abandoned_cart';
                $check_abandoned_entry_sql = $wpdb->prepare("SELECT cart_json FROM $cart_table WHERE customer_mobile_no LIKE '$customernumber' AND status IN (0,1)"); // db call ok; no-cache ok
                $abandoned_results = $wpdb->get_results($check_abandoned_entry_sql); // db call ok; no-cache ok
                $abandoned_results = json_decode(json_encode($abandoned_results), true);
                foreach($abandoned_results as $cart_data)
                {   
                $cart_data= unserialize($cart_data['cart_json']);
                $cart_data = json_decode(json_encode($cart_data), true);
                if(!empty($cart_data)){
                    foreach($cart_data as $arr2){
                        $product_id=$arr2['product_id'];
                        $quantity = $arr2['quantity'];
                        WC()->cart->add_to_cart( $product_id,$quantity );
                    }
                }else{
                
                    wp_redirect( home_url(), 301 );
                }              
                }
            
            }else{
                
                if (is_user_logged_in()) { //If user has signed in and the request is not triggered by checkout fields or Exit Intent
                //  $current_user = wp_get_current_user(); //Retrieving users data
                
                    $customer_id = WC()->session->get_customer_id();
                    $cart = $this->whatso_read_cart();
                    $get_user_data = $this->whatso_get_user_data();
                    
                    $cart_table = $wpdb->prefix . 'whatso_abandoned_cart';
                    $check_abandoned_entry_sql = $wpdb->prepare("SELECT id, customer_id,cart_json FROM $cart_table WHERE customer_mobile_no LIKE '$customernumber' AND status IN (0,1)"); // db call ok; no-cache ok
                    $abandoned_results = $wpdb->get_results($check_abandoned_entry_sql); // db call ok; no-cache ok
                    $abandoned_results = json_decode(json_encode($abandoned_results), true);
                
            
                    foreach($abandoned_results as $c_id)
                    {
                    $cid= $c_id['customer_id'];
                    }
            
                    if($id == $cid  ){
                        if ( WC()->cart->get_cart_contents_count() == 0 ) {
                        $cart_table = $wpdb->prefix . 'whatso_abandoned_cart';
                        $check_abandoned_entry_sql = $wpdb->prepare("SELECT cart_json FROM $cart_table WHERE customer_mobile_no LIKE '$customernumber' AND status IN (0,1)"); // db call ok; no-cache ok
                        $abandoned_results = $wpdb->get_results($check_abandoned_entry_sql); // db call ok; no-cache ok
                        $abandoned_results = json_decode(json_encode($abandoned_results), true);
                        foreach($abandoned_results as $cart_data)
                        {   
                            $cart_data= unserialize($cart_data['cart_json']);
                            $cart_data = json_decode(json_encode($cart_data), true);
                            foreach($cart_data as $arr2){
                                $product_id=$arr2['product_id'];
                                $quantity = $arr2['quantity'];
                                WC()->cart->add_to_cart( $product_id,$quantity );
                            }
                    }
                }   
                    }else{
                        wp_redirect( home_url(), 301 );
                    } 
                
                }
               
            }
        
        }
    }
  
    public function showBeforeATC()
    {

        if ('before_atc' !== WHATSO_Utils::getSetting('wc_button_position') || 'on' == get_post_meta(get_the_ID(), 'whatso_remove_button', true)) {
            return;
        }
        echo esc_html($this)->setContainer();
    }

    public function showAfterATC()
    {

        if ('after_atc' !== WHATSO_Utils::getSetting('wc_button_position') || 'on' == get_post_meta(get_the_ID(), 'whatso_remove_button', true)) {
            return;
        }
        echo esc_html($this)->setContainer();
    }

    public function showAfterLongDescription($content)
    {
        if ('product' !== sanitize_text_field(get_post_type())
            || !is_single()
            || 'on' === get_post_meta(get_the_ID(), 'whatso_remove_button', true)
        ) {
            return $content;
        }

        return $content . $this->setContainer();
    }

    public function showAfterShortDescription($post_excerpt)
    {

        if ('after_short_description' !== WHATSO_Utils::getSetting('wc_button_position')
            || 'on' === get_post_meta(get_the_ID(), 'whatso_remove_button', true)
            || !is_single()
        ) {
            return $post_excerpt;
        }
        return $post_excerpt . $this->setContainer();
    }

    private function setContainer()
    {

        $selected_accounts = json_decode(WHATSO_Utils::getSetting('selected_accounts_for_woocommerce', '[]'), true);
        $selected_accounts = is_array($selected_accounts) ? $selected_accounts : array();

        $custom_accounts = json_decode(get_post_meta(get_the_ID(), 'whatso_selected_accounts', true));
        $custom_accounts = is_array($custom_accounts) ? $custom_accounts : array();
        if (count($custom_accounts) > 0) {
            $selected_accounts = $custom_accounts;
        }


        $page_title = esc_html(get_the_title());
        $page_url = esc_url(get_permalink());

        return '<div class="whatso-wc-buttons-container" data-ids="' . implode(',', $selected_accounts) . '" data-page-title="' . $page_title . '" data-page-url="' . $page_url . '"></div>';
    }

    public function addMetaBoxes()
    {

        add_meta_box(
            'whatso_wc_button',
            esc_html__('WhatsApp Contact Button', 'whatso'),
            array($this, 'showMetaBox'),
            array('product')
        );
    }

    public function showMetaBox($post)
    {

        ?>
        <p class="description"><?php esc_html_e('You can set a custom WhatsApp button for this product. Leave the following fields blank if you wish to use the default values.', 'whatso'); ?></p>
        <table class="form-table">
            <caption>Remove Whatso Button</caption>
            <tbody>
                <tr>
                    <th scope="row"><?php esc_html_e('Remove Button', 'whatso'); ?></th>
                    <td>
                        <input type="checkbox" name="whatso_remove_button" id="whatso_remove_button" value="on" <?php echo esc_html('on') === strtolower(sanitize_text_field(get_post_meta($post->ID, 'whatso_remove_button', true))) ? 'checked' : ''; ?> /> <label for="whatso_remove_button"><?php esc_html_e('Remove WhatsApp button for this product', 'whatso'); ?></label>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="form-table" id="whatso-custom-wc-button-settings">
            <caption>Select Account to Display</caption>
            <tbody>
                <tr>
                    <th scope="col"><label for="whatso_account_number"><?php esc_html_e('Selected Accounts', 'whatso'); ?></label></th>
                    <td><?php WHATSO_Templates::displaySelectedAccounts('selected_accounts_for_product', sanitize_text_field(get_the_ID())); ?></td>
                </tr>
            </tbody>
        </table>

        <?php

        wp_nonce_field('whatso_wc_meta_box', 'whatso_wc_meta_box_nonce');
    }

    public function saveMetaBoxes($post_id)
    {

        /* Check if our nonce is set. */
        if (!isset($_POST['whatso_wc_meta_box_nonce'])) { return;
        }

        $nonce = sanitize_text_field(wp_unslash($_POST['whatso_wc_meta_box_nonce']));

        /* Verify that the nonce is valid. */
        if (!wp_verify_nonce($nonce, 'whatso_wc_meta_box')) {
            return;
        }
        $remove_button = sanitize_text_field(wp_unslash($_POST['whatso_remove_button']));
        $remove_button = isset($remove_button) ? 'on' : 'off';
        $ids = array();
        $the_posts = isset($_POST['whatso_selected_account']) ? array_values(sanitize_text_field(wp_unslash($_POST['whatso_selected_account']))) : array();
        foreach ($the_posts as $v) {
            $ids[] = (int) $v;
        }

        update_post_meta($post_id, 'whatso_selected_accounts', wp_json_encode($ids));
        update_post_meta($post_id, 'whatso_remove_button', $remove_button);
    }

    public function adminEnqueueScripts($hook)
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
            WHATSO_PREFIX . '_message_notification_cf7',
        );
        $plugin_data = get_file_data(WHATSO_PLUGIN_BOOTSTRAP_FILE, array( 'version' ));
        $plugin_version = isset($plugin_data[0]) ? $plugin_data[0] : false;
        wp_enqueue_style('whatso-admin', WHATSO_PLUGIN_URL . 'assets/css/admin.css');
        if ('post.php' != $hook || 'product' != sanitize_text_field(get_current_screen()->post_type)) {
            return;
        }
        wp_enqueue_script('whatso-public', WHATSO_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), false, true);

        if (('admin.php' === $pagenow && isset($_GET['page']) && in_array(strtolower(sanitize_text_field($_GET['page'])), $settings_pages)) 
            || 'whatso_accounts' === get_post_type()
        ) {
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

    /**
     * send message to customer and admin when order placed
     */
    public function order_processed($order_id, $posted_data, $order)
    {
        $execute_flag = true;
        global $wpdb;
        $order_table = $wpdb->prefix . 'whatso_order_notification';
        if (is_a($order, 'WC_Order_Refund')) {
            $execute_flag = false;
        }

        if ($order == false) {
            $execute_flag = false;
        }

        if ($execute_flag) {

            if (!empty(get_option('whatso_notifications'))) {
                $data = get_option('whatso_notifications');
                $data = json_decode($data);
                $whatso_username = $data->whatso_username;
                $whatso_password = $data->whatso_password;
                $whatso_mobileno = $data->whatso_mobileno;
                $whatso_message = $data->whatso_message;
                $whatso_customer_message = $data->whatso_customer_message;

                $customer_notification = $data->customer_notification;
                $data1 = get_option('whatso_abandoned');
                $data1 = json_decode($data1);
                $admin_mobile = $data1->admin_mobile;

                $store_name = get_bloginfo('name');
                $billing_email = $order->get_billing_email();
                $order_currency = $order->get_currency();
                $order_amount = $order->get_total();
                $order_date = $order->get_date_created();
                $order_customer = $order->get_billing_first_name();
                $items = $order->get_items();
                $products_array = array();

                foreach ($items as $item) {
                    $quantity = $item->get_quantity();
                    $product = $item->get_product();
                    $product_name = '';
                    if (!is_object($product)) {
                        $product_name = $item->get_name();
                    } else {

                        $product_name = $product->get_title();
                    }
                    array_push($products_array, $product_name);
                }

                $countryCode = $order->get_billing_country();
                if (empty($countryCode)) {
                    $countryCode = $order->get_shipping_country();
                }
                $city = $order->get_billing_city();
                if (empty($city)) {
                    $city = $order->get_shipping_city();
                }
                $stateCode = $order->get_billing_state();
                if (empty($stateCode)) {
                    $stateCode = $order->get_shipping_state();
                }

                $customernumber = $order->get_billing_phone();

                $exploded_names = implode(",", $products_array);

                $order_date_formatted = $order_date->date("d-M-Y H:i");

                $whatso_message = str_replace('{customername}', $order_customer, $whatso_message);
                $whatso_message = str_replace('{storename}', $store_name, $whatso_message);
                $whatso_message = str_replace('{orderdate}', $order_date_formatted, $whatso_message);
                $whatso_message = str_replace('{productname}', $exploded_names, $whatso_message);
                $whatso_message = str_replace('{amountwithcurrency}', $order_currency . ' ' . $order_amount, $whatso_message);
                $whatso_message = str_replace('{customeremail}', $billing_email, $whatso_message);
                $whatso_message = str_replace('{billingcity}', $city, $whatso_message);
                $whatso_message = str_replace('{billingstate}', $stateCode, $whatso_message);
                $whatso_message = str_replace('{billingcountry}', $countryCode, $whatso_message);
                $whatso_message = str_replace('{customernumber}', $customernumber, $whatso_message);
                $whatso_message = preg_replace("/\r\n/", "<br>", $whatso_message);


                $customernumber = preg_replace('/[^0-9]/', '', $customernumber);

                $data_decoded = array(
                "Username" => $whatso_username, "Password" => $whatso_password, "MessageText" => $whatso_message, "MobileNumbers" => $whatso_mobileno, "ScheduleDate" => '', "FromNumber" => $admin_mobile,
                "Channel" => '1'
                );

                $data = json_encode($data_decoded);

                $url = "https://api.whatso.net/api/v2/SendMessage";

                $response = wp_remote_post(
                    $url, array(
                    'method' => 'POST',
                    'headers' => array(
                    'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
                    ),
                    'body' => $data
                    )
                );
                if (is_array($response) && isset($response['body'])) {

                       $response_obj = json_decode($response['body']);
                    if (is_object($response_obj)) {
                        ///code to update whatso_order_noti
                        $insert_array = array(
                        'user_type' => 'admin',
                        'message_api_request' => $data,
                        'message_api_response' =>  wp_json_encode($response_obj) ,
                        );
        
                        $wpdb->insert($order_table, $insert_array);
                    }
                }
                $customernumber = preg_replace('/[^0-9]/', '', $customernumber);
                $country_code = $countryCode;

                if ($customernumber != "") {

                    if ($country_code ==  "UK") //united kingdom
                    {
                        $customernumber = $this->mobile_number_validation(10, 44, $customernumber);
                    } elseif ($country_code ==  "AT") //Australia
                    {
                        $customernumber = $this->mobile_number_validation(10, 61, $customernumber);
                    } elseif ($country_code ==  "US") //United Status
                    {
                        $customernumber = $this->mobile_number_validation(10, 1, $customernumber);
                    } elseif ($country_code ==  "RU") //Russia
                    {
                        $customernumber = $this->mobile_number_validation(10, 7, $customernumber);
                    } elseif ($country_code ==  "IT") //Italy
                    {
                               $customernumber = $this->mobile_number_validation(10, 39, $customernumber);
                    } elseif ($country_code ==  "IN") //India
                    {
                            $customernumber = $this->mobile_number_validation(10, 91, $customernumber);
                    } elseif ($country_code ==  "IR") //Iran
                    {
                        $customernumber = $this->mobile_number_validation(10, 98, $customernumber);
                    } elseif ($country_code ==  "CA") //Canada
                    {
                        $customernumber = $this->mobile_number_validation(10, 1, $customernumber);
                    } elseif ($country_code ==  "ZA") //South Africa
                    {
                        $customernumber = $this->mobile_number_validation(9, 27, $customernumber);
                    } elseif ($country_code ==  "BR") //Brazil
                    {
                        $customernumber = $this->mobile_number_validation(11, 55, $customernumber);
                    } elseif ($country_code ==  "CN") //China
                    {
                        $customernumber = $this->mobile_number_validation(11, 86, $customernumber);
                    } elseif ($country_code ==  "ID") //Indonesia
                    {
                        $customernumber = $this->mobile_number_validation(10, 62, $customernumber);
                    } elseif ($country_code ==  "PK") //Pakistan
                    {
                        $customernumber = $this->mobile_number_validation(10, 92, $customernumber);
                    } elseif ($country_code ==  "NG") //Nigeria
                    {
                        $customernumber = $this->mobile_number_validation(8, 234, $customernumber);
                    } elseif ($country_code ==  "BD") //Bangladesh
                    {
                        $customernumber = $this->mobile_number_validation(10, 880, $customernumber);
                    } elseif ($country_code ==  "MX") //Mexico
                    {
                        $customernumber = $this->mobile_number_validation(10, 52, $customernumber);
                    } elseif ($country_code ==  "JP") //japan
                    {
                        $customernumber = $this->mobile_number_validation(10, 81, $customernumber);
                    } elseif ($country_code ==  "ET") //Ethiopia
                    {
                        $customernumber = $this->mobile_number_validation(9, 251, $customernumber);
                    } elseif ($country_code ==  "PH") //Phillipines
                    {
                        $customernumber = $this->mobile_number_validation(10, 63, $customernumber);
                    } elseif ($country_code ==  "EG") //Egypt
                    {
                        $customernumber = $this->mobile_number_validation(10, 20, $customernumber);
                    } elseif ($country_code ==  "VN") //Vietnam
                    {
                        $customernumber = $this->mobile_number_validation(9, 84, $customernumber);
                    } elseif ($country_code ==  "DE") //Germany
                    {
                        $customernumber = $this->mobile_number_validation(10, 49, $customernumber);
                    } elseif ($country_code ==  "TR") //Turkey
                    {
                        $customernumber = $this->mobile_number_validation(11, 90, $customernumber);
                    } elseif ($country_code ==  "TH") //Thailand
                    {
                        $customernumber = $this->mobile_number_validation(9, 66, $customernumber);
                    } elseif ($country_code ==  "FR") //France
                    {
                        $customernumber = $this->mobile_number_validation(9, 33, $customernumber);
                    } elseif ($country_code ==  "TZ") //Tanzania
                    {
                        $customernumber = $this->mobile_number_validation(9, 255, $customernumber);
                    } elseif ($country_code ==  "ES") //Spain
                    {
                        $customernumber = $this->mobile_number_validation(9, 34, $customernumber);
                    } elseif ($country_code ==  "MM") //Myanmar
                    {
                        $customernumber = $this->mobile_number_validation(10, 95, $customernumber);
                    } elseif ($country_code ==  "KE") //kenya
                    {
                        $customernumber = $this->mobile_number_validation(10, 254, $customernumber);
                    }

                    elseif ($country_code ==  "UG") //Uganda
                    {
                        $customernumber = $this->mobile_number_validation(9, 256, $customernumber);
                    } elseif ($country_code ==  "AR") //Argentina
                    {
                        $customernumber = $this->mobile_number_validation(9, 54, $customernumber);
                    } elseif ($country_code ==  "DZ") //Algeria
                    {
                        $customernumber = $this->mobile_number_validation(9, 213, $customernumber);
                    } elseif ($country_code ==  "SD") //Sudan
                    {
                        $customernumber = $this->mobile_number_validation(9, 249, $customernumber);
                    }
                    elseif ($country_code ==  "AF") //Afghanistan
                    {
                        $customernumber = $this->mobile_number_validation(9, 93, $customernumber);
                    } elseif ($country_code ==  "PL") //Poland
                    {
                        $customernumber = $this->mobile_number_validation(9, 48, $customernumber);
                    } elseif ($country_code ==  "SA") //Saudi Arabia
                    {
                        $customernumber = $this->mobile_number_validation(9, 966, $customernumber);
                    } elseif ($country_code ==  "PE") //Peru
                    {
                        $customernumber = $this->mobile_number_validation(9, 51, $customernumber);
                    } elseif ($country_code ==  "MY") //Malaysia
                    {
                        $customernumber = $this->mobile_number_validation(7, 60, $customernumber);
                    } elseif ($country_code ==  "MZ") //Mozambique
                    {
                        $customernumber = $this->mobile_number_validation(12, 258, $customernumber);
                    } elseif ($country_code ==  "GH") //Ghana
                    {
                        $customernumber = $this->mobile_number_validation(9, 233, $customernumber);
                    } elseif ($country_code ==  "YE") //Yemen
                    {
                        $customernumber = $this->mobile_number_validation(9, 967, $customernumber);
                    } elseif ($country_code ==  "VE") //Venezuela
                    {
                        $customernumber = $this->mobile_number_validation(7, 58, $customernumber);
                    } else {
                        $customernumber = $this->mobile_number_validation_without_country($customernumber);
                    }
                }


                if ($customer_notification == '1') {
                    
                    $whatso_customer_message = str_replace('{customername}', $order_customer, $whatso_customer_message);
                    $whatso_customer_message = str_replace('{storename}', $store_name, $whatso_customer_message);
                    $whatso_customer_message = str_replace('{orderdate}', $order_date_formatted, $whatso_customer_message);
                    $whatso_customer_message = str_replace('{productname}', $exploded_names, $whatso_customer_message);
                    $whatso_customer_message = str_replace('{amountwithcurrency}', $order_currency . ' ' . $order_amount, $whatso_customer_message);
                    $whatso_customer_message = str_replace('{customeremail}', $billing_email, $whatso_customer_message);
                    $whatso_customer_message = str_replace('{billingcity}', $city, $whatso_customer_message);
                    $whatso_customer_message = str_replace('{billingstate}', $stateCode, $whatso_customer_message);
                    $whatso_customer_message = str_replace('{billingcountry}', $countryCode, $whatso_customer_message);
                    $whatso_customer_message = str_replace('{customernumber}', $customernumber, $whatso_customer_message);
                    $whatso_customer_message = preg_replace("/\r\n/", "<br>", $whatso_customer_message);



                    $data_decoded = array(
                    "Username" => $whatso_username, "Password" => $whatso_password, "MessageText" => $whatso_customer_message, "MobileNumbers" => $customernumber, "ScheduleDate" => '', "FromNumber" => $admin_mobile,
                    "Channel" => '1'
                    );
                    $data = json_encode($data_decoded);

                    $url = "https://api.whatso.net/api/v2/SendMessage";

                    $response = wp_remote_post(
                        $url, array(
                        'method' => 'POST',
                        'headers' => array(
                        'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
                        ),
                        'body' => $data
                        )
                    );
                    if (is_array($response) && isset($response['body'])) {
                              $response_obj = json_decode($response['body']);
                        if (is_object($response_obj)) {
                            ///code to update whatso_order_noti
                            $insert_array = array(
                            'user_type' => 'customer',
                            'message_api_request' => $data,
                            'message_api_response' =>  wp_json_encode($response_obj) ,
                                  );
            
                                  $wpdb->insert($order_table, $insert_array);
                        }
                    }
                }
            }
        }
    }
    /**
     * validate mobile numebr
     */
    public function mobile_number_validation_without_country($customernumber)
    {
        $data = get_option('whatso_abandoned');
        $data = json_decode($data);
        $default_county_code = $data->default_country;
        $customernumber = $default_county_code . $customernumber;
        return $customernumber;
    }
    /**
     * validate mobile number with countrycode
     */
    public function  mobile_number_validation($countrynumberlength, $countrycode, $customernumber)
    {
        
        if (strlen($customernumber) === $countrynumberlength) {

          return  $customernumber = $countrycode . $customernumber;

        } elseif (strlen($customernumber) === $countrynumberlength - 1) {

           return   $customernumber;
        } elseif (strlen($customernumber) == $countrynumberlength + 1) {
            $result = substr($customernumber, 0, 1);
            if (($result == "0") || ($result == $countrycode)) {
                $customernumber = substr($customernumber, 1, $countrynumberlength);
                $customernumber = $countrycode . $customernumber;
            } else {
               return  $customernumber ;
            }
        } elseif (strlen($customernumber) == $countrynumberlength + 2) {
            $result = substr($customernumber, 0, 2);
            if (strcmp($result, $countrycode)) {
               return  $customernumber ;
            }
        } elseif (strlen($customernumber) == $countrynumberlength + 3) {

            $result = substr($customernumber, 0, 3);

            if (strcmp($result, $countrycode)) {
               return  $customernumber ;
            }
        } elseif (strlen($customernumber) >= $countrynumberlength + 4) {

            $result = substr($customernumber, 0, 4);

            if (strcmp($result, $countrycode)) {
                return $customernumber;
            }
        }
        // return $customernumber;
        //Additional Validation
        $data = get_option('whatso_abandoned');
        $data = json_decode($data);
        $default_county_code = $data->default_country;
        // get the countrynumberlength(withcode) and check if the $customernumber.lenght is equal  to it or not
        $countrynumberlength1 = $countrynumberlength + strlen($countrycode);

        if (strlen($customernumber) === $countrynumberlength1) {
            // if true return $customernumber
            return $customernumber;
        }
        // if not true - get the default country code saved by admin and append it to the $customernumber in another temp variable
        else {
            $tempnumber = $default_county_code . $customernumber;
            // now check if tempvariable lenght is equal to countrynumberlength - if yes, return tempvariable number else return $customernumber
            if (strlen($tempnumber) == $countrynumberlength1) {
                // if true return $customernumber
                return $tempnumber;
            }
        }
    }

    /**
     * function to send test message
     */
    public static function whatso_test_message()
    {
        if (!empty(get_option('whatso_notifications'))) {
            $data = get_option('whatso_notifications');
            $data = json_decode($data);
            $whatso_username = $data->whatso_username;
            $whatso_password = $data->whatso_password;
        }    
        $data1 = get_option('whatso_abandoned');
        $data1 = json_decode($data1);
        $admin_mobile = $data1->admin_mobile;
                $whatso_message="";
                $customernumber="";


        $data2 = get_option('whatso_test_message');
        $data2 = json_decode($data2);
        $abandoned_num = $data2->abandoned_num;
        $abandoned_message = $data2->abandoned_message;
        $message_type = $data2->message_type;
        $order_admin_num = $data2->order_admin_num;
        $order_customer_num = $data2->order_customer_num;
        $order_admin_message = $data2->order_admin_message;
        $order_customer_message = $data2->order_customer_message;

        $store_name = get_bloginfo('name');
        $base_url = site_url($path='', $scheme=null);
        $current_time = current_time('mysql');
        $date = date_create($current_time);
        $create_date_time= date_format($date, 'd-M-Y H:i');

        $order_admin_num = preg_replace('/[^0-9]/', '', $order_admin_num);
        $abandoned_num = preg_replace('/[^0-9]/', '', $abandoned_num);
        $order_customer_num = preg_replace('/[^0-9]/', '', $order_customer_num);
        $abandoned_message = str_replace('{storename}', $store_name, $abandoned_message);
        $abandoned_message = str_replace('{siteurl}', $base_url, $abandoned_message);
        $abandoned_message = preg_replace("/\r\n/", "<br>", $abandoned_message);
        $order_admin_message = str_replace('{storename}', $store_name, $order_admin_message);
        $order_admin_message = str_replace('{orderdate}', $create_date_time, $order_admin_message);
        $order_admin_message = preg_replace("/\r\n/", "<br>", $order_admin_message);
        $order_customer_message = str_replace('{storename}', $store_name, $order_customer_message);
        $order_customer_message = str_replace('{orderdate}', $create_date_time, $order_customer_message);
        $order_customer_message = preg_replace("/\r\n/", "<br>", $order_customer_message);

        if ($message_type == '1') {

            
            //test message of abandoned cart
            //call api to send test message
            $data_decoded = array(
            "Username" => $whatso_username, "Password" => $whatso_password, "MessageText" => $abandoned_message, "MobileNumbers" => $abandoned_num, "ScheduleDate" => '', "FromNumber" => $admin_mobile,
            "Channel" => '1'
            );

            $data = json_encode($data_decoded);

            $url = "https://api.whatso.net/api/v2/SendMessage";

            $response = wp_remote_post(
                $url, array(
                'method' => 'POST',
                'headers' => array(
                'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
                ),
                'body' => $data
                )
            );

            if (is_array($response) && isset($response['body'])) {

                $response_obj = json_decode($response['body']);
                if (is_object($response_obj)) {
                    return $response_obj->ResponseMessage;
                } else {
                    return false;
                }
            }
        }
        if ($message_type == '0') {
            //test message of order notification
            //admin message
            //call api to send test message


            $data_decoded = array(
            "Username" => $whatso_username, "Password" => $whatso_password, "MessageText" => $order_admin_message, "MobileNumbers" => $order_admin_num, "ScheduleDate" => '', "FromNumber" => $admin_mobile,
            "Channel" => '1'
            );

            $data = json_encode($data_decoded);

            $url = "https://api.whatso.net/api/v2/SendMessage";

            $response = wp_remote_post(
                $url, array(
                'method' => 'POST',
                'headers' => array(
                'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
                ),
                'body' => $data
                )
            );
            

            //customer message
            //call api to send test message
            $data_decoded = array(
            "Username" => $whatso_username, "Password" => $whatso_password, "MessageText" => $order_customer_message, "MobileNumbers" => $order_customer_num, "ScheduleDate" => '', "FromNumber" => $admin_mobile,
            "Channel" => '1'
            );

            $data = json_encode($data_decoded);

            $url = "https://api.whatso.net/api/v2/SendMessage";

            $response = wp_remote_post(
                $url, array(
                'method' => 'POST',
                'headers' => array(
                'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
                ),
                'body' => $data
                )
            );
            if (is_array($response) && isset($response['body'])) {

                $response_obj = json_decode($response['body']);
                if (is_object($response_obj) && isset($response_obj->ResponseMessage) && $response_obj->ResponseMessage == 'Success') {
                    return $response_obj->ResponseMessage;
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * Function to get email and login
     */
    public static function whatso_get_login()
    {
        if (!empty(get_option('whatso_notifications'))) {
            $data = get_option('whatso_notifications');
            $data = json_decode($data);
            $whatso_emailid = $data->whatso_email;
        }

        $data_decoded = array("EmailId" => $whatso_emailid, "Password" => "","Host"=>"","IsJwtToken" => 1);

        $data = json_encode($data_decoded);

        $url = "https://webapi.whatso.net/api/login/login";

        $response = wp_remote_post(
            $url, array(
                'method' => 'POST',
                'headers' => array(
                    'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
                ),
                'body' => $data
            )
        );

        if (is_array($response) && isset($response['body'])) {

            $response_obj = json_decode($response['body']);
            if (is_object($response_obj) && isset($response_obj->message) && $response_obj->message == 'Login successfully') {
                if (!get_option('whatso_email_login') || get_option('whatso_email_login') ) {
                    $result = update_option('whatso_email_login', wp_json_encode($response_obj));
                }

            }
        }
        do_action('whatso_save_contact_to');
    }
    /**
     * Function to save contacts on whatso site
     */
    public static function whatso_save_contact()
    {
        if (!empty(get_option('whatso_email_login'))) {
            $data = get_option('whatso_email_login');
            $data = json_decode($data);
        }
        $data1= $data->data;
        $data1=json_decode(json_encode($data1), true);


        $jwtToken = $data1['jwtToken'];
        $accountdata= $data1['accountsData'];
        $accountid=$accountdata['id'];
        global $wpdb;
        $customer_id = $wpdb->get_results($wpdb->prepare("SELECT o.customer_id,MAX(o.order_id) As order_id,MAX(o.date_created) AS last_order_date
    FROM " . $wpdb->prefix . "posts AS p 
    INNER JOIN " . $wpdb->prefix . "wc_order_stats  AS o ON p.ID = o.order_id
    INNER JOIN " . $wpdb->prefix . "postmeta  AS pm ON o.order_id = pm.post_id
    WHERE p.post_status IN ('wc-processing', 'wc-completed') 
    GROUP BY o.customer_id
    ORDER BY o.order_id DESC
    ")); // db call ok; no-cache ok

        $customer_id = json_decode(json_encode($customer_id), true);
        $fromdatepicker = date("Y-m-d", time() - (86400 * 1825));
        $todatepicker = date("Y-m-d", time() + (86400 * 1));


//Query to get customer email
        $customer_lookup = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT email FROM " . $wpdb->prefix . "wc_customer_lookup
    WHERE date_last_active BETWEEN  %s AND %s",$fromdatepicker,$todatepicker));// db call ok; no-cache ok


        foreach ($customer_lookup as $clook) {
            $mail = $clook->email;
            //Query to get customer details
            $customer_detail = $wpdb->get_results($wpdb->prepare("select t.first_name,t.country, t.date_last_active,t.email
        from " . $wpdb->prefix . "wc_customer_lookup t
        inner join (
            select first_name, max(date_last_active) as MaxDate
            from " . $wpdb->prefix . "wc_customer_lookup
            where date_last_active BETWEEN  %s AND %s
            group by email
        )tm on t.date_last_active = tm.MaxDate",$fromdatepicker,$todatepicker));// db call ok; no-cache ok

        }


        $customer_detail = json_decode(json_encode($customer_detail), true);

        foreach ($customer_detail as $c_detail) {
            $c_email = $c_detail['email'];
            $order_id = $wpdb->get_results($wpdb->prepare("SELECT max(o.order_id) AS order_id
                    FROM " . $wpdb->prefix . "wc_order_stats AS o 
                    INNER JOIN " . $wpdb->prefix . "wc_customer_lookup AS pm ON o.customer_id = pm.customer_id
                    WHERE pm.email = %s
                    AND  o.status IN ('wc-processing', 'wc-completed') 
                    GROUP BY pm.email
                    ", $c_email));// db call ok; no-cache ok
            $order_id = json_decode(json_encode($order_id), true);

            foreach ($order_id as $c_id) {
                $order_id = $c_id['order_id'];

                $customer_phone =  $wpdb->get_results($wpdb->prepare(
                    "SELECT MAX(meta_value) AS Contact_no
                            FROM " . $wpdb->prefix . "postmeta
                            WHERE post_id= $order_id
                            AND meta_key= '_billing_phone'
                            OR meta_key='shipping_phone'
                            "
                ));// db call ok; no-cache ok
                $customer_phone = json_decode(json_encode($customer_phone), true);
            }
            $customer_name = $c_detail['first_name'];
            $country_code = $c_detail['country'];
            $c_date = $c_detail['date_last_active'];
            $date = date_create($c_date);
            $create_date_time = date_format($date, 'd-M-Y H:i');

            foreach ($customer_phone as $c_phone) {
                $customernumber=$c_phone['Contact_no'];
            }
            $contact = new WHATSO_WooCommerce;
            if ($customernumber != "") {

                if ($country_code ==  "UK") //united kingdom
                {
                    $customernumber = $contact::mobile_number_validation(10, 44, $customernumber);
                } elseif ($country_code ==  "AT") //Australia
                {
                    $customernumber = $contact::mobile_number_validation(10, 61, $customernumber);
                } elseif ($country_code ==  "US") //United Status
                {
                    $customernumber = $contact::mobile_number_validation(10, 1, $customernumber);
                } elseif ($country_code ==  "RU") //Russia
                {
                    $customernumber = $contact::mobile_number_validation(10, 7, $customernumber);
                } elseif ($country_code ==  "IT") //Italy
                {
                    $customernumber = $contact::mobile_number_validation(10, 39, $customernumber);
                } elseif ($country_code ==  "IN") //India
                {
                    $customernumber = $contact::mobile_number_validation(10, 91, $customernumber);
                } elseif ($country_code ==  "IR") //Iran
                {
                    $customernumber = $contact::mobile_number_validation(10, 98, $customernumber);
                } elseif ($country_code ==  "CA") //Canada
                {
                    $customernumber = $contact::mobile_number_validation(10, 1, $customernumber);
                } elseif ($country_code ==  "ZA") //South Africa
                {
                    $customernumber = $contact::mobile_number_validation(9, 27, $customernumber);
                } elseif ($country_code ==  "BR") //Brazil
                {
                    $customernumber = $contact::mobile_number_validation(11, 55, $customernumber);
                } elseif ($country_code ==  "CN") //China
                {
                    $customernumber = $contact::mobile_number_validation(11, 86, $customernumber);
                } elseif ($country_code ==  "ID") //Indonesia
                {
                    $customernumber = $contact::mobile_number_validation(10, 62, $customernumber);
                } elseif ($country_code ==  "PK") //Pakistan
                {
                    $customernumber = $contact::mobile_number_validation(10, 92, $customernumber);
                } elseif ($country_code ==  "NG") //Nigeria
                {
                    $customernumber = $contact::mobile_number_validation(8, 234, $customernumber);
                } elseif ($country_code ==  "BD") //Bangladesh
                {
                    $customernumber = $contact::mobile_number_validation(10, 880, $customernumber);
                } elseif ($country_code ==  "MX") //Mexico
                {
                    $customernumber = $contact::mobile_number_validation(10, 52, $customernumber);
                } elseif ($country_code ==  "JP") //japan
                {
                    $customernumber = $contact::mobile_number_validation(10, 81, $customernumber);
                } elseif ($country_code ==  "ET") //Ethiopia
                {
                    $customernumber = $contact::mobile_number_validation(9, 251, $customernumber);
                } elseif ($country_code ==  "PH") //Phillipines
                {
                    $customernumber = $contact::mobile_number_validation(10, 63, $customernumber);
                } elseif ($country_code ==  "EG") //Egypt
                {
                    $customernumber = $contact::mobile_number_validation(10, 20, $customernumber);
                } elseif ($country_code ==  "VN") //Vietnam
                {
                    $customernumber = $contact::mobile_number_validation(9, 84, $customernumber);
                } elseif ($country_code ==  "DE") //Germany
                {
                    $customernumber = $contact::mobile_number_validation(10, 49, $customernumber);
                } elseif ($country_code ==  "TR") //Turkey
                {
                    $customernumber = $contact::mobile_number_validation(11, 90, $customernumber);
                } elseif ($country_code ==  "TH") //Thailand
                {
                    $customernumber = $contact::mobile_number_validation(9, 66, $customernumber);
                } elseif ($country_code ==  "FR") //France
                {
                    $customernumber = $contact::mobile_number_validation(9, 33, $customernumber);
                } elseif ($country_code ==  "TZ") //Tanzania
                {
                    $customernumber = $contact::mobile_number_validation(9, 255, $customernumber);
                } elseif ($country_code ==  "ES") //Spain
                {
                    $customernumber = $contact::mobile_number_validation(9, 34, $customernumber);
                } elseif ($country_code ==  "MM") //Myanmar
                {
                    $customernumber = $contact::mobile_number_validation(10, 95, $customernumber);
                } elseif ($country_code ==  "KE") //kenya
                {
                    $customernumber = $contact::mobile_number_validation(10, 254, $customernumber);
                }

                elseif ($country_code ==  "UG") //Uganda
                {
                    $customernumber = $contact::mobile_number_validation(9, 256, $customernumber);
                } elseif ($country_code ==  "AR") //Argentina
                {
                    $customernumber =$contact::mobile_number_validation(9, 54, $customernumber);
                } elseif ($country_code ==  "DZ") //Algeria
                {
                    $customernumber = $contact::mobile_number_validation(9, 213, $customernumber);
                } elseif ($country_code ==  "SD") //Sudan
                {
                    $customernumber = $contact::mobile_number_validation(9, 249, $customernumber);
                }
                elseif ($country_code ==  "AF") //Afghanistan
                {
                    $customernumber = $contact::mobile_number_validation(9, 93, $customernumber);
                } elseif ($country_code ==  "PL") //Poland
                {
                    $customernumber = $contact::mobile_number_validation(9, 48, $customernumber);
                } elseif ($country_code ==  "SA") //Saudi Arabia
                {
                    $customernumber = $contact::mobile_number_validation(9, 966, $customernumber);
                } elseif ($country_code ==  "PE") //Peru
                {
                    $customernumber = $contact::mobile_number_validation(9, 51, $customernumber);
                } elseif ($country_code ==  "MY") //Malaysia
                {
                    $customernumber = $contact::mobile_number_validation(7, 60, $customernumber);
                } elseif ($country_code ==  "MZ") //Mozambique
                {
                    $customernumber = $contact::mobile_number_validation(12, 258, $customernumber);
                } elseif ($country_code ==  "GH") //Ghana
                {
                    $customernumber = $contact::mobile_number_validation(9, 233, $customernumber);
                } elseif ($country_code ==  "YE") //Yemen
                {
                    $customernumber = $contact::mobile_number_validation(9, 967, $customernumber);
                } elseif ($country_code ==  "VE") //Venezuela
                {
                    $customernumber = $contact::mobile_number_validation(7, 58, $customernumber);
                }
            }


            $contactdetails[] = array("firstName"=>$customer_name,"mobileNumber"=>$customernumber,"column1"=>$create_date_time);
            }
        $data_decoded = array("AccountId" => $accountid, "CountryCode" => "","contactDetails"=>$contactdetails);
        $data = json_encode($data_decoded,JSON_PRETTY_PRINT);
        $data=stripslashes($data);

        $url = "https://webapi.whatso.net/api/contact/save-contacts";

        $response = wp_remote_post(
            $url, array(
                'method' => 'POST',
                'headers' => array(
                    'Content-Type' => 'application/json; charset=utf-8','Authorization'=> 'Bearer ' . $jwtToken,'WPRequest' => 'abach34h4h2h11h3h' ,
                ),
                'body' => $data
            )
        );
        if (is_array($response) && isset($response['body'])) {

            $response_obj = json_decode($response['body']);
            if (is_object($response_obj) && isset($response_obj->message) && $response_obj->message == 'Success') {
                if (!get_option('whatso_save_contact') || get_option('whatso_save_contact') ) {
                    $result = update_option('whatso_save_contact', wp_json_encode($response_obj));
                }
                return $response_obj->message;
            } else {
                if (!get_option('whatso_save_contact') || get_option('whatso_save_contact') ) {
                    $result = update_option('whatso_save_contact', wp_json_encode($response_obj));
                }
                return false;
            }
        }

    }
    /**
     * to get user credentials from email
     *  * @Parameters: $whatso_emailid is email of user to send credentials email
     */


    public static function whatso_get_user_credentials($whatso_emailid)
    {
        $data_decoded = array("emailId" => $whatso_emailid, "forWhichFunctionality" => "api");

        $data = json_encode($data_decoded);

        $url = "https://webapi.whatso.net/api/UnAuthorized/get-api-credentials";

        $response = wp_remote_post(
            $url, array(
            'method' => 'POST',
            'headers' => array(
            'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
            ),
            'body' => $data
            )
        );
        if (is_array($response) && isset($response['body'])) {

            $response_obj = json_decode($response['body']);
            if (is_object($response_obj) && isset($response_obj->message) && $response_obj->message == 'Success') {
                return $response_obj->message;
            } else {
                return false;
            }
        }
    }
    /**
     * get user plan from credentials
     */
    public static function whatso_get_user_plan()
    {
        if (!empty(get_option('whatso_notifications'))) {
            $data = get_option('whatso_notifications');
            $data = json_decode($data);
            $whatso_username = $data->whatso_username;
            $whatso_password = $data->whatso_password;
            $whatso_emailid = $data->whatso_email;
        }
        $data_decoded = array("emailId" => $whatso_emailid, "password" => $whatso_password, "username" => $whatso_username);
        $data = json_encode($data_decoded);

        $url = "https://webapi.whatso.net/api/UnAuthorized/get-plan";

        $response = wp_remote_post(
            $url, array(
            'method' => 'POST',
            'headers' => array(
            'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
            ),
            'body' => $data
            )
        );
        
        if (is_array($response) && isset($response['body'])) {

            $response_obj = json_decode($response['body']);
            
            if (is_object($response_obj) && isset($response_obj->message) && $response_obj->message == 'Success') {
                if (!get_option('whatso_user_plan') || get_option('whatso_user_plan') ) {
                    $result = update_option('whatso_user_plan', wp_json_encode($response_obj));
                }
            }
            
        }
        do_action('whatso_user_settings');
 }
    /**
     * Function to add js file to checkout form
     */

    public function whatso_enqueue_checkout_script()
    {
        $plugin_data = get_file_data(WHATSO_PLUGIN_BOOTSTRAP_FILE, array('version'));
        $plugin_version = isset($plugin_data[0]) ? $plugin_data[0] : false;
        wp_enqueue_script('whatso-public-abandoned-js', WHATSO_PLUGIN_URL . 'assets/js/public-cart.js', array('jquery'), $plugin_version, true);
        wp_localize_script('whatso-public-abandoned-js', 'whatso_public_data', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('whatso-ajax-nonce')));
    }
    /**
     * Function to get user data
     */

    public function whatso_get_user_data()
    {
        $user_data = array();

        if (is_user_logged_in()) { //If user has signed in and the request is not triggered by checkout fields or Exit Intent
            $current_user = wp_get_current_user(); //Retrieving users data
            //Looking if a user has previously made an order. If not, using default WordPress assigned data
            (isset($current_user->billing_first_name)) ? $name = $current_user->billing_first_name : $name = $current_user->user_firstname; //If/Else shorthand (condition) ? True : False
            (isset($current_user->billing_last_name)) ? $surname = $current_user->billing_last_name : $surname = $current_user->user_lastname;
            (isset($current_user->billing_email)) ? $email = $current_user->billing_email : $email = $current_user->user_email;
            (isset($current_user->billing_phone)) ? $phone = $current_user->billing_phone : $phone = '';
            (isset($current_user->billing_country)) ? $country = $current_user->billing_country : $country = '';
            (isset($current_user->billing_city)) ? $city = $current_user->billing_city : $city = '';
            (isset($current_user->billing_postcode)) ? $postcode = $current_user->billing_postcode : $postcode = '';

            if ($country == '') { //Trying to Geolocate user's country in case it was not found
                $country = WC_Geolocation::geolocate_ip(); //Getting users country from his IP address
                $country = $country['country'];
            }

            $location = array(
            'country'     => $country,
            'city'         => $city,
            'postcode'     => $postcode
            );

            $user_data = array(
            'first_name'    => $name,
            'last_name'        => $surname,
            'email'            => $email,
            'phone'            => $phone,
            'location'        => $location,
            'other_fields'    => ''
            );
        }

        return $user_data;
    }
    /**
     * Function to get user plan and update settings
     */
    public  function whatso_update_user_settings()
    {

        if (!empty(get_option('whatso_user_plan'))) {
            $data = get_option('whatso_user_plan');
            $data = json_decode($data);
            $response_code = $data->responseStatusCode;
            $message = $data->message;
            $loginLink = $data->loginLink;
            $isWhatsoDesktopSoftwarePurchased = $data->isWhatsoDesktopSoftwarePurchased;
            $whatsoDesktopSoftwarePurchasedPlan = $data->whatsoDesktopSoftwarePurchasedPlan;
            $isAbandonedCartPurchased = $data->isAbandonedCartPurchased;
            $abandonedCartPurchasedPlan = $data->abandonedCartPurchasedPlan;
            $isAPIPurchased = $data->isAPIPurchased;
            $apiPurchasedPlan = $data->apiPurchasedPlan;
            $isSMSPurchased = $data->isSMSPurchased;
            $smsPurchasedPlan = $data->smsPurchasedPlan;
        }

        if (!empty(get_option('whatso_user_settings'))) {
            $data1 = get_option('whatso_user_settings');
            $data1 = json_decode($data1);
            $isOrderNotificationToAdmin = $data1->isOrderNotificationToAdmin;
            $isCustomizeMessageToAdmin = $data1->isCustomizeMessageToAdmin;
            $isOrderNotificationToCustomer = $data1->isOrderNotificationToCustomer;
            $isCustomizMessageOfAbandoned = $data1->isCustomizMessageOfAbandoned;
            $multiple_messages = $data1->multiple_messages;
            $isMessageFromAdminNumber = $data1->isMessageFromAdminNumber;
            $official_number = $data1->official_number;
            $isDisplayReport = $data1->isDisplayReport;
            $loginLink1 = $data1->loginLink;
        }

        if ($isAbandonedCartPurchased == "false") {

            if (get_option('whatso_user_settings')) {
                $data = get_option('whatso_user_settings');
                $update_user_settings = array(
                'isOrderNotificationToAdmin' => "true",
                'isCustomizeMessageToAdmin' => "false",
                'isOrderNotificationToCustomer' => "true",
                'isCustomizMessageToCustomer' => "false",
                'isCustomizMessageOfAbandoned' => "false",
                'multiple_messages' => '5',
                'isMessageFromAdminNumber' => "false",
                'official_number' => '918141001180',
                'isDisplayReport' => "false",
                'loginlink' => $loginLink,
                );
                $result = update_option('whatso_user_settings', wp_json_encode($update_user_settings));
            } else {

                if (!get_option('whatso_user_settings')) {
                    $data = get_option('whatso_user_settings');
                    $update_user_settings = array(
                    'isOrderNotificationToAdmin' => "true",
                    'isCustomizeMessageToAdmin' => "false",
                    'isOrderNotificationToCustomer' => "true",
                    'isCustomizMessageToCustomer' => "false",
                    'isCustomizMessageOfAbandoned' => "false",
                    'multiple_messages' => '5',
                    'isMessageFromAdminNumber' => "false",
                    'official_number' => '918141001180',
                    'isDisplayReport' => "false",
                    'loginlink' => $loginLink,
                    );
                    $result = update_option('whatso_user_settings', wp_json_encode($update_user_settings));
                }
            }
        } elseif ($isAbandonedCartPurchased == "true" && $abandonedCartPurchasedPlan == "BasicPlan") {

            if (get_option('whatso_user_settings')) {
                $data = get_option('whatso_user_settings');
                $update_user_settings = array(
                'isOrderNotificationToAdmin' => "true",
                'isCustomizeMessageToAdmin' => "true",
                'isOrderNotificationToCustomer' => "true",
                'isCustomizMessageToCustomer' => "true",
                'isCustomizMessageOfAbandoned' => "true",
                'multiple_messages' => '5',
                'isMessageFromAdminNumber' => "true",
                'official_number' => '918141001180',
                'isDisplayReport' => "false",
                'loginlink' => $loginLink,
                );
                $result = update_option('whatso_user_settings', wp_json_encode($update_user_settings));
            } else {
                $data = get_option('whatso_user_settings');
                $update_user_settings = array(
                'isOrderNotificationToAdmin' => "true",
                'isCustomizeMessageToAdmin' => "true",
                'isOrderNotificationToCustomer' => "true",
                'isCustomizMessageToCustomer' => "true",
                'isCustomizMessageOfAbandoned' => "true",
                'multiple_messages' => '5',
                'isMessageFromAdminNumber' => "true",
                'official_number' => '918141001180',
                'isDisplayReport' => "false",
                'loginlink' => $loginLink,
                );
                $result = update_option('whatso_user_settings', wp_json_encode($update_user_settings));
            }
        } elseif ($isAbandonedCartPurchased == "true" && $abandonedCartPurchasedPlan == "ProPlan") {

            if (get_option('whatso_user_settings')) {
                $data = get_option('whatso_user_settings');
                $update_user_settings = array(
                'isOrderNotificationToAdmin' => "true",
                'isCustomizeMessageToAdmin' => "true",
                'isOrderNotificationToCustomer' => "true",
                'isCustomizMessageToCustomer' => "true",
                'isCustomizMessageOfAbandoned' => "true",
                'multiple_messages' => '5',
                'isMessageFromAdminNumber' => "true",
                'official_number' => '918141001180',
                'isDisplayReport' => "false",
                'loginlink' => $loginLink,
                );
                $result = update_option('whatso_user_settings', wp_json_encode($update_user_settings));
            } else {
                $data = get_option('whatso_user_settings');
                $update_user_settings = array(
                'isOrderNotificationToAdmin' => "true",
                'isCustomizeMessageToAdmin' => "true",
                'isOrderNotificationToCustomer' => "true",
                'isCustomizMessageToCustomer' => "true",
                'isCustomizMessageOfAbandoned' => "true",
                'multiple_messages' => '5',
                'isMessageFromAdminNumber' => "true",
                'official_number' => '918141001180',
                'isDisplayReport' => "false",
                'loginlink' => $loginLink,
                );
                $result = update_option('whatso_user_settings', wp_json_encode($update_user_settings));
            }

        } elseif ($isAbandonedCartPurchased == "true" && $abandonedCartPurchasedPlan == "UltimatePlan") {
            if (get_option('whatso_user_settings')) {
                $data = get_option('whatso_user_settings');
                $update_user_settings = array(
                'isOrderNotificationToAdmin' => "true",
                'isCustomizeMessageToAdmin' => "true",
                'isOrderNotificationToCustomer' => "true",
                'isCustomizMessageToCustomer' => "true",
                'isCustomizMessageOfAbandoned' => "true",
                'multiple_messages' => '5',
                'isMessageFromAdminNumber' => "true",
                'official_number' => '918141001180',
                'isDisplayReport' => "true",
                'loginlink' => $loginLink,
                );
                $result = update_option('whatso_user_settings', wp_json_encode($update_user_settings));
            } else {
                $data = get_option('whatso_user_settings');
                $update_user_settings = array(
                'isOrderNotificationToAdmin' => "true",
                'isCustomizeMessageToAdmin' => "true",
                'isOrderNotificationToCustomer' => "true",
                'isCustomizMessageToCustomer' => "true",
                'isCustomizMessageOfAbandoned' => "true",
                'multiple_messages' => '5',
                'isMessageFromAdminNumber' => "true",
                'official_number' => '918141001180',
                'isDisplayReport' => "true",
                'loginlink' => $loginLink,
                );
                $result = update_option('whatso_user_settings', wp_json_encode($update_user_settings));
            }
        }
        //return $result;

    }
    /**
     * Function to read cart
     */
    public function whatso_read_cart()
    {
        if (!WC()->cart) { //Exit if Woocommerce cart has not been initialized
            return;
        }
        //Retrieving cart total value and currency
        $cart_total = WC()->cart->total;
        $cart_currency = get_woocommerce_currency();
        $current_time = current_time('mysql', false); //Retrieving current time

        $session_id = WC()->session->get_customer_id();

        //Retrieving cart
        $products = WC()->cart->get_cart_contents();
        $product_array = array();

        foreach ($products as $product => $values) {
            $item = wc_get_product($values['data']->get_id());

            $product_title = $item->get_title();
            $product_quantity = $values['quantity'];
            $product_variation_price = '';
            $product_tax = '';

            if (isset($values['line_total'])) {
                $product_variation_price = $values['line_total'];
            }
            if (isset($values['line_tax'])) { //If we have taxes, add them to the price
                $product_tax = $values['line_tax'];
            }

            // Handling product variations
            if ($values['variation_id']) { //If user has chosen a variation
                $single_variation = new WC_Product_Variation($values['variation_id']);

                //Handling variable product title output with attributes
                $product_attributes = $this->attribute_slug_to_title($single_variation->get_variation_attributes());
                $product_variation_id = $values['variation_id'];
            } else {
                $product_attributes = false;
                $product_variation_id = '';
            }

            //Inserting Product title, Variation and Quantity into array
            $product_array[] = array(
            'product_title' => $product_title . $product_attributes,
            'quantity' => $product_quantity,
            'product_id' => $values['product_id'],
            'product_variation_id' => $product_variation_id,
            'product_variation_price' => $product_variation_price,
            'product_tax' => $product_tax
            );
        }

        return $results_array = array(
        'cart_total'     => $cart_total,
        'cart_currency' => $cart_currency,
        'current_time'     => $current_time,
        'session_id'     => $session_id,
        'product_array' => $product_array
        );
    }
    /**
     * Ajax to get user inputs from checkout fields
     */
    public function whatso_abandoned_save()
    {

        // Check for nonce security   
        if (isset($_POST) && isset($_POST['nonce'])) {
            if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'whatso-ajax-nonce')) {
                die('Busted!');
            }
        }
        if (isset($_POST) && isset($_POST['action']) && $_POST['action'] == 'whatso_abandoned_save') {

            if (!WC()->cart) { //Exit if Woocommerce cart has not been initialized
                return false;
            }

            $whatso_abandoned_email = '';
            $whatso_abandoned_name = '';
            $whatso_abandoned_surname = '';
            $whatso_abandoned_phone = '';
            $whatso_abandoned_country = '';


            if (isset($_POST['whatso_abandoned_name'])) {
                $whatso_abandoned_name = sanitize_text_field(wp_unslash($_POST['whatso_abandoned_name']));
            }
            if (isset($_POST['whatso_abandoned_surname'])) {
                $whatso_abandoned_surname = sanitize_text_field(wp_unslash($_POST['whatso_abandoned_surname']));
            }
            if (isset($_POST['whatso_abandoned_phone'])) {
                $whatso_abandoned_phone = sanitize_text_field(wp_unslash($_POST['whatso_abandoned_phone']));
            }
            if (isset($_POST['whatso_abandoned_email'])) {
                $whatso_abandoned_email = sanitize_text_field(wp_unslash($_POST['whatso_abandoned_email']));
            }
            if (isset($_POST['whatso_abandoned_country'])) {
                $whatso_abandoned_country = sanitize_text_field(wp_unslash($_POST['whatso_abandoned_country']));
            }

            if (!empty($whatso_abandoned_name)) {
                WC()->session->set('whatso_first_name', $whatso_abandoned_name);
            }
            if (!empty($whatso_abandoned_surname)) {
                WC()->session->set('whatso_last_name', $whatso_abandoned_surname);
            }
            if (!empty($whatso_abandoned_phone)) {
                WC()->session->set('whatso_customer_phone', $whatso_abandoned_phone);
            }
            if (!empty($whatso_abandoned_email)) {
                WC()->session->set('whatso_customer_email', $whatso_abandoned_email);
            }
            if (!empty($whatso_abandoned_country)) {
                WC()->session->set('whatso_customer_country', $whatso_abandoned_country);
            }
        }
        die();
    }
    /**
     * Function to save cart data
     */
    public function save_cart_data()
    {
        global $woocommerce;
        global $wpdb;
        if (!WC()->cart) { //Exit if Woocommerce cart has not been initialized
            return false;
        }
        if (is_user_logged_in()) {
            if (!WC()->session) { //If session does not exist, exit function
                return;
            }
            $customer_id = WC()->session->get_customer_id();
            $cart = $this->whatso_read_cart();
            $get_user_data = $this->whatso_get_user_data();
            // $current_time = current_time( 'mysql', false ); //Retrieving current time
            $cart_table = $wpdb->prefix . 'whatso_abandoned_cart';
            $get_sql = $wpdb->prepare("SELECT COUNT(id) FROM $cart_table WHERE customer_id = %d AND status IN (0,1)", $customer_id);
            $result_count = $wpdb->get_var($get_sql);

            if ($result_count > 0) {

                $update_array = array(
                'customer_email' => $get_user_data['email'],
                'customer_mobile_no' => $get_user_data['phone'],
                'customer_first_name' => $get_user_data['first_name'],
                'customer_last_name' => $get_user_data['last_name'],
                'cart_json' => serialize($cart['product_array']),
                'cart_total_json' => '{}',
                'cart_total' => $cart['cart_total'],
                'cart_currency' => $cart['cart_currency'],
                'last_access_time' => $cart['current_time']
                );

                $wpdb->update($cart_table, $update_array, array('customer_id' => $customer_id));
            } else {

                $insert_array = array(
                'customer_id' => $customer_id,
                'customer_email' => $get_user_data['email'],
                'customer_mobile_no' => $get_user_data['phone'],
                'customer_first_name' => $get_user_data['first_name'],
                'customer_last_name' => $get_user_data['last_name'],
                'customer_type' => 'REGISTERED',
                'cart_json' => serialize($cart['product_array']),
                'cart_total_json' => '{}',
                'cart_total' => $cart['cart_total'],
                'cart_currency' => $cart['cart_currency'],
                'last_access_time' => $cart['current_time']

                );

                $wpdb->insert($cart_table, $insert_array);
            }
        }
    }
    /**
     * Function to add cart access date in sessions
     */

    public function add_whatso_session_key()
    {
        global $wpdb;
        global $woocommerce;
        if (!WC()->cart) { //Exit if Woocommerce cart has not been initialized
            return false;
        }
        $current_time = current_time('mysql', false); //Retrieving current time
        //if(!WC()->session->get('cartbounty_session_id')){ //In case browser session is not set, we make sure it gets set
        WC()->session->set('whatso_cart_last_access_time', $current_time); //Storing session_id in WooCommerce session
        //}
        $customer_id = WC()->session->get_customer_id();

        $cart_table = $wpdb->prefix . 'whatso_abandoned_cart';
        $get_sql = $wpdb->prepare("SELECT COUNT(id) FROM $cart_table WHERE customer_id = %s AND status IN (0,1)", $customer_id);
        $result_count = $wpdb->get_var($get_sql);

        if ($result_count > 0) {

            $wpdb->update($cart_table, array('last_access_time' => $current_time), array('customer_id' => $customer_id, 'status' => 1));
        }
    }
    /**
     * Cron job time configuration function
     *
     * @Parameters: $intervals is a array of intervals for each cron
     */

    function whatso_cron_intervals($intervals)
    {
        $intervals['whatso_abandoned_cart_cron_iterval'] = array( //Defining cron Interval for sending out email notifications about abandoned carts
        'interval' => 180,
        'display' => 'Every 3 minutes'
        );
        $intervals['clear_tables'] = array( //Defining cron Interval for removing abandoned carts that do not have products
        'interval' => 24 * 60 * 60,
        'display' => 'Every day'
        );
        return $intervals;
    }
    /**
     * Cron job execution function
     */
    public function whatso_schedulers()
    {
        $data = get_option('whatso_abandoned');
        if ($data) {
            $data = json_decode($data);
            //print_r($data);
            $whatso_mobile = "";
            $whatso_trigger = "";
            $whatso_trigger2 = "";
            $whatso_trigger3 = "";
            $whatso_trigger4 = "";
            $whatso_trigger5 = "";
            $is_enabled = "";
            if (isset($data->admin_mobile)) {
                $whatso_mobile = $data->admin_mobile;
            }
            if (isset($data->whatso_trigger_time)) {
                $whatso_trigger = $data->whatso_trigger_time;
            }
            if (isset($data->whatso_trigger_time2)) {
                $whatso_trigger2 = $data->whatso_trigger_time2;
            }
            if (isset($data->whatso_trigger_time3)) {
                $whatso_trigger3 = $data->whatso_trigger_time3;
            }
            if (isset($data->whatso_trigger_time4)) {
                $whatso_trigger4 = $data->whatso_trigger_time4;
            }
            if (isset($data->whatso_trigger_time5)) {
                $whatso_trigger5 = $data->whatso_trigger_time5;
            }
            if (isset($data->ac_enable)) {
                $is_enabled = $data->ac_enable;
            }

            if ($is_enabled == 'checked') {

                if (!wp_next_scheduled('whatso_send_hook')) {
                    wp_schedule_event(time(), 'whatso_abandoned_cart_cron_iterval', 'whatso_send_hook');
                }
                if (!wp_next_scheduled('whatso_clear_table_hook')) {
                    wp_schedule_event(time(), 'clear_tables_interval', 'whatso_clear_table_hook');
                }
            } else {
                wp_clear_scheduled_hook('whatso_send_hook');
                wp_clear_scheduled_hook('whatso_clear_table_hook');
            }
        }
    }
    /**
     * Function executed by cron to clear table
     */

    public function whatso_clear_abandoned_carts_table()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "whatso_abandoned_cart WHERE datediff(now(), last_access_time) > 45 AND status IN (0,1)"));
    }
    /**
     * Function executed by cronjob to abandoned cart
     */
    public function whatso_move_sessions_to_whatso_abandoned_table()
    {
        try {
            global $wpdb;
            global $woocommerce;

            $product_cart = $this->whatso_read_cart();
            $current_time = current_time('mysql', false); //Retrieving current time
            $cart_table = $wpdb->prefix . 'whatso_abandoned_cart';

            // to get product title
            $table1 = $wpdb->prefix . "posts";
            $woocommerce_session_table = $wpdb->prefix . 'woocommerce_sessions';

            $get_sessions_sql = $wpdb->prepare("SELECT * FROM $woocommerce_session_table");

            $get_all_sessions = $wpdb->get_results($get_sessions_sql); // db call ok; no-cache ok

            $json_data = get_option('whatso_abandoned');
            $json_decoded = json_decode($json_data);
            $abandoned_interval = $json_decoded->whatso_trigger_time;
            $ac_enable = $json_decoded->ac_enable;

            $whatso_trigger_time2 = $json_decoded->whatso_trigger_time2;
            $whatso_trigger_time3 = $json_decoded->whatso_trigger_time3;
            $whatso_trigger_time4 = $json_decoded->whatso_trigger_time4;
            $whatso_trigger_time5 = $json_decoded->whatso_trigger_time5;

            $message2_enable = (isset($json_decoded->message2_enable)) ? $json_decoded->message2_enable : '';
            $message3_enable = (isset($json_decoded->message3_enable)) ? $json_decoded->message3_enable : '';
            $message4_enable = (isset($json_decoded->message4_enable)) ? $json_decoded->message4_enable : '';
            $message5_enable = (isset($json_decoded->message5_enable)) ? $json_decoded->message5_enable : '';

            $data = get_option('whatso_notifications');
            $data = json_decode($data);
            $whatso_username = $data->whatso_username;
            $whatso_password = $data->whatso_password;
            $whatso_message = $data->whatso_ac_message;
            $whatso_ac_message2 = $data->whatso_ac_message2;
            $whatso_ac_message3 = $data->whatso_ac_message3;
            $whatso_ac_message4 = $data->whatso_ac_message4;
            $whatso_ac_message5 = $data->whatso_ac_message5;

            $whatso_mobileno = $data->whatso_mobileno;
            $data = get_option('whatso_abandoned');
            $data = json_decode($data);
            $from_number = $data->admin_mobile;

            foreach ($get_all_sessions as $row) {
                $session_id = $row->session_key;
                $session_content = unserialize($row->session_value);
                $cart = unserialize($session_content['cart']);
                $cart_totals = unserialize($session_content['cart_totals']);
                $last_access_time = $session_content['whatso_cart_last_access_time'];
                $customer = unserialize($session_content['customer']);

                $cart_id_array = json_decode(json_encode($cart), true);

                foreach ($cart_id_array as $arr2) {

                    $product_id = $arr2['product_id'];
                    $cart_content =  $wpdb->get_results($wpdb->prepare("SELECT post_title FROM $table1 WHERE ID = %d ORDER BY ID DESC", $product_id)); // db call ok; no-cache ok
                    $array1 = json_decode(json_encode($cart_content), true);

                    $cart_data = json_encode($array1);


                    $var = explode(",", $array1['0']['post_title']);
                    $product_name = $var['0'];
                    $products_array = array();
                    array_push($products_array, $product_name);
                    $exploded_names = implode(",", $products_array);
                }
                $customer_first_name = '';
                $customer_last_name = '';
                $customer_email = '';
                $customer_mobile_no = '';
                $customer_country = '';
                $cart_total = '';

                if (is_array($customer) and isset($customer['phone'])) {
                    $customer_mobile_no = $customer['phone'];
                }

                if (is_array($customer) and isset($customer['first_name'])) {
                    $customer_first_name = $customer['first_name'];
                }

                if (is_array($customer) and isset($customer['last_name'])) {
                    $customer_last_name = $customer['last_name'];
                }

                if (is_array($customer) and isset($customer['email'])) {
                    $customer_email = $customer['email'];
                }

                if (is_array($customer) and isset($customer['country'])) {
                    $customer_country = $customer['country'];
                }

                if (is_array($cart_totals) and isset($cart_totals['total'])) {
                    $cart_total = $cart_totals['total'];
                }

                //If nothing found check for billing fields

                if (empty($customer_first_name)) {
                    if (isset($session_content['billing_first_name'])) {
                        $customer_first_name = $session_content['billing_first_name'];
                    }
                }

                if (empty($customer_last_name)) {
                    if (isset($session_content['billing_last_name'])) {
                        $customer_last_name = $session_content['billing_last_name'];
                    }
                }

                if (empty($customer_email)) {
                    if (isset($session_content['billing_email'])) {
                        $customer_email = $session_content['billing_email'];
                    }
                }

                if (empty($customer_mobile_no)) {
                    if (isset($session_content['billing_phone'])) {
                        $customer_mobile_no = $session_content['billing_phone'];
                    }
                }

                if (empty($customer_country)) {
                    if (isset($session_content['billing_country'])) {
                        $customer_country = $session_content['billing_country'];
                    }
                }

                //If nothing found check for whatso fields

                if (empty($customer_first_name)) {
                    if (isset($session_content['whatso_first_name'])) {
                        $customer_first_name = $session_content['whatso_first_name'];
                    }
                }

                if (empty($customer_last_name)) {
                    if (isset($session_content['whatso_last_name'])) {
                        $customer_last_name = $session_content['whatso_last_name'];
                    }
                }

                if (empty($customer_email)) {
                    if (isset($session_content['whatso_customer_email'])) {
                        $customer_email = $session_content['whatso_customer_email'];
                    }
                }

                if (empty($customer_mobile_no)) {
                    if (isset($session_content['whatso_customer_phone'])) {
                        $customer_mobile_no = $session_content['whatso_customer_phone'];
                    }
                }

                if (empty($customer_country)) {
                    if (isset($session_content['whatso_customer_country'])) {
                        $customer_country = $session_content['whatso_customer_country'];
                    }
                }

                $customernumber = preg_replace('/[^0-9]/', '', $customer_mobile_no);

                if (is_array($cart) && !empty($cart) && !empty($customer_mobile_no) && !empty($last_access_time) && is_numeric($customer_mobile_no)) {
                    $get_time_difference = (strtotime($current_time) - strtotime($last_access_time)) / 60;

                    if ($get_time_difference >= $abandoned_interval) {

                        $get_sql = $wpdb->prepare("SELECT COUNT(id) FROM $cart_table WHERE customer_id = %s AND status IN (0,1)", $session_id); // db call ok; no-cache ok
                        $customer_id = $session_id;
                        $result_count = $wpdb->get_var($get_sql);
                        $store_name = get_bloginfo('name');
                        //Remove regular expression from mobile number
                        $country_code = $customer_country;

                        if ($customernumber != "") {

                            if ($country_code ==  "UK") //united kingdom
                                  {
                                $customernumber = $this->mobile_number_validation(10, 44, $customernumber);
                            } elseif ($country_code ==  "AT") //Australia
                               {
                                $customernumber = $this->mobile_number_validation(10, 61, $customernumber);
                            } elseif ($country_code ==  "US") //United Status
                            {
                                   $customernumber = $this->mobile_number_validation(10, 1, $customernumber);
                            } elseif ($country_code ==  "RU") //Russia
                            {
                                $customernumber = $this->mobile_number_validation(10, 7, $customernumber);
                            } elseif ($country_code ==  "IN") //India
                            {
                                $customernumber = $this->mobile_number_validation(10, 91, $customernumber);
                            } elseif ($country_code ==  "IR") //Iran
                            {
                                $customernumber = $this->mobile_number_validation(10, 98, $customernumber);
                            } elseif ($country_code ==  "CA") //Canada
                            {
                                $customernumber = $this->mobile_number_validation(10, 1, $customernumber);
                            } elseif ($country_code ==  "ZA") //South Africa
                            {
                                $customernumber = $this->mobile_number_validation(9, 27, $customernumber);
                            } elseif ($country_code ==  "BR") //Brazil
                            {
                                $customernumber = $this->mobile_number_validation(11, 55, $customernumber);
                            } elseif ($country_code ==  "CN") //China
                            {
                                $customernumber = $this->mobile_number_validation(11, 86, $customernumber);
                            } elseif ($country_code ==  "ID") //Indonesia
                            {
                                $customernumber = $this->mobile_number_validation(10, 62, $customernumber);
                            } elseif ($country_code ==  "PK") //Pakistan
                            {
                                $customernumber = $this->mobile_number_validation(10, 92, $customernumber);
                            } elseif ($country_code ==  "NG") //Nigeria
                            {
                                $customernumber = $this->mobile_number_validation(8, 234, $customernumber);
                            } elseif ($country_code ==  "BD") //Bangladesh
                            {
                                $customernumber = $this->mobile_number_validation(10, 880, $customernumber);
                            } elseif ($country_code ==  "MX") //Mexico
                            {
                                $customernumber = $this->mobile_number_validation(10, 52, $customernumber);
                            } elseif ($country_code ==  "JP") //japan
                            {
                                $customernumber = $this->mobile_number_validation(10, 81, $customernumber);
                            } elseif ($country_code ==  "ET") //Ethiopia
                            {
                                $customernumber = $this->mobile_number_validation(9, 251, $customernumber);
                            } elseif ($country_code ==  "PH") //Phillipines
                            {
                                $customernumber = $this->mobile_number_validation(10, 63, $customernumber);
                            } elseif ($country_code ==  "EG") //Egypt
                            {
                                $customernumber = $this->mobile_number_validation(10, 20, $customernumber);
                            } elseif ($country_code ==  "VN") //Vietnam
                            {
                                $customernumber = $this->mobile_number_validation(9, 84, $customernumber);
                            } elseif ($country_code ==  "DE") //Germany
                            {
                                $customernumber = $this->mobile_number_validation(10, 49, $customernumber);
                            } elseif ($country_code ==  "TR") //Turkey
                            {
                                $customernumber = $this->mobile_number_validation(11, 90, $customernumber);
                            } elseif ($country_code ==  "TH") //Thailan
                            {
                                $customernumber = $this->mobile_number_validation(9, 66, $customernumber);
                            } elseif ($country_code ==  "FR") //France
                            {
                                $customernumber = $this->mobile_number_validation(9, 33, $customernumber);
                            } elseif ($country_code ==  "IT") //Italy
                            {
                                $customernumber = $this->mobile_number_validation(13, 39, $customernumber);
                            } elseif ($country_code ==  "TZ") //Tanzania
                            {
                                $customernumber = $this->mobile_number_validation(9, 255, $customernumber);
                            } elseif ($country_code ==  "ES") //Spain
                            {
                                $customernumber = $this->mobile_number_validation(9, 34, $customernumber);
                            } elseif ($country_code ==  "MM") //Myanmar
                            {
                                $customernumber = $this->mobile_number_validation(10, 95, $customernumber);
                            } elseif ($country_code ==  "KE") //kenya
                            {
                                $customernumber = $this->mobile_number_validation(10, 254, $customernumber);
                            }
                            elseif ($country_code ==  "UG") //Uganda
                            {
                                $customernumber = $this->mobile_number_validation(9, 256, $customernumber);
                            } elseif ($country_code ==  "AR") //Argentina
                            {
                                $customernumber = $this->mobile_number_validation(9, 54, $customernumber);
                            } elseif ($country_code ==  "DZ") //Algeria
                            {
                                $customernumber = $this->mobile_number_validation(9, 213, $customernumber);
                            } elseif ($country_code ==  "SD") //Sudan
                            {
                                $customernumber = $this->mobile_number_validation(9, 249, $customernumber);
                            }elseif ($country_code ==  "AF") //Afghanistan
                            {
                                $customernumber = $this->mobile_number_validation(9, 93, $customernumber);
                            } elseif ($country_code ==  "PL") //Poland
                            {
                                $customernumber = $this->mobile_number_validation(9, 48, $customernumber);
                            } elseif ($country_code ==  "SA") //Saudi Arabia
                            {
                                $customernumber = $this->mobile_number_validation(9, 966, $customernumber);
                            } elseif ($country_code ==  "PE") //Peru
                            {
                                $customernumber = $this->mobile_number_validation(9, 51, $customernumber);
                            } elseif ($country_code ==  "MY") //Malaysia
                            {
                                $customernumber = $this->mobile_number_validation(7, 60, $customernumber);
                            } elseif ($country_code ==  "MZ") //Mozambique
                            {
                                $customernumber = $this->mobile_number_validation(12, 258, $customernumber);
                            } elseif ($country_code ==  "GH") //Ghana
                            {
                                $customernumber = $this->mobile_number_validation(9, 233, $customernumber);
                            } elseif ($country_code ==  "YE") //Yemen
                            {
                                $customernumber = $this->mobile_number_validation(9, 967, $customernumber);
                            } elseif ($country_code ==  "VE") //Venezuela
                            {
                                $customernumber = $this->mobile_number_validation(7, 58, $customernumber);
                            } else {
                                $customernumber = $this->mobile_number_validation_without_country($customernumber);
                            }
                        }

                        if ($result_count > 0) {

                               $update_array = array(
                                'customer_email' => $customer_email,
                                'customer_mobile_no' => $customernumber,
                                'customer_first_name' => $customer_first_name,
                                'customer_last_name' => $customer_last_name,
                                'cart_json' => serialize($cart),
                                'cart_total_json' => serialize($cart_totals),
                                'abandoned_date_time' => $current_time,
                                'cart_total' => $cart_total,
                                'cart_currency' => '',
                                'last_access_time' => $last_access_time,
                                'status' => 1
                               );

                               $wpdb->update($cart_table, $update_array, array('customer_id' => $customer_id, 'status' => 1)); // db call ok; no-cache ok

                        } else {

                            $insert_array = array(
                            'customer_id' => $customer_id,
                            'customer_email' => $customer_email,
                            'customer_mobile_no' => $customernumber,
                            'customer_first_name' => $customer_first_name,
                            'customer_last_name' => $customer_last_name,
                            'customer_type' => '',
                            'cart_json' => serialize($cart),
                            'cart_total_json' => serialize($cart_totals),
                            'abandoned_date_time' => $current_time,
                            'cart_total' => $cart_total,
                            'cart_currency' => '',
                            'last_access_time' => $last_access_time,
                            'status' => 1
                            );

                            $wpdb->insert($cart_table, $insert_array); // db call ok; no-cache ok

                        }

                        $check_dnd_function = $this->check_dnd();

                        if ($check_dnd_function == "true") {

                        } else {

                            $this->send_abandoned_whatsapp_message($session_id, $customer_mobile_no, $customer_first_name, $customer_email, $customernumber, $customer_id, $current_time, $exploded_names, $whatso_username, $whatso_password, $whatso_message, $from_number, 1);
                        }

                        if ($message2_enable == 'checked') {
                            if ($get_time_difference >= $whatso_trigger_time2) {

                                $check_dnd_function = $this->check_dnd();

                                if ($check_dnd_function == "true") {
 
                                } else {

                                    $this->send_abandoned_whatsapp_message($session_id, $customer_mobile_no, $customer_first_name, $customer_email, $customernumber, $customer_id, $current_time, $exploded_names, $whatso_username, $whatso_password, $whatso_ac_message2, $from_number, 2);
                                }
                            }
                        }

                        if ($message3_enable == 'checked') {
                            if ($get_time_difference >= $whatso_trigger_time3) {
                                $check_dnd_function = $this->check_dnd();

                                if ($check_dnd_function == "true") {
                                } else {
                                    $this->send_abandoned_whatsapp_message($session_id, $customer_mobile_no, $customer_first_name, $customer_email, $customernumber, $customer_id, $current_time, $exploded_names, $whatso_username, $whatso_password, $whatso_ac_message3, $from_number, 3);
                                }
                            }
                        }

                        if ($message4_enable == 'checked') {
                            if ($get_time_difference >= $whatso_trigger_time4) {

                                $check_dnd_function = $this->check_dnd();

                                if ($check_dnd_function == "true") {
                                } else {
                                    $this->send_abandoned_whatsapp_message($session_id, $customer_mobile_no, $customer_first_name, $customer_email, $customernumber, $customer_id, $current_time, $exploded_names, $whatso_username, $whatso_password, $whatso_ac_message4, $from_number, 4);
                                }
                            }
                        }

                        if ($message5_enable == 'checked') {
                            if ($get_time_difference >= $whatso_trigger_time5) {
                                $check_dnd_function = $this->check_dnd();

                                if ($check_dnd_function == "true") {
                                } else {
                                    $this->send_abandoned_whatsapp_message($session_id, $customer_mobile_no, $customer_first_name, $customer_email, $customernumber, $customer_id, $current_time, $exploded_names, $whatso_username, $whatso_password, $whatso_ac_message5, $from_number, 5);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            //we are not  adding any log files now
        }
    }

    //check dnd function
    public function check_dnd()
    {
        $data = get_option('whatso_dnd_data');
        $data = json_decode($data);
        $is_dnd_enable = $data->is_dnd_enable;
        $dnd_from = $data->dnd_from;
        $dnd_to = $data->dnd_to;

        if ($is_dnd_enable == "checked") {
            $type = 'mysql';
            $gmt = false;
            if ('mysql' === $type) {
                $type = 'H:i:s';
            }
            $timezone = $gmt ? new DateTimeZone('UTC') : wp_timezone();
            $datetime = new DateTime('now', $timezone);
            $current_time = $datetime->format($type);

            if (($current_time > $dnd_from) && ($current_time < $dnd_to)) {
               
                return "true";
            } else {

                return "false";
            }
           
        } else {
            return "false";
        }
    }
    
    /**
     * Function to send ac message
     */
    public function send_abandoned_whatsapp_message($session_id, $customer_mobile_no, $customer_first_name, $customer_email, $customernumber, $customer_id, $current_time, $exploded_names, $whatso_username, $whatso_password, $whatso_message, $from_number, $message_sequence)
    {
        global $wpdb;
        $cart_table = $wpdb->prefix . 'whatso_abandoned_cart';
        $sent_status = 0;

        if ($message_sequence > 0) {
            $sent_status = $message_sequence - 1;
        }

        $message_sent_obj = $wpdb->get_row($wpdb->prepare("SELECT COUNT(id) as count, message_api_response as response_array FROM $cart_table WHERE customer_id = %s AND message_sent = $sent_status AND status IN (0,1)", $session_id)); // db call ok; no-cache ok                        

        $is_message_sent = 0;
        $response_object_array = '';

        if (is_object($message_sent_obj) && !empty($message_sent_obj)) {
            $is_message_sent = $message_sent_obj->count;
            $response_object_array = $message_sent_obj->response_array;;
        }

        if ($is_message_sent > 0) {
            if ($customer_mobile_no and is_numeric($customer_mobile_no)) {
                $checkout_url = wc_get_checkout_url();
                $name = "id";                     
                $num="num";
                $numvalue= $customernumber;

                $checkout_url = $checkout_url . "?$name=$session_id" ."&$num=$customernumber";
                $store_name = get_bloginfo('name');
                $base_url = site_url($path='', $scheme=null);
                $whatso_message = str_replace('{customername}', $customer_first_name, $whatso_message);
                $whatso_message = str_replace('{productname}', $exploded_names, $whatso_message);

                $whatso_message = str_replace('{storename}', $store_name, $whatso_message);
                $whatso_message = str_replace('{siteurl}', $base_url, $whatso_message);
                $whatso_message = preg_replace("/\r\n/", "<br>", $whatso_message);

                $whatso_message = str_replace('{orderdate}', $current_time, $whatso_message);
                $whatso_message = str_replace('{customeremail}', $customer_email, $whatso_message);
                $whatso_message = str_replace('{customernumber}', $customernumber, $whatso_message);
                $whatso_message = str_replace('{checkoutlink}', $checkout_url, $whatso_message);
                

                $data_decoded = array(
                "Username" => $whatso_username, "Password" => $whatso_password, "MessageText" => $whatso_message, "MobileNumbers" => $customernumber, "ScheduleDate" => '', "FromNumber" => $from_number,
                "Channel" => '1'
                );

                $data = json_encode($data_decoded);

                $url = "https://api.whatso.net/api/v2/SendMessage";

                $response = wp_remote_post(
                    $url, array(
                    'method' => 'POST',
                    'headers' => array(
                    'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
                    ),
                    'body' => $data
                    )
                );


                if (is_array($response) and isset($response['body'])) {

                    $response_obj = json_decode($response['body']);


                    if (is_object($response_obj) && isset($response_obj->ResponseMessage) && $response_obj->ResponseMessage == 'Success') {

                        $array_to_insert = array();

                        $response_array = json_decode($response_object_array);

                        if (is_array($response_array)) {
                               $array_to_insert = $response_array;
                        }

                        array_push($array_to_insert, $response_obj);
                        $wpdb->update($cart_table, array("message_sent" => $message_sequence, "message_api_response" => json_encode($array_to_insert)), array('customer_id' => $customer_id)); // db call ok; no-cache ok
                    }
                }
            }
        }
    }
    /**
     * Function change status to recover once order is placed
     */

    public function recover_order($order_id, $posted_data, $order)
    {
        global $wpdb;
        $execute_flag = true;
        $cart_table = $wpdb->prefix . 'whatso_abandoned_cart';
        if (is_a($order, 'WC_Order_Refund')) {
            $execute_flag = false;
        }

        if ($execute_flag) {
            $billing_phone  = $order->get_billing_phone();
            $customernumber = preg_replace('/[^0-9]/', '', $billing_phone);
            $country_code = $order->get_billing_country();

            if ($customernumber != "") {

                if ($country_code ==  "UK") //united kingdom
                {
                    $customernumber = $this->mobile_number_validation(10, 44, $customernumber);
                } elseif ($country_code ==  "AT") //Australia
                {
                    $customernumber = $this->mobile_number_validation(10, 61, $customernumber);
                } elseif ($country_code ==  "US") //United Status
                {
                    $customernumber = $this->mobile_number_validation(10, 1, $customernumber);
                } elseif ($country_code ==  "RU") //Russia
                {
                    $customernumber = $this->mobile_number_validation(10, 7, $customernumber);
                } elseif ($country_code ==  "IT") //Italy
                {
                    $customernumber = $this->mobile_number_validation(10, 39, $customernumber);
                } elseif ($country_code ==  "IN") //India
                {
                    $customernumber = $this->mobile_number_validation(10, 91, $customernumber);
                } elseif ($country_code ==  "IR") //Iran
                {
                    $customernumber = $this->mobile_number_validation(10, 98, $customernumber);
                } elseif ($country_code ==  "CA") //Canada
                {
                    $customernumber = $this->mobile_number_validation(10, 1, $customernumber);
                } elseif ($country_code ==  "ZA") //South Africa
                {
                    $customernumber = $this->mobile_number_validation(9, 27, $customernumber);
                } elseif ($country_code ==  "BR") //Brazil
                {
                    $customernumber = $this->mobile_number_validation(11, 55, $customernumber);
                } elseif ($country_code ==  "CN") //China
                {
                    $customernumber = $this->mobile_number_validation(11, 86, $customernumber);
                } elseif ($country_code ==  "ID") //Indonesia
                {
                    $customernumber = $this->mobile_number_validation(10, 62, $customernumber);
                } elseif ($country_code ==  "PK") //Pakistan
                {
                    $customernumber = $this->mobile_number_validation(10, 92, $customernumber);
                } elseif ($country_code ==  "NG") //Nigeria
                {
                    $customernumber = $this->mobile_number_validation(8, 234, $customernumber);
                } elseif ($country_code ==  "BD") //Bangladesh
                {
                    $customernumber = $this->mobile_number_validation(10, 880, $customernumber);
                } elseif ($country_code ==  "MX") //Mexico
                {
                    $customernumber = $this->mobile_number_validation(10, 52, $customernumber);
                } elseif ($country_code ==  "JP") //japan
                {
                    $customernumber = $this->mobile_number_validation(10, 81, $customernumber);
                } elseif ($country_code ==  "ET") //Ethiopia
                {
                    $customernumber = $this->mobile_number_validation(9, 251, $customernumber);
                } elseif ($country_code ==  "PH") //Phillipines
                {
                    $customernumber = $this->mobile_number_validation(10, 63, $customernumber);
                } elseif ($country_code ==  "EG") //Egypt
                {
                    $customernumber = $this->mobile_number_validation(10, 20, $customernumber);
                } elseif ($country_code ==  "VN") //Vietnam
                {
                    $customernumber = $this->mobile_number_validation(9, 84, $customernumber);
                } elseif ($country_code ==  "DE") //Germany
                {
                    $customernumber = $this->mobile_number_validation(10, 49, $customernumber);
                } elseif ($country_code ==  "TR") //Turkey
                {
                    $customernumber = $this->mobile_number_validation(11, 90, $customernumber);
                } elseif ($country_code ==  "TH") //Thailan
                {
                    $customernumber = $this->mobile_number_validation(9, 66, $customernumber);
                } elseif ($country_code ==  "FR") //France
                {
                    $customernumber = $this->mobile_number_validation(9, 33, $customernumber);
                } elseif ($country_code ==  "TZ") //Tanzania
                {
                    $customernumber = $this->mobile_number_validation(9, 255, $customernumber);
                } elseif ($country_code ==  "ES") //Spain
                {
                    $customernumber = $this->mobile_number_validation(9, 34, $customernumber);
                } elseif ($country_code ==  "MM") //Myanmar
                {
                    $customernumber = $this->mobile_number_validation(10, 95, $customernumber);
                } elseif ($country_code ==  "KE") //kenya
                {
                    $customernumber = $this->mobile_number_validation(10, 254, $customernumber);
                }
                elseif ($country_code ==  "UG") //Uganda
                {
                    $customernumber = $this->mobile_number_validation(9, 256, $customernumber);
                } elseif ($country_code ==  "AR") //Argentina
                {
                    $customernumber = $this->mobile_number_validation(9, 54, $customernumber);
                } elseif ($country_code ==  "DZ") //Algeria
                {
                    $customernumber = $this->mobile_number_validation(9, 213, $customernumber);
                } elseif ($country_code ==  "SD") //Sudan
                {
                    $customernumber = $this->mobile_number_validation(9, 249, $customernumber);
                }
                elseif ($country_code ==  "AF") //Afghanistan
                {
                    $customernumber = $this->mobile_number_validation(9, 93, $customernumber);
                } elseif ($country_code ==  "PL") //Poland
                {
                    $customernumber = $this->mobile_number_validation(9, 48, $customernumber);
                } elseif ($country_code ==  "SA") //Saudi Arabia
                {
                    $customernumber = $this->mobile_number_validation(9, 966, $customernumber);
                } elseif ($country_code ==  "PE") //Peru
                {
                    $customernumber = $this->mobile_number_validation(9, 51, $customernumber);
                } elseif ($country_code ==  "MY") //Malaysia
                {
                    $customernumber = $this->mobile_number_validation(7, 60, $customernumber);
                } elseif ($country_code ==  "MZ") //Mozambique
                {
                    $customernumber = $this->mobile_number_validation(12, 258, $customernumber);
                } elseif ($country_code ==  "GH") //Ghana
                {
                    $customernumber = $this->mobile_number_validation(9, 233, $customernumber);
                } elseif ($country_code ==  "YE") //Yemen
                {
                    $customernumber = $this->mobile_number_validation(9, 967, $customernumber);
                } elseif ($country_code ==  "VE") //Venezuela
                {
                    $customernumber = $this->mobile_number_validation(7, 58, $customernumber);
                } else {
                    $customernumber = $this->mobile_number_validation_without_country($customernumber);
                }
            }

            $check_abandoned_entry_sql = $wpdb->prepare("SELECT id, customer_id FROM $cart_table WHERE customer_mobile_no LIKE '$customernumber' AND status IN (0,1)"); // db call ok; no-cache ok

            $abandoned_results = $wpdb->get_results($check_abandoned_entry_sql); // db call ok; no-cache ok

            if (is_array($abandoned_results) && COUNT($abandoned_results) > 0) {
                foreach ($abandoned_results as $result) {
                    $customer_id = $result->customer_id;
                    $wpdb->update($cart_table, array("status" => 2), array('customer_id' => $customer_id)); // db call ok; no-cache ok
                }
            }
        }
    }
}
    function wpcf7_before_send_mail_function( $contact_form, $abort, $submission ) 
    {
        $store_name = get_bloginfo('name');
        global $wpdb;
        $detail_table = $wpdb->prefix . 'message_notification_details';
        $base_url = site_url($path='', $scheme=null);
        
        $data = (array) $submission->get_posted_data();

        if ( isset( $data['your-name'] ) ) {
            $firstname=$submission->get_posted_data('your-name');
        }
        else{
            $firstname="-Not Available-";
        }
        if ( isset( $data['your-email'] ) ) 
        {
            $your_email = $submission->get_posted_data('your-email');
        }
        else{
            $your_email="-Not Available-";
        }
        if ( isset( $data['your-subject'] ) ) {
            $your_subject = $submission->get_posted_data('your-subject');
        }
        else{
            $your_subject="-Not Available-";
        }
        if ( isset( $data['your-message'] ) ) {
            $your_message = $submission->get_posted_data('your-message');
        }
        else{
            $your_message="-Not Available-";
        }
        if ( isset( $data['telephone'] ) ) {
            $telephone = $submission->get_posted_data('telephone');
        }
        else{
            $telephone="-Not Available-";
        }
        // do something       
        if (empty(get_option('test1')) || !empty(get_option('test1'))) {
            $update_notifications_arr = array(
                'firstname'=>$firstname,
                'your_email'   =>  $your_email,
                'your_subject'   =>  $your_subject,
                'your_message'   =>  $your_message,
                'telephone'   =>  $telephone,
            );
            $result = update_option('test1', wp_json_encode($update_notifications_arr));
        }

        if (preg_match('/(http|ftp|mailto|www|https)/', $your_message, $matches)) {
            return false;
        }
        else{
            if (!empty(get_option('whatso_notifications')) || !empty(get_option('whatso_abandoned')) || !empty(get_option('whatso_cf7'))) {
                $data = get_option('whatso_notifications');
                $data = json_decode($data);
                $whatso_username = $data->whatso_username;
                $whatso_password = $data->whatso_password;
               
               
                $data = get_option('whatso_abandoned');
                $data = json_decode($data);
                $from_number = $data->admin_mobile;

                $data = get_option('whatso_cf7');
                $data = json_decode($data);
                $admin_mobileno = $data->admin_mobileno;
                $admin_message = $data->admin_message;
                $enable_notification =$data->enable_notification;
                
                if ($enable_notification === "1") {
                    $admin_message = str_replace('{storename}', $store_name, $admin_message);
                    $admin_message = str_replace('{customersubject}', $your_subject, $admin_message);
                    $admin_message = str_replace('{customermessage}', $your_message, $admin_message);		
                    $admin_message = str_replace('{customeremail}', $your_email, $admin_message);
                    $admin_message = str_replace('{customernumber}', $telephone, $admin_message);
                    $admin_message = str_replace('{storeurl}', $base_url, $admin_message);
                    $admin_message = preg_replace("/\r\n/", "<br>", $admin_message);

                    $data_decoded = array(
						"Username" => $whatso_username, "Password" => $whatso_password, "MessageText" => $admin_message, "MobileNumbers" => $admin_mobileno, "ScheduleDate" => '', "FromNumber" => $from_number,
						"Channel" => '1'
					);

					$data = json_encode($data_decoded);

					$url = "https://api.whatso.net/api/v2/SendMessage";

					$response = wp_remote_post($url, array(
						'method' => 'POST',
						'headers' => array(
							'Content-Type' => 'application/json; charset=utf-8', 'WPRequest' => 'abach34h4h2h11h3h'
						),
						'body' => $data
					));
                    if (is_array($response) and isset($response['body'])) {

						$response_obj = json_decode($response['body']);
						if (is_object($response_obj)) {
							//code to update whatso_order_notification_details
							$insert_array = array(
								'user_type' => 'admin',
								'message_api_request' => $data,
								'message_api_response' =>  wp_json_encode($response_obj),
							);

							$wpdb->insert($detail_table, $insert_array);
						}
					}
                }
            }
            else{
                return false;
            }
        }
    }

add_filter( 'wpcf7_before_send_mail', 'wpcf7_before_send_mail_function', 10, 3 );

?>
