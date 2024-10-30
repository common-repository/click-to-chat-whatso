<?php require_once 'ac_dashboard.php';

//User data
$data = get_option('whatso_user_settings');
$data = json_decode($data);

$isDisplayReport = $data->isDisplayReport;

    global $wpdb;
    $table= $wpdb->prefix . "whatso_abandoned_cart";
    $query = $wpdb->prepare("SELECT id,create_date_time,status, message_api_response FROM $table");
    $Response_string = $wpdb->get_results($query);

    $reports_array = array();

foreach($Response_string as $key => $value){

    $single_record = array();
    $id=$value->id;
    $create_date_time=$value->create_date_time;
    //$create_date_time= date_format($create_date_time, 'jS F Y H:i');
    $date = date_create($create_date_time);

    $create_date_time= date_format($date, 'd-M-Y H:i:s');
    $status=$value->status;

    $json_data = json_decode($value->message_api_response);

    if(is_array($json_data)) {

        $array_length = count($json_data);

        if($array_length > 0) {

            $json_data = array_reverse($json_data);

            for($i=0;$i<$array_length;$i++){
                $single_record = array();
                $single_record['status'] = $status;
                $single_record['id'] = $id;
                $single_record['create_date_time'] = $create_date_time;
                    
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

        $single_record['status'] = $status;
        $single_record['id'] = $id;
        $single_record['create_date_time'] = $create_date_time;

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

   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />  
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
   <script>
  jQuery(document).ready(function() {
      jQuery('#datatable').DataTable({
        "order": [[ 3, "desc" ]]
    } );
} );
    </script>
</head>

<body>
    <div class="container mt-3">
    <?php
    if($isDisplayReport!="false") {

        if($Response_string==null || empty($reports_array)) {
            ?>
    <div class="row">
            <div class="col-md-12">
                <div class="card" style="width:fit-content;">
                    <div class="row card-body">

                        <h4 class="dashboardmsg">
                            <?php esc_html_e('Looks like you do not have any saved Abandoned carts yet.But do not worry, as soon as someone fills the Name & Phone number fields of your WooCommerce Checkout form and abandons the cart, the message will automatically appear here.','whatso');?>
                        </h4>

                    </div>
                </div>
            </div>
        </div>
            <?php
        }else{
            ?>
        
    <table id="datatable"class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th><?php echo esc_attr('S.No.');?></th>
                <th><?php echo esc_attr('Message');?></th>
                <th><?php echo esc_attr('Contact No.');?></th>
                <th><?php echo esc_attr('Time');?></th>
                <th><?php echo esc_attr('Status');?></th>
             </tr>
         </thead>
         <tbody>
            <?php
            $sno=0;
            foreach($reports_array as $key => $value){
                $sno++;
                ?> 
            <tr>
                <td><?php echo esc_attr($value['id']); ?></td>
                <td><?php echo wp_kses_post($value['message_text']); ?></td>
                <td><?php echo esc_attr($value['mobile_numbers']); ?></td>
                <td><?php echo esc_attr($value['create_date_time']); ?></td>
                <td><?php echo esc_attr(($value['status'] == '2') ? 'Recovered' : 'Abandoned'); ?></td>
            </tr>

                <?php
            } ?>
         <tbody>
    </table>
            <?php
        }
    }
    else{
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card" style="width:fit-content;">
                    <div class="row card-body">

                        <h5 class="dashboardmsg">
                            Looks like your current plan does not support this feature.<br/> If you want to access this feature please update your plan from <a href="https://www.whatso.net/woocommerce-abandoned-cart" target="_blank">Whatso.net</a>
                        </h5>

                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    </div>
</body>
</html>
