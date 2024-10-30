<?php
//for now this variable are declared when action hooks are set this will be removed
//User data

$data1 = get_option('whatso_user_settings');
$data1 = json_decode($data1);

$isOrderNotificationToAdmin = $data1->isOrderNotificationToAdmin;
$isCustomizeMessageToAdmin = $data1->isCustomizeMessageToAdmin;
$isOrderNotificationToCustomer = $data1->isOrderNotificationToCustomer;
$isCustomizMessageToCustomer = $data1->isCustomizMessageToCustomer;
$isCustomizMessageOfAbandoned = $data1->isCustomizMessageOfAbandoned;
$multiple_messages = $data1->multiple_messages;
$isMessageFromAdminNumber = $data1->isMessageFromAdminNumber;
$official_number = $data1->official_number;
$isDisplayReport = $data1->isDisplayReport;
$login = $data1->loginlink;

$data = get_option('whatso_notifications');
$data = json_decode($data);
$whatso_username = $data->whatso_username;
$whatso_password = $data->whatso_password;
$whatso_mobileno = $data->whatso_mobileno;
$whatso_message = $data->whatso_message;
$whatso_customer_message = $data->whatso_customer_message;

$customer_notification = $data->customer_notification;
$whatso_ac_message = $data->whatso_ac_message;
$whatso_ac_message2 = $data->whatso_ac_message2;
$whatso_ac_message3 = $data->whatso_ac_message3;
$whatso_ac_message4 = $data->whatso_ac_message4;
$whatso_ac_message5 = $data->whatso_ac_message5;
$whatso_email = $data->whatso_email;

$ac_enable1 = "";


//Abandoned_data
$data = get_option('whatso_abandoned');
$data = json_decode($data);
$default_country = $data->default_country;
$admin_mobile = $data->admin_mobile;
$whatso_trigger = $data->whatso_trigger_time;
$whatso_time1 = $data->whatso_time1;
$whatso_trigger2 = $data->whatso_trigger_time2;
$whatso_time2 = $data->whatso_time2;
$whatso_trigger3 = $data->whatso_trigger_time3;
$whatso_time3 = $data->whatso_time3;
$whatso_trigger4 = $data->whatso_trigger_time4;
$whatso_time4 = $data->whatso_time4;
$whatso_trigger5 = $data->whatso_trigger_time5;
$whatso_time5 = $data->whatso_time5;
$ac_enable1 = $data->ac_enable;
$message1_enable = $data->message1_enable;
$message2_enable = $data->message2_enable;
$message3_enable = $data->message3_enable;
$message4_enable = $data->message4_enable;
$message5_enable = $data->message5_enable;


if ($whatso_trigger == "") {
    $whatso_trigger = "20";
}

