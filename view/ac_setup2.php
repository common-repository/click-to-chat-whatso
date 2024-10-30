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


    $admin_mobile = $official_number;


if (!empty($_POST)) {
    $legit = true;
    if (! isset($_POST['whatso_setup_mobile_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_setup_mobile_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_setup_mobile_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'whatso_setup_mobile') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }
    $default_country = isset($_POST['default_country']) ? sanitize_text_field(wp_unslash($_POST['default_country'])) : '';
    $admin_mobile = isset($_POST['admin_mobile']) ? sanitize_text_field(wp_unslash($_POST['admin_mobile'])) : '';


   $admin_mobile = $default_country . $admin_mobile;
   $trigger_time="";
   $whatso_time1="";

   if ($isMessageFromAdminNumber != "true") {

    $admin_mobile = $official_number;
}

    if (!empty(get_option('whatso_abandoned'))) {
        $update_notifications_arr = array(
            'default_country' => $default_country,
            'admin_mobile'   =>  $admin_mobile,
            'whatso_trigger_time'   =>  $trigger_time,
            'whatso_time1'   =>  $whatso_time1,
        );
        $result = update_option('whatso_abandoned', wp_json_encode($update_notifications_arr));
        wp_redirect('admin.php?page=whatso_admin_settings');
    }
    if (empty(get_option('whatso_abandoned'))) {
        $update_notifications_arr = array(
            'default_country' => $default_country,
            'admin_mobile'   =>  $admin_mobile,
            'whatso_trigger_time'   =>  $trigger_time,
            'whatso_time1'   =>  $whatso_time1,
        );
        $result = update_option('whatso_abandoned', wp_json_encode($update_notifications_arr));
        wp_redirect('admin.php?page=whatso_admin_settings');
    }
    if (!(get_option('whatso_abandoned'))) {
        $update_notifications_arr = array(
            'default_country' => $default_country,
            'admin_mobile'   =>  $admin_mobile,
        );
        $result = update_option('whatso_abandoned', wp_json_encode($update_notifications_arr));
        wp_redirect('admin.php?page=whatso_admin_settings');
    }

}
$data = get_option('whatso_user_plan');
$data = json_decode($data);
$package=$data->abandonedCartPurchasedPlan;
$notice ="";
if(str_contains($package, 'Basic') == true) {
    $notice=esc_html('You are using Basic Plan');
}
elseif(str_contains($package, 'Pro') == true) {
    $notice=esc_html('You are using Pro Plan');
}
elseif(str_contains($package, 'Ultimate') == true) {
    $notice=esc_html('You are using Ultimate plan');
}
elseif($package=="") {
    $notice=esc_html('You are using free version of plugin.');
}

$img_url = plugin_dir_url(__DIR__);
$logo = $img_url . 'assets/images/Mobilechart.png';
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
    <title><?php esc_html_e('Notification Setup','whatso');?></title>

</head>

<!--html form for setup1-->

<body>
    <div class="float-right d-flex justify-content-end">
<div class="text-success packagelabel justify-content-end">
        <h6 ><?php echo esc_html($notice)?></h6>
        </div>
        </div>
    <form method="post" name="form1" action="">
        <div class="container-fluid max-width-600 mt-4">
            <div class="row text-center">
                <h3 class="sub-title"><?php esc_html_e('Message will go from below number','whatso');?></h3>
            </div>

            <div class="form-body">
                <div class="row mb-2 mt-4">
                    <label class="lbl"><?php esc_html_e('Enter your contact number','whatso');?> <span class="required_star"> *</span> </label>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <div class="position-relative">
                            <select id="default_country" name="default_country" class='country-code form-control input-line full-width country-code' style="font-size: 16px;" <?php if ($isMessageFromAdminNumber != "true") { ?> disabled <?php } ?>>
                                <option value="91" name="default_country" id="default_time_in" class='input-line full-width' selected><label for="box_position_right"><?php esc_html_e('+91 India', 'whatso'); ?></label>
                                </option>
                                <option value="92" name="default_country" id="default_time_in" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+92 Pakistan', 'whatso'); ?></label>
                                </option>
                                <option value="1" name="default_country" id="default_time_us" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+1 United States', 'whatso'); ?></label>
                                </option>
                                <option value="62" name="default_country" id="default_time_in" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+62 Indonesia', 'whatso'); ?></label>
                                </option>
                                <option value="60" name="default_country" id="default_time_in" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+60 Malaysia', 'whatso'); ?></label>
                                </option>
                                <option value="65" name="default_country" id="default_time_in" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+65 Singapore', 'whatso'); ?></label>
                                </option>
                                <option value="234" name="default_country" id="default_time_in" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+234 Nigeria', 'whatso'); ?></label>
                                </option>
                                <option value="55" name="default_country" id="default_time_br" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+55 Brazil', 'whatso'); ?></label>
                                </option>
                                <option value="971" name="default_country" id="default_time_in" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+971 United Arab Emirates', 'whatso'); ?></label>
                                </option>
                                <option value="20" name="default_country" id="default_time_in" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+20 Egypt', 'whatso'); ?></label>
                                </option>
                                <option value="27" name="default_country" id="default_time_sa" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+27 South Africa', 'whatso'); ?></label>
                                </option>
                                <option value="966" name="default_country" id="default_time_in" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+966 Saudi Arabia', 'whatso'); ?></label>
                                </option>
                                <option value="880" name="default_country" id="default_time_in" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+880 Bangladesh', 'whatso'); ?></label>
                                </option>
                                <option value="44" name="default_country" id="default_time_uk" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+44 United Kingdom', 'whatso'); ?></label>
                                </option>
                                <option value="86" name="default_country" id="default_time_uk" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+86 China', 'whatso'); ?></label>
                                </option>
                                <option value="39" name="default_country" id="default_time_uk" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+39 Italy', 'whatso'); ?></label>
                                </option>
                                <option value="852" name="default_country" id="default_time_uk" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+852 Hong Kong', 'whatso'); ?></label>
                                </option>
                                <option value="972" name="default_country" id="default_time_uk" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+972 Israel', 'whatso'); ?></label>
                                </option>
                                <option value="90" name="default_country" id="default_time_uk" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+90 Turkey', 'whatso'); ?></label>
                                </option>
                                <option value="49" name="default_country" id="default_time_uk" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+49 Germany', 'whatso'); ?></label>
                                </option>
                                <option value="213" name="default_country" id="default_time_uk" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+213 Algeria', 'whatso'); ?></label>
                                </option>
                                <option value="98" name="default_country" id="default_time_ir" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+98 Iran', 'whatso'); ?></label>
                                </option>
                                <option value="94" name="default_country" id="default_time_ir" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+94 Sri Lanka', 'whatso'); ?></label>
                                </option>
                                <option value="31" name="default_country" id="default_time_ir" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+31 Netharland', 'whatso'); ?></label>
                                </option>
                                <option value="52" name="default_country" id="default_time_aus" class='input-line full-width'><label for="box_position_left"><?php esc_html_e('+52 Mexico', 'whatso'); ?></label>
                                </option>
                                <option value="7" name="default_country" id="default_time_ru" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+7 Russia', 'whatso'); ?></label>
                                </option>
                                <option value="39" name="default_country" id="default_time_it" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+39 Italy', 'whatso'); ?></label>
                                </option>
                                <option value="1" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+1 Canada', 'whatso'); ?></label>
                                </option>
                                <option value="213" name="default_country" id="default_time_uk" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+213 Algeria', 'whatso'); ?></label>
                                </option>
                                <option value="98" name="default_country" id="default_time_ir" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+98 Iran', 'whatso'); ?></label>
                                </option>
                                <option value="31" name="default_country" id="default_time_ir" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+31 Netharland', 'whatso'); ?></label>
                                </option>
                                <option value="52" name="default_country" id="default_time_aus" class='input-line full-width'><label for="box_position_left"><?php esc_html_e('+52 Mexico', 'whatso'); ?></label>
                                </option>
                                <option value="7" name="default_country" id="default_time_ru" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+7 Russia', 'whatso'); ?></label>
                                </option>
                                <option value="39" name="default_country" id="default_time_it" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+39 Italy', 'whatso'); ?></label>
                                </option>
                                <option value="1" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+1 Canada', 'whatso'); ?></label>
                                </option>

                                <option value="254" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+254 Kenya', 'whatso'); ?></label>
                                </option>
                                <option value="962" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+962 Jordan', 'whatso'); ?></label>
                                </option>
                                <option value="33" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+33 France', 'whatso'); ?></label>
                                </option>
                                <option value="54" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+54 Argentina', 'whatso'); ?></label>
                                </option>
                                <option value="965" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+965 Kuwait', 'whatso'); ?></label>
                                </option>
                                <option value="51" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+51 Peru', 'whatso'); ?></label>
                                </option>
                                <option value="974" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+974 Qatar', 'whatso'); ?></label>
                                </option>
                                <option value="61" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+61 Australia', 'whatso'); ?></label>
                                </option>
                                <option value="233" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+233 Ghana', 'whatso'); ?></label>
                                </option>
                                <option value="593" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+593 Ecuador', 'whatso'); ?></label>
                                </option>
                                <option value="380" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+380 Ukarine', 'whatso'); ?></label>
                                </option>
                                <option value="593" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+593 Ecuador', 'whatso'); ?></label>
                                </option>
                                <option value="63" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+63 Philippine', 'whatso'); ?></label>
                                </option>
                                <option value="964" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+964 Iraq', 'whatso'); ?></label>
                                </option>
                                <option value="961" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+961 Lebanon', 'whatso'); ?></label>
                                </option>
                                <option value="968" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+968 Oman', 'whatso'); ?></label>
                                </option>
                                <option value="40" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+40 Romania', 'whatso'); ?></label>
                                </option>
                                <option value="973" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+973 Bahrain', 'whatso'); ?></label>
                                </option>
                                <option value="56" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+56 Chille', 'whatso'); ?></label>
                                </option>
                                <option value="237" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+237 Cameroon', 'whatso'); ?></label>
                                </option>
                                <option value="213" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+213 Algeria', 'whatso'); ?></label>
                                </option>
                                <option value="225" name="default_country" id="default_time_can" class='input-line full-width'><label for="box_position_right"><?php esc_html_e('+225 Ivory Coast', 'whatso'); ?></label>
                                </option>
                            </select>
                            <input type="text" name="admin_mobile" id="admin_mobile" onClick="this.setSelectionRange(0, this.value.length)" autocomplete="off" class="form-control input-line full-width country-code-input" maxlength="15" onpaste="return false" onkeypress="return isNumber(event)" <?php if ($isMessageFromAdminNumber != "true") { ?> readonly="readonly" <?php } ?> value="<?php echo esc_html($admin_mobile); ?>"required>
                            <span id="phonemsg d-none"></span>
                        </div>
                    </div>
                </div>
                <?php wp_nonce_field('whatso_setup_mobile', 'whatso_setup_mobile_nonce'); ?>
                <?php if ($isMessageFromAdminNumber != "true") { ?>
                <div class="row text-center mt-4 mb-4">
                <h6> If you want to send message from your number<br/> please upgrade plan from <a href="#">whatso.net</a> </h6>
            </div>
            <?php } ?>
                <div class="row mb-2 justify-content-center">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-theme" onclick="FormValidation()">Save &
                            Continue</a></button>

                    </div>
                </div>
            </div>
        </div>
    </form>
    <div>
        <br>

        <div class="card">
        <p>
                    <h5><b><?php  esc_html_e('How to send message from my own WhatsApp mobile number ? ', 'whatso') ?> </b></h5>
                </p>
                <h6><?php  esc_html_e('
                    Sending automated WhatsApp messages to your customer from the WooCommerce plugin is now easy with Whatso Platform. You have 3 options to send automated messages: ', 'whatso') ?>
                    <br />    <br />

                    <strong> <?php  esc_html_e('Whatso Official API:', 'whatso') ?></strong><?php  esc_html_e(' You can send messages via ', 'whatso') ?> <a target="_blank" href="https://www.whatso.net/official-whatsapp-business-api"><?php  esc_html_e(' WhatsApp official API ', 'whatso') ?></a><?php  esc_html_e(' as well. The benefit of official API is that you can get a green tick mark and can send up to 100,000 messages per day. You can email us to get more details about getting an official API.', 'whatso') ?><br /><br />
                    <strong> <?php  esc_html_e('Using our number:', 'whatso') ?></strong><?php  esc_html_e(' You can use our number to send automated WhatsApp messages. 100s of websites are already using our number to send automated messages and are getting great success.', 'whatso') ?><br /><br />
                    <strong> <?php  esc_html_e('Whatso Chrome Extension:', 'whatso') ?></strong> <a target="_blank" href="https://chrome.google.com/webstore/detail/whatso-bulk-message-sende/keibcfaoccngmlngokogbegkphdmakgg"><?php  esc_html_e('Chrome extension', 'whatso') ?></a> <?php  esc_html_e('will help you send messages from your number when you upgrade to any of the paid plans of the Whatso Abandoned Cart plugin. You can get started on this immediately right now by downloading our plugin.', 'whatso') ?><br />
                    </h6>
        </div>
    </div>
    <br>
    <div>
    <img src="<?php echo esc_url($logo);?>" class="img-fluid" alt="">
    </div>
    <script>
        function blockSpecialChar(e) {
            var k;
            document.all ? k = e.keyCode : k = e.which;
            //alert(k); //39 == '
            return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57) || k == 39);

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
            }

        }

        function FormValidation() {

            var txtmobile = document.getElementById("admin_mobile").value;
            var phoneno = /^[0-9]*$/;
            //Check Mobile no.
            if (txtmobile.length < 5 || txtmobile.length > 15) {
                document.getElementById('phonemsg').classList.remove("d-none");
                document.getElementById('phonemsg').innerHTML = "Number must be atleast 5 digits";
                return false;
            } else {
                document.getElementById('phonemsg').classList.add("d-none");
                document.getElementById('errormsg').innerHTML = "";
                return true;
            }
            document.forms["form1"].submit();

        }
    </script>
</body>

</html>