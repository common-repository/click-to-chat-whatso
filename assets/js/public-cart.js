(function( $ ) {
	'use strict';
	 
	 jQuery(document).ready(function(e){

		var timer2;
		//console.log(whatso_public_data);
		function getFieldData() { //Reading WooCommerce field values
			if(jQuery("#billing_email").length > 0 || jQuery("#billing_phone").length > 0){ //If at least one of these two fields exist on page
				var whatso_abandoned_email = jQuery("#billing_email").val();
				if (typeof whatso_abandoned_email === 'undefined' || whatso_abandoned_email === null) { //If email field does not exist on the Checkout form
				   whatso_abandoned_email = '';
				}
				var atposition = whatso_abandoned_email.indexOf("@");
				var dotposition = whatso_abandoned_email.lastIndexOf(".");

				var whatso_abandoned_phone = jQuery("#billing_phone").val();
				if (typeof whatso_abandoned_phone === 'undefined' || whatso_abandoned_phone === null) { //If phone number field does not exist on the Checkout form
				   whatso_abandoned_phone = '';
				}
				
				clearTimeout(timer2);

				if (!(atposition < 1 || dotposition < atposition + 2 || dotposition + 2 >= whatso_abandoned_email.length) || whatso_abandoned_phone.length >= 1){ //Checking if the email field is valid or phone number is longer than 1 digit
					//If Email or Phone valid
					var whatso_abandoned_name = jQuery("#billing_first_name").val();
					var whatso_abandoned_surname = jQuery("#billing_last_name").val();
					var whatso_abandoned_phone = jQuery("#billing_phone").val();
					var whatso_abandoned_country = jQuery("#billing_country").val();
					
					var data_ = {
						action:								"whatso_abandoned_save",
						nonce: 									whatso_public_data.nonce,
						whatso_abandoned_email:					whatso_abandoned_email,
						whatso_abandoned_name:					whatso_abandoned_name,
						whatso_abandoned_surname:					whatso_abandoned_surname,
						whatso_abandoned_phone:					whatso_abandoned_phone,
						whatso_abandoned_country:					whatso_abandoned_country
					}

					timer2 = setTimeout(function(){
						$.ajax({
							type: "POST",
							url: whatso_public_data.ajax_url,
							data: data_,
							success: function(result) {
								//console.log(result);
							}
						
						});
						// jQuery.post(whatso_public_data.ajaxurl, data, //Ajaxurl coming from localized script and contains the link to wp-admin/admin-ajax.php file that handles AJAX requests on Wordpress
						// function(response) {
						// 	//console.log(response);
						// 	//If we have successfully captured abandoned cart, we do not have to display Exit intent form anymore
						// });
						
					}, 800);
				}else{
					//console.log("Not a valid email or phone address");
				}
			}
		}
		
		jQuery(document).on('keyup', '#billing_phone', getFieldData);
		jQuery(document).on('keyup', '#billing_email', getFieldData);
		jQuery(document).on('keyup', '#billing_first_name', getFieldData);
		jQuery(document).on('blur', '#billing_phone', getFieldData);
		jQuery(document).on('blur', '#billing_email', getFieldData);
		jQuery(document).on('blur', '#billing_first_name', getFieldData);

		//All action happens on or after changing Email or Phone fields or any other fields in the Checkout form. All Checkout form input fields are now triggering plugin action. Data saved to Database only after Email or Phone fields have been entered.
		getFieldData(); //Automatically collect and save input field data if input fields already filled on page load
		
	});

})( jQuery );