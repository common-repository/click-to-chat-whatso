<!DOCTYPE html>
<html lang="en">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<?php

//User data
$data = get_option('whatso_user_settings');
$data = json_decode($data);
$isOrderNotificationToAdmin = $data->isOrderNotificationToAdmin;
$isCustomizeMessageToAdmin = $data->isCustomizeMessageToAdmin;
$isOrderNotificationToCustomer = $data->isOrderNotificationToCustomer;
$isCustomizMessageToCustomer = $data->isCustomizMessageToCustomer;
$isCustomizMessageOfAbandoned = $data->isCustomizMessageOfAbandoned;
$multiple_messages = $data->multiple_messages;
$isMessageFromAdminNumber = $data->isMessageFromAdminNumber;
$official_number = $data->official_number;
$isDisplayReport = $data->isDisplayReport;



/**
 * Add or update notfication field
 */

if (isset($_POST) && !empty($_POST)) {
    $legit = true;
    if (! isset($_POST['whatso_notification_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_notification_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_notification_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'whatso_notification') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }


    $mobileno = isset($_POST['mobileno']) ? sanitize_text_field(wp_unslash($_POST['mobileno'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    $whatso_customer_message = isset($_POST['whatso_customer_message']) ? sanitize_textarea_field(wp_unslash($_POST['whatso_customer_message'])) : '';

    if (isset($_POST['customer_checkbox'])) {
        $customer_notification = "1";
    } else {
        $customer_notification = "0";
    }


    $update_notifications_arr = array();
    $flag = 1;

    if (empty($mobileno)) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please Enter Mobile Number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    } elseif (strlen($mobileno) < 12) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please enter 12 digit number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    } else {
        $numbers = explode(',', $mobileno);
        $numbers = array_filter($numbers);
        $numbers = array_map('trim', $numbers);
        $error = 0;
        $inValidNumbers = array();
        foreach ($numbers as $number) {
            if (is_numeric($number)) {
                if (strlen($number) < 12) {
                    $error++;
                    array_push($inValidNumbers, $number);
                }
            } else {
                $error++;
                array_push($inValidNumbers, $number);
                $flag = 0;
                $error_message = '';
                $error_message .= '<div class="notice notice-error is-dismissible">';
                $error_message .= '<p>' . esc_html('Please enter valid number') . ' ' . implode(", ", $inValidNumbers) . '</p>';
                $error_message .= '</div>';
                echo wp_kses_post($error_message);
            }
        }
        if ($error != 0) {
            $flag = 0;
            $error_message = '';
            $error_message .= '<div class="notice notice-error is-dismissible">';
            $error_message .= '<p>' . esc_html('Please enter 12 digit number of') . ' ' . implode(", ", $inValidNumbers) . '</p>';
            $error_message .= '</div>';
            echo wp_kses_post($error_message);
        }
        if (count($numbers) > 10) {
            $flag = 0;
            $error_message = '';
            $error_message .= '<div class="notice notice-error is-dismissible">';
            $error_message .= '<p>' . esc_html('You cannot enter more then 10 numbers') . '</p>';
            $error_message .= '</div>';
            echo wp_kses_post($error_message);
        }
    }
    if (empty($message) || strlen($message) < 2) {
        $flag = 0;
        $error_message = '';
        $error_message .= '<div class="notice notice-error is-dismissible">';
        $error_message .= '<p>' . esc_html('Your message must be atleast 2 characters.') . '</p>';
        $error_message .= '</div>';
        echo wp_kses_post($error_message);
    }
    if (empty($whatso_customer_message) || strlen($whatso_customer_message) < 2) {
        $flag = 0;
        $error_message = '';
        $error_message .= '<div class="notice notice-error is-dismissible">';
        $error_message .= '<p>' . esc_html('Your message must be atleast 2 characters.') . '</p>';
        $error_message .= '</div>';
        echo wp_kses_post($error_message);
    }
    if ($flag == 1) {
        $mobileno = implode(", ", $numbers);
        $data = get_option('whatso_notifications');
        $data = json_decode($data);
        $whatso_username = $data->whatso_username;
        $whatso_password = $data->whatso_password;

        $whatso_ac_message = $data->whatso_ac_message;
        $whatso_ac_message2 = $data->whatso_ac_message2;

        $whatso_ac_message3 = $data->whatso_ac_message3;
        $whatso_ac_message4 = $data->whatso_ac_message4;
        $whatso_ac_message5 = $data->whatso_ac_message5;
        $whatso_email = $data->whatso_email;
        $update_notifications_arr = array(
            'whatso_username'   =>  $whatso_username,
            'whatso_password'   =>  $whatso_password,
            'whatso_mobileno'   =>  $mobileno,
            'whatso_message'    =>  $message,
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
        if ($result) {
            $success = '';
            $success .= '<div class="notice notice-success is-dismissible">';
            $success .= '<p>' . esc_html('Details update successfully.') . '</p>';
            $success .= '</div>';
            echo wp_kses_post($success);
        }
    }
}
/**
 * Get data of field
 */
$data = '';
$whatso_username = '';
$whatso_password = '';
$whatso_mobileno = '';
$whatso_message = '';
$whatso_customer_message = '';
$customer_notification = '';
if (!empty(get_option('whatso_notifications'))) {
    $data = get_option('whatso_notifications');
    $data = json_decode($data);
    $whatso_username = $data->whatso_username;
    $whatso_password = $data->whatso_password;
    $whatso_mobileno = $data->whatso_mobileno;
    $whatso_message = $data->whatso_message;
    $customer_notification = $data->customer_notification;
    $whatso_ac_message = $data->whatso_ac_message;
    $whatso_ac_message2 = $data->whatso_ac_message2;
    $whatso_ac_message3 = $data->whatso_ac_message3;
    $whatso_ac_message4 = $data->whatso_ac_message4;
    $whatso_ac_message5 = $data->whatso_ac_message5;

    $whatso_email = $data->whatso_email;
    $whatso_customer_message = $data->whatso_customer_message;
}
if (empty($whatso_message)) {
    $whatso_message = "hi, an order is placed on {storename} at {orderdate}.\n\nThe order is for {productname} and order amount is {amountwithcurrency}.\n\nCustomer details are: {customernumber}\n\n{customeremail}";
}
if (empty($whatso_customer_message)) {
    $whatso_customer_message = "{customername}, your {productname} order of {amountwithcurrency} has been placed. \n\nWe will keep you updated about your order status.\n\n{storename}";
}


//for display report
global $wpdb;
    $table= $wpdb->prefix . "whatso_order_notification";
    $query = $wpdb->prepare("SELECT * FROM $table");
    $Response_string = $wpdb->get_results($query);

    $reports_array = array();

foreach($Response_string as $key => $value){

    $single_record = array();
    $user_type=$value->user_type;
    $create_date_time=$value->create_date_time;
    $date = date_create($create_date_time);
    $create_date_time= date_format($date, 'd-M-Y H:i');
    $json_data = json_decode($value->message_api_response);

    if(is_array($json_data)) {

        $array_length = count($json_data);

        if($array_length > 0) {

            $json_data = array_reverse($json_data);

            for($i=0;$i<$array_length;$i++){
                $single_record = array();
                    

                $single_record['create_date_time'] = $create_date_time;
                $single_record['user_type']=$user_type;
                $message_text = '';
                $mobile_numbers = '';
                    
                if($json_data[$i]->ErrorCode == '200') {
                    $message_text = $json_data[$i]->MessageText;
                    $mobile_numbers = $json_data[$i]->MobileNumbers;
                    $single_record['message_text'] = $message_text;
                    $single_record['mobile_numbers'] = $mobile_numbers;
                        
                    array_push($reports_array, $single_record);
                }
            }
        }

    }else{

           

        $single_record['create_date_time'] = $create_date_time;
        $single_record['user_type']=$user_type;
        $message_text = '';
        $mobile_numbers = '';
            
        if($json_data->ErrorCode == '200') {                
            $message_text = $json_data->MessageText;
            $mobile_numbers = $json_data->MobileNumbers;
            $single_record['message_text'] = $message_text;
            $single_record['mobile_numbers'] = $mobile_numbers;
            array_push($reports_array, $single_record);
        }
    }
}
$img_url = plugin_dir_url(__DIR__);
$logo = $img_url . 'assets/images/whatsoLogoNew_black.png';

?>
 <script>
     jQuery(document).ready(function() {
         jQuery('#datatable').DataTable({
        "order": [[ 3, "desc" ]],
    } );
    
} );
    </script>
<body>
    <div class="container">
        <div>
            <img src="<?php echo esc_url($logo);?>" class="imgclass" alt="">

        </div>

        <ul class="breadcrumb">
            <li><a href="admin.php?page=whatso_floating_menu_setup"><b>Whatso</b></a></li>
            <li><b><?php esc_html_e('Order notification setup','whatso');?></b></li>
        </ul>
        <div class="tabbable boxed parentTabs">
            <div id="setting_tabs">
                <ul class="nav nav-tabs">
                    <li>
                        <a href="#set1" class="active"><?php esc_html_e('Settings','whatso');?></a>
                    </li>
                    <li><a href="#set2"><?php esc_html_e('Report','whatso');?></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade active in show" id="set1">

                    <div class="box">
                        <form class="form_div" method="post">
                            <div class="mb-2 mt-4">
                                <label><?php esc_html_e('WhatsApp number with country code ', 'whatso'); ?></label><span style="margin-top: 24px;margin-left: 2px; color: rgb(241, 6, 6);font-size: 16px;">*</span>
                                <label class="lbl1">(You will receive notifications on this number)</label>
                            </div>

                            <div class="mb-2">
                                <input type="text" name="mobileno" id="mobileno" autocomplete="off" maxlength="200" placeholder="Enter Mobile Number with country code. Do not prefix with a 0 or +" class="text_input form-control" value="<?php echo esc_html($whatso_mobileno); ?>" <?php if ($isOrderNotificationToAdmin != "true") { ?> readonly title="update to paid plan" data-toggle="tooltip" <?php 
                                                                                                                                                                                                                                            } ?> />
                                <lable class="error" id="mobile_error"><?php esc_html_e('Please enter 12 digit number.', 'whatso'); ?></lable>
                                <lable class="error" id="mobile_number_error"><?php esc_html_e('Please Enter only Number.', 'whatso'); ?></lable>
                            </div>

                            <div class="mb-2">
                                <label class="lbl1"><?php esc_html_e('Message to Site Owner ', 'whatso'); ?></label><span style="margin-top: 24px;margin-left: 2px; color: rgb(241, 6, 6);font-size: 16px;">*</span>
                            </div>

                            <div class="mb-2">
                                <textarea class="form-control message" name="message" id="message" autocomplete="off" maxlength="1500" placeholder="Enter message that you want to be sent when the order is placed." <?php if ($isCustomizeMessageToAdmin != "true") { ?>readonly title="update to paid plan" data-toggle="tooltip" <?php 
                                                                                                                                                                                                                      } ?>><?php echo esc_html($whatso_message); ?></textarea>
                                <lable class="error" id="message_error"><?php esc_html_e('Your message must be atleast 2 characters.', 'whatso'); ?></lable>
                                <lable class="error" id="message_variable_error"><?php esc_html_e('Replace {#var#} with your data.', 'whatso'); ?></lable>
                            </div>
                            <?php if ($isCustomizeMessageToAdmin != "false") { ?>
                                <div class="mb-2">
                                    <label class="lbl1"><?php esc_html_e('Use below placeholder fields to dynamically add your order details in the WhatsApp message', 'whatso'); ?></label>
                                </div>

                                <div class="mb-2">

                                    <div class="placeholder_panal">
                                        <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{storename}' )"><?php esc_html_e('Store name', 'whatso'); ?></button>
                                        <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{orderdate}' )"><?php esc_html_e('Order date', 'whatso'); ?></button>
                                        <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{productname}' )"><?php esc_html_e('Product name', 'whatso'); ?></button>
                                        <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{amountwithcurrency}' )"><?php esc_html_e('Amount with currency', 'whatso'); ?></button><br />
                                        <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{customeremail}' )"><?php esc_html_e('Customer email', 'whatso'); ?></button>
                                        <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{customernumber}' )"><?php esc_html_e('Customer number', 'whatso'); ?></button>
                                        <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{billingcity}' )"><?php esc_html_e('Billing city', 'whatso'); ?></button>
                                        <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{billingstate}' )"><?php esc_html_e('Billing state', 'whatso'); ?></button>
                                        <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{billingcountry}' )"><?php esc_html_e('Billing country', 'whatso'); ?></button>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="mb-2">
                                <?php if ($customer_notification == '1') { ?>
                                    <input type="checkbox" id="customer_checkbox" name="customer_checkbox" value="customer_checkbox" <?php if ($isOrderNotificationToCustomer != "true") { ?> disabled="disabled" <?php 
                                                                                                                                     } ?> checked />


                                <?php } else {  ?>
                                    <input type="checkbox" id="customer_checkbox" name="customer_checkbox" value="customer_checkbox" <?php if ($isOrderNotificationToCustomer != "true") { ?> disabled="disabled" <?php 
                                                                                                                                     } ?> />

                                <?php } ?>
                                <label class="lbl1" style="margin-left: 9px;" <?php if ($isOrderNotificationToCustomer != "true") { ?>title="update to paid plan" data-toggle="tooltip" <?php 
                                                                              } ?>> Do you want to send order notification to your customer on WhatsApp?</label>

                            </div>
                            <div id="displayCustomerMsgDiv" class="d-none">
                                <div class="mb-2">
                                    <label class="lbl1"><?php esc_html_e('Message text to Customer ', 'whatso'); ?></label><span style="margin-top: 24px;margin-left: 2px; color: rgb(241, 6, 6);font-size: 16px;">*</span>
                                </div>

                                <div class="mb-2">
                                    <textarea class="form-control message" name="whatso_customer_message" id="whatso_customer_message" autocomplete="off" maxlength="1500" <?php if ($isCustomizMessageToCustomer != "true") { ?>readonly title="update to paid plan" data-toggle="tooltip" <?php 
                                                                                                                                                                           } ?>><?php echo esc_html($whatso_customer_message); ?></textarea>
                                    <label class="error" id="message_error1"><?php esc_html_e('Your message must be atleast 2 characters.', 'whatso'); ?></label>
                                    <label class="error" id="variable_error"><?php esc_html_e('Replace {#var#} with your data.', 'whatso'); ?></label>

                                </div>
                                <?php if ($isCustomizMessageToCustomer != "false") { ?>
                                    <div class="mb-2">
                                        <label class="lbl1"><?php esc_html_e('Use below placeholder fields to dynamically add your order details in the WhatsApp message', 'whatso'); ?></label>
                                    </div>

                                    <div class="mb-2">
                                        <div class="placeholder_panal">
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{customername}' )"><?php esc_html_e('Customer name', 'whatso'); ?></button>
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{storename}' )"><?php esc_html_e('Store name', 'whatso'); ?></button>
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{orderdate}' )"><?php esc_html_e('Order date', 'whatso'); ?></button>
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{productname}' )"><?php esc_html_e('Product name', 'whatso'); ?></button>
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{amountwithcurrency}' )"><?php esc_html_e('Amount with currency', 'whatso'); ?></button><br />
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{customeremail}' )"><?php esc_html_e('Customer email', 'whatso'); ?></button>
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{customernumber}' )"><?php esc_html_e('Customer number', 'whatso'); ?></button>
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{billingcity}' )"><?php esc_html_e('Billing city', 'whatso'); ?></button>
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{billingstate}' )"><?php esc_html_e('Billing state', 'whatso'); ?></button>
                                            <button type="button" class="placeholder_button" onclick="add_placeholder( 'whatso_customer_message', '{billingcountry}' )"><?php esc_html_e('Billing country', 'whatso'); ?></button>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php wp_nonce_field('whatso_notification', 'whatso_notification_nonce'); ?>
                            <div class="mb-2 text-center">
                                <input type="submit" class="btn btn-theme" name="notification_submit" value="Submit" onclick="submitfunction()" />
                            </div>

                            <div class="mb-2">
                                <p class="note"><?php esc_html_e('Note:', 'whatso'); ?></p>
                                <ol class="list-items">
                                    <li><?php esc_html_e('This form helps you to setup configuration for sending a WhatsApp message to the website-owner / adminstrator and the customer for every successful order.','whatso');?></li>
                                    <li><?php esc_html_e('In the mobile number field, you need to enter the number of the web-administrator or the founder of the store. You can add upto 10 numbers separated by a comma.','whatso');?></li>
                                    <li><?php esc_html_e('The message field contains the message that will be sent when an order is successfully placed. You can use the placeholder keywords to set dynamic message content.','whatso');?></li>
                                </ol>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="tab-pane fade" id="set2">
                    <?php
                  

                    if($Response_string==null || empty($reports_array)) {
                        ?>
                <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card" style="width:fit-content;">
                                <div class="row card-body">

                                    <h5 class="dashboardmsg">
                                        <?php esc_html_e('Looks like you do not have any orders yet.But do not worry, as soon as someone place order, the message will automatically appear here.','whatso');?>
                                    </h5>

                                </div>
                            </div>
                        </div>
                </div>
                        <?php
                    }else{
                        ?>
                    <div class="mt-4">
                <table id="datatable"class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>

                            <th><?php echo esc_attr('Name');?></th>
                            <th><?php echo esc_attr('Contact No.');?></th>
                            <th><?php echo esc_attr('Message');?></th>
                            <th><?php echo esc_attr('Date/Time');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sno=0;
                        foreach($reports_array as $key => $value){
                            $sno++;
                            ?> 
                        <tr>

                            <td><?php echo esc_attr($value['user_type']); ?></td>
                            <td><?php echo esc_attr($value['mobile_numbers']); ?></td>
                            <td><?php echo wp_kses_post($value['message_text']); ?></td>
                            <td><?php echo esc_attr($value['create_date_time']); ?></td>
                            
                        </tr>
                        
                        <?php } ?>
                    <tbody>
                </table>
                    </div>
                        <?php
                    }
                
                    ?>
                </div>
            </div>
        </div>
    </div>
        
</body>



    <script>
        jQuery("ul.nav-tabs a").click(function(e) {
        e.preventDefault();
            jQuery(this).tab('show');
    });
        jQuery(function() {
            jQuery('[data-toggle="tooltip"]').tooltip()
        })
        let mobileno = document.querySelector('#mobileno');


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
                document.getElementById('mobile_number_error').style.display = 'block';
                return false
            } else {
                document.getElementById('mobile_number_error').style.display = 'none';
                return true;
            }

        }
        mobileno.onkeyup = function() {
            if (this.value.length < 12 || this.value.length > 159) {
                document.getElementById('mobile_error').style.display = 'block';
                return false;
            } else {
                // document.getElementById('mobile_number_error').style.display = 'none';
                document.getElementById('mobile_error').style.display = 'none';
                return true;
            }
        }

        message.onkeyup = function() {
            if (this.value.length < 2 || this.value.length > 1500) {
                document.getElementById('message_error').style.display = 'block';
                return false;
            } else {
                // document.getElementById('mobile_number_error').style.display = 'none';
                document.getElementById('message_error').style.display = 'none';
                return true;
            }
        }
        whatso_customer_message.onkeyup = function() {
            if (this.value.length < 2 || this.value.length > 1500) {
                document.getElementById('message_error1').style.display = 'block';
                return false;
            } else {
                // document.getElementById('mobile_number_error').style.display = 'none';
                document.getElementById('message_error1').style.display = 'none';
                return true;
            }
        }


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
                    normalizedValue = el.value.replace(/\n/g, "\n");

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
                        start += normalizedValue.slice(0, start).split("\r\n").length - 1;

                        if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                            end = len;
                        } else {
                            end = -textInputRange.moveEnd("character", -len);
                            end += normalizedValue.slice(0, end).split("\r\n").length - 1;
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
            return offset - (el.value.slice(0, offset).split("\n").length - 1);
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
    </script>
    <script>


        jQuery(document).ready(function($){
            $('#customer_checkbox').change(function() {
                if (this.checked) {
                    $("#displayCustomerMsgDiv").removeClass("d-none");

                } else {
                    $("#displayCustomerMsgDiv").addClass("d-none");
                }

            });


            if ($('#customer_checkbox').prop('checked') == true) {
                $("#displayCustomerMsgDiv").removeClass("d-none");

            } else {
                $("#displayCustomerMsgDiv").addClass("d-none");
            }

        });

        function submitfunction() {
            var owner_message = document.getElementById("message").value;
            var customer_maessage = document.getElementById("whatso_customer_message").value;

            var word = "{#var#}";
            if (owner_message.indexOf(word) != -1) {
                event.preventDefault ? event.preventDefault() : event.returnValue = false;
                document.getElementById('message_variable_error').style.display = 'block';

                return false;
            } else if (customer_maessage.indexOf(word) != -1) {
                event.preventDefault ? event.preventDefault() : event.returnValue = false;
                document.getElementById('variable_error').style.display = 'block';
                document.getElementById('message_variable_error').style.display = 'none';
                return false;
            } else {
                document.getElementById('variable_error').style.display = 'none';

                return true;

            }


        }
    </script>

</html>
