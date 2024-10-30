<?php
require_once 'ac_dashboard.php';

//User data
$data = get_option('whatso_user_settings');
$data = json_decode($data);

$isCustomizMessageOfAbandoned = $data->isCustomizMessageOfAbandoned;
$multiple_messages = $data->multiple_messages;


$data = get_option('whatso_abandoned');
$data = json_decode($data);
$default_country = $data->default_country;
$whatso_mobile = $data->admin_mobile;
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

if ($whatso_time1 == 'select_hour') {
    $whatso_trigger = convertminutestohour($whatso_trigger);
}
if ($whatso_time2 == 'select_hour') {
    $whatso_trigger2 = convertminutestohour($whatso_trigger2);
}
if ($whatso_time3 == 'select_hour') {
    $whatso_trigger3 = convertminutestohour($whatso_trigger3);
}
if ($whatso_time4 == 'select_hour') {
    $whatso_trigger4 = convertminutestohour($whatso_trigger4);
}
if ($whatso_time5 == 'select_hour') {
    $whatso_trigger5 = convertminutestohour($whatso_trigger5);
}
if ($whatso_time1 == 'select_day') {
    $whatso_trigger = convertminutestoday($whatso_trigger);
}
if ($whatso_time2 == 'select_day') {
    $whatso_trigger2 = convertminutestoday($whatso_trigger2);
}
if ($whatso_time3 == 'select_day') {
    $whatso_trigger3 = convertminutestoday($whatso_trigger3);
}
if ($whatso_time4 == 'select_day') {
    $whatso_trigger4 = convertminutestoday($whatso_trigger4);
}
if ($whatso_time5 == 'select_day') {
    $whatso_trigger5 = convertminutestoday($whatso_trigger5);
}

//update message text
$data = get_option('whatso_notifications');
$data = json_decode($data);
$whatso_username = $data->whatso_username;
$whatso_password = $data->whatso_password;
$whatso_mobileno = $data->whatso_mobileno;
$whatso_message = $data->whatso_message;
$customer_notification = $data->customer_notification;
$whatso_customer_message = $data->whatso_customer_message;
$whatso_ac_message = $data->whatso_ac_message;
$whatso_ac_message2 = $data->whatso_ac_message2;
$whatso_ac_message3 = $data->whatso_ac_message3;
$whatso_ac_message4 = $data->whatso_ac_message4;
$whatso_ac_message5 = $data->whatso_ac_message5;
$whatso_email = $data->whatso_email;


