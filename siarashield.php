<?php
/*
Plugin Name: SiaraShield
Plugin URI: https://wordpress.org/plugins/siara-shield/
Description: CyberSiara
Author: CyberSiara
Version: 3.4
*/
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'siarashield_captcha_menu');

//this should be added if we are introducing new forms
//add_action( 'wpcf7_submit', 'action_wpcf7_submit', 10, 3 );

//region add action
add_action( 'wpcf7_before_send_mail', 'on_submit', 10, 3 );
//end region

function on_submit( $form, &$abort, $submission )
{
	$data = $submission->get_posted_data();

	$token = sanitize_text_field($data['CyberSiaraToken']);
	
	$ip_curl = curl_init();

	curl_setopt_array($ip_curl, array(
	CURLOPT_URL => 'http://checkip.dyndns.org',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
	CURLOPT_HTTPHEADER => array(
		'Accept: application/json'
	),
	));

	$response = curl_exec($ip_curl);

	curl_close($ip_curl);
	$client_ip = trim(strip_tags(@explode("Current IP Address: ",$response)[1]));

	if($token)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://embed.mycybersiara.com/api/validate-token",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer ".$token,
			"key: ".get_option('siarashield_private_key'),
			"ip: ".$client_ip
		),
		));
		$response = curl_exec($curl);
		
		curl_close($curl);
		if (is_string($response))
		{
			$response = json_decode($response,true);
			
			if($response['HttpStatusCode'])
			{
				if($response['HttpStatusCode']!=200)
				{
					$abort = TRUE;
				}
			}
			else
			{
				$abort = TRUE;
			}
		}
		else
		{
			$abort = TRUE;
		}
	}
	else
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://embed.mycybersiara.com/api/validate-token",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer ".$token,
			"key: ".get_option('siarashield_private_key'),
			"ip: ".$client_ip
		),
		));
		$response = curl_exec($curl);
		
		curl_close($curl);
		if (is_string($response))
		{
			$response = json_decode($response,true);
			
			if($response['HttpStatusCode'])
			{
				if($response['HttpStatusCode']!=200)
				{
					$abort = TRUE;
				}
			}
			else
			{
				$abort = TRUE;
			}
		}
		else
		{
			$abort = TRUE;
		}
	}
}

add_action( 'comment_post', 'custom_validate_comment', 10, 2 );

function custom_validate_comment($comment_ID, $comment_approved) {
	$abort = false;	
	$token = sanitize_text_field($_REQUEST['CyberSiaraToken']);
	if($token)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://embed.mycybersiara.com/api/validate-token",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer ".$token,
			"key: ".get_option('siarashield_private_key'),
			"ip: ".$_SERVER['REMOTE_ADDR']
		),
		));
		$response = curl_exec($curl);
		
		curl_close($curl);
		if (is_string($response))
		{
			$response = json_decode($response,true);			
			if($response['HttpStatusCode'])
			{
				if($response['HttpStatusCode']!=200)
				{
					$abort = TRUE;
				}
			}
			else
			{
				$abort = TRUE;
			}
		}
		else
		{
			$abort = TRUE;
		}
	}
	else
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://embed.mycybersiara.com/api/validate-token",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer ".$token,
			"key: ".get_option('siarashield_private_key'),
			"ip: ".$_SERVER['REMOTE_ADDR']
		),
		));
		$response = curl_exec($curl);
		
		curl_close($curl);
		if (is_string($response))
		{
			$response = json_decode($response,true);			
			if($response['HttpStatusCode'])
			{
				if($response['HttpStatusCode']!=200)
				{
					$abort = TRUE;
				}
			}
			else
			{
				$abort = TRUE;
			}
		}
		else
		{
			$abort = TRUE;
		}
	}
}
//add_action('pre_comment_on_post', 'custom_validate_comment');

add_action( 'wp_ajax_ajaxcomments', 'custom_submit_ajax_comment' ); // wp_ajax_{action} for registered user
add_action( 'wp_ajax_nopriv_ajaxcomments', 'custom_submit_ajax_comment' ); // wp_ajax_nopriv_{action} for not registered users
 
