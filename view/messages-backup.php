<?php require_once 'ac_dashboard.php'; 
//update message text
    $data = get_option( 'whatso_notifications' );
    $data = json_decode( $data );
    $whatso_username = $data->whatso_username;
    $whatso_password = $data->whatso_password;
    $whatso_mobileno = $data->whatso_mobileno;
    $whatso_message = $data->whatso_message;
    $customer_notification = $data->customer_notification;
    $whatso_customer_message=$data->whatso_customer_message;
    $whatso_ac_message = $data->whatso_ac_message;
    $whatso_email = $data->whatso_email;

    if (!empty($_POST)) {

    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash( $_POST['message'] )) : '';
     if ( !empty( get_option( 'whatso_notifications' ) ) ) {
     $update_notifications_arr = array(
            'whatso_username'   =>  $whatso_username,
            'whatso_password'   =>  $whatso_password,
            'whatso_mobileno'   =>  $whatso_mobileno,
            'whatso_message'   =>  $whatso_message,
            'whatso_customer_message'=>$whatso_customer_message,
            'customer_notification'=> $customer_notification,
            'whatso_ac_message'    =>  $message,
            'whatso_email' =>$whatso_email,
        );
        $result = update_option( 'whatso_notifications', wp_json_encode( $update_notifications_arr ) );
        }
         echo '<center><p class="mta" style="visibility:visible;"><font color="green" >Message updated successfully!</p></font>' ; 
        wp_redirect("admin.php?page=whatso_floating_widget&tab=messages");
    }
?>
<div class="container">
    <form  method="post" name="form1" class="form-content" action="" >
        <div class="form-body">
            <div class="mb-2">
                    <label class="lbl">Message text </label> <span class="required_star">*</span>
            </div>
            <div class="row mb-2">
                <div class="col-9">
                <textarea class="message form-control" name="message" id="message" autocomplete="off" maxlength="1500" 
                    placeholder="Enter message that you want to be sent when the order is placed."><?php echo esc_html( $whatso_ac_message ); ?></textarea>
                    <lable class="error" id="message_error"><?php esc_html_e( 'Your message must be atleast 2 characters.', 'whatso' ); ?></lable>
                    <lable class="error" id="message_error1"><?php esc_html_e( 'Replace {#var#} with your data.', 'whatso' ); ?></lable>
                    <span id="phonemsg"></span>
            </div>
            </div>
        
            <div class="mb-2">
                    <label class="normal"><?php esc_html_e( 'Use below placeholder fields to dynamically add your order details in the 
                    WhatsApp message', 'whatso' ); ?></label>
            </div>

            <div class="mb-2">
                    <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{storename}' )"><?php esc_html_e( 'Store Name', 'whatso' ); ?></button>
                    <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{productname}' )"><?php esc_html_e( 'Product Name', 'whatso' ); ?></button>
                    <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{siteurl}' )"><?php esc_html_e( 'Site URL', 'whatso' ); ?></button>
                    <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{orderdate}' )"><?php esc_html_e( 'Order Date', 'whatso' ); ?></button>
                    <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{amountwithcurrency}' )"><?php esc_html_e( 'Amount With Currency', 'whatso' ); ?></button>
                    <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{customernumber}' )"><?php esc_html_e( 'Customer Number', 'whatso' ); ?></button>
                    <button type="button" class="placeholder_button" onclick="add_placeholder( 'message', '{customeremail}' )"><?php esc_html_e( 'Customer Email', 'whatso' ); ?></button>
                </div>
            <div class="mb-2">
                    
    
            </div>

            <div class="submit-btn mb-2">
                    <button type="submit" class="btn btn-theme" onclick="FormValidation()">Save</button>
            </div>
        
            <div class="mb-2">
                    <label class="lbl"><?php esc_html_e( 'You can also use message given below', 'whatso' ); ?></label>
            </div>


            <div class="row">
                <div class="col-md-9">
                    <div class="position-relative">
                        <textarea readonly id="myText2"
                            class="message1 form-control">Hi there, we noticed you didn't finish your order on {storename} Visit {siteurl} to try again and place the order. Thanks, {#var#}.Sent from Whatso</textarea>
                        <div class="" style="position: absolute;bottom: 0;right: 10px;">
                            <i class="fa fa-copy toolTip font-24" onclick="getApprovedSMSTemplate1()" class="hov"
                                name="copy" value="copy"></i>
                            <span id="msgCopied1" class="copyLink text-dark toolTipText"></span></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
<script>

message.onkeyup = function(){
    if(this.value.length < 2 || this.value.length > 1500){
        document.getElementById('message_error').style.display = 'block';
        return false;
    }else {
        // document.getElementById('mobile_number_error').style.display = 'none';
        document.getElementById('message_error').style.display = 'none';
        return true;
    }
}
/**
 * Add placeholder on text area
 */

function getInputSelection(el) {
    var start = 0, end = 0, normalizedValue, range, textInputRange, len, endRange;
    if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
        start = el.selectionStart;
        end = el.selectionEnd;
    } else {
        range = document.selection.createRange();

        if (range && range.parentElement() == el) {
            len = el.value.length;
            normalizedValue = el.value.replace(/\r\n/g, "\n");

            // Create a working TextRange that lives only in the input
            textInputRange = el.createTextRange();
            textInputRange.moveToBookmark(range.getBookmark());

            // Check if the start and end of the selection are at the very end
            // of the input, since moveStart/moveEnd doesn't return what we want
            // in those cases
            endRange = el.createTextRange();
            endRange.collapse(false);

            if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                start = end = len;
            } else {
                start = -textInputRange.moveStart("character", -len);
                start += normalizedValue.slice(0, start).split("\n").length - 1;

                if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                    end = len;
                } else {
                    end = -textInputRange.moveEnd("character", -len);
                    end += normalizedValue.slice(0, end).split("\n").length - 1;
                }
            }
        }
    }
    return {
        start: start,
        end: end
    };
}

