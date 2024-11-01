jQuery(function($){

	var list_form_data = siarashield_object.list_form_data;

	$.each(list_form_data,function(key, value){

		var form_id_class=value.form_id_class;
		if(form_id_class=='class'){
			form_id_class='.';
		}
		else if(form_id_class=='id'){
			form_id_class='#';
		}

		var submit_button_id_class= value.submit_button_id_class;
		if(submit_button_id_class=='class'){
			submit_button_id_class='.';
		}
		else if(submit_button_id_class=='id'){
			submit_button_id_class='#';
		}

		var class_selector = form_id_class+value.form_id_class_name+' '+submit_button_id_class+value.submit_button_id_class_name;

		//$('<div class="SiaraShield"></div>').insertBefore(class_selector);
		var parent_div = $(class_selector).parent() ;
		$('<div class="SiaraShield"></div>').insertBefore(parent_div);
		$(class_selector).addClass('CaptchaSubmit');
		
	});
});