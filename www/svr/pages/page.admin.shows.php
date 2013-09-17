<?php
require_once "authentication.php";
class Page{
	var $request = null;
	var $sql = null;
	var $usr;
	function Page($request){
		$this->request = $request;
		$this->usr = $GLOBALS['USER']; //Get the user from router.php
		$this->usr->get_info();
		if($this->usr->get_user_info("permissions") < 1){
			header("Location: /home");
			die();
		}
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Edit shows";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
		<script src="/res/scripts/custom-radio-checkbox.jquery.js" type="text/javascript"></script>
		<script src="/res/scripts/jquery.scrollTo-1.4.2-min.js" type="text/javascript"></script>

		<script src="/res/scripts/tiny_mce/jquery.tinymce.js" type="text/javascript"></script>
		
		<script type="text/javascript">
			function load_show_index(){
				$("#show_list_container").load('/admin/showdata?get_show_list','',function(response,status,xhr){
					$('.show_list_item').click(load_show);
				});
			}
			function load_show(){
				var show_id = $(this).attr("id").substring(5);
				load_the_show(show_id);
			}
			function load_the_show(show_id){
				$('#show_info_header').html("Show information");
				$('.errors').hide();
				$('#show_message').hide();
				//alert(show_id);
				$('#show_info_container').show();
				$('#show_info').load('/admin/showdata?get_show_info&show_id='+show_id,'',function(response,status,xhr){
					$('#show_info input').customInput();
					$('#save_edit_button').click(save_show_information);
					$('textarea.rich').tinymce({
						// Location of TinyMCE script
						script_url : '/res/scripts/tiny_mce/tiny_mce.js',
						mode : "textareas",
						theme : "advanced",
						plugins : "advimage,advlink,media,xhtmlxtras",
						theme_advanced_buttons1 : "bold,italic,underline,strikethrough,sub,sup,|,bullist,numlist,|,blockquote,link,unlink,|,image,media,code",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "center",
						theme_advanced_statusbar_location : "none",
						theme_advanced_resizing : true
				   });
				});
			}
			function load_by_id(sid){
				load_the_show(sid);
			}
			function load_show_template(){
				$('#show_info_header').html("Add new show");
				$('.errors').hide();
				$('#show_message').delay(1000).slideUp("slow");
				//alert(show_id);
				$('#show_info_container').show();
				$('#show_info').load('/admin/showdata?get_show_template','',function(response,status,xhr){
					$('#show_info input').customInput();
					$('#save_new_button').click(save_new_show_info);
					$('textarea.rich').tinymce({
						// Location of TinyMCE script
						script_url : '/res/scripts/tiny_mce/tiny_mce.js',
						mode : "textareas",
						theme : "advanced",
						plugins : "advimage,advlink,media,xhtmlxtras",
						theme_advanced_buttons1 : "bold,italic,underline,strikethrough,sub,sup,|,bullist,numlist,|,blockquote,link,unlink,|,image,media,code",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "center",
						theme_advanced_statusbar_location : "none",
						theme_advanced_resizing : true
				   });
				});
			}
			function save_show_information(){
				submit_show_info(false);
			}
			function save_new_show_info(){
				submit_show_info(true);
			}
			function submit_show_info(is_new){
				$('#show_message').hide();
				$('.errors').hide();
				$('.error').removeClass('error');
				
				var error = false;
				var errors = new Array();
				
				if(!is_new){
					var show_id = $("input#show_id").val();
					if(show_id == ""){
						errors.push("There is an error with the form! Try refreshing the page.");
						error = true; 
					}
				}
				
				var show_name = $("input#input_show_name").val();
				if (show_name == "") {
					$('#input_container_show_name input, #input_container_show_name label').addClass("error");
					errors.push("Show name cannot be empty!");
					error = true; 
				}
				
				var show_abbr = $("input#input_show_abbr").val();
				if (show_abbr == "") {
					$('#input_container_show_abbr input, #input_container_show_abbr label').addClass("error");
					errors.push("Show abbreviation cannot be empty!");
					error = true; 
				}
				
				var show_term = $("input#input_show_term").val();
				if (show_term == "") {
					$('#input_container_show_term input, #input_container_show_term label').addClass("error");
					errors.push("Show term cannot be empty!");
					error = true; 
				}
				else if (!(show_term.toLowerCase() == "spring" || show_term.toLowerCase() == "fall" || show_term.toLowerCase() == "summer")){
					$('#input_container_show_term input, #input_container_show_term label').addClass("error");
					errors.push("Show term must be a valid semester period (Spring, fall,...)!");
					error = true; 
				}
				
				re = /\d{4}/; /* caution, will fail in 8000 years */
				var show_year = $("input#input_show_year").val();
				if (show_year == "") {
					$('#input_container_show_year input, #input_container_show_year label').addClass("error");
					errors.push("Show year cannot be empty!");
					error = true; 
				}
				else if(!re.test(show_year)){
					$('#input_container_show_year input, #input_container_show_year label').addClass("error");
					errors.push("Show year must be a valid, 4 digit year!");
					error = true; 
				}
				
				var location = $("input[name=input_show_location]:checked").val();
				if(location == null || location == ""){
					$('#input_container_show_location label').addClass("error");
					errors.push("You must select a location!");
					error = true;
				}
				
				var byline = $("#input_show_byline").val();
				var director = $("#input_show_director").val();
				var synopsis = $("#input_show_synopsis").val();
				
				if(error){
					$("#show_errors").empty();
					var mes = $("<p></p>").html("The following errors occurred:");
					var list = $("<ul></ul>");
					for(i=0;i<errors.length;i++){
						li = $("<li></li>").html(errors[i]);
						list.append(li);
					}
					$("#show_errors").append(mes).append(list).show();
					$.scrollTo("#show_errors");
					return false;
				}
				dataString = "show_name="+escape(show_name)+"&show_abbr="+escape(show_abbr)+"&show_term="+escape(show_term)+"&show_year="+escape(show_year)+"&location="+escape(location)+"&byline="+escape(byline)+"&director="+escape(director)+"&synopsis="+escape(synopsis);
				if(!is_new) dataString = "show_id="+escape(show_id)+"&" + dataString;
				//alert(dataString);
				var dataUrl = "/admin/showdata?submit_show_edit";
				if(is_new) dataUrl = "/admin/showdata?submit_new_show"
				$.ajax({
					type: "POST",
					url: dataUrl,
					data: dataString,
					success: function(data, textStatus, jqXHR) {	
						//alert(data);
						var data_return = $.parseJSON(data);
						if(data_return.status == "success"){
							$("#show_message").html("Changes saved successfully!").show();
							$.scrollTo("#show_message");
							load_show_index();
							if(data_return.id != "-1" && is_new){
								load_by_id(data_return.id);
							}
							$("#show_message").delay(1000).slideUp("slow");
						}
						else if(data_return.status == "error"){
							$("#show_errors").empty();
							var mes = $("<p></p>").html("The server returned the following errors!");
							var list = $("<ul></ul>");
							$.each(data_return.errors,function(i,item){
								li = $("<li></li>").html(item);
								list.append(li);
							});
							$("#show_errors").append(mes).append(list).show();
							$.scrollTo("#show_errors");
						}
						else{
							$("#show_errors").empty();
							var mes = $("<p></p>").html("An unexpected error occcurred!<br />" + $data);
							$("#show_errors").append(mes).show();
							$.scrollTo("#show_errors");
						}
					},
					error: function(data){
						$("#show_errors").empty();
						var mes = $("<p></p>").html("An unexpected error occcurred!<br />" + $data);
						$("#show_errors").append(mes).show();
						$.scrollTo("#show_errors");
					}
				});
				return false;
				
				
			}
			$(document).ready(function(){
				$('.errors').hide();
				$('#show_message').hide();
				$('#add_show_button').click(load_show_template);
				load_show_index();
			});
		</script>
		<style type="text/css">
			#show_bar{
				width: 250px;
				float: left;
			}
			#show_info{
				
			}
			#show_info_container{
				display: none;
				width: 600px;
				float: right;
			}
			.clear{
				width: 10px;
				clear: both;
			}
			div.form-container div{
				padding: 5px 0;
			}
			div.form-container input{
				border: 2px solid #6A6A6A;
				font-family: AurulentSansRegular;
				font-size: 14px;
				padding: 3px;
			}
			div.form-container label{
				color: #6A6A6A;
				font-family: AurulentSansRegular;
				font-size: 15px;
				font-weight: normal;
			}
			div.radio_container{
				margin-left: 120px !important;
			}
			div.form-container label em{
				font-size: 100% !important;
			}
			div.form-container textarea{
				border: 2px solid #6A6A6A;
				font-family: AurulentSansRegular;
				font-size: 13px;
				padding: 3px;
			}
			body{
				font-family: OpenSansRegular;
				font-size: 14px;
			}
			div#show_list_container > div{
				background-color: #E0E0E0;
				color: #6A6A6A;
				margin: 4px;
				padding: 2px 10px;
				cursor: pointer;
			}
			div#show_list_container > div:hover{
				background-color: #D0D0D0;
				color: #000000;
			}
			#add_show_button, #submit_options input{
				background-color: transparent;
				border: 2px solid #6A6A6A;
				color: #6A6A6A;
				cursor: pointer;
				font-family: AurulentSansRegular;
				font-size: 14px;
				margin: 5px 0;
				padding: 5px;
				width: 100%;
			}
			#add_show_button:hover, #submit_options input:hover{
				border: 2px solid #404040;
				color: #404040;
			}
			#submit_options div{
				width: 48%;
				padding: 4px;
				display: inline-block;
			}
			#show_message{
				background-color: #EEFFEE;
				border: 2px solid #66CC66;
				margin: 0 0 10px;
				padding: 5px 10px;
			}
			
			.custom-checkbox, .custom-radio { position: relative; margin-left: 0 !important; }
			/* input, label positioning */
			.custom-checkbox input, .custom-radio input {
				position: absolute;
				left: 2px;
				top: 3px;
				margin: 0;
				z-index: 0;
			}

			.custom-checkbox label, .custom-radio label {
				display: block;
				position: relative;
				z-index: 1;
				font-size: 1.3em;
				padding-right: 1em;
				line-height: 1;
				padding: .5em 0 .5em 30px;
				margin: 0 0 .3em;
				cursor: pointer;
			}
			.custom-checkbox label {
				background: url('/res/images/layout/checkbox.png') no-repeat; 
			}
			.custom-radio label { 
				background: url('/res/images/layout/radio.png') no-repeat; 
			}
			.custom-checkbox label, .custom-radio label {
				background-position: -10px -10px;
			}

			.custom-checkbox label.hover,
			.custom-checkbox label.focus,
			.custom-radio label.hover,
			.custom-radio label.focus {
				background-position: -10px -110px;
			}

			.custom-checkbox label.checked, 
			.custom-radio label.checked {
				background-position: -10px -210px;
			}

			.custom-checkbox label.checkedHover, 
			.custom-checkbox label.checkedFocus {
				background-position: -10px -310px;
			}

			.custom-checkbox label.focus, 
			.custom-radio label.focus {
				outline: 1px dotted #ccc;
			}
			
		</style>
		
		<style type="text/css">

		/* General styles */
		/* body { margin: 0; padding: 0; font: 80%/1.5 Arial,Helvetica,sans-serif; color: #111; background-color: #FFF; } */
		h2 { margin: 0px; padding: 10px; font-family: Georgia, "Times New Roman", Times, serif; font-size: 200%; font-weight: normal; color: #FFF; background-color: #CCC; border-bottom: #BBB 2px solid; }
		p#copyright { margin: 20px 10px; font-size: 90%; color: #999; }

		/* Form styles */
		div.form-container { margin: 10px; padding: 5px; background-color: #FFF; /* border: #EEE 1px solid; */ }

		p.legend { margin-bottom: 1em; }
		p.legend em { color: #C00; font-style: normal; }

		div.errors { margin: 0 0 10px 0; padding: 5px 10px; border: #FC6 2px solid; background-color: #FFC; }
		div.errors p { margin: 0; }
		div.errors p em { color: #C00; font-style: normal; font-weight: bold; }

		div.form-container form p { margin: 0; }
		div.form-container form p.note { margin-left: 170px; font-size: 90%; color: #333; }
		div.form-container form fieldset { margin: 10px 0; padding: 10px; border: #DDD 1px solid; }
		div.form-container form legend { font-weight: bold; color: #666; }
		div.form-container form fieldset div { padding: 0.25em 0; }
		div.form-container label { margin-right: 10px; padding-right: 10px; width: 100px; display: block; float: left; text-align: right; position: relative; }
		div.form-container label.error, 
		div.form-container span.error { color: #C00; }
		div.form-container label em { position: absolute; right: 0; font-size: 120%; font-style: normal; color: #C00; }
		div.form-container input.error { border-color: #C00; background-color: #FEF; }
		div.form-container input:focus,
		div.form-container input.error:focus, 
		div.form-container textarea:focus {	background-color: #FFC; border-color: #FC6; }
		div.form-container div.controlset label, 
		div.form-container div.controlset input { display: inline; float: none; }
		div.form-container div.controlset label.controlset { display: block; float: left; }
		div.form-container div.controlset div { margin-left: 170px; }
		div.form-container div.buttonrow { margin-left: 180px; }

		</style>

			<!-- CUSTOM CSS/LINK HERE -->
	<?php
	}

	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		$this->usr->get_info();
		if($this->usr->get_user_info("permissions") < 1){
			header("Location: /home");
			return false;
		}
		return true;
	}

	/**
	 * If desired, you may override the template being used
	 * return a string containing the directory path, or false for no override.
	 **/
	function getOverrideTemplate(){
		return false;
		//return SERVER_RES_DIR.'template.html';
	}


	//Return nothing; print out the page. 
	function getContent(){?>
		<div id="show_bar">
			<div>
				<h2>Shows</h2>
				<div id="show_list_container">
				</div>
				<div style="padding: 4px;"><input type="button" id="add_show_button" value="Add Show" /></div>
			</div>
		</div>
		<div id="show_info_container">
			<h2 id="show_info_header">Show Info</h2>
			<div id="show_errors" class="errors"></div>
			<div id="show_message"></div>
			<div id="show_info" class="form-container"></div>
		</div>
		<div class="clear"></div>
		<?php
	}
}
?>