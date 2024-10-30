<?php
    $box_position = '' === WHATSO_Utils::getSetting('box_position') ? 'right' : WHATSO_Utils::getSetting('box_position');
    $availability = array(
      'sunday' => array(
        'hour_start' => 0,
        'minute_start' => 0,
        'hour_end' => 23,
        'minute_end' => 59
      )
      ,
      'monday' => array(
        'hour_start' => 0,
        'minute_start' => 0,
        'hour_end' => 23,
        'minute_end' => 59
      )
      ,
      'tuesday' => array(
        'hour_start' => 0,
        'minute_start' => 0,
        'hour_end' => 23,
        'minute_end' => 59
      )
      ,
        'wednesday' => array(
        'hour_start' => 0,
        'minute_start' => 0,
        'hour_end' => 23,
        'minute_end' => 59
      )
      ,
      'thursday' => array(
        'hour_start' => 0,
        'minute_start' => 0,
        'hour_end' => 23,
        'minute_end' => 59
      )
      ,
      'friday' => array(
        'hour_start' => 0,
        'minute_start' => 0,
        'hour_end' => 23,
        'minute_end' => 59
      )
      ,
      'saturday' => array(
        'hour_start' => 0,
        'minute_start' => 0,
        'hour_end' => 23,
        'minute_end' => 59
      )
    );

    static $stateOptionName = WHATSO_SETTINGS_NAME;
    $option = get_option(self::$stateOptionName);
    $data = json_decode($option, true);

    ?>

 <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quick Setup</title>
        </style>
    </head>
    
    <!--html form for quicksetup-->
    <body style="background: #E5E5E5;">
        <div class="container">
                <h1> <?php esc_html_e('Click to Chat','whatso');?></h1>
                <ul class="breadcrumb">
                    <li><a href="admin.php?page=whatso_floating_menu_setup"><b><?php  esc_html_e('Whatso','whatso');?></b></a></li>
                    <li><a href="admin.php?page=whatso_floating_ctc_setup"><b><?php  esc_html_e('Click to Chat','whatso');?></b></a></li>
                    <li><b>Quick Setup</b></li>
                </ul>
    
                <h1 class="heading"><?php esc_html_e('Click to chat quick setup','whatso');?></h1>
            
            <div class="box-head">
                <form  method="post" name="form1">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="lblheading" for="email"><?php esc_html_e('Your Name','whatso');?> </label><span class="starclr">*</span>
                                <input type="hidden" name="whatso_name"  value="whatso_name"> 
                                <input type="text" onkeypress="return blockSpecialChar(event)" onClick="this.setSelectionRange(0, this.value.length)" 
                                name="whatso_name1" id="whatso_name1" placeholder='<?php  esc_html_e('Enter your name');?>' autocomplete="off" class="input-line full-width"
                                onpaste="return false"  maxlength="50" required>
                                <span id="errormsg"></span>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="lblheading" for="email"><?php esc_html_e('Mobile Number','whatso');?> </label><span class="starclr">*</span>
                                <input type="hidden" name="whatso_number" value="whatso_number"> 
                                <input type="text" name="whatso_number1" id="whatso_number1" onClick="this.setSelectionRange(0, this.value.length)" 
                                placeholder='<?php  esc_html_e(' Mobile number with country code');?>' autocomplete="off" class='input-line full-width'  maxlength="15"
                                onpaste="return false" onkeypress="return isNumber(event)"  required>
                                <span id="phonemsg"></span><br/>
                            </div>
                        </div>
                    </div>

                  
                    <input type="hidden" name="whatso_title" id="whatso_title" value="whatso_title">
                
                    <input type="hidden" name="whatso_predefined_text" id="whatso_predefined_text" value="whatso_predefined_text">
                
                    <input type="hidden" name="whatso_button_label" id="whatso_button_label" value="whatso_button_label">
                
                    <input type="hidden" name="whatso_offline_text" id="whatso_offline_text" value="whatso_offline_text">
                
                    <input type="hidden" name="whatso_hide_on_large_screen" id="whatso_hide_on_large_screen" value="whatso_hide_on_large_screen">
                
                    <input type="hidden" name="whatso_hide_on_small_screen" id="whatso_hide_on_small_screen" value="whatso_hide_on_small_screen">
                
                    <input type="hidden" name="whatso_pin_account" id="whatso_pin_account" value="whatso_pin_account">
                
                    <input type="hidden" name="whatso_background_color_on_hover" id="whatso_background_color_on_hover" value="whatso_background_color_on_hover">
                
                    <input type="hidden" name="whatso_text_color" id="whatso_text_color" value="whatso_text_color">
                
                    <input type="hidden" name="whatso_text_color_on_hover" id="whatso_text_color_on_hover" value="whatso_text_color_on_hover">
                
                    <input type="hidden" name="whatso_included_ids" id="whatso_included_ids" value="whatso_included_ids">
                
                    <input type="hidden" name="whatso_excluded_ids" id="whatso_excluded_ids" value="whatso_excluded_ids">
                
                    <input type="hidden" name="whatso_target_languages" id="whatso_target_languages" value="whatso_target_languages">
                

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label class="lblheading" for="email"><?php esc_html_e('Widget position','whatso');?> </label><span class="starclr">*</span>
                                <select id="box_position" name="box_position" class='input-line full-width' style="font-size:15px;font-family:roboto">
                                    <option value="left" name="box_position" id="box_position_left" <?php echo esc_html('left') === esc_attr($box_position) ? 'selected' : ''; ?> class='input-line full-width' ><label for="box_position_left"><?php esc_html_e('Bottom Left', 'whatso'); ?></label></option>
                                    <option value="right" name="box_position"  id="box_position_right" <?php echo esc_html('right') === esc_attr($box_position) ? 'selected' : ''; ?> class='input-line full-width'><label for="box_position_right"><?php esc_html_e('Bottom Right', 'whatso'); ?></label></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group mb-2">
                                <label for="" class="lblheading mb-2"><?php esc_html_e('Widget background color :','whatso');?></label><br/>
                                <input name="toggle_background_color" type="radio" style="background-color: #1EA185;height:25px;width:25px;" id="toggle_background_color" value="<?php  esc_html_e('#1EA185');?>">
                                <input name="toggle_background_color" type="radio" style="background-color: #2F80ED;height:25px;width:25px;" id="toggle_background_color" value="<?php  esc_html_e('#2F80ED');?>">
                                <input name="toggle_background_color" type="radio" style="background-color: #F29B26;height:25px;width:25px;" id="toggle_background_color" value="<?php  esc_html_e('#F29B26');?>">
                                <input name="toggle_background_color" type="radio" style="background-color: #BD392F;height:25px;width:25px;" id="toggle_background_color" value="<?php  esc_html_e('#BD392F');?>">
                                <input name="toggle_background_color" type="radio" style="background-color: #FFCA63;height:25px;width:25px;" id="toggle_background_color" value="<?php  esc_html_e('#FFCA63');?>" >
                                <input name="toggle_background_color" type="radio" style="background-color: #FC3333;height:25px;width:25px;" id="toggle_background_color" value="<?php  esc_html_e('#FC3333');?>" >
                                <input name="toggle_background_color" type="radio" style="background-color: #0FD9A2;height:25px;width:25px;" id="toggle_background_color" value="<?php  esc_html_e('#0FD9A2');?>" >
                                <input name="toggle_background_color" type="radio" style="background-color: #9BBB5C;height:25px;width:25px;" id="toggle_background_color" value="<?php  esc_html_e('#9BBB5C');?>" >
      
                            </div>
                        </div>
                    </div>

                    
                        <div class="mb-2">
                            <label class="lblheading"><?php esc_html_e('Select widgets page','whatso');?> </label><span class="starclr">*</span>
                            <input type="hidden" name="whatso_target" id="whatso_target" value="whatso_target"> 
                        </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <label for="home" class="check_label"><?php esc_html_e('Homepage','whatso');?>
                                            <input type="checkbox" class="home" name="whatso_target1[]" value="home" id="home" checked>
                                            <span class="checkmark"></span>
                                        </label> <br>    
                                    </div>
                                    <div class="col-6">
                                        <label for="blog" class="check_label"><?php esc_html_e('Blog index','whatso');?>
                                            <input type="checkbox" class="blogIndex" name="whatso_target1[]" value="blog" id="blog" checked>
                                            <span class="checkmark"></span>
                                        </label><br>      
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="archive" class="check_label"><?php esc_html_e('Archives','whatso');?>
                                            <input type="checkbox" class="archive" name="whatso_target1[]" value="archive" id="archive" checked>
                                            <span class="checkmark"></span>
                                        </label><br> 
                                    </div>
                                    <div class="col-6">
                                        <label for="page" class="check_label"><?php esc_html_e('Pages','whatso');?>
                                            <input type="checkbox" class="page" name="whatso_target1[]" value="page" id="page" checked>
                                            <span class="checkmark"></span>
                                        </label><br>      
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="post" class="check_label"><?php esc_html_e('Blog Posts','whatso');?>
                                            <input type="checkbox" class="blogPost" name="whatso_target1[]" value="post" id="post" checked>
                                            <span class="checkmark"></span>
                                        </label><br> 
                                    </div>
                                </div>
                    <div class="row">
                        <span id="msg3"></span>
                    </div>
                    <?php wp_nonce_field('whatso_quick_setup', 'whatso_quick_setup_nonce'); ?>
                    <div class="row">
                        <div class="col-6">
                            <input type="hidden" name="whatso_availability" id="whatso_availability" value="whatso_availability"> 
                            <div class="submit_button">
                                <button type="button" class="btn btn-theme" onclick="FormValidation()">Create Account</button>
                            </div>
                        </div>
                     </div>
                </form>
            </div>
        </div>

        <?php 
        
        if (!empty($_POST)) {
            $legit = true;
            if (! isset($_POST['whatso_quick_setup_nonce']) ) {
                $legit = false;
            }
            $nonce = isset($_POST['whatso_quick_setup_nonce']) ? sanitize_text_field(wp_unslash($_POST['whatso_quick_setup_nonce'])) : '';
            if (! wp_verify_nonce($nonce, 'whatso_quick_setup') ) {
                $legit = false;
            }
            if (! $legit ) {
                wp_safe_redirect(add_query_arg());
                exit();
            }

            $whatso_name1 = isset($_POST['whatso_name1']) ? sanitize_text_field(wp_unslash($_POST['whatso_name1'])) : '';
            $whatso_name = isset($_POST['whatso_name']) ? sanitize_text_field(wp_unslash($_POST['whatso_name'])) : '';
            $whatso_number1 = isset($_POST['whatso_number1']) ? sanitize_text_field(wp_unslash($_POST['whatso_number1'])) : '';
            $whatso_number = isset($_POST['whatso_number']) ? sanitize_text_field(wp_unslash($_POST['whatso_number'])) : '';
            $whatso_target1= isset($_POST['whatso_target1']) ? sanitize_text_field(wp_unslash($_POST['whatso_target1'])) : '';
            $whatso_target= isset($_POST['whatso_target']) ? sanitize_text_field(wp_unslash($_POST['whatso_target'])) : '';
            $whatso_title = isset($_POST['whatso_title']) ? sanitize_text_field(wp_unslash($_POST['whatso_title'])) : '';
            $whatso_predefined_text = isset($_POST['whatso_predefined_text']) ? sanitize_text_field(wp_unslash($_POST['whatso_predefined_text'])) : '';  
            $whatso_button_label = isset($_POST['whatso_button_label']) ? sanitize_text_field(wp_unslash($_POST['whatso_button_label'])) : '';    
            $whatso_offline_text = isset($_POST['whatso_offline_text']) ? sanitize_text_field(wp_unslash($_POST['whatso_offline_text'])) : '';
            $whatso_hide_on_large_screen = isset($_POST['whatso_hide_on_large_screen']) ? sanitize_text_field(wp_unslash($_POST['whatso_hide_on_large_screen'])) : '';
            $whatso_hide_on_small_screen = isset($_POST['whatso_hide_on_small_screen']) ? sanitize_text_field(wp_unslash($_POST['whatso_hide_on_small_screen'])) : '';
            $whatso_pin_account = isset($_POST['whatso_pin_account']) ? sanitize_text_field(wp_unslash($_POST['whatso_pin_account'])) : '';
            $whatso_background_color = isset($_POST['whatso_background_color ']) ? sanitize_text_field(wp_unslash($_POST['whatso_background_color '])) : '';
            $whatso_background_color_on_hover = isset($_POST['whatso_background_color_on_hover']) ? sanitize_text_field(wp_unslash($_POST['whatso_background_color_on_hover'])) : '';
            $whatso_text_color = isset($_POST['whatso_text_color']) ? sanitize_text_field(wp_unslash($_POST['whatso_text_color'])) : '';
            $whatso_text_color_on_hover = isset($_POST['whatso_text_color_on_hover']) ? sanitize_text_field(wp_unslash($_POST['whatso_text_color_on_hover'])) : '';
            $whatso_included_ids = isset($_POST['whatso_included_ids']) ? sanitize_text_field(wp_unslash($_POST['whatso_included_ids'])) : '';
            $whatso_excluded_ids = isset($_POST['whatso_excluded_ids']) ? sanitize_text_field(wp_unslash($_POST['whatso_excluded_ids'])) : '';
            $whatso_target_languages = isset($_POST['whatso_target_languages']) ? sanitize_text_field(wp_unslash($_POST['whatso_target_languages'])) : '';
            $box_position = isset($_POST['box_position']) ? sanitize_text_field(wp_unslash($_POST['box_position'])) : '';
            $toggle_background_color = isset($_POST['toggle_background_color']) ? sanitize_text_field(wp_unslash($_POST['toggle_background_color'])) : '';
            $whatso_availability = isset($_POST['whatso_availability']) ? sanitize_text_field(wp_unslash($_POST['whatso_availability'])) : '';
            global $wpdb;
            $table = $wpdb->prefix . "postmeta";
            $table1 = $wpdb->prefix . "posts";
            $table2= $wpdb->prefix . "options";
            $date=date('y.m.d h:i:s');
        
            $post_data = array(
                'ID' => "",
                'post_author'=>"1",
                'post_date'=>$date,
                'post_date_gmt'=>$date,
                'post_title'    =>  $whatso_name1,
                'comment_status'=>"closed",
                'ping_status'=>"closed",
                'post_name'=> $whatso_name1,
                'post_type' => "whatso_accounts"

            );
            $format = array(
                '%s',
                '%s',

          
            );
            $success=$wpdb->insert($table1, $post_data, $format);
            $post_title= $whatso_name1;
            $post_id    = $wpdb->get_results(" SELECT ID  FROM $wpdb->posts WHERE post_title = '$post_title' ");
            $array = json_decode(json_encode($post_id), true);
            foreach($array as $arr2){
                foreach($arr2 as $id=>$p_id){
                    $p_id;
                }
            }
       
            $guid = get_permalink($p_id);
            $wpdb->update( 
                $table1, 
                array( 
                  'guid' => $guid,
                ), 
                array( 'ID' => $p_id )
            );
       
            $data = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_name,
                'meta_value'    => $whatso_name1,

            );
            $success=$wpdb->insert($table, $data, $format);
        
            $number = $whatso_number1;
            $num=substr($number, 0, 1);
            if($num != "+") {
                $num2 = "+";
                $number=$num2.$number;
            }
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_number,
                'meta_value'    => $number,

            );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_title,
                'meta_value'    => '',

            );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_predefined_text,
                'meta_value'    => 'Hi!',

            );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_button_label,
                'meta_value'    => '',

            );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_offline_text,
                'meta_value'    => '',
    
              );
            $success=$wpdb->insert($table, $data1, $format);
      
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_hide_on_large_screen,
                'meta_value'    => 'off',
  
              );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_hide_on_small_screen,
                'meta_value'    => 'off',
  
              );
            $success=$wpdb->insert($table, $data1, $format); 
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_pin_account,
                'meta_value'    => 'off',
  
              );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
              'post_id' => $p_id,
              'meta_key' => $whatso_background_color ,
              'meta_value'    =>  $toggle_background_color,

            );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_background_color_on_hover,
                'meta_value'    => '',
  
              );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_text_color,
                'meta_value'    => '',
  
              );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_text_color_on_hover,
                'meta_value'    => '',
  
              );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_included_ids,
                'meta_value'    => '[]',
  
              );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_excluded_ids,
                'meta_value'    => '[]',
  
              );
            $success=$wpdb->insert($table, $data1, $format);
        
            $data1 = array(
                'post_id' => $p_id,
                'meta_key' => $whatso_target_languages,
                'meta_value'    => '[]',
    
              );
            $success=$wpdb->insert($table, $data1, $format);

            if (isset($whatso_target1)) {
                foreach (rest_sanitize_array($_POST['whatso_target1']) as $page) {
                    $pages = " " . $page;
                    $t[] = sanitize_text_field($pages);
                }
            }else{
                $pages= [];
            }  
        
            $data1 = array(
            'post_id' => $p_id,
            'meta_key' => $whatso_target,
            'meta_value'    => wp_json_encode($t),
              );
            $success=$wpdb->insert($table, $data1, $format);
       
            $data1 = array(
            'post_id' => $p_id,
            'meta_key' => $whatso_availability,
            'meta_value'    => wp_json_encode($availability),

            );
            $success=$wpdb->insert($table, $data1, $format);
    
            WHATSO_Utils::updateSetting('box_position', $box_position);
            $category='selected_accounts_for_widget';
            $selected_accounts= json_decode(WHATSO_Utils::getSetting($category, ''), true);
          
            if($selected_accounts == []) {
      
                $p_id = is_array($p_id) ? $p_id : array($p_id,0,0);

                WHATSO_Utils::updateSetting('selected_accounts_for_widget', wp_json_encode($p_id)); 

            }
            else{

                array_push($selected_accounts, $p_id); 
                WHATSO_Utils::updateSetting('selected_accounts_for_widget', wp_json_encode($selected_accounts)); 

            } 
                WHATSO_Utils::updateSetting('toggle_background_color', $toggle_background_color);
                WHATSO_Utils::generateCustomCSS();          
        
            if($success) {
                      echo wp_kses_post('<p class="mta" style="visibility:visible;"><font color="green" >Account created successfully. You can check widget on website. Click here for more &nbsp;<a href="edit.php?post_type=whatso_accounts">Settings</a>.</p></font>') ;
            }
            else{
                      echo wp_kses_post('<p>Please try again!</p>' );
            }  
        }
        ?>
        
        <script>
            function blockSpecialChar(e){
                    var k;
                    document.all ? k = e.keyCode : k = e.which;
                    //alert(k); //39 == '
                    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57)||k == 39);
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
                    if( !regex.test(key) ) {
                      theEvent.returnValue = false;
                      if(theEvent.preventDefault) theEvent.preventDefault();
                    }

            }
            function FormValidation(){
                    var txtname = document.getElementById("whatso_name1").value;
                    var txtmobile = document.getElementById("whatso_number1").value;
                    var phoneno = /^[0-9]*$/;
                    //Check Name
                    if(txtname.length < 2 ||txtname.length >50 )
                    {
                      document.getElementById('errormsg').innerHTML  = "Name must be atleast 2 characters";
                      return false;
                    }else{
                        document.getElementById('errormsg').innerHTML  = "";
                    }
        

                    //Check Mobile
                    if(txtmobile.length < 5 ||txtmobile.length >15 )
                    {
                      document.getElementById('phonemsg').innerHTML  = "Number must be atleast 5 digits";
                      return false;
                    }else{
                        document.getElementById('errormsg').innerHTML  = "";
                    }

                    // Check Checkbox
                    if(!document.getElementById('home').checked && !document.getElementById('blog').checked && !document.getElementById('archive').checked && !document.getElementById('page').checked && !document.getElementById('post').checked)
                    {
                      document.getElementById('msg3').innerHTML  = "Please select atleast one checkbox";
                      return false;

                    }else{
                        document.getElementById('errormsg').innerHTML  = "";
                    }
                    //Submit
                    document.forms["form1"].submit();
             }
        </script>
    </body>
</html>
