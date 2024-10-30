<?php
$data = get_option('whatso_notifications');
$data = json_decode($data);
$whatso_email = $data->whatso_email;


if (isset($_POST['getbutton'])) {
    $legit = true;
    if (!isset($_POST['whatso_user_email_nonce'])) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_user_email_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_user_email_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'whatso_user_email')) {
        $legit = false;
    }
    if (!$legit) {
        wp_safe_redirect(add_query_arg());
        exit();
    }

    $email = isset($_POST['whatso_email1']) ? sanitize_email(wp_unslash($_POST['whatso_email1'])) : '';


    $update_notifications_arr = array(
        'whatso_email' => $email,
    );
    $result = update_option('whatso_notifications', wp_json_encode($update_notifications_arr));


    WHATSO_WooCommerce::whatso_get_user_credentials("$email");
    wp_redirect('admin.php?page=whatso_admin_settings');
}

$data = get_option('whatso_notifications');
$data = json_decode($data);
$whatso_email = $data->whatso_email;

if (isset($_POST['submitbutton'])) {
    $legit = true;
    if (!isset($_POST['whatso_user_credentials_nonce'])) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_user_credentials_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_user_credentials_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'whatso_user_credentials')) {
        $legit = false;
    }
    if (!$legit) {
        wp_safe_redirect(add_query_arg());
        exit();
    }


    if (!empty($_POST)) {
        $username = isset($_POST['whatso_username']) ? sanitize_text_field(wp_unslash($_POST['whatso_username'])) : '';
        $password = isset($_POST['whatso_password']) ? sanitize_text_field(wp_unslash($_POST['whatso_password'])) : '';
        $whatso_mobileno = "";
        $customer_notification = "";
        $whatso_message = "hi, an order is placed on {storename} at {orderdate}.\n\nThe order is for {productname} and order amount is {amountwithcurrency}.\n\nCustomer details are: {customernumber}\n\n{customeremail}";

        $whatso_customer_message = "{customername}, your {productname} order of {amountwithcurrency} has been placed. \n\nWe will keep you updated about your order status.\n\n{storename}";

        $whatso_ac_message = "Hi We noticed you didn't finish your order on {storename}.\n\nVisit {siteurl} to complete your order.  \n\nThanks, {storename}.";
        $whatso_ac_message2 = "{customername}, You left some items in your cart!\n\nWe wanted to make sure you had the chance to get what you need. \n\nContinue shopping: {storename}";

        $whatso_ac_message3 = "Hi we see you left few items in the cart at {siteurl}. Your items are waiting for you! Grab your favorites before they go out of stock. \n\nYour friends from {storename}";

        $whatso_ac_message4 = "{customername}, Your cart is waiting for you at {siteurl}\n\nComplete your purchase before someone else buys them! Click {siteurl} to finish your order now.\n \nThanks!\n {storename}";

        $whatso_ac_message5 = "Hello Did you forget to complete your order on {siteurl}. \nJust click the link to finish the order!\n\nYour friends at {storename}";




        if (empty(get_option('whatso_notifications'))) {
            $update_notifications_arr = array(
                'whatso_username'   =>  $username,
                'whatso_password'   =>  $password,
                'whatso_mobileno'   =>  $whatso_mobileno,
                'whatso_message'   =>  $whatso_message,
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

            WHATSO_WooCommerce::whatso_get_user_plan();
            wp_redirect('admin.php?page=whatso_admin_settings');
        } else if (!empty(get_option('whatso_notifications'))) {
            $update_notifications_arr = array(
                'whatso_username'   =>  $username,
                'whatso_password'   =>  $password,
                'whatso_mobileno'   =>  $whatso_mobileno,
                'whatso_message'   =>  $whatso_message,
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
            WHATSO_WooCommerce::whatso_get_user_plan();
            wp_redirect('admin.php?page=whatso_admin_settings');
        }

        //data set in mesaage notification for cf7 details
        $admin_mobileno = "";
        $enable_notification = "";
        $admin_message = "A new contact form submission is received and is published on {storename}. You can check it out : Customer Email: {customeremail}, Subject: {customersubject}, Message: {customermessage}, Customer number: {customernumber}. Thank you.\n\n{storeurl}";
        if (!empty(get_option('whatso_cf7')) || empty(get_option('whatso_cf7'))) {
            $update_notifications_arr = array(

                'admin_mobileno'   =>  $admin_mobileno,
                'admin_message'    =>  $admin_message,
                'enable_notification' => $enable_notification,

            );
            $result = update_option('whatso_cf7', wp_json_encode($update_notifications_arr));
        }
    }
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

    <title>setup screen</title>
    <style>
        #wpfooter {
            position: fixed;
        }
    </style>
</head>

<body>


    <div class="container max-width-600">

        <form method="post" name="form" action="">

            <h2 class="head-title font-20 text-center"><?php esc_html_e('Let us setup something amazing', 'whatso'); ?>
            </h2>

            <div class="row mb-3">
                <div class="col-12">
                    <label class="lbl"><?php esc_html_e('Email address', 'whatso'); ?> <sup class="required_star"> *</sup> </label>

                </div>

                <div class="col-12">
                    <input type="text" name="whatso_email1" id="whatso_email1" placeholder='Enter E-mail ID' autocomplete="off" maxlength="64" class="text_input" value="<?php echo esc_html($whatso_email); ?>" required>
                    <span id="error_email" class="error"><?php esc_html_e('Please enter Valid E-mail ID.', 'whatso'); ?></span>
                </div>
                <div id="alert" class="col-12 text-center text-success mt-3">
                    <h6 style="color:green;"><?php esc_html_e('An email is sent to you with your username and password. Please add the same in below form.', 'whatso'); ?></h6>
                </div>
            </div>
            <?php wp_nonce_field('whatso_user_email', 'whatso_user_email_nonce'); ?>
            <div class="row mb-3">
                <div class="col-md-12 text-center"><button type="submit" class="btn btn-theme" name="getbutton"> Get Credentials </button></div>
            </div>
        </form>
        <form method="post" name="form1" action="">
            <div class="row mb-3">
                <div class="col-12">
                    <label class="lbl"><?php esc_html_e('Username', 'whatso'); ?> <sup class="required_star"> *</sup></label>
                    <input type="hidden" name="whatso_username1">
                </div>
                <div class="col-12">
                    <input type="text" name="whatso_username" id="whatso_username" placeholder="Enter username" autocomplete="off" maxlength="32" class="text_input" required>
                    <span id="error_username" class="error"><?php esc_html_e('Please copy API username properly from website.', 'whatso'); ?></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <label class="lbl"><?php esc_html_e('Password', 'whatso'); ?> <sup class="required_star"> *</sup></label>
                    <input type="hidden" name="whatso_password1">
                </div>
                <div class="col-12">
                    <input type="text" name="whatso_password" id="whatso_password" placeholder='Enter Password' autocomplete="off" maxlength="32" class="text_input" required>
                    <span id="error_password" class="error"><?php esc_html_e('Please copy API password properly from website.', 'whatso'); ?></span>
                </div>
            </div>
            <?php wp_nonce_field('whatso_user_credentials', 'whatso_user_credentials_nonce'); ?>
            <div class="row mb-3">
                <div class="col-md-12 text-center"> <button type="submit" class="btn btn-theme" name="submitbutton" id="submitbutton" onclick="FormValidation()">Continue</button>
                </div>

            </div>
    </div>
    </form>
    </div>
</body>


<script>
  
    window.onload = pageLoad();
    let username = document.querySelector('#whatso_username');
    let password = document.querySelector('#whatso_password');
    let email = document.querySelector('#whatso_email');

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

        if (this.value.length < 32 || this.value.length > 32) {
            document.getElementById('error_password').style.display = 'block';
            return false;
        } else {
            document.getElementById('error_password').style.display = 'none';
            return true;
        }
    }
    email.onkeyup = function() {

        var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (email.value.match(mailformat)) {

            document.getElementById('error_email').style.display = 'none';
            return true
        } else {
            document.getElementById('error_email').style.display = 'block';
            return false;
        }
        document.forms["form1"].submit();
    }

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
    function pageLoad() {
        element1 = document.getElementById('whatso_email1').value;



        if (element1 != "") {
            document.getElementById('alert').style.display = "";
            jQuery("#whatso_username").removeAttr('readonly');
            jQuery("#whatso_password").removeAttr('readonly');
            jQuery("#submitbutton").removeAttr('disabled');


        } else {
            document.getElementById('alert').style.display = "none";
            jQuery("#whatso_username").attr('readonly', 'readonly');
            jQuery("#whatso_password").attr('readonly', 'readonly');
            jQuery("#submitbutton").attr('disabled', 'disabled');

        }

    }
</script>

</html>