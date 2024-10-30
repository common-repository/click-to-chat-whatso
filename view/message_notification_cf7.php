<?php
//set data
if (isset($_POST) && !empty($_POST)) {
    if (
        !isset($_POST['save_admin_details'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['save_admin_details'])), 'save_details')
    ) {
        return;
    }
    
    

    $admin_mobileno = isset($_POST['admin_mobileno']) ? sanitize_text_field(wp_unslash($_POST['admin_mobileno'])) : '';
    $admin_mobileno = preg_replace('/[^0-9,]/u', '', $admin_mobileno);
    $admin_message = isset($_POST['admin_message']) ? sanitize_textarea_field(wp_unslash($_POST['admin_message'])) : '';
    
    if (isset($_POST['customer_checkbox'])) {
        $enable_notification = "1";
    } else {
        $enable_notification = "0";
    }

    $update_notifications_arr = array();
    $flag = 1;
   
    if (empty($admin_mobileno)) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please Enter Mobile Number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    } elseif (strlen($admin_mobileno) <= 7) {
        $flag = 0;
        $error_mobileno = '';
        $error_mobileno .= '<div class="notice notice-error is-dismissible">';
        $error_mobileno .= '<p>' . esc_html('Please enter atleast 7 digit number.') . '</p>';
        $error_mobileno .= '</div>';
        echo wp_kses_post($error_mobileno);
    } else {
        $numbers = explode(',', $admin_mobileno);
        $numbers = array_filter($numbers);
        $numbers = array_map('trim', $numbers);
        $error = 0;
        $inValidNumbers = array();
        foreach ($numbers as $number) {
            if (is_numeric($number)) {
                if (strlen($number) < 7) {
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
            $error_message .= '<p>' . esc_html('Please enter 7 digit number of') . ' ' . implode(", ", $inValidNumbers) . '</p>';
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

    if ($flag === 1) {
        $admin_mobileno = implode(", ", $numbers);

        $update_notifications_arr = array(
            
            'admin_mobileno'   =>  $admin_mobileno,
            'admin_message'    =>  $admin_message,
            'enable_notification' => $enable_notification,

        );
        $result = update_option('whatso_cf7', wp_json_encode($update_notifications_arr));

        if ($result) {
            $success = '';
            $success .= '<div class="notice notice-success is-dismissible">';
            $success .= '<p>' . esc_html('Details update successfully.') . '</p>';
            $success .= '</div>';
            echo wp_kses_post($success);
        }

    }


}

//get data
$data = get_option('whatso_cf7');
$data = json_decode($data);
$admin_mobileno = $data->admin_mobileno;
$admin_message = $data->admin_message;
$enable_notification =$data->enable_notification;


$img_url = plugin_dir_url(__DIR__);
$logo = $img_url . 'assets/images/whatsoLogoNew_black.png';

?>
<div class="container">
    <div>
    <img src="<?php echo esc_url($logo);?>" class="imgclass">
    </div>

    <ul class="breadcrumb">
        <li><b><?php esc_attr_e( 'Message Notification via Contact Form 7' ); ?></b></li>
    </ul>
    <div class="box">
        <form class="form_div" method="post">
            <div class="row mb-3">
                <div class="col-10">
                    <label class="lbl1"><?php esc_attr_e( 'WhatsApp number with country code ' ); ?> <span class="required_star">*</span>(You will receive notifications on this number)</label>
                    <input type="text" name="admin_mobileno" id="admin_mobileno" autocomplete="off" maxlength="200" placeholder="Enter Mobile Number with country code. Do not prefix with a 0 or +" class="text_input form-control" value="<?php echo esc_html($admin_mobileno); ?>" />

                </div>
            </div>
           
            <div class="row mb-3 ">
                <div class="col-10 ">
                    <label class="lbl1"><?php esc_html_e('Message', 'whatso'); ?></label><span class="required_star">*</span>

                    <textarea class="form-control message"  name="admin_message" id="admin_message" autocomplete="off" placeholder="Enter message that you want to be sent when the order is placed."  readonly rows="7"><?php  esc_attr_e($admin_message); ?></textarea>

                </div>
            </div>

            <div class="row mb-3">
                <div class="col-10">
                    <?php if ($enable_notification === '1') { ?>
                        <input type="checkbox" id="customer_checkbox" name="customer_checkbox" value="customer_checkbox" checked />
                    <?php } else {  ?>
                        <input type="checkbox" id="customer_checkbox" name="customer_checkbox" value="customer_checkbox" />
                    <?php } ?>
                    <label class="lbl1 mb-1" > <?php esc_attr_e( 'Enable Notification ' ); ?></label>

                </div>
            </div>
           
            <div class="row mb-3">
                <div class="col-10">
                
                    <input type="submit" class="btn btn-theme" name="notification_submit" value="Submit"  />
                    <?php
                        wp_nonce_field('save_details', 'save_admin_details');
                        ?>
                </div>
            </div> 
            
            <div class="mb-2">
                <h6><?php esc_html_e('Note : This plugin will not send any WhatsApp if the contact-form 7 submission has any URL link in it to avoid spamming', 'whatso'); ?></h6>                 
            </div>
        </form>
    </div>
</div>
<script>
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

</script>