<?php
// Stop immediately if accessed directly.
if (! defined('ABSPATH') ) {
    die();
}

if(!get_option('whatso_user_plan') || !get_option('whatso_user_settings')) {
    
        WHATSO_Utils::setView('ac_setup1');
    
}elseif(!get_option('whatso_notifications')) {
  
        WHATSO_Utils::setView('ac_setup1');
   
}elseif (get_option('whatso_notifications')) {
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
    $ac_enable = "";

    if (!get_option('whatso_abandoned')) {
        WHATSO_Utils::setView('ac_setup2');
    }else{
        $data1 = get_option('whatso_abandoned');
        $data1 = json_decode($data1);
        $admin_mobile = $data1->admin_mobile;
        
    }

   
    if (($whatso_username != "") && ($whatso_password != "")&&($admin_mobile != "")) {
        WHATSO_Utils::setView('broadcast_message');
    }
    else if(($whatso_username != "") && ($whatso_password != "")&&($admin_mobile == "")) {
        WHATSO_Utils::setView('ac_setup2');
    }
    else{
        WHATSO_Utils::setView('ac_setup1');
    }

     
} else {
    WHATSO_Utils::setView('ac_setup1');
}
