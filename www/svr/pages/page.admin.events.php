<?php

class Page{
	var $request = null;
	var $sql = null;
	var $usr;
	function Page($request){
		$this->request = $request;
		$this->usr = $GLOBALS['USER']; //Get the user from router.php
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
		<script src="/res/scripts/jquery.customselect.js" type="text/javascript"></script>
		<script src="/res/scripts/jquery-ui-1.8.21.custom.min.js" type="text/javascript"></script>
		<script src="/res/scripts/jquery-ui-timepicker-addon.js" type="text/javascript"></script>

		<script src="/res/scripts/tiny_mce/jquery.tinymce.js" type="text/javascript"></script>
		
		<script type="text/javascript">
			function load_event_list(){
				$("#event_list").load('/admin/showdata?get_event_list','',function(response,status,xhr){
					//$('.show_list_item').click(load_show);
					load_new_event_template()
				});
			}
			function update_seat_quantity(event, ui){
				$('#input_seat_quantity').val(ui.value);
			}
			function load_new_event_template(){
				$("#new_event_container").load('/admin/showdata?get_event_template','',function(response,status,xhr){
					$('select.styled_select').customStyle();
					$('.styled_checkbox').customInput();
					$('#seat_quanitity_slider').slider({max: 200,min: 1, value: 1, create: update_seat_quantity, slide: update_seat_quantity, stop: update_seat_quantity});
					$('#save_new_event_button').click(submit_new_event);
					$('#input_event_date').datetimepicker({
						ampm: true,
						timeFormat: 'h:mm tt',
						maxDate: null,
						onSelect: function(){
							var selected_date = $(this).datetimepicker('getDate');
							if(selected_date != null){
								$('#input_close_date').datetimepicker('option','maxDate',new Date(selected_date.getTime()));
							}
						},
						onClose: function(dateText, inst) {
							var event_close_box = $('#input_close_date');
							if (event_close_box.val() != '') {
								var test_event_date = new Date(dateText);
								var event_close_date = new Date(event_close_box.val());
								if (event_close_date > test_event_date)
									event_close_box.val($(this).val());
							}
						}
					});
					$('#input_close_date').datetimepicker({
						ampm: true,
						timeFormat: 'h:mm tt',
						onClose: function(dateText, inst) {
							var event_date_box = $('#input_event_date');
							if (event_date_box.val() != '') {
								var test_close_date = new Date(dateText);
								var event_date = new Date(event_date_box.val());
								if (test_close_date > event_date)
									$('#input_close_date').val(event_date_box.val());
							}
						}
					});
				});
			}
			function submit_new_event(){
				$('#event_message').hide();
				$('.errors').hide();
				$('.error').removeClass('error');
			
				var error = false;
				var errors = new Array();
				
				id_re = /\d{1,}/;
				show_id = $('#input_event_name').val();
				if(show_id == null || show_id == "" || !id_re.test(show_id)){
					$('#input_container_event_name select, #input_container_event_name label').addClass("error");
					errors.push("A valid event must be selected!");
					error = true; 
				}
				
				event_date = $('#input_event_date').datetimepicker('getDate');
				if(event_date == null){
					$('#input_container_event_date input, #input_container_event_date label').addClass("error");
					errors.push("A valid event date must be entered!");
					error = true; 
				}
				event_close = $('#input_close_date').datetimepicker('getDate');
				
				is_active = $('#input_event_active').is(':checked');
				if(is_active !== true) is_active = false; //Just in case
				
				seat_re = /\d{1,3}/;
				seat_quantity = $('#input_seat_quantity').val();
				if(seat_quantity == null || seat_quantity == "" || !seat_re.test(seat_quantity)){
					$('#input_container_seat_quantity input, #input_container_seat_quantity label').addClass("error");
					errors.push("A valid number must be entered for seat quantity!");
					error = true; 
				}
				
				if(error){
					$("#event_errors").empty();
					var mes = $("<p></p>").html("The following errors occurred:");
					var list = $("<ul></ul>");
					for(i=0;i<errors.length;i++){
						li = $("<li></li>").html(errors[i]);
						list.append(li);
					}
					$("#event_errors").append(mes).append(list).show();
					$.scrollTo("#event_errors");
					return false;
				}
				
				dataString = "show_id="+escape(show_id)+"&event_date="+escape(Math.floor(event_date.getTime() / 1000))+"&event_active="+escape(is_active)+"&event_capacity="+escape(seat_quantity);
				if(event_close != null) dataString += "&event_close="+escape(Math.floor(event_close.getTime() / 1000));
				
				$.ajax({
					type: "POST",
					url: "/admin/showdata?submit_new_event",
					data: dataString,
					success: function(data, textStatus, jqXHR) {
						var data_return = $.parseJSON(data);
						if(data_return.status == "success"){
							$("#event_message").html("Changes saved successfully!").show();
							$.scrollTo("#event_message");
							load_event_list();
							$("#event_message").delay(1000).slideUp("slow");
						}
						else{
							$("#event_errors").empty();
							var mes = $("<p></p>").html("The server returned the following errors!");
							var list = $("<ul></ul>");
							$.each(data_return.errors,function(i,item){
								li = $("<li></li>").html(item);
								list.append(li);
							});
							$("#event_errors").append(mes).append(list).show();
							$.scrollTo("#event_errors");
						}
					}
				});
				return false;
				
			}
				
			$(document).ready(function(){
				$('.errors').hide();
				$('#event_message').hide();
				//$('#add_show_button').click(load_show_template);
				load_event_list();
				//load_new_event_template();
			});
		</script>
		<link type="text/css" href="/res/styles/fp_theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
		<style type="text/css">
			.clear{
				width: 10px;
				clear: both;
				padding: 0 !important;
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
			div.radio_container, div.checkbox_container{
				margin-left: 170px !important;
			}
			div.custom-checkbox{
				padding: 0 !important;
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
			#event_message{
				background-color: #EEFFEE;
				border: 2px solid #66CC66;
				margin: 0 0 10px;
				padding: 5px 10px;
			}
			
			.event_data{
				display: inline-block;
				float: left;
			}
			.event_controls{
				display: inline-block;
				float: right;
			}
			.event_data h4{
				margin: 0;
			}
			
			span.customStyleSelectBox { 
				border: 2px solid #6A6A6A;
				font-family: AurulentSansRegular;
				font-size: 14px;
				padding: 3px 5px 3px 10px;
			} 
			span.customStyleSelectBox.changed { 
				/* background-color: #f0dea4; */
			} 
			.customStyleSelectBoxInner { 
				background:url('/res/images/layout/downarrow.png') no-repeat center right; 
				padding: 0 7px 0 0;
			}
			select:focus ~ span.customStyleSelectBox{
				background-color: #FFC;
				border-color: #FC6;
			}
			.styled_select{
				cursor: pointer;
			}
			.save_button{
				background-color: transparent;
				border: 2px solid #6A6A6A;
				color: #6A6A6A;
				cursor: pointer;
				font-family: AurulentSansRegular;
				font-size: 14px;
				margin: 5px 0 5px 2px;
				padding: 3px 20px !important;
			}
			.save_button:hover{
				border: 2px solid #404040;
				color: #404040;
			}
			.ui-slider{
				padding: 0 !important;
			}
			#seat_quanitity_slider{
				margin: 10px 0 0 170px;
				width: 200px;
			}
			select.error ~ .customStyleSelectBox{
				border-color: #C00; background-color: #FEF;
			}
			.event_form{
				display: inline-block;
			}
		</style>
		<style type="text/css">
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
			
			
			/* css for timepicker */
.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
.ui-timepicker-div dl { text-align: left; }
.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
.ui-timepicker-div td { font-size: 90%; }
.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
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
		div.form-container label { margin-right: 10px; padding-right: 10px; width: 150px; display: block; float: left; text-align: right; position: relative; }
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
		
		p.note { font-size: 12px; margin: 5px 0 0 170px; }

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
		<div id="event_list_container">
			<h2 id="event_info_header">Event Information</h2>
			<div id="event_list" class="form-container"></div>
		</div>
		<div class="clear"></div>
		<div id="add_new_event">
			<h2 id="event_edit_header">Add New Event</h2>
			<div id="event_errors" class="errors"></div>
			<div id="event_message"></div>
			<div id="new_event_container" class="form-container"></div>
		</div>
		<div id="output"></div>
		<?php
	}
}
?>