function custom_submit_ajax_comment(){
	/*
	 * Wow, this cool function appeared in WordPress 4.4.0, before that my code was muuuuch mooore longer
	 *
	 * @since 4.4.0
	 */
	$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
	if ( is_wp_error( $comment ) ) {
		$error_data = intval( $comment->get_error_data() );
		if ( ! empty( $error_data ) ) {
			wp_die( '<p>' . $comment->get_error_message() . '</p>', __( 'Comment Submission Failure' ), array( 'response' => $error_data, 'back_link' => true ) );
		} else {
			wp_die( 'Unknown error' );
		}
	}
 
	/*
	 * Set Cookies
	 */
	$user = wp_get_current_user();
	do_action('set_comment_cookies', $comment, $user);
 
	/*
	 * If you do not like this loop, pass the comment depth from JavaScript code
	 */
	$comment_depth = 1;
	$comment_parent = $comment->comment_parent;
	while( $comment_parent ){
		$comment_depth++;
		$parent_comment = get_comment( $comment_parent );
		$comment_parent = $parent_comment->comment_parent;
	}
 
 	/*
 	 * Set the globals, so our comment functions below will work correctly
 	 */
	$GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $comment_depth;
 
	/*
	 * Here is the comment template, you can configure it for your website
	 * or you can try to find a ready function in your theme files
	 */
	$comment_html = '<li ' . comment_class('', null, null, false ) . ' id="comment-' . get_comment_ID() . '">
		<article class="comment-body" id="div-comment-' . get_comment_ID() . '">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					' . get_avatar( $comment, 100 ) . '
					<b class="fn">' . get_comment_author_link() . '</b> <span class="says">says:</span>
				</div>
				<div class="comment-metadata">
					<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . sprintf('%1$s at %2$s', get_comment_date(),  get_comment_time() ) . '</a>';
 
					if( $edit_link = get_edit_comment_link() )
						$comment_html .= '<span class="edit-link"><a class="comment-edit-link" href="' . $edit_link . '">Edit</a></span>';
 
				$comment_html .= '</div>';
				if ( $comment->comment_approved == '0' )
					$comment_html .= '<p class="comment-awaiting-moderation">Your comment is awaiting moderation.</p>';
 
			$comment_html .= '</footer>
			<div class="comment-content">' . apply_filters( 'comment_text', get_comment_text( $comment ), $comment ) . '</div>
		</article>
	</li>';
	echo $comment_html;
 
	die();
 
}
/*Comment Validation*/
function comment_validation_init() {
  if(is_single() && comments_open() ) { ?>        
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script type="text/javascript">
	jQuery(document).ready(function($) {
        $('#commentform').validate({
            rules: {
                author: {
                    required: true,
                    minlength: 2
                },
                email: {
                    required: true,
                    email: true
                },
                comment: {
                    required: true,
                    minlength: 20
                }
            },
            messages: {
                author: "Please provide a name",
                email: "Please enter a valid email address.",
                comment: "Please fill the required field"
            },
            errorElement: "div",
            errorPlacement: function(error, element) {
                element.after(error);
            }
        });
    });
/*
 * Let's begin with validation functions
 */
 jQuery.extend(jQuery.fn, {
	/*
	 * check if field value lenth more than 3 symbols ( for name and comment ) 
	 */
	validate: function () {
		if (jQuery(this).val().length < 3) {jQuery(this).addClass('error');return false} else {jQuery(this).removeClass('error');return true}
	},
	/*
	 * check if email is correct
	 * add to your CSS the styles of .error field, for example border-color:red;
	 */
	validateEmail: function () {
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/,
		    emailToValidate = jQuery(this).val();
		if (!emailReg.test( emailToValidate ) || emailToValidate == "") {
			jQuery(this).addClass('error');return false
		} else {
			jQuery(this).removeClass('error');return true
		}
	},
});
 
jQuery(function($){
 
	/*
	 * On comment form submit
	 */
	$( '#commentform' ).submit(function(){
 
		// define some vars
		var button = $('#submit'), // submit button
		    respond = $('#respond'), // comment form container
		    commentlist = $('.comment-list'), // comment list container
		    cancelreplylink = $('#cancel-comment-reply-link');
 
		// if user is logged in, do not validate author and email fields
		if( $( '#author' ).length )
			$( '#author' ).validate();
 
		if( $( '#email' ).length )
			$( '#email' ).validateEmail();
 
		// validate comment in any case
		$( '#comment' ).validate();
 
		// if comment form isn't in process, submit it
		if ( !button.hasClass( 'loadingform' ) && !$( '#author' ).hasClass( 'error' ) && !$( '#email' ).hasClass( 'error' ) && !$( '#comment' ).hasClass( 'error' ) ){
 
			// ajax request
			$.ajax({
				type : 'POST',
				url : '<?php echo admin_url( 'admin-ajax.php' ); ?>', // admin-ajax.php URL
				data: $(this).serialize() + '&action=ajaxcomments', // send form data + action parameter
				beforeSend: function(xhr){
					// what to do just after the form has been submitted
					button.addClass('loadingform').val('Loading...');
				},
				error: function (request, status, error) {
					if( status == 500 ){
						alert( 'Error while adding comment' );
					} else if( status == 'timeout' ){
						alert('Error: Server doesn\'t respond.');
					} else {
						// process WordPress errors
						var wpErrorHtml = request.responseText.split("<p>"),
							wpErrorStr = wpErrorHtml[1].split("</p>");
 
						alert( wpErrorStr[0] );
					}
				},
				success: function ( addedCommentHTML ) {
 
					// if this post already has comments
					if( commentlist.length > 0 ){
 
						// if in reply to another comment
						if( respond.parent().hasClass( 'comment' ) ){
 
							// if the other replies exist
							if( respond.parent().children( '.children' ).length ){	
								respond.parent().children( '.children' ).append( addedCommentHTML );
							} else {
								// if no replies, add <ol class="children">
								addedCommentHTML = '<ol class="children">' + addedCommentHTML + '</ol>';
								respond.parent().append( addedCommentHTML );
							}
							// close respond form
							cancelreplylink.trigger("click");
						} else {
							// simple comment
							commentlist.append( addedCommentHTML );
						}
					}else{
						// if no comments yet
						addedCommentHTML = '<ol class="comment-list">' + addedCommentHTML + '</ol>';
						respond.before( $(addedCommentHTML) );
					}
					// clear textarea field
					$('#comment').val('');
				},
				complete: function(){
					// what to do after a comment has been added
					button.removeClass( 'loadingform' ).val( 'Post Comment' );
				}
			});
		}
		return false;
	});
});	
    </script>
    <?php
    }
}
add_action('wp_footer', 'comment_validation_init');