if (!empty($_POST)) {

    $legit = true;
    if (! isset($_POST['whatso_message_send_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_message_send_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_message_send_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'whatso_message_send') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    $message2 = isset($_POST['message2']) ? sanitize_textarea_field(wp_unslash($_POST['message2'])) : '';
    $message3 = isset($_POST['message3']) ? sanitize_textarea_field(wp_unslash($_POST['message3'])) : '';
    $message4 = isset($_POST['message4']) ? sanitize_textarea_field(wp_unslash($_POST['message4'])) : '';
    $message5 = isset($_POST['message5']) ? sanitize_textarea_field(wp_unslash($_POST['message5'])) : '';


    $whatso_trigger = isset($_POST['trigger_1']) ? sanitize_textarea_field(wp_unslash($_POST['trigger_1'])) : '';
    $whatso_trigger2 = isset($_POST['trigger_2']) ? sanitize_textarea_field(wp_unslash($_POST['trigger_2'])) : '';
    $whatso_trigger3 = isset($_POST['trigger_3']) ? sanitize_textarea_field(wp_unslash($_POST['trigger_3'])) : '';
    $whatso_trigger4 = isset($_POST['trigger_4']) ? sanitize_textarea_field(wp_unslash($_POST['trigger_4'])) : '';
    $whatso_trigger5 = isset($_POST['trigger_5']) ? sanitize_textarea_field(wp_unslash($_POST['trigger_5'])) : '';

    $whatso_time1 = isset($_POST['select_time1']) ? sanitize_textarea_field(wp_unslash($_POST['select_time1'])) : '';
    $whatso_time2 = isset($_POST['select_time2']) ? sanitize_textarea_field(wp_unslash($_POST['select_time2'])) : '';
    $whatso_time3 = isset($_POST['select_time3']) ? sanitize_textarea_field(wp_unslash($_POST['select_time3'])) : '';
    $whatso_time4 = isset($_POST['select_time4']) ? sanitize_textarea_field(wp_unslash($_POST['select_time4'])) : '';
    $whatso_time5 = isset($_POST['select_time5']) ? sanitize_textarea_field(wp_unslash($_POST['select_time5'])) : '';

    if ($whatso_time1 == 'select_hour') {
        $whatso_trigger = converthourtominutes($whatso_trigger);
    }
    if ($whatso_time2 == 'select_hour') {
        $whatso_trigger2 = converthourtominutes($whatso_trigger2);
    }
    if ($whatso_time3 == 'select_hour') {
        $whatso_trigger3 = converthourtominutes($whatso_trigger3);
    }
    if ($whatso_time4 == 'select_hour') {
        $whatso_trigger4 = converthourtominutes($whatso_trigger4);
    }
    if ($whatso_time5 == 'select_hour') {
        $whatso_trigger5 = converthourtominutes($whatso_trigger5);
    }
    if ($whatso_time1 == 'select_day') {
        $whatso_trigger = convertdaytominutes($whatso_trigger);
    }
    if ($whatso_time2 == 'select_day') {
        $whatso_trigger2 = convertdaytominutes($whatso_trigger2);
    }
    if ($whatso_time3 == 'select_day') {
        $whatso_trigger3 = convertdaytominutes($whatso_trigger3);
    }
    if ($whatso_time4 == 'select_day') {
        $whatso_trigger4 = convertdaytominutes($whatso_trigger4);
    }
    if ($whatso_time5 == 'select_day') {
        $whatso_trigger5 = convertdaytominutes($whatso_trigger5);
    }
    $message1_enable1 = "";
    $message2_enable2 = "";
    $message3_enable3 = "";
    $message4_enable4 = "";
    $message5_enable5 = "";

    if (isset($_POST['message1_enable'])) {
        $message1_enable1 = "checked";
    } else {
        $message1_enable1 = "";
    }
    if (isset($_POST['message2_enable'])) {
        $message2_enable2 = "checked";
    } else {
        $message2_enable2 = "";
    }
    if (isset($_POST['message3_enable'])) {
        $message3_enable3 = "checked";
    } else {
        $message3_enable3 = "";
    }
    if (isset($_POST['message4_enable'])) {
        $message4_enable4 = "checked";
    } else {
        $message4_enable4 = "";
    }
    if (isset($_POST['message5_enable'])) {
        $message5_enable5 = "checked";
    } else {
        $message5_enable5 = "";
    }
    if (empty($message)) {
        $message = "Hi We noticed you didn't finish your order on {storename}.\n\nVisit {siteurl} to complete your order.  \n\nThanks, {storename}.";
    }
    
    if (empty($message2)) {
        $message2 = "{customername}, You left some items in your cart!\n\nWe wanted to make sure you had the chance to get what you need. \n\nContinue shopping: {storename}";
    }
    if (empty($message3)) {
        $message3 = "Hi we see you left few items in the cart at {siteurl}. Your items are waiting for you! Grab your favorites before they go out of stock. \n\nYour friends from {storename}";
    }
    if (empty($message4)) {
        $message4 = "{customername}, Your cart is waiting for you at {siteurl}\n\nComplete your purchase before someone else buys them! Click {siteurl} to finish your order now.\n \nThanks!\n {storename}";
    }
    if (empty($message5)) {
        $message5="Hello Did you forget to complete your order on {siteurl}. \nJust click the link to finish the order!\n\nYour friends at {storename}";
    }

    if (!empty(get_option('whatso_abandoned'))) {
        $update_notifications_arr = array(
            'default_country' => $default_country,
            'admin_mobile'   =>  $whatso_mobile,
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
            'message1_enable' => $message1_enable1,
            'message2_enable' => $message2_enable2,
            'message3_enable' => $message3_enable3,
            'message4_enable' => $message4_enable4,
            'message5_enable' => $message5_enable5,
        );
        $result = update_option('whatso_abandoned', wp_json_encode($update_notifications_arr));
    }
    if (!empty(get_option('whatso_notifications'))) {
        $update_notifications_arr = array(
            'whatso_username'   =>  $whatso_username,
            'whatso_password'   =>  $whatso_password,
            'whatso_mobileno'   =>  $whatso_mobileno,
            'whatso_message'   =>  $whatso_message,
            'whatso_customer_message' => $whatso_customer_message,
            'customer_notification' => $customer_notification,
            'whatso_ac_message'    =>  $message,
            'whatso_ac_message2'    =>  $message2,
            'whatso_ac_message3'    =>  $message3,
            'whatso_ac_message4'    =>  $message4,
            'whatso_ac_message5'    =>  $message5,
            'whatso_email' => $whatso_email,
        );
        $result = update_option('whatso_notifications', wp_json_encode($update_notifications_arr));
    }
    echo wp_kses_post('<p class="mta" style="visibility:visible;"><font color="green" >Message updated successfully!</font></p>');
    wp_redirect("admin.php?page=whatso_floating_widget&tab=messages");
}
function converthourtominutes($hour)
{
    return$minutes = $hour * 60;

}
function convertminutestohour($hour)
{
    return $hour = $hour / 60;

}
function convertdaytominutes($day)
{
   return $minutes = $day * 1440;

}
function convertminutestoday($minutes)
{
    return $day = $minutes / 1440;

}
if (empty($whatso_ac_message)) {
    $whatso_ac_message = "Hi We noticed you didn't finish your order on {storename}.\n\nVisit {siteurl} to complete your order.  \n\nThanks, {storename}.";
}

if (empty($whatso_ac_message2)) {
    $whatso_ac_message2 = "{customername}, You left some items in your cart!\n\nWe wanted to make sure you had the chance to get what you need. \n\nContinue shopping: {storename}";
}
if (empty($whatso_ac_message3)) {
    $whatso_ac_message3 = "Hi we see you left few items in the cart at {siteurl}. Your items are waiting for you! Grab your favorites before they go out of stock. \n\nYour friends from {storename}";
}
if (empty($whatso_ac_message4)) {
    $whatso_ac_message4 = "{customername}, Your cart is waiting for you at {siteurl}\n\nComplete your purchase before someone else buys them! Click {siteurl} to finish your order now.\n \nThanks!\n {storename}";
}
if (empty($whatso_ac_message5)) {
    $whatso_ac_message5="Hello Did you forget to complete your order on {siteurl}. \nJust click the link to finish the order!\n\nYour friends at {storename}";
}
if (empty($whatso_trigger2)) {
    $whatso_trigger2 = "12";
    $whatso_time2 = "select_hour";
}
if (empty($whatso_trigger3)) {
    $whatso_trigger3 = "1";
    $whatso_time3 = "select_day";
}
if (empty($whatso_trigger4)) {
    $whatso_trigger4 = "2";
    $whatso_time4 = "select_day";
}
if (empty($whatso_trigger5)) {
    $whatso_trigger5 = "3";
    $whatso_time5 = "select_day";
}

?>
<div class="container">
    <form method="post" name="form1" class="form-content" action="">
        <div class="form-body">
            <div class="row">
                <div class="col-md-12 messages-box">
                    <p class="mt-3 font-16"><strong><?php esc_html_e('You can send up to 5 messages for an abandoned cart. Type the message you want to send in the below textbox and select the duration. The duration is calculated from the time of abandoning of cart. Select the checkbox to enable the message.','whatso')?></strong></p>
                    <div class="card">
                        <div class="card-body">
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label><?php esc_html_e('Message 1','whatso');?></label>
                                        <div  <?php if($isCustomizMessageOfAbandoned!="true"){?> title="update to paid plan" data-toggle="tooltip"<?php }?> >
                                        <textarea required class="form-control t" name="message" id="message" rows="3" <?php if($isCustomizMessageOfAbandoned!="true"){?> title="update to paid plan" data-toggle="tooltip" disabled="disable"<?php }?> ><?php echo esc_html($whatso_ac_message); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="container-fluid border-1">
                                            <label><?php esc_html_e('Schedule Time','whatso');?></label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="text" name="trigger_1" id="trigger_1" required class="form-control" value="<?php echo esc_html($whatso_trigger); ?>" onkeypress="return isNumber(event)" />
                                                </div>
                                                <div class="col-6">
                                                    <select class="form-control" name="select_time1" value="select_time1" required>
                                                        <option value="select_minute" name="select_time1" id="select_minute" <?php echo esc_html('select_minute') === esc_attr($whatso_time1) ? 'selected' : ''; ?>><?php esc_html_e('Minute', 'whatso'); ?></option>
                                                        <option value="select_hour" name="select_time1" id="select_hour" <?php echo esc_html('select_hour') === esc_attr($whatso_time1) ? 'selected' : ''; ?>><?php esc_html_e('Hour', 'whatso'); ?></option>
                                                        <option value="select_day" name="select_time1" id="select_day" <?php echo esc_html('select_day') === esc_attr($whatso_time1) ? 'selected' : ''; ?>><?php esc_html_e('Day', 'whatso'); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label><?php esc_html_e('Enable Message:','whatso');?> <input type="checkbox" name="message1_enable" id="message1_enable" value="checked" <?php if($multiple_messages >= '1'){ ?> checked="checked" <?php }else{?> disabled="disable" title="update to paid plan" data-toggle="tooltip" <?php } ?>  readonly></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 messages-box">
                    <div class="card">
                        <div class="card-body">
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label><?php esc_html_e('Message 2','whatso');?></label>
                                        <div  <?php if($isCustomizMessageOfAbandoned!="true"){?> title="update to paid plan" data-toggle="tooltip"<?php }?> >
                                        <textarea required class="form-control" name="message2" id="message2" rows="3" <?php if ($message2_enable != "checked") { ?> readonly="readonly" <?php } ?> <?php if($isCustomizMessageOfAbandoned!="true"){?>disabled="disable" title="update to paid plan" data-toggle="tooltip"<?php }?> ><?php echo esc_html($whatso_ac_message2); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="container-fluid border-1">
                                            <label><?php esc_html_e('Schedule Time','whatso');?></label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="text" name="trigger_2" id="trigger_2" required class="form-control" onkeypress="return isNumber(event)" value="<?php echo esc_html($whatso_trigger2); ?>" <?php if ($message2_enable != "checked") { ?> readonly="readonly" <?php } ?> />
                                                </div>
                                                <div class="col-6">
                                                    <select class="form-control" name="select_time2" value="select_time2" required <?php if ($message2_enable != "checked") { ?> readonly="readonly" <?php } ?>>
                                                        <option value="select_minute" name="select_time2" id="select_minute" <?php echo esc_html('select_minute') === esc_attr($whatso_time2) ? 'selected' : ''; ?>><?php esc_html_e('Minute', 'whatso'); ?></option>
                                                        <option value="select_hour" name="select_time2" id="select_hour" <?php echo esc_html('select_hour') === esc_attr($whatso_time2) ? 'selected' : ''; ?>><?php esc_html_e('Hour', 'whatso'); ?></option>
                                                        <option value="select_day" name="select_time2" id="select_day" <?php echo esc_html('select_day') === esc_attr($whatso_time2) ? 'selected' : ''; ?>><?php esc_html_e('Day', 'whatso'); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label> <?php esc_html_e('Enable Message:','whatso');?> <input type="checkbox" name="message2_enable" id="message2_enable" value="checked" <?php if($multiple_messages >= '2'){ if ($message2_enable == "checked") { ?> checked="checked" <?php }}else{?> disabled="disable" title="update to paid plan" data-toggle="tooltip" <?php } ?> /></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 messages-box">
                    <div class="card">
                        <div class="card-body">
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label><?php esc_html_e('Message 3','whatso');?></label>
                                        <div  <?php if($isCustomizMessageOfAbandoned!="true"){?> title="update to paid plan" data-toggle="tooltip"<?php }?> >
                                        <textarea required class="form-control" name="message3" id="message3" rows="3" <?php if ($message3_enable != "checked") { ?> readonly="readonly" <?php } ?> <?php if($isCustomizMessageOfAbandoned!="true"){?>disabled="disable" title="update to paid plan" data-toggle="tooltip"<?php }?> ><?php echo esc_html($whatso_ac_message3); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="container-fluid border-1">
                                            <label><?php esc_html_e('Schedule Time','whatso');?></label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="text" required class="form-control" name="trigger_3" id="trigger_3" onkeypress="return isNumber(event)" value="<?php echo esc_html($whatso_trigger3); ?>" <?php if ($message3_enable != "checked") { ?> readonly="readonly" <?php } ?> />
                                                </div>
                                                <div class="col-6">
                                                    <select class="form-control" name="select_time3" value="select_time3" <?php if ($message3_enable != "checked") { ?> readonly="readonly" <?php } ?> required>
                                                        <option value="select_minute" name="select_time3" id="select_minute" <?php echo esc_html('select_minute') === esc_attr($whatso_time3) ? 'selected' : ''; ?>><?php esc_html_e('Minute', 'whatso'); ?></option>
                                                        <option value="select_hour" name="select_time3" id="select_hour" <?php echo esc_html('select_hour') === esc_attr($whatso_time3) ? 'selected' : ''; ?>><?php esc_html_e('Hour', 'whatso'); ?></option>
                                                        <option value="select_day" name="select_time3" id="select_day" <?php echo esc_html('select_day') === esc_attr($whatso_time3) ? 'selected' : ''; ?>><?php esc_html_e('Day', 'whatso'); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label><?php esc_html_e('Enable Message:','whatso');?> <input type="checkbox" name="message3_enable" id="message3_enable" value="checked" <?php if($multiple_messages >= '3'){ if ($message3_enable == "checked") { ?> checked="checked" <?php }}else{?> disabled="disable" title="update to paid plan" data-toggle="tooltip" <?php } ?> ></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 messages-box">
                    <div class="card">
                        <div class="card-body">
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label><?php esc_html_e('Message 4','whatso');?></label>
                                        <div  <?php if($isCustomizMessageOfAbandoned!="true"){?> title="update to paid plan" data-toggle="tooltip"<?php }?> >
                                        <textarea required class="form-control" name="message4" id="message4" rows="3" <?php if ($message4_enable != "checked") { ?> readonly="readonly" <?php } ?> <?php if($isCustomizMessageOfAbandoned!="true"){?>disabled="disable" title="update to paid plan" data-toggle="tooltip"<?php }?> ><?php echo esc_html($whatso_ac_message4); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="container-fluid border-1">
                                            <label><?php esc_html_e('Schedule Time','whatso');?></label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="text" required class="form-control" name="trigger_4" id="trigger_4" onkeypress="return isNumber(event)" value="<?php echo esc_html($whatso_trigger4); ?>" <?php if ($message4_enable != "checked") { ?> readonly="readonly" <?php } ?> />
                                                </div>
                                                <div class="col-6">
                                                    <select class="form-control" name="select_time4" value="select_time4" <?php if ($message4_enable != "checked") { ?> readonly="readonly" <?php } ?> required>
                                                        <option value="select_minute" name="select_time4" id="select_minute" <?php echo esc_html('select_minute') === esc_attr($whatso_time4) ? 'selected' : ''; ?>><?php esc_html_e('Minute', 'whatso'); ?></option>
                                                        <option value="select_hour" name="select_time4" id="select_hour" <?php echo esc_html('select_hour') === esc_attr($whatso_time4) ? 'selected' : ''; ?>><?php esc_html_e('Hour', 'whatso'); ?></option>
                                                        <option value="select_day" name="select_time4" id="select_day" <?php echo esc_html('select_day') === esc_attr($whatso_time4) ? 'selected' : ''; ?>><?php esc_html_e('Day', 'whatso'); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label><?php esc_html_e('Enable Message:','whatso');?> <input type="checkbox" name="message4_enable" id="message4_enable" value="checked" <?php if($multiple_messages >= '4'){ if ($message4_enable == "checked") { ?> checked="checked" <?php }}else{?> disabled="disable" title="update to paid plan" data-toggle="tooltip" <?php } ?> ></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 messages-box">
                    <div class="card">
                        <div class="card-body">
                            <div class="container-fluid p-0">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label><?php esc_html_e('Message 5','whatso');?></label>
                                        <div  <?php if($isCustomizMessageOfAbandoned!="true"){?> title="update to paid plan" data-toggle="tooltip"<?php }?> >
                                        <textarea required class="form-control" name="message5" id="message5" rows="3" <?php if ($message5_enable != "checked") { ?> readonly="readonly" <?php } ?> <?php if($isCustomizMessageOfAbandoned!="true"){?>disabled="disable" title="update to paid plan" data-toggle="tooltip"<?php }?> ><?php echo esc_html($whatso_ac_message5); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="container-fluid border-1">
                                            <label><?php esc_html_e('Schedule Time','whatso');?></label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="text" required name="trigger_5" id="trigger_5" class="form-control" onkeypress="return isNumber(event)" value="<?php echo esc_html($whatso_trigger5); ?>" <?php if ($message5_enable != "checked") { ?> readonly="readonly" <?php } ?> />
                                                </div>
                                                <div class="col-6">
                                                    <select class="form-control" name="select_time5" value="select_time5" <?php if ($message5_enable != "checked") { ?> readonly="readonly" <?php } ?> required>
                                                        <option value="select_minute" name="select_time5" id="select_minute" <?php echo esc_html('select_minute') === esc_attr($whatso_time5) ? 'selected' : ''; ?>><?php esc_html_e('Minute', 'whatso'); ?></option>
                                                        <option value="select_hour" name="select_time5" id="select_hour" <?php echo esc_html('select_hour') === esc_attr($whatso_time5) ? 'selected' : ''; ?>><?php esc_html_e('Hour', 'whatso'); ?></option>
                                                        <option value="select_day" name="select_time5" id="select_day" <?php echo esc_html('select_day') === esc_attr($whatso_time5) ? 'selected' : ''; ?>><?php esc_html_e('Day', 'whatso'); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label><?php esc_html_e('Enable Message:','whatso');?> <input type="checkbox" name="message5_enable" id="message5_enable" value="checked" <?php if($multiple_messages == '5'){ if ($message5_enable == "checked") { ?> checked="checked" <?php }}else{?> disabled="disable" title="update to paid plan" data-toggle="tooltip" <?php } ?> ></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($isCustomizMessageOfAbandoned!="false"){?>
                <div class="col-md-12">
                    <div class="my-3">
                        <button type="button" class="placeholder_button" onclick="copyText(event,'{storename}')"><?php esc_html_e('Store Name', 'whatso'); ?></button>
                        <button type="button" class="placeholder_button" onclick="copyText(event,'{productname}')"><?php esc_html_e('Product Name', 'whatso'); ?></button>
                        <button type="button" class="placeholder_button" onclick="copyText(event,'{siteurl}')"><?php esc_html_e('Site URL', 'whatso'); ?></button>
                        <button type="button" class="placeholder_button" onclick="copyText(event,'{orderdate}')"><?php esc_html_e('Order Date', 'whatso'); ?></button>
                        <button type="button" class="placeholder_button" onclick="copyText(event,'{amountwithcurrency}')"><?php esc_html_e('Amount With Currency', 'whatso'); ?></button>
                        <button type="button" class="placeholder_button" onclick="copyText(event,'{customernumber}')"><?php esc_html_e('Customer Number', 'whatso'); ?></button>
                        <button type="button" class="placeholder_button" onclick="copyText(event,'{customeremail}')"><?php esc_html_e('Customer Email', 'whatso'); ?></button>
                        <button type="button" class="placeholder_button" onclick="copyText(event,'{checkoutlink}')"><?php esc_html_e('Checkout Link', 'whatso'); ?></button>
                    </div>
                </div>
                <?php }?>
                <?php wp_nonce_field('whatso_message_send', 'whatso_message_send_nonce'); ?>
                <div class="col-md-12 text-center mt-4">
                    <button type="submit" class="btn btn-theme">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    jQuery(function() {
        jQuery('[data-toggle="tooltip"]').tooltip()
    })

    jQuery(".form-content").submit(function() {
        var values = [];
        jQuery(".messages-box").each(function() {
            var int = jQuery(this).find('input').val();
            var val = jQuery(this).find('select').val();
            if (int != undefined && int != null && int != '') {
                if (val == 'select_hour') {
                    int = int * 60;
                } else if (val == 'select_day') {
                    int = int * 1440;
                }
                values.push(parseInt(int));
            }
        })
        for (var i = 0; i < values.length; i++) {
            for (var j = 0; j < values.length; j++) {
                if (i <= j) {
                    if (values[i] > values[j]) {
                        alert('All schedule time are greater then previous values')
                        return false;
                    }
                }
            }
        }
    })

    function isNumber(evt) {

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
    /**
     * Add placeholder on text area
     */

    function getInputSelection(el) {
        var start = 0,
            end = 0,
            normalizedValue, range, textInputRange, len, endRange;
        if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
            start = el.selectionStart;
            end = el.selectionEnd;
        } else {
            range = document.selection.createRange();

            if (range && range.parentElement() == el) {
                len = el.value.length;
                normalizedValue = el.value.replace(/\r\n/g, "\n");

                // Create a working TextRange that lives only in the input
                textInputRange = el.createTextRange();
                textInputRange.moveToBookmark(range.getBookmark());

                // Check if the start and end of the selection are at the very end
                // of the input, since moveStart/moveEnd doesn't return what we want
                // in those cases
                endRange = el.createTextRange();
                endRange.collapse(false);

                if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                    start = end = len;
                } else {
                    start = -textInputRange.moveStart("character", -len);
                    start += normalizedValue.slice(0, start).split("\n").length - 1;

                    if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                        end = len;
                    } else {
                        end = -textInputRange.moveEnd("character", -len);
                        end += normalizedValue.slice(0, end).split("\n").length - 1;
                    }
                }
            }
        }
        return {
            start: start,
            end: end
        };
    }

    function offsetToRangeCharacterMove(el, offset) {
        return offset - (el.value.slice(0, offset).split("\r\n").length - 1);
    }

    function setSelection(el, start, end) {
        if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
            el.selectionStart = start;
            el.selectionEnd = end;
        } else if (typeof el.createTextRange != "undefined") {
            var range = el.createTextRange();
            var startCharMove = offsetToRangeCharacterMove(el, start);
            range.collapse(true);
            if (start == end) {
                range.move("character", startCharMove);
            } else {
                range.moveEnd("character", offsetToRangeCharacterMove(el, end));
                range.moveStart("character", startCharMove);
            }
            range.select();
        }
    }

    function insertTextAtCaret(el, text) {
        var pos = getInputSelection(el).end;
        var newPos = pos + text.length;
        var val = el.value;
        el.value = val.slice(0, pos) + text + val.slice(pos);
        setSelection(el, newPos, newPos);
    }

    function add_placeholder(text_area_id, placeholder) {
        var textarea = document.getElementById(text_area_id);
        textarea.focus();
        insertTextAtCaret(textarea, placeholder);
        return false;
    }
    jQuery("input[type='checkbox']").change(function() {
        if (jQuery(this).is(':checked') == true) {
            jQuery(this).closest('.card').find('textarea').removeAttr('readonly');
            jQuery(this).closest('.card').find('input[type="text"]').removeAttr('readonly');
            jQuery(this).closest('.card').find('select').removeAttr('readonly');
        } else {
            jQuery(this).closest('.card').find('textarea').attr('readonly', 'readonly');
            jQuery(this).closest('.card').find('input[type="text"]').attr('readonly', 'readonly');
            jQuery(this).closest('.card').find('select').attr('readonly', 'readonly');
        }
        var i = 0;
        jQuery("input[type='checkbox']").each(function(index) {
            if (jQuery(this).is(':checked') == true) {
                if (i != index) {
                    jQuery(this).prop('checked', false);
                    alert("Not Allowed. Please select in sequence");
                    jQuery(this).closest('.card').find('textarea').attr('readonly', 'readonly');
                    jQuery(this).closest('.card').find('input[type="text"]').attr('readonly', 'readonly');
                    jQuery(this).closest('.card').find('select').attr('readonly', 'readonly');
                };
                i++;
            }
        })
    });

    function getApprovedSMSTemplate1() {
        var copyText = document.getElementById("myText2");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        var toolTipText = document.getElementById("msgCopied1");
        document.getElementById('msgCopied1').innerHTML = "Message Copied!";
        toolTipText.classList.add("show");
        document.execCommand("copy");
        setTimeout(function() {
            toolTipText.classList.remove("show");
        }, 1000);
    }

    function copyText(event, text) {
        navigator.clipboard.writeText(text);
        jQuery('#copied').remove();
        jQuery(event.currentTarget).append('<span style="position: fixed;top: ' + (event.y + 25) + 'px;left: ' + (event.x - 20) +
            'px;background: #333;color: #FFF;border-radius: 10px;padding: 5px 10px;" id="copied">Copied</span>');
        setTimeout(function() {
            jQuery('#copied').remove()
        }, 1000);
    }

    function FormValidation() {
        var owner_message = document.getElementById("message").value;
        var word = "{#var#}";
        if (owner_message.indexOf(word) != -1) {
            event.preventDefault ? event.preventDefault() : event.returnValue = false;
            document.getElementById('message_error1').style.display = 'block';

            return false;
        } else {
            document.getElementById('message_error1').style.display = 'none';

            return true;

        }
    }
</script>