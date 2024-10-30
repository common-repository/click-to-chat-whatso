<?php
global $wpdb;
if (!empty($_POST)) {
    if (
        !isset($_POST['whatso_filter_date_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['whatso_filter_date_nonce'])), 'whatso_filter_date')
    ) {
        return;
    }
    $fromdatepicker = isset($_POST['fromdatepicker']) ? sanitize_text_field(wp_unslash($_POST['fromdatepicker'])) : '';
    $todatepicker = isset($_POST['todatepicker']) ? sanitize_text_field(wp_unslash($_POST['todatepicker'])) : '';
}


//Query to get order id
$customer_id = $wpdb->get_results($wpdb->prepare("SELECT o.customer_id,MAX(o.order_id) As order_id,MAX(o.date_created) AS last_order_date
    FROM " . $wpdb->prefix . "posts AS p 
    INNER JOIN " . $wpdb->prefix . "wc_order_stats  AS o ON p.ID = o.order_id
    INNER JOIN " . $wpdb->prefix . "postmeta  AS pm ON o.order_id = pm.post_id
    WHERE p.post_status IN ('wc-processing', 'wc-completed') 
    GROUP BY o.customer_id
    ORDER BY o.order_id DESC
    ")); // db call ok; no-cache ok

$customer_id = json_decode(json_encode($customer_id), true);

if ($fromdatepicker == "" && $todatepicker == "") {
    $fromdatepicker = date("Y-m-d", time() - (86400 * 1825));
    $todatepicker = date("Y-m-d", time() + (86400 * 1));

}

$img_url = plugin_dir_url(__DIR__);
$logo = $img_url . 'assets/images/whatsoLogoNew_black.png';
$smiley = $img_url . 'assets/images/SmileyImg.png';

//Query to get customer email
$customer_lookup = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT email FROM " . $wpdb->prefix . "wc_customer_lookup
    WHERE date_last_active BETWEEN  %s AND %s",$fromdatepicker,$todatepicker));// db call ok; no-cache ok


foreach ($customer_lookup as $clook) {
    $mail = $clook->email;
    //Query to get customer details
    $customer_detail = $wpdb->get_results($wpdb->prepare("select t.first_name,t.country, t.date_last_active,t.email
        from " . $wpdb->prefix . "wc_customer_lookup t
        inner join (
            select first_name, max(date_last_active) as MaxDate
            from " . $wpdb->prefix . "wc_customer_lookup
            where date_last_active BETWEEN  %s AND %s
            group by email
        )tm on t.date_last_active = tm.MaxDate",$fromdatepicker,$todatepicker));// db call ok; no-cache ok

}
$customer_detail = json_decode(json_encode($customer_detail), true);


//onclick of yes button to send contact to site

if (isset($_POST['yesbtn'])) {
    $legit = true;
    if (! isset($_POST['whatso_filter_date_nonce']) ) {
        $legit = false;
    }
    $nonce = isset($_POST['whatso_filter_date_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_filter_date_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'whatso_filter_date') ) {
        $legit = false;
    }
    if (! $legit ) {
        wp_safe_redirect(add_query_arg());
        exit();
    }

    WHATSO_WooCommerce::whatso_get_login();
    
    if (!empty(get_option('whatso_save_contact'))) {
        $data = get_option('whatso_save_contact');
        $data = json_decode($data);
        $responseStatusCode= $data->responseStatusCode;
        $data_contact = $data->data;
        $data_contact = json_decode(json_encode($data_contact),true);
        $duplicateRecord= $data_contact['duplicateRecord'];
        $invalidRecord= $data_contact['invalidRecord'];
        $successfullRecord= $data_contact['successfullRecord'];
        $alreadyExistsRecord= $data_contact['alreadyExistsRecord'];

    }
    if ($successfullRecord > 0) {
        $success = '';
        $success .= '<div class="notice notice-success is-dismissible">';
        $success .= '<p>' . esc_html('Your valid contacts are saved successfully on whatso.net .Now you can create campaign on ');
        $success .= '<a href="https://app.whatso.net/?ReturnUrl=%2Fcontact" target="_blank">' . esc_html('whatso') . '</a>' . '</p>';

        $success .= '</div>';
        echo wp_kses_post($success);
    }
    if ($alreadyExistsRecord > 0) {
        $error = '';
        $error .= '<div class="notice notice-error is-dismissible">';
        $error .= '<p>' . esc_html('Contacts already exist!');
        $error .= '</div>';
        echo wp_kses_post($error);

    }
    if ($invalidRecord > 0) {
        $error = '';
        $error .= '<div class="notice notice-error is-dismissible">';
        $error .= '<p>' . esc_html('Some numbers are invalid.');
        $error .= '</div>';

        echo wp_kses_post($error);

    }
}
?>
<script>
    jQuery(document).ready(function($) {
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
            <div>
                <img src="<?php echo esc_url($logo); ?>" class="imgclass" alt="">

            </div>

            <ul class="breadcrumb">
                <li><a href="admin.php?page=whatso_floating_menu_setup"><b>Whatso</b></a></li>
                <li><b><?php esc_html_e('Broadcast Messages', 'whatso'); ?></b></li>
            </ul>

            <div class="d-flex justify-content-between">
                <div class="card mb-3 w-25 align-items-center">
                    <img src="<?php echo esc_url($smiley); ?>" class="smileIcon" alt="" />
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <p class="card-text3 mt-3">
                                    <?php
                                    echo esc_attr(count($customer_detail));
                                    ?>
                                </p>
                                <label class="w-100 m-auto"><b><?php esc_html_e('Total Contacts', 'whatso'); ?></b></label>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="form1" name="form1" method="POST" action="#" style="margin-top: 37px;float: right;">
                    <div class="d-flex">
                        <span class="me-2">From Date:</span> <input type="text" id="fromdatepicker" name="fromdatepicker" />
                        <span class="me-2 ms-2">To Date:</span> <input type="text" id="todatepicker" name="todatepicker" />

                        <button type="submit" name="submit" class="btn btn-theme search-btn ms-2">Search</button>
                        <?php wp_nonce_field('whatso_filter_date', 'whatso_filter_date_nonce'); ?>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <table id="datatable" data-order='[[ 3, "desc" ]]' class="table display table-striped">
        <thead>
            <tr>
                <th><?php echo esc_attr('Name');?></th>
                <th><?php echo esc_attr('E-mail');?></th>
                <th><?php echo esc_attr('Contact No.');?></th>
                <th><?php echo esc_attr('Last order date');?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                foreach ($customer_detail as $c_detail) {
                    $c_email = $c_detail['email'];
                    $order_id = $wpdb->get_results($wpdb->prepare("SELECT max(o.order_id) AS order_id
                    FROM " . $wpdb->prefix . "wc_order_stats AS o 
                    INNER JOIN " . $wpdb->prefix . "wc_customer_lookup AS pm ON o.customer_id = pm.customer_id
                    WHERE pm.email = %s
                    AND  o.status IN ('wc-processing', 'wc-completed') 
                    GROUP BY pm.email
                    ", $c_email));// db call ok; no-cache ok
                    $order_id = json_decode(json_encode($order_id), true);

                    foreach ($order_id as $c_id) {
                        $order_id = $c_id['order_id'];

                        $customer_phone =  $wpdb->get_results($wpdb->prepare(
                            "SELECT MAX(meta_value) AS Contact_no
                            FROM " . $wpdb->prefix . "postmeta
                            WHERE post_id= $order_id
                            AND meta_key= '_billing_phone'
                            OR meta_key='shipping_phone'
                            "
                        ));// db call ok; no-cache ok
                        $customer_phone = json_decode(json_encode($customer_phone), true);
                     }

                ?>
                <td>
                    <?php
                        $c_name = $c_detail['first_name'];
                        echo esc_attr($c_name);
                    ?>
                </td>
                <td>
                        <?php
                        $c_email = $c_detail['email'];
                        echo esc_attr($c_email);
                        ?>
                </td>
                <td>
                        <?php
                        $country_code = $c_detail['country'];
                        foreach ($customer_phone as $c_phone) {

                            $c_no= $c_phone['Contact_no'];
                            $c_no = preg_replace('/[^0-9]/', '', $c_no);


                            echo esc_attr($c_no);

                        }
                        ?>
                </td>
                <td>
                        <?php
                        $c_date = $c_detail['date_last_active'];
                        $date = date_create($c_date);
                        echo esc_attr($create_date_time = date_format($date, 'd-M-Y H:i')); ?>
                </td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
    <div class="row align-items-center mt-5">
        <div class="col-md-8 mt-2">
            <h5><?php echo esc_attr('Note : By clicking Yes, your orders data shall be in sync with Whatso.net website. This will help you send WhatsApp marketing campaign through our Whatso.net website.');?></h5>
        </div>
        <div class="col-md-4">
            <div class="card mt-0">
                <div class="card-body text-center">
                    <h5><?php echo esc_attr('Send data to Whatso.net');?></h5>
                    <form method="post" id="form2" name="form2">

                    <button type="submit" class="btn btn-theme" name="yesbtn" id="yesbtn" >Yes</button>
                        <?php wp_nonce_field('whatso_filter_date', 'whatso_filter_date_nonce'); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($){
        
        $("#yesbtn").click(function(){
            alert("Are you sure you want to send?");
        
        });
    });

</script>