function siarashield_captcha_menu() {
    add_menu_page('Siara Shield Settings',
            'Siara Shield',
            'manage_options',
            'siarashield-captcha',
            'siarashield_captcha_settings'
	);
}

function siarashield_captcha_settings(){?>
<div class="wrap">
  <form action="options.php" method="post">
    <?php settings_fields("section");?>

    <?php
	do_settings_sections("captcha-options");
	?>
<style>
table.form-table.form-selection, table.form-table.form-selection th ,table.form-table.form-selection td {
    border: 1px solid #c3c4c7;
}	
.field_group,.select-group{
    width: 100%;
}
.form-selection thead th{
	text-align: center;
}
.add-more-field-container{
	margin-top: 20px;
}
.step-section p{
	font-size: 16px;
}
</style>
<table class="form-table form-selection wp-list-table widefat fixed striped">
	<thead>
	  <tr>
	    <th rowspan="2">Form Name</th>
	    <th colspan="2">Form Section</th>
	    <th colspan="2">Submit Button Section</th>
	    <th rowspan="2">Action</th>
	  </tr>
	 <tr> 
	  	<th>ID/Class</th> 
	  	<th>ID/Class (Name)</th> 
	  	<th>ID/Class</th> 
	  	<th>ID/Class (Name)</th> 
	  </tr>
	</thead>
	<?php
		$list_form_data =  get_option('list_form_data');
	?>
	<tbody class="all_input_fields_wrap"> 
	<?php
		if($list_form_data) {
			foreach ($list_form_data as $key => $value)	{
				$last_key=$key;
			?>
			<tr data-key="<?php echo $key; ?>">
			    <td><input type="text" id="form_name" class="field_group" name="list_form_data[<?php echo $key; ?>][form_name]" value="<?php echo trim($value['form_name']); ?>" />
			   </td>
			    <td>
			    	<?php  $form_id_class = $value['form_id_class']; ?>
					<select name="list_form_data[<?php echo $key; ?>][form_id_class]" id="form_id_class" class="select-group">
			    		<option value="id" <?php if ($form_id_class == "id") { echo 'selected'; } ?> >ID</option>
			    		<option value="class" <?php if ($form_id_class == "class") { echo 'selected'; } ?> >Class</option>
			    	</select>
			    </td>
			    <td><input type="text" id="form_id_class_name" class="field_group" name="list_form_data[<?php echo $key; ?>][form_id_class_name]" value="<?php echo trim($value['form_id_class_name']) ?>" /></td>
			    <td>
			    	<?php  $submit_button_id_class = $value['submit_button_id_class']; ?>
			    	<select name="list_form_data[<?php echo $key; ?>][submit_button_id_class]"  id="submit_button_id_class" class="select-group">
			    		<option value="id" <?php if ($submit_button_id_class == "id") { echo 'selected'; } ?> >ID</option>
			    		<option value="class" <?php if ($submit_button_id_class == "class") { echo 'selected'; } ?> >Class</option>
			    	</select>
			    </td>
			    <td><input type="text" id="submit_button_id_class_name" class="field_group" name="list_form_data[<?php echo $key; ?>][submit_button_id_class_name]" value="<?php echo trim($value['submit_button_id_class_name']) ?>" />
			    </td>
			    <td><img src="<?php echo plugin_dir_url(__FILE__) ?>/images/trash.png" class="remove_field"></td>
		    </tr>
			<?php	
				}
			}
				else
				{
			?>
			<tr data-key="0">
			    <td><input type="text" id="form_name" class="field_group" name="list_form_data[0][form_name]" />
			   	</td>
			    <td>
					<select name="list_form_data[0][form_id_class]" id="form_id_class" class="select-group" >
			    		<option value="id">ID</option>
			    		<option value="class">Class</option>
			    	</select>
			    </td>
			    <td><input type="text" id="form_id_class_name" class="field_group" name="list_form_data[0][form_id_class_name]" /></td>
			    <td>
			    	<select name="list_form_data[0][submit_button_id_class]"  id="submit_button_id_class" class="select-group">
			    		<option value="id">ID</option>
			    		<option value="class">Class</option>
			    	</select>
			    </td>
			    <td><input type="text" id="submit_button_id_class_name" class="field_group" name="list_form_data[0][submit_button_id_class_name]"/>
			    </td>
			    <td></td>
		  	</tr>
		<?php
			}
		?>
 	</tbody>	
</table>
<div class="add-more-field-container">
	<a class="add_more_field_button "><img src="<?php echo plugin_dir_url(__FILE__) ?>/images/plus.png" title="Add More Field" ></a>
</div>
<script type="text/javascript">
		jQuery(document).ready(function($) {
			
		    // Dynamic input fields ( Add / Remove input fields )
		    var max_fields      = 1000; //maximum input boxes allowed
		    var wrapper         = $(".all_input_fields_wrap"); //Fields wrapper
		    var add_button      = $(".add_more_field_button"); //Add button ID

		    $(add_button).click(function(e){ //on add input button click
		    	var data_key=$(".all_input_fields_wrap tr:last").attr('data-key');
		    	data_key++;
		   		var x=data_key;
		        e.preventDefault();
		        if(x <= max_fields){ //max input box allowed
		             //text box increment
		            $(wrapper).append('<tr data-key="'+x+'"><td><input type="text" id="form_name" class="field_group" name="list_form_data['+x+'][form_name]" value="" /></td><td><select name="list_form_data['+x+'][form_id_class]" id="form_id_class" class="select-group"><option value="id" >ID</option><option value="class">Class</option></select></td><td><input type="text" id="form_id_class_name" class="field_group" name="list_form_data['+x+'][form_id_class_name]" value=""></td><td><select name="list_form_data['+x+'][submit_button_id_class]" class="select-group"><option value="id">ID</option><option value="class">Class</option></select></td><td><input type="text" id="submit_button_id_class_name" class="field_group" name="list_form_data['+x+'][submit_button_id_class_name]" value=""></td><td><img src="<?php echo plugin_dir_url(__FILE__) ?>/images/trash.png" class="remove_field"></td></tr>');
		        }
		    });

		    $('.form-selection').on("click",".remove_field", function(e){ //user click on remove text
		        e.preventDefault(); $(this).parent().parent().remove(); //x--;
		    })
		});
		</script>
	<?php
		submit_button();
	?>
  	</form>
  	<div class="step-section">
		<h2><strong>Notes :- </strong></h2>

	  	<p>Below is the video to show how to get the form submit button class and ID in different browsers.</p>

		<p>1.0 Watch the video to learn how to get form submit button class and ID in Chrome browser.</p>
		<iframe width="560" height="315" src="https://www.youtube.com/embed/yjeEkFBaNOI?rel=0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>
</div>
<?php
}

