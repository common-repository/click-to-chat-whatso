<?php

 global $wpdb;
        $table = $wpdb->prefix . "posts";
        $table1 = $wpdb->prefix . "wc_order_stats";

        $order_id    =  $wpdb->get_results($wpdb->prepare(" SELECT ID FROM ".$wpdb->prefix."posts WHERE post_status IN ('wc-processing', 'wc-completed') ")); // db call ok; no-cache ok
        $order_id = json_decode(json_encode($order_id), true);
        //success amount
        $order_suc_amount    =  $wpdb->get_results($wpdb->prepare(" SELECT SUM(net_total) FROM ".$wpdb->prefix."wc_order_stats WHERE status IN ('wc-processing', 'wc-completed') AND date_created > now() - INTERVAL 45 day ")); // db call ok; no-cache ok
        $order_suc_amount1 = json_decode(json_encode($order_suc_amount), true);
         $array = json_decode(json_encode($order_suc_amount1), true);
      
foreach($array as $arr2){
    foreach($arr2 as $aid=>$order_suc_amt){
                $order_suc_amt;
        if($order_suc_amt== "") {
                  $order_suc_amt=0;
        }
                
    }
}
        
        //failed amount
        $order_failed_amount = $wpdb->get_results($wpdb->prepare(" SELECT SUM(net_total) FROM ".$wpdb->prefix."wc_order_stats WHERE status = 'wc-on-hold' AND date_created > current_date - INTERVAL 45 day")); // db call ok; no-cache ok
        $order_failed_amount1 = json_decode(json_encode($order_failed_amount), true);
         $array = json_decode(json_encode($order_failed_amount1), true);
foreach($array as $arr2){
    foreach($arr2 as $aid=>$order_failed_amt){
                $order_failed_amt;
        if($order_failed_amt == "") {
            $order_failed_amt=0;
        }
    }
}
      
        //cancelled amt
        $order_failed1_amount    =  $wpdb->get_results($wpdb->prepare(" SELECT SUM(net_total) FROM ".$wpdb->prefix."wc_order_stats WHERE status = 'wc-failed' AND date_created > current_date - INTERVAL 45 day ")); // db call ok; no-cache ok
        $order_failed1_amount1 = json_decode(json_encode($order_failed1_amount), true);
         $array = json_decode(json_encode($order_failed1_amount1), true);
foreach($array as $arr2){
    foreach($arr2 as $aid=>$order_failed1_amt){
                $order_failed1_amt;
        if($order_failed1_amt == "") {
            $order_failed1_amt=0;
        }
    }
}
        $total_failed_amt=$order_failed_amt + $order_failed1_amt;

        //succes count
        $order_success    =  $wpdb->get_results($wpdb->prepare(" SELECT COUNT(*) FROM ".$wpdb->prefix."posts WHERE post_status IN ('wc-processing', 'wc-completed') AND post_date > current_date - INTERVAL 45 day  ")); // db call ok; no-cache ok
        $array = json_decode(json_encode($order_success), true);
foreach($array as $arr2){
    foreach($arr2 as $aid=>$order_suc){
                $order_suc;
    }
}
        
        //failed count
       
        $order_cancelled    =  $wpdb->get_results($wpdb->prepare(" SELECT COUNT(*) FROM  ".$wpdb->prefix."posts WHERE post_status = 'wc-on-hold' AND post_date > current_date - INTERVAL 45 day")); // db call ok; no-cache ok
        $array1 = json_decode(json_encode($order_cancelled), true);
foreach($array1 as $arr2){
    foreach($arr2 as $aid=>$order_can){
                $order_can;
    }
}
        $order_failed    =  $wpdb->get_results($wpdb->prepare(" SELECT COUNT(*) FROM  ".$wpdb->prefix."posts WHERE post_status = 'wc-failed' AND post_date > current_date - INTERVAL 45 day")); // db call ok; no-cache ok
         $array2 = json_decode(json_encode($order_failed), true);
foreach($array2 as $arr2){
    foreach($arr2 as $aid=>$order_fail){
                $order_fail;
    }
}
        $total_failed=$order_can+$order_fail;

$img_url = plugin_dir_url(__DIR__);
$logo = $img_url . 'assets/images/whatsoLogoNew_black.png';
$vector = $img_url . 'assets/images/VectorArrow.png';


?>
<div class="container position-relative">
    <div class="text-center">
        <h1><img src="<?php echo esc_url($logo);?>" height="100"
                class="imgclass" alt=""></h1>
        <h2 class="mb-0 head-title"><?php esc_html_e('Abandoned Cart Recovery','whatso');?></h2>
        <p class="font-20"><?php esc_html_e('Whatso Abandoned Cart is the easiest way to recover all your Orders!','whatso');?></p>
        <?php if (class_exists('WooCommerce') ) {?>
        <div class="mt-4">
            <p class="subtitle">A Summary of last <strong style="color: #000000;">45 days</strong> Orders</p>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-4 text-center">
            <div class="card px-3">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <p class="card-text text-success"><?php echo esc_html($order_suc); ?></p>
                            <label class="lbl"><?php esc_html_e('Successfull Orders','whatso');?></label>
                        </div>
                        <div class="vl"></div>
                        <div class="col">
                            <p class="card-text text-success"><?php echo esc_html($order_suc_amt); ?></p>
                            <label class="lbl"><?php esc_html_e('Amount','whatso');?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4 text-center">
            <div class="card px-3">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <p class="card-text1 text-danger"><?php echo esc_html($total_failed); ?></p>
                            <label class="lbl"><?php esc_html_e('Failed Orders','whatso');?></label>
                        </div>
                        <div class="vl"></div>
                        <div class="col">
                            <p class="card-text1 text-danger"><?php echo esc_html($total_failed_amt); ?></p>
                            <label class="lbl"><?php esc_html_e('Amount to recover','whatso');?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 offset-6">
            <img src="<?php echo esc_url($vector);?>" class="imgRow" alt="">
        </div>
    </div>
    <div class="row">
        <div class="col-4 offset-7">
            <p class="row-content m-0 mb-2 text-center">
                We can help you to<br /> recover this.
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-4 offset-7">
            <p class="row-content m-0 mb-2 text-center">
            <a href="admin.php?page=whatso_ac_setup1" onclick="FormValidation()" class="btn btn-theme">Let's Get
                Started!</a>
            </p>

        </div>
    </div>
    </form>
            <?php 
        }
        else
        {
            ?>
    <div class="rowCol">
        <div>
            <h1 class="alt-heading">WooCommerce is required<br> for our abandoned cart plugin!</h1>
        </div>
        <div>
            <a href="<?php echo esc_url(get_site_url()); ?>/wp-admin/plugin-install.php?s=woocomerce&tab=search&type=term"
                class="btn btn-theme">Install Plugin</a>
        </div>
    </div>
            <?php 
        }
        ?>
</div>