function offsetToRangeCharacterMove(el, offset) {
    return offset - (el.value.slice(0, offset).split("\r\n").length - 1);
}

function setSelection(el, start, end) {
    if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
        el.selectionStart = start;
        el.selectionEnd = end;
    } else if (typeof el.createTextRange != "undefined") {
        var range = el.createTextRange();
        var startCharMove = offsetToRangeCharacterMove(el, start);
        range.collapse(true);
        if (start == end) {
            range.move("character", startCharMove);
        } else {
            range.moveEnd("character", offsetToRangeCharacterMove(el, end));
            range.moveStart("character", startCharMove);
        }
        range.select();
    }
}
function insertTextAtCaret(el, text) {
    var pos = getInputSelection(el).end;
    var newPos = pos + text.length;
    var val = el.value;
    el.value = val.slice(0, pos) + text + val.slice(pos);
    setSelection(el, newPos, newPos);
}

function add_placeholder( text_area_id, placeholder ) {
    var textarea = document.getElementById( text_area_id );
    textarea.focus();
    insertTextAtCaret(textarea, placeholder);
    return false;
}

 function getApprovedSMSTemplate1() {
            var copyText = document.getElementById("myText2");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            var toolTipText = document.getElementById("msgCopied1");
            document.getElementById('msgCopied1').innerHTML  = "Message Copied!";
            toolTipText.classList.add("show");
            document.execCommand("copy");
            setTimeout(function () { toolTipText.classList.remove("show"); }, 1000);
        }

        function FormValidation(){
            var owner_message=document.getElementById("message").value;
                var word="{#var#}";
                if(owner_message.indexOf(word)!=-1){
                    event.preventDefault ? event.preventDefault() : event.returnValue = false;
                    document.getElementById('message_error1').style.display = 'block';
                    
                    return false;
                }
                else{
                    document.getElementById('message_error1').style.display = 'none';
                   
                  return true;
                   
                }
        }
</script>