function siarashield_captcha_fields()
{
add_settings_section("section", "Siarashield Settings", null, "captcha-options");

//add_settings_field("siarashield_css", "Enable Captcha Css", "siarashield_css_element", "captcha-options", "section");

//add_settings_field("siarashield_contactform", "Enable In Contact Form", "siarashield_contact_element", "captcha-options", "section");
//add_settings_field("siarashield_commentform", "Enable In Comment Form", "siarashield_comment_element", "captcha-options", "section");

add_settings_field("siarashield_tocken", "SiaraShield Public Key", "siarashield_tocken_element", "captcha-options", "section");
add_settings_field("siarashield_private_key", "SiaraShield Private Key", "siarashield_private_key_element", "captcha-options", "section");

// add_settings_field("siarashield_form_selection", "SiaraShield Form Name", "siarashield_form_selection_element", "captcha-options", "section");



register_setting("section", "siarashield_css");

//register_setting("section", "siarashield_contactform");
register_setting("section", "siarashield_commentform");

register_setting("section", "siarashield_tocken");
register_setting("section", "siarashield_private_key");

register_setting("section", "siarashield_form_selection");

register_setting("section", "list_form_data");
register_setting("section", "field_id_class");
// register_setting("section", "form_field_name");
register_setting("section", "button_id_class");
register_setting("section", "button_name");

}
add_action("admin_init", "siarashield_captcha_fields");

