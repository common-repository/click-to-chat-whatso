<?php require_once 'ac_dashboard.php';


if (!empty($_POST)) {
    $legit = true;
    if (! isset($_POST['whatso_dashboard_display_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_dashboard_display_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_dashboard_display_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'whatso_dashboard_display') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }
    $fromdatepicker = isset($_POST['fromdatepicker']) ? sanitize_text_field(wp_unslash($_POST['fromdatepicker'])) : '';
    $todatepicker = isset($_POST['todatepicker']) ? sanitize_text_field(wp_unslash($_POST['todatepicker'])) : '';

}
      $data = get_option('whatso_abandoned');
      $data = json_decode($data);
      $whatso_mobile = $data->admin_mobile;
      $whatso_trigger = $data->whatso_trigger_time;
      $ac_enable = $data->ac_enable;

      global $wpdb;
      $table = $wpdb->prefix . "whatso_abandoned_cart";
      $table1 = $wpdb->prefix . "posts";

      //AC Count

if(!isset($fromdatepicker) && !isset($todatepicker) ) {
    $fromdatepicker=date("Y-m-d", time()-(86400*45));
    $todatepicker=date("Y-m-d", time()+(86400*1));


}
      $abandoned_cart_count = $wpdb->get_results($wpdb->prepare(" SELECT COUNT(*) FROM ".$wpdb->prefix."whatso_abandoned_cart WHERE status = '1' AND last_access_time BETWEEN  %s AND %s ", $fromdatepicker, $todatepicker)); // db call ok; no-cache ok

      $abandoned_cart_count1 = json_decode(json_encode($abandoned_cart_count), true);
      $array = json_decode(json_encode($abandoned_cart_count1), true);
foreach($array as $arr2){
    foreach($arr2 as $_id=>$abandoned_cart_count1){
        $abandoned_cart_count1;
        if($abandoned_cart_count1 == "") {
            $abandoned_cart_count1=0;
        }
    }
}
      //RC Count
      $recovered_cart_count = $wpdb->get_results($wpdb->prepare(" SELECT COUNT(*) FROM ".$wpdb->prefix."whatso_abandoned_cart WHERE status = '2' AND last_access_time BETWEEN  %s AND %s ", $fromdatepicker, $todatepicker)); // db call ok; no-cache ok
      $recovered_cart_count1 = json_decode(json_encode($recovered_cart_count), true);
      $array = json_decode(json_encode($recovered_cart_count1), true);
foreach($array as $arr2){
    foreach($arr2 as $_id=>$recovered_cart_count1){
        $recovered_cart_count1;
        if($recovered_cart_count1 == "") {
            $recovered_cart_count1=0;
        }
    }
}
      //AC Amount

      $abandoned_cart_amount = $wpdb->get_results($wpdb->prepare(" SELECT SUM(cart_total) FROM ".$wpdb->prefix."whatso_abandoned_cart WHERE status = '1' AND last_access_time BETWEEN  %s AND %s ", $fromdatepicker, $todatepicker)); // db call ok; no-cache ok
      $abandoned_cart_amount1 = json_decode(json_encode($abandoned_cart_amount), true);
      $array = json_decode(json_encode($abandoned_cart_amount1), true);
foreach($array as $arr2){
    foreach($arr2 as $_id=>$abandoned_cart_amount1){
        $abandoned_cart_amount1;
        if($abandoned_cart_amount1 == "") {
            $abandoned_cart_amount1=0;
        }
    }
}
      //RC Amount

      $recovered_cart_amount = $wpdb->get_results($wpdb->prepare(" SELECT SUM(cart_total) FROM ".$wpdb->prefix."whatso_abandoned_cart WHERE status = '2' AND last_access_time BETWEEN  %s AND %s ", $fromdatepicker, $todatepicker)); // db call ok; no-cache ok
      $recovered_cart_amount1 = json_decode(json_encode($abandoned_cart_amount), true);
      $array = json_decode(json_encode($recovered_cart_amount), true);
foreach($array as $arr2){
    foreach($arr2 as $_id=>$recovered_cart_amount1){
        $recovered_cart_amount1;
        if($recovered_cart_amount1 == "") {
            $recovered_cart_amount1=0;
        }
    }
}
      $currency = get_option('woocommerce_currency');

      $display_dashboard = $wpdb->get_results($wpdb->prepare(" SELECT id,customer_first_name,customer_last_name,customer_mobile_no,cart_total,create_date_time,status, cart_json  FROM ".$wpdb->prefix."whatso_abandoned_cart WHERE last_access_time BETWEEN  %s AND %s ORDER BY id DESC ", $fromdatepicker, $todatepicker)); // db call ok; no-cache ok
$img_url = plugin_dir_url(__DIR__);
$smiley = $img_url . 'assets/images/SmileyImg.png';

//broadcast



?>
<script>
 jQuery(document).ready(function($){
     $("#fromdatepicker").datepicker({
         dateFormat: "yy-mm-dd"
     });
     $("#todatepicker").datepicker({
         dateFormat: "yy-mm-dd"
     });
     $('#datatable').DataTable({
         searching: false
     });
 });


</script>

<div class="container">
    <div class="row">
        <div class="col-12">
            <?php
            if($ac_enable == "checked") {
                echo "";
            }
            else{
                echo wp_kses_post('<br><span style="color: #BD392F;
                    margin-top: 20px;
                    font-size: 16px;
                    font-weight: 500;float: left;">Please enable abandoned cart from
                                <a href="admin.php?page=whatso_admin_settings" style="text-decoration:underline;">settings</a> to activate abandoned cart.</span>');
            }
            ?>
            <form id="form1" name="form1" method="POST" action="#" style="margin-top: 37px;float: right;">
                <p>
                    <span class="me-2">From Date:</span> <input type="text" id="fromdatepicker" name="fromdatepicker" />
                    <span class="me-2 ms-2">To Date:</span> <input type="text" id="todatepicker" name="todatepicker" />
                    <?php wp_nonce_field('whatso_dashboard_display', 'whatso_dashboard_display_nonce'); ?>
                    <button type="submit" name="submit" class="btn btn-theme search-btn ms-2">Search</button>
                </p>
            </form>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-4">
            <div class="card mb-3">
                <img src="<?php echo esc_url($smiley);?>" class="smile" alt=""/>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col text-center">
                            <p class="card-text3">
                                <?php echo esc_attr($recovered_cart_count1); ?>
                            </p>
                            <label class="w-100 m-auto"><b><?php esc_html_e('Recovered Orders','whatso');?></b></label>
                        </div>
                        <div class="col-auto vl"></div>
                        <div class="col text-center">
                            <p class="card-text3">
                                <?php echo esc_attr($recovered_cart_amount1); ?>
                            </p>
                            <label class="w-100 m-auto"><b><?php esc_html_e('Amount','whatso');?></b></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col text-center">
                            <p class="card-text1">
                                <?php echo esc_attr($abandoned_cart_count1); ?>
                            </p>
                            <label class="w-100 m-auto"><b><?php esc_html_e('Abandoned Orders','whatso');?></b></label>
                        </div>
                        <div class="col-auto vl"></div>
                        <div class="col text-center">
                            <p class="card-text1">
                                <?php echo esc_attr($abandoned_cart_amount1);?>
                            </p>
                            <label class="w-100 m-auto"><b><?php esc_html_e('Amount to recover','whatso');?></b></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($display_dashboard == null) {
                  echo '</br>';?>
    <div class="row">
        <div class="col-md-12">
            <div class="card mw-100">
                <div class="row card-body">

                    <h5 class="dashboardmsg">
                        <?php esc_html_e(  'Looks like you do not have any saved Abandoned carts yet.
                        But do not worry, as soon as someone fills the Name & Phone number fields of your WooCommerce
                        Checkout form and abandons the cart, it will automatically appear here.','whatso');?>
                    </h5>

                </div>
            </div>
        </div>
    </div>
</div>
    <?php   }else{
        ?>
<table id="datatable" data-order='[[ 4, "desc" ]]' class="table display table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact No.</th>
            <th>Cart Item</th>
            <th>Time</th>
            <th>
                Price(<?php echo esc_attr($currency); ?>)
            </th>
            <th>Status</th>

        </tr>
    </thead>
    <tbody>
        <tr>
            <?php
            foreach ( $display_dashboard as $row ) {
                ?>
            <td>
                <?php echo esc_attr($row->id); ?>
            </td>
            <td>
                <?php echo esc_attr($row->customer_first_name);
                            echo "\n";
                            echo esc_attr($row->customer_last_name);?>
            </td>
            <td>
                <?php echo esc_attr($row->customer_mobile_no);?>
            </td>

            <td>
                <?php

                        $cart_id = unserialize($row->cart_json);
                        $array = json_decode(json_encode($cart_id), true);

                foreach($array as $arr2){

                    $product_id=$arr2['product_id'];
                    $cart_content=  $wpdb->get_results($wpdb->prepare("SELECT post_title FROM ".$wpdb->prefix."posts WHERE ID = %d ORDER BY ID DESC", $product_id)); // db call ok; no-cache ok
                    $array1 = json_decode(json_encode($cart_content), true);

                    $cart_data= json_encode($array1);
                    $var = explode(",", $array1['0']['post_title']);
                    $cart=$var['0'];
                    $products_array=array();
                    array_push($products_array, $cart);
                    echo esc_attr($exploded_names = implode(",", $products_array));
                    echo '<br>';
                }

                ?>
            </td>
            <td>
                <?php
                $date = date_create($row->create_date_time);
                echo esc_attr($create_date_time= date_format($date, 'd-M-Y H:i'));?>
            </td>
            <td>
                <?php echo esc_attr($row->cart_total); ?>
            </td>

            <td>
                <?php
                        $_status=$row->status;
                if($_status== '1') {
                    echo esc_html_e('Abandoned','whatso');
                } else {?>
                <label class="text-success">Recovered</label>
<?php }
                ?>
            </td>
        </tr>
                <?php
            }
    }
    ?>
    </tbody>
</table>
