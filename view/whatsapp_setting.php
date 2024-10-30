<?php 
require_once 'ac_dashboard.php'; 

$data = get_option('whatso_user_plan');
$data = json_decode($data);
$package=$data->abandonedCartPurchasedPlan;
$notice ="";
if(str_contains($package, 'Basic') == true) {
    $notice=esc_html('You are using '.$package.' please click on this link to buy the premium version of plugin');
}
elseif(str_contains($package, 'Pro') == true) {
    $notice=esc_html('You are already using '.$package.' please click on this link to buy the premium version of plugin');
}
elseif(str_contains($package, 'Ultimate') == true) {
    $notice=esc_html('You are already using '.$package.' stay connected to us via');
}
elseif($package=="") {
    $notice=esc_html('You are using free version of plugin.By default messages will go from our number. If you want to send from your number please click on this link to buy the premium version of plugin');
}

?>
<div class="container">
    <div class="row">
    <h6 class="row-content"><?php echo esc_html($notice)?><a href="https://www.whatso.net/woocommerce-abandoned-cart" target="_blank">WooCommerce Abandoned-Cart</a>. </h6>
    </div>
</div>