function siarashield_css_element()
{
?>
<input type="checkbox" name="siarashield_css" id="siarashield_css" value="1" <?php echo checked( 1, esc_attr(get_option('siarashield_css')), false ); ?> />
<?php
}
/*function siarashield_contact_element()
{
?>
<input type="checkbox" name="siarashield_contactform" id="siarashield_contactform" value="1" <?php echo checked( 1, esc_attr(get_option('siarashield_contactform')), false ); ?> />
<?php
}*/
function siarashield_comment_element()
{
?>
<input type="checkbox" name="siarashield_commentform" id="siarashield_commentform" value="1" <?php echo checked( 1, esc_attr(get_option('siarashield_commentform')), false ); ?> />
<?php
}

function siarashield_tocken_element()
{
?>
<input type="text" name="siarashield_tocken" size='50' id="siarashield_tocken" value="<?php echo esc_attr(get_option('siarashield_tocken')); ?>" />
<p class="description">
  <?php _e( 'Please Enter Public Key.' ); ?>
</p>
<?php
}

function siarashield_private_key_element()
{
?>
<input type="text" name="siarashield_private_key" size='50' id="siarashield_private_key" value="<?php echo esc_attr(get_option('siarashield_private_key')); ?>" />
<p class="description">
  <?php _e( 'Please Enter Private Key.' ); ?>
</p>
<a href="https://mycybersiara.com/Register" target="_blank">Get Keys Here</a>
<?php
}


