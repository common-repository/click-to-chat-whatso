<?php
// Stop immediately if accessed directly.
if (! defined('ABSPATH') ) {
    die();
}
if(!get_option('whatso_user_plan') || !get_option('whatso_user_settings')) {
    
    WHATSO_Utils::setView('ac_setup1');

}elseif(!get_option('whatso_notifications')) {

    WHATSO_Utils::setView('ac_setup1');

}elseif (!get_option('whatso_abandoned')) {
    
    WHATSO_Utils::setView('ac_setup2');
}
else{
    WHATSO_Utils::setView('notifications_setup');
}

?>