if (!empty($_POST['updatebutton'])) {

    $legit = true;
    if (! isset($_POST['whatso_update_form_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_update_form_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_update_form_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'whatso_update_form') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }
    $admin_mobile = isset($_POST['admin_mobile']) ? sanitize_text_field(wp_unslash($_POST['admin_mobile'])) : '';
    $username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
    $password = isset($_POST['password']) ? sanitize_text_field(wp_unslash($_POST['password'])) : '';
    $whatso_email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $whatso_trigger = isset($_POST['trigger_1']) ? sanitize_textarea_field(wp_unslash($_POST['trigger_1'])) : '';
    $whatso_time1 = isset($_POST['select_time1']) ? sanitize_textarea_field(wp_unslash($_POST['select_time1'])) : '';
    $dnd_from = isset($_POST['dnd_from']) ? sanitize_text_field(wp_unslash($_POST['dnd_from'])) : '';
    $dnd_to = isset($_POST['dnd_to']) ? sanitize_text_field(wp_unslash($_POST['dnd_to'])) : '';

    if (isset($_POST['ac_enable'])) {
        $ac_enable1 = "checked";
    } else {
        $ac_enable1 = "";
    }

    if (isset($_POST['dnd_enable'])) {
        $is_dnd_enable = "checked";
    } else {
        $is_dnd_enable = "";
    }
    $update_notifications_arr = array();
    $flag = 1;
    if (strlen($username) > 32 || strlen($username) < 32) {
        $flag = 0;
        $error_username = '';
        $error_username .= '<div class="notice notice-error is-dismissible">';
        $error_username .= '<p>' . esc_html('Please copy API username properly from website.') . '</p>';
        $error_username .= '</div>';
        echo wp_kses_post($error_username);
    }
    if (strlen($password) > 32 || strlen($password) < 32) {
        $flag = 0;
        $error_password = '';
        $error_password .= '<div class="notice notice-error is-dismissible">';
        $error_password .= '<p>' . esc_html('Please copy API password properly from website.') . '</p>';
        $error_password .= '</div>';
        echo wp_kses_post($error_password);
    }
    if (empty($admin_mobile)) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please Enter Mobile Number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    } elseif (strlen($admin_mobile) < 7) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please enter minimum 7 digits number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    }
    if (!filter_var($whatso_email, FILTER_VALIDATE_EMAIL)) {

        $flag = 0;
        $error_emailid = '';
        $error_emailid .= '<div class="notice notice-error is-dismissible">';
        $error_emailid .= '<p>' . esc_html('Please Enter valid Email Address.') . '</p>';
        $error_emailid .= '</div>';
        echo wp_kses_post($error_emailid);
    }

    if (empty($whatso_trigger)) {

        $flag = 0;
        $error_trigger = '';
        $error_trigger .= '<div class="notice notice-error is-dismissible">';
        $error_trigger .= '<p>' . esc_html('Please Enter valid Email Address.') . '</p>';
        $error_trigger .= '</div>';
        echo wp_kses_post($error_trigger);
    }

    if ($whatso_time1 == 'select_hour') {
        $whatso_trigger = converthourtominutes($whatso_trigger);
    }
    if ($whatso_time1 == 'select_day') {
        $whatso_trigger = convertdaytominutes($whatso_trigger);
    }
    if ($flag == 1) {

        $data = get_option('whatso_notifications');
        $data = json_decode($data);

        $whatso_ac_message = $data->whatso_ac_message;
        $whatso_ac_message2 = $data->whatso_ac_message2;
        $whatso_ac_message3 = $data->whatso_ac_message3;
        $whatso_ac_message4 = $data->whatso_ac_message4;
        $whatso_ac_message5 = $data->whatso_ac_message5;


        $update_notifications_arr = array(
            'whatso_username'   =>  $username,
            'whatso_password'   =>  $password,
            'whatso_mobileno'   =>  $whatso_mobileno,
            'whatso_message'    =>  $whatso_message,
            'customer_notification' => $customer_notification,
            'whatso_customer_message' => $whatso_customer_message,
            'whatso_ac_message'    =>  $whatso_ac_message,
            'whatso_ac_message2'    =>  $whatso_ac_message2,
            'whatso_ac_message3'    =>  $whatso_ac_message3,
            'whatso_ac_message4'    =>  $whatso_ac_message4,
            'whatso_ac_message5'    =>  $whatso_ac_message5,
            'whatso_email' => $whatso_email,

        );
        $result = update_option('whatso_notifications', wp_json_encode($update_notifications_arr));

        if (!empty(get_option('whatso_abandoned'))) {
            $update_notifications_arr = array(
                'default_country' => $default_country,
                'admin_mobile'   =>  $admin_mobile,
                'whatso_trigger_time'   =>  $whatso_trigger,
                'whatso_time1'   =>  $whatso_time1,
                'whatso_trigger_time2'   =>  $whatso_trigger2,
                'whatso_time2'   =>  $whatso_time2,
                'whatso_trigger_time3'   =>  $whatso_trigger3,
                'whatso_time3'   =>  $whatso_time3,
                'whatso_trigger_time4'   =>  $whatso_trigger4,
                'whatso_time4'   =>  $whatso_time4,
                'whatso_trigger_time5'   =>  $whatso_trigger5,
                'whatso_time5'   =>  $whatso_time5,
                'ac_enable' => $ac_enable1,
                'message1_enable' => $message1_enable,
                'message2_enable' => $message2_enable,
                'message3_enable' => $message3_enable,
                'message4_enable' => $message4_enable,
                'message5_enable' => $message5_enable,

            );
            $result = update_option('whatso_abandoned', wp_json_encode($update_notifications_arr));
        }
        
            //dnd_enable
            $update_dnd_array = array(
                'is_dnd_enable' => $is_dnd_enable,
                'dnd_from' => $dnd_from,
                'dnd_to' => $dnd_to,
            );
            $result = update_option('whatso_dnd_data', wp_json_encode($update_dnd_array));
       
        
        $success = '';
        $success .= '<div class="notice notice-success is-dismissible">';
        $success .= '<p>' . esc_html('Details update successfully . Setup more in Order Notification and in Abandoned Cart') . '</p>';
        $success .= '</div>';
        echo wp_kses_post($success);

        //call check userplan
        WHATSO_WooCommerce::whatso_get_user_plan();
    }
}
function converthourtominutes($hour)
{
    $minutes = $hour * 60;
    return $minutes;
}
function convertminutestohour($hour)
{
    $hour = $hour / 60;
    return $hour;
}
function convertdaytominutes($day)
{
    $minutes = $day * 1440;
    return $minutes;
}
function convertminutestoday($minutes)
{
    $day = $minutes / 1440;
    return $day;
}
//User data
$data1 = get_option('whatso_user_settings');
$data1 = json_decode($data1);

$isOrderNotificationToAdmin = $data1->isOrderNotificationToAdmin;
$isCustomizeMessageToAdmin = $data1->isCustomizeMessageToAdmin;
$isOrderNotificationToCustomer = $data1->isOrderNotificationToCustomer;
$isCustomizMessageToCustomer = $data1->isCustomizMessageToCustomer;
$isCustomizMessageOfAbandoned = $data1->isCustomizMessageOfAbandoned;
$multiple_messages = $data1->multiple_messages;
$isMessageFromAdminNumber = $data1->isMessageFromAdminNumber;
$official_number = $data1->official_number;
$isDisplayReport = $data1->isDisplayReport;
$login = $data1->loginlink;

$data = get_option('whatso_notifications');
$data = json_decode($data);
$whatso_username = $data->whatso_username;
$whatso_password = $data->whatso_password;
$whatso_mobileno = $data->whatso_mobileno;
$whatso_message = $data->whatso_message;
$whatso_customer_message = $data->whatso_customer_message;

$customer_notification = $data->customer_notification;
$whatso_ac_message = $data->whatso_ac_message;
$whatso_ac_message2 = $data->whatso_ac_message2;
$whatso_ac_message3 = $data->whatso_ac_message3;
$whatso_ac_message4 = $data->whatso_ac_message4;
$whatso_ac_message5 = $data->whatso_ac_message5;
$whatso_email = $data->whatso_email;

if ($isMessageFromAdminNumber != "true") {

    $admin_mobile = $official_number;
}
//to set value of test 1 in option

if (isset($_POST['test1'])) {
    $legit = true;
    if (! isset($_POST['whatso_send_test_msg_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_send_test_msg_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_send_test_msg_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'whatso_send_test_msg') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }
    $abandoned_num = isset($_POST['abandoned_num']) ? sanitize_text_field(wp_unslash($_POST['abandoned_num'])) : '';
    $abandoned_message = isset($_POST['abandoned_message']) ? sanitize_textarea_field(wp_unslash($_POST['abandoned_message'])) : '';
    $message_type = "1";
    $order_admin_num = "";
    $order_customer_num = "";
    $order_admin_message = "";
    $order_customer_message = "";
    $update_notifications_arr = array();
    $flag = 1;

    if (empty($abandoned_num)) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please Enter Mobile Number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    } elseif (strlen($abandoned_num) < 7) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please enter minimum 7 digits number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    }
    if ($flag == 1) {
        if (!empty(get_option('whatso_test_message')) || empty(get_option('whatso_test_message'))) {
            $update_notifications_arr = array(
                'abandoned_num' => $abandoned_num,
                'abandoned_message'   =>  $abandoned_message,
                'message_type'   =>  $message_type,
                'order_admin_num'   =>  $order_admin_num,
                'order_customer_num'   =>  $order_customer_num,
                'order_admin_message'   =>  $order_admin_message,
                'order_customer_message'   =>  $order_customer_message,
            );
            $result = update_option('whatso_test_message', wp_json_encode($update_notifications_arr));

            $success = '';
            $success .= '<div class="notice notice-success is-dismissible">';
            $success .= '<p>' . esc_html('Test Message send successfully!') . '</p>';
            $success .= '</div>';
            echo wp_kses_post($success);
    
            //call test message api
            
            WHATSO_WooCommerce::whatso_test_message();
            
        }
    }
}
//to set value of test 2 in option
if (isset($_POST['test2'])) {
    $legit = true;
    if (! isset($_POST['whatso_send_test_msg_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_send_test_msg_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_send_test_msg_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'whatso_send_test_msg') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }
    $order_admin_num = isset($_POST['order_admin_num']) ? sanitize_text_field(wp_unslash($_POST['order_admin_num'])) : '';
    $order_customer_num = isset($_POST['order_customer_num']) ? sanitize_text_field(wp_unslash($_POST['order_customer_num'])) : '';
    $order_admin_message = isset($_POST['order_admin_message']) ? sanitize_textarea_field(wp_unslash($_POST['order_admin_message'])) : '';
    $order_customer_message = isset($_POST['order_customer_message']) ? sanitize_textarea_field(wp_unslash($_POST['order_customer_message'])) : '';
    $message_type = "0";
    $abandoned_num = "";
    $abandoned_message = "";

    $update_notifications_arr = array();
    $flag = 1;

    if (empty($order_admin_num)) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please Enter Mobile Number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    } elseif (strlen($order_admin_num) < 7) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please enter minimum 7 digits number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    }
    if (empty($order_customer_num)) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please Enter Mobile Number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    } elseif (strlen($order_customer_num) < 7) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please enter minimum 7 digits number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    }

    if ($flag == 1) {
        if (!empty(get_option('whatso_test_message')) || empty(get_option('whatso_test_message'))) {
            $update_notifications_arr = array(
                'abandoned_num' => $abandoned_num,
                'abandoned_message'   =>  $abandoned_message,
                'message_type'   =>  $message_type,
                'order_admin_num'   =>  $order_admin_num,
                'order_customer_num'   =>  $order_customer_num,
                'order_admin_message'   =>  $order_admin_message,
                'order_customer_message'   =>  $order_customer_message,
            );
            $result = update_option('whatso_test_message', wp_json_encode($update_notifications_arr));

            $success = '';
            $success .= '<div class="notice notice-success is-dismissible">';
            $success .= '<p>' . esc_html('Test Message send successfully!') . '</p>';
            $success .= '</div>';
            echo wp_kses_post($success);
            
            //call test message api
            WHATSO_WooCommerce::whatso_test_message();
        }
    }
}

$abandoned_message = "Hi TestName, We noticed you didn't finish your order on {storename}.\n\nVisit {siteurl} to complete your order.  \n\nThanks, {storename}.";


$order_admin_message = "Hi TestName, an order is placed on {storename} at {orderdate}.\n\nThe order is for TestProduct and order amount is $10.\n\nCustomer details are: Test Customer\n\nTestcustomer@gmail.com";

$order_customer_message = "Hi TestName, your TestProduct order of $10 has been placed. \n\nWe will keep you updated about your order status.\n\n{storename}";

$data1 = get_option('whatso_dnd_data');
$data1 = json_decode($data1);

$is_dnd_enable = $data1->is_dnd_enable;
$dnd_from = $data1->dnd_from;
$dnd_to = $data1->dnd_to;

if ($whatso_time1 == 'select_hour') {
    $whatso_trigger = convertminutestohour($whatso_trigger);
}
if ($whatso_time1 == 'select_day') {
    $whatso_trigger = convertminutestoday($whatso_trigger);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <div class="container">
        <ul class="breadcrumb">
            <li class="breadRow"><a href="admin.php?page=whatso_floating_menu_setup"><b>Whatso</b></a></li>
            <li class="breadcrum2"><b><?php esc_html_e('Settings','whatso');?></b></li>
        </ul>
        <div class="tabbable boxed parentTabs">
            <div id="setting_tabs">
                <ul class="nav nav-tabs">
                    <li>
                        <a href="#set1" class="active"><?php esc_html_e('Settings','whatso');?></a>
                    </li>
                    <li><a href="#set2"><?php esc_html_e('Try it out!','whatso');?></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade active in show" id="set1">


                    <form class="form_div" method="post" name="form1">
                        <div class="row mt-4 mb-3">
                            <div class="col-12 col-md-12 col-sm-12">
                                <h3><?php esc_html_e('Profile Settings','whatso');?></h3>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="" class="lbl"><?php esc_html_e('Username','whatso');?> </label> <span class="required_star">*</span><br />
                                <input type="text" name="username" id="username" value="<?php echo esc_html($whatso_username); ?>" class="text_input" maxlength="32" required>
                                <span id="error_username" class="error"><?php esc_html_e('Please copy API username properly from website.', 'whatso'); ?></span>
                            </div>

                            <div class="col-6">
                                <label for="" class="lbl"><?php esc_html_e('Password','whatso');?> </label> <span class="required_star">*</span><br />
                                <input type="text" name="password" id="password" value="<?php echo esc_html($whatso_password); ?>" class="text_input"  maxlength="32" required>
                                <span id="error_password" class="error"><?php esc_html_e('Please copy API password properly from website.', 'whatso'); ?></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="" class="lbl"><?php esc_html_e('E-mail address','whatso');?> </label> <span class="required_star">*</span><br />
                                <input type="text" name="email" id="email" value="<?php echo esc_html($whatso_email); ?>" class="text_input" required>
                                <span id="error_emailid" class="error"><?php esc_html_e('Please enter validate email.', 'whatso'); ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <label style="margin-left: 2px;"><?php esc_html_e('Mobile Number (from which you want to send message) ', 'whatso'); ?></label><span class="required_star">*</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <input type="text" name="admin_mobile" id="admin_mobile" onClick="this.setSelectionRange(0, this.value.length)" autocomplete="off" class='input-line full-width' maxlength="15" onpaste="return false" onkeypress="return isNumber(event)" required <?php if ($isMessageFromAdminNumber != "true") { ?> readonly="readonly" style="cursor:not-allowed;" <?php } ?>value="<?php echo esc_html($admin_mobile); ?>">
                                <span id="phonemsg" style="display:none"></span><br />
                                <span id="error_mobileno" class="error"><?php esc_html_e('Please enter correct Mobile number.', 'whatso'); ?></span>
                            </div>
                            <div class="col-6">
                                <a <?php if ($isMessageFromAdminNumber != "true") { ?>href="https://www.whatso.net/blog/how-to-use-google-chrome-extension-for-whatso-whatsapp-api/" readonly="readonly" style="cursor:not-allowed;" title="In order to send messages from your number please update your package from whatso.net" <?php } else { ?>href="<?php echo esc_html($login); ?>" style="cursor:pointer;" title="In order to send messages from your number please follow instruction on this link." <?php } ?> target="_blank" data-toggle="tooltip" data-position="right" data-html="true">Scan Your WhatsApp QR from here</a>
                            </div>
                        </div>
                        <div class="row mt-5 mb-3">
                            <div class="col-12 col-md-12 col-sm-12">
                                <h3><?php esc_html_e('AbandonedCart Settings','whatso');?></h3>
                            </div>
                        </div>
                        <div class="row mb-3 mt-4">

                            <div class="col-8">

                                <label class="lbl"><?php esc_html_e('Enable abandoned cart','whatso');?>
                                    &nbsp;&nbsp;&nbsp;
                                    <input type="checkbox" name="ac_enable" class="check-ac" id="ac_enable" value="checked" <?php if ($ac_enable1 == "checked") { ?> checked="checked" <?php } ?>></label>

                            </div>

                        </div>
                        <div class="row mb-3 mt-4">
                            <div class="col-3">
                                <label class="lbl"><?php esc_html_e('Consider cart abandoned after','whatso');?></label>

                            </div>
                            <div class="col-1">
                                <input type="number" name="trigger_1" id="trigger_1" required value="<?php echo esc_html($whatso_trigger); ?>" class="form-control">
                                <span id="error_trigger" class="error"><?php esc_html_e('Please enter the abandoned time.', 'whatso'); ?></span>
                            </div>
                            <div class="col-2">
                                <select class="form-control" name="select_time1" value="select_time1" required>
                                    <option value="select_minute" name="select_time1" id="select_minute" <?php echo esc_html('select_minute') === esc_attr($whatso_time1) ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Minute', 'whatso'); ?></option>
                                    <option value="select_hour" name="select_time1" id="select_hour" <?php echo esc_html('select_hour') === esc_attr($whatso_time1) ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Hour', 'whatso'); ?></option>
                                    <option value="select_day" name="select_time1" id="select_day" <?php echo esc_html('select_day') === esc_attr($whatso_time1) ? 'selected' : ''; ?>>
                                        <?php esc_html_e('Day', 'whatso'); ?></option>
                                </select>
                            </div>
                            <div class="row mb-3 mt-5">

                                <div class="col-8">

                                    <label class="lbl"><?php esc_html_e('Enable Do not send message at given time.', 'whatso'); ?>
                                        &nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="dnd_enable" class="check-ac" id="dnd_enable" value="checked" <?php if ($is_dnd_enable == "checked") { ?> checked="checked" <?php } ?>></label>
                                </div>

                            </div>
                        </div>
                        <div class="row mb-3 mt-3">
                            <div class="col-2">
                                <label class="lbl pt-1"><?php esc_html_e('Start Time:', 'whatso'); ?></label>
                            </div>
                            <div class="col-2 p-0">
                                <input type="time" name="dnd_from" id="dnd_from" required step="2" value="<?php echo esc_html($dnd_from); ?>">

                            </div>
                            <div class="col-1 hourslabel"> <?php esc_html_e('Hour', 'whatso'); ?></div>
                        </div>
                        <div class="row mb-3 mt-4">
                            <div class="col-2">
                                <label class="lbl pt-1"><?php esc_html_e('End Time:', 'whatso'); ?></label>
                            </div>
                            <div class="col-2 p-0">
                                <input type="time" name="dnd_to" id="dnd_to" required step="2" value="<?php echo esc_html($dnd_to); ?>">

                            </div>
                            <div class="col-1 hourslabel "> <?php esc_html_e('Hour', 'whatso'); ?></div>
                        </div>
                        <div class="row">
                        <h6 class="godndMsg"><?php esc_html_e('* Abandoned Messages will not go From ', 'whatso'); ?><?php echo esc_html($dnd_from); ?><?php esc_html_e(' To ', 'whatso'); ?><?php echo esc_html($dnd_to); ?></h6>
                        <h6 class="notdndMsg"><?php esc_html_e('* The Do not Send Message is disable', 'whatso'); ?></h6>
                    </div>
                        <div class="row">
                            <?php wp_nonce_field('whatso_update_form', 'whatso_update_form_nonce'); ?>

                            <div class="col-md-12 mt-5 text-center">
                                <div class="w-100 m-auto">
                                    <input type="submit" class="btn btn-theme" name="updatebutton" value="Submit" onclick="FormValidation()" />
                                </div>
                            </div>
                            <div class="col-md-12  mt-5 text-center">

                                <h5><?php esc_html_e('If you had already bought the plan, kindly click on above button to refresh.','whatso');?>
                                </h5>
                            </div>
                        </div>
                    </form>

                </div>

                <div class="tab-pane fade" id="set2">

                    <div class="card mx-auto w-50 rounded" id="testing_tab">
                        <div class="tabbable">
                            <ul class="nav nav-tabs  justify-content-evenly">
                                <li class="active"><a href="#sub21" class="active ">Abandoned cart Message</a>
                                </li>
                                <li><a href="#sub22"><?php esc_html_e('Order Notification Message','whatso');?></a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active in show" id="sub21">
                                    <form class="form_div" method="post" name="form3" id="form3">
                                        <div class="row">
                                            <div class="col-12 p-4">
                                                <label class="mb-2"><?php esc_html_e('Enter the number where you like to receive a message','whatso');?></label>
                                                <input type="text" name="abandoned_num" onClick="this.setSelectionRange(0, this.value.length)" autocomplete="off" class='input-line full-width mb-2' maxlength="15" onpaste="return false" onkeypress="return isNumber(event)" id="mobileno" placeholder="Enter Mobile Number with country code.Do not use 0 or +." required />
                                                <span id="error_mobileno" class="error"><?php esc_html_e('Please enter correct Mobile number.', 'whatso'); ?></span>
                                                <span id="phonemsg1" style="display:none"><?php esc_html_e('Please enter minimum 7 digits number.', 'whatso'); ?></span><br />
                                                <textarea required class="form-control" name="abandoned_message" id="message" rows="6" ><?php echo esc_html($abandoned_message); ?></textarea>

                                            </div>
                                            <?php wp_nonce_field('whatso_send_test_msg', 'whatso_send_test_msg_nonce'); ?>
                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn btn-theme" name="test1"><?php esc_html_e('Send Test Message','whatso');?></button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="sub22">
                                    <form class="form_div" method="post" name="form4">
                                        <div class="row">
                                            <div class="col-12 p-4 pb-2">
                                                <label class="mb-2"><?php esc_html_e('Enter Admin number where you like to receive a message','whatso');?></label>
                                                <input type="text" name="order_admin_num" onClick="this.setSelectionRange(0, this.value.length)" autocomplete="off" class='input-line full-width mb-2' maxlength="15" onpaste="return false" onkeypress="return isNumber(event)" id="mobileno1" placeholder="Enter Mobile Number with country code.Do not use 0 or +." required>
                                                <span id="error_mobileno" class="error"><?php esc_html_e('Please enter correct Mobile number.', 'whatso'); ?></span>
                                                <span id="phonemsg2" style="display:none"><?php esc_html_e('Please enter minimum 7 digits number.', 'whatso'); ?></span><br />
                                                <textarea required class="form-control" name="order_admin_message" id="message" rows="7" ><?php echo esc_html($order_admin_message); ?></textarea>

                                            </div>
                                            <div class="col-12 p-4 pt-2">
                                                <label class="mb-2"><?php esc_html_e('Enter Customer number where you like to receive a message','whatso');?></label>
                                                <input type="text" name="order_customer_num" onClick="this.setSelectionRange(0, this.value.length)" autocomplete="off" class='input-line full-width mb-2' placeholder="Enter Mobile Number with country code.Do not use 0 or +." maxlength="15" onpaste="return false" onkeypress="return isNumber(event)" id="mobileno2" required>
                                                <span id="error_mobileno" class="error"><?php esc_html_e('Please enter correct Mobile number.', 'whatso'); ?></span>
                                                <span id="phonemsg3" style="display:none"><?php esc_html_e('Please enter minimum 7 digits number.', 'whatso'); ?></span><br />
                                                <textarea required class="form-control" name="order_customer_message" id="message" rows="6" ><?php echo esc_html($order_customer_message); ?></textarea>

                                            </div>
                                            <?php wp_nonce_field('whatso_send_test_msg', 'whatso_send_test_msg_nonce'); ?>
                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn btn-theme" name="test2">Send Test Message</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
  
    </div>

</body>
<script>
    jQuery(function() {
        jQuery('[data-toggle="tooltip"]').tooltip()
    })
    jQuery("ul.nav-tabs a").click(function(e) {
        e.preventDefault();
        jQuery(this).tab('show');
    });
</script>
<script>
    let username = document.querySelector('#username');
    let password = document.querySelector('#password');
    let email = document.querySelector('#email');
    let admin = document.querySelector('#admin_mobile');
    let mobileno = document.querySelector('#mobileno');
    let mobileno1 = document.querySelector('#mobileno1');
    let mobileno2 = document.querySelector('#mobileno2');
    username.onkeyup = function() {
        if (this.value.length < 32 || this.value.length > 32) {
            document.getElementById('error_username').style.display = 'block';
            return false;
        } else {
            document.getElementById('error_username').style.display = 'none';
            return true;
        }
    }
    password.onkeyup = function() {
        // alert('hello');
        if (this.value.length < 32 || this.value.length > 32) {
            document.getElementById('error_password').style.display = 'block';
            return false;
        } else {
            document.getElementById('error_password').style.display = 'none';
            return true;
        }
    }
    email.onkeyup = function() {
        // alert('hello');
        var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (email.value.match(mailformat)) {

            document.getElementById('error_email').style.display = 'none';
            return true
        } else {
            document.getElementById('error_email').style.display = 'block';
            return false;
        }

    }
    admin.onkeyup = function() {
            if (this.value.length < 12 || this.value.length > 159) {
                document.getElementById('phonemsg1').style.display = 'block';
                return false;
            } else {
                // document.getElementById('mobile_number_error').style.display = 'none';
                document.getElementById('phonemsg1').style.display = 'none';
                return true;
            }
    }
    mobileno.onkeyup = function() {
            if (this.value.length < 7 || this.value.length > 159) {
                document.getElementById('phonemsg1').style.display = 'block';
                return false;
            } else {
                // document.getElementById('mobile_number_error').style.display = 'none';
                document.getElementById('phonemsg1').style.display = 'none';
                return true;
            }
    }
    mobileno1.onkeyup = function() {
            if (this.value.length < 7 || this.value.length > 159) {
                document.getElementById('phonemsg2').style.display = 'block';
                return false;
            } else {
                // document.getElementById('mobile_number_error').style.display = 'none';
                document.getElementById('phonemsg2').style.display = 'none';
                return true;
            }
    }
    mobileno2.onkeyup = function() {
            if (this.value.length < 7 || this.value.length > 159) {
                document.getElementById('phonemsg3').style.display = 'block';
                return false;
            } else {
                // document.getElementById('mobile_number_error').style.display = 'none';
                document.getElementById('phonemsg3').style.display = 'none';
                return true;
            }
    }

    function blockSpecialChar(e) {
        var k;
        document.all ? k = e.keyCode : k = e.which;
        //alert(k); //39 == '
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57) || k == 39);

    }

    function isNumber(evt) {
        // alert('hello');
        var theEvent = evt || window.event;

        // Handle paste
        if (theEvent.type === 'paste') {
            //key = event.clipboardData.getData('text/plain');
            theEvent.returnValue = false;

        } else {
            // Handle key press
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);

        }
        var regex = /[0-9]/;
        if (!regex.test(key)) {
            theEvent.returnValue = false;
            if (theEvent.preventDefault) theEvent.preventDefault();

        }

    }

    function FormValidation() {

        var txtmobile = document.getElementById("admin_mobile").value;
        var phoneno = /^[0-9]*$/;
        //Check Mobile
        if (txtmobile.length < 5 || txtmobile.length > 15) {
            document.getElementById('phonemsg').innerHTML = "Number must be atleast 5 digits";
            return false;
        } else {
            document.getElementById('phonemsg').innerHTML = "";
            return true;
        }
        document.forms["form1"].submit();
    }
    // dnd_enable
    window.onload = pageLoad();
    
    jQuery("#dnd_enable").click(function () {
    pageLoad();
});
    function pageLoad() {
        if(jQuery("#dnd_enable").prop('checked') == true){
            //disable the start end time
            jQuery("#dnd_to"). prop('disabled', false); //enable
            jQuery("#dnd_from"). prop('disabled', false); //enabl
          
            jQuery(".godndMsg").css("display","");
            jQuery(".notdndMsg").css("display","none");
            
        }
        else{
            jQuery("#dnd_to"). prop('disabled', true); //disable
            jQuery("#dnd_from"). prop('disabled', true); //disable
        
            jQuery(".notdndMsg").css("display","");
            jQuery(".godndMsg").css("display","none");
          
        }
    //do something
}

</script>

</html>