function siarashield_wp_enqueue_scripts() {
	
	wp_enqueue_script('captchaResources', 'https://embedcdn.mycybersiara.com/CaptchaFormate/CaptchaResources_WP.js', array(), null, true);

	// register custom js
	wp_register_script(
        'custom_script', 
        plugin_dir_url(__FILE__) . 'js/custom.js',
        array( 'jquery' )
    );
	
	//get submit btn classor id and retun array in custom js

	$list_form_data=get_option('list_form_data');

	$siarashield_js_object_array = array(
		'list_form_data'	=> $list_form_data,
	);
	// pass php varaible in js
    wp_localize_script( 'custom_script', 'siarashield_object', $siarashield_js_object_array );

    // Enqueued script with localized data.
    wp_enqueue_script( 'custom_script' );
}

add_action('wp_enqueue_scripts','siarashield_wp_enqueue_scripts');

function siarashield_wp_enqueue_css() {
	wp_enqueue_style('captcha-resources', 'https://embedcdn.mycybersiara.com/CaptchaFormate/CaptchaResources.css');
}
add_action('get_footer','siarashield_wp_enqueue_css');

function siarashield_footer_styles(){
	if(get_option('siarashield_css') == 1):
	wp_enqueue_style('siarashield-custom', plugin_dir_url(__FILE__).'css/vicapcustom.css');
	endif;
}

add_action( 'get_footer', 'siarashield_footer_styles' );
add_action( 'wp_footer', 'siarashield_script');

function siarashield_script(){
$siarashield_tocken = get_option('siarashield_tocken');
$siarashield_private_key = get_option('siarashield_private_key');
?>

<?php if($siarashield_tocken) { ?>
<script type="text/javascript">
//jQuery('input:submit').hide();

jQuery(function () {	
  InitCaptcha('<?php echo $siarashield_tocken; ?>');
  //jQuery('input:submit').show();
	jQuery('.CaptchaSubmit').click(function () {
			if (CheckCaptcha() != true) {
				return false;
			}else {
				
				
				
			}

			if (CheckCaptcha() != true) {
				return false;
			}else {
				
				
			}	
		});
});
</script>

<?php
}
}

function siarashield_captcha_output(){
ob_start();
?>
<div class="Siarashield">
</div>
<?php
return ob_get_clean();
}
//region short code
add_shortcode('siarashield', 'siarashield_captcha_output');
//region short code end
//region start
if ( function_exists( 'wpcf7_add_shortcode' )) {
	wpcf7_add_shortcode('siarashield', 'custom_lptitle_shortcode_handler', true);
}
//region end
function custom_lptitle_shortcode_handler( $tag ) {
global $post;
$html = '<div class="SiaraShield"></div>';
return $html;
}

/*----------------------Comment form enable disable------------------*/
if(get_option('siarashield_commentform') == 1):

function commentsiarashield_captcha_output(){
ob_start();
?>
<div class="SiaraShield">
</div>
<?php
return ob_get_clean();
}
//region short code
add_shortcode('commentsiarashield', 'commentsiarashield_captcha_output');

function filter_comment_form_submit_button( $submit_button, $args ) {
    $submit_before = do_shortcode('[commentsiarashield]');
	$submit_button = '<input name="submit" type="submit" class="btn-submit CaptchaSubmit" id="'.esc_attr( $args['id_submit'] ).'" value="'.esc_attr( $args['label_submit'] ).'" />';
    return $submit_before . $submit_button;
}
// add the filter
add_filter( 'comment_form_submit_button', 'filter_comment_form_submit_button', 10, 2 );

endif;
?>
