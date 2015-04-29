<?php

class Page{
	var $request = null;
	var $sql = null;
	var $usr;
	var $reservation_status = null;
	var $reservation_details = array();
	var $reservation_show_name = "";
	function Page($request){
		$this->request = $request;
		//$this->usr = new User();
		$this->usr = $GLOBALS['USER']; //Get the user from router.php
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Tickets";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
		<script src="/res/scripts/custom-radio-checkbox.jquery.js" type="text/javascript"></script>
		<script src="/res/scripts/jquery.customselect.js" type="text/javascript"></script>
		<script src="/res/scripts/jquery.scrollTo-1.4.2-min.js" type="text/javascript"></script>
		<script type="text/javascript">
		function clear_reservation_form() {
			$('form#reservation_form').find(':input').each(function() {
				switch(this.type) {
					case 'password':
					case 'select-multiple':
					case 'select-one':
					case 'text':
					case 'textarea':
						$(this).val('');
						break;
					case 'checkbox':
					case 'radio':
						this.checked = false;
				}
			});
			$('form#reservation_form .custom-radio label').each(function(){
				$(this).removeClass("checked");
			});
		}
		function submit_reservation(){
			$('#reservation_errors').hide();
			$('.errors').hide();
			$('.error').removeClass('error');
			$('#reservation_form input').attr("disabled", "disabled"); /* disable the form */
			$('#loadingAnimatin').show();
		
			var error = false;
			var errors = new Array();
			
			var selected_event = $("input[name=input_selected_event]:checked:not(.soldout):not(.closed)").val();
			if(selected_event == null || selected_event == ""){
				$('#input_container_show_location label:not(.disabled)').addClass("error");
				errors.push("You must select a valid event!");
				error = true;
			}
			id_re = /\d{1,2}/;
			ticket_quantity = $('#input_ticket_quanity').val();
			if(ticket_quantity == null || ticket_quantity == "" || !id_re.test(ticket_quantity)){
				$('#input_container_ticket_quanity select, #input_container_ticket_quanity label').addClass("error");
				errors.push("Please select a valid ticket quantity.");
				error = true; 
			}
			
			var dataString = "input_selected_event="+escape(selected_event)+"&input_ticket_quantity="+escape(ticket_quantity);
			
			if($('#input_container_preferred_contact').length !== 0 && $('input[name=input_preferred_contact]:checked').length == 0){
				$('#input_container_preferred_contact label').addClass("error");
				errors.push("Please select your preferred contact information.");
				error = true; 
			}
			else if($('#input_container_preferred_contact').length !== 0 && $('input[name=input_preferred_contact]:checked').val() == 1){
				dataString += "&input_preferred_contact=1";
			}
			else{
				var first_name = $("input#input_first_name").val();
				if (first_name == "" || first_name == null) {
					$('#input_container_first_name input, #input_container_first_name label').addClass("error");
					errors.push("Please enter your first name.");
					error = true; 
				}
				
				var last_name = $("input#input_last_name").val();
				if (last_name == "" || last_name == null) {
					$('#input_container_last_name input, #input_container_last_name label').addClass("error");
					errors.push("Please enter your last name.");
					error = true; 
				}
				
				var email_address = $("input#input_email_address").val();
				AtPos = email_address.indexOf("@");
				StopPos = email_address.lastIndexOf(".");
				if (email_address == "" || email_address == null) {
					$('#input_container_email_address input, #input_container_email_address label').addClass("error");
					errors.push("Please enter your email address.");
					error = true; 
				}
				else if(AtPos == -1 || StopPos == -1 || StopPos < AtPos || StopPos - AtPos == 1 || email_address.match(/@/g).length > 1){
					$('#input_container_email_address input, #input_container_email_address label').addClass("error");
					errors.push("Please enter a valid email address.");
					error = true; 
				}
				dataString += "&input_first_name="+escape(first_name)+"&input_last_name="+escape(last_name)+"&input_email_address="+escape(email_address);
			}
			if(error){
				$("#reservation_errors").empty();
				var mes = $("<p></p>").html("The following errors occurred:");
				var list = $("<ul></ul>");
				for(i=0;i<errors.length;i++){
					li = $("<li></li>").html(errors[i]);
					list.append(li);
				}
				$("#reservation_errors").append(mes).append(list).show();
				$.scrollTo("#reservation_errors");
				$('#reservation_form input').removeAttr("disabled"); /* re-enable the form */
				$('#loadingAnimatin').hide();
				return false;
			}
			
			//alert(dataString);
			$.ajax({
				type: "POST",
				url: "/tickets/submit?ajax_request",
				data: dataString,
				success: function(data, textStatus, jqXHR) {
					var data_return = $.parseJSON(data);
					if(data_return.status == "success"){
						$("#reservation_message").html('<strong>Reservation successful!</strong><p>Thank you for reservation! You will receive a confirmation email with further information.</p><p>Let your friends know and make them jealous! <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://floridaplayers.org/tickets" data-text="I just reserved my tickets for #FloridaPlayers Presents '+data_return.show_name+'!" data-via="florida_players" data-hashtags="'+data_return.show_hashtag+'" data-size="large" data-count="none" data-dnt="true">Tweet</a><scr'+'ipt>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</scr'+'ipt>').show();
						$.scrollTo("#reservation_message");
						clear_reservation_form();
						$('#input_container_show_location').load("/tickets #input_container_show_location > *",function(){
							$('#input_container_show_location .custom_radio_check').customInput();
						});
						
						if(data_return.errors != null){
							$("#reservation_errors").empty();
							var mes = $("<p></p>").html("The server returned the following errors!");
							var list = $("<ul></ul>");
							$.each(data_return.errors,function(i,item){
								li = $("<li></li>").html(item);
								list.append(li);
							});
							$("#reservation_errors").append(mes).append(list).show();
						}
						try{
							clicky.log('/tickets/submit#success','Reservation success','pageview');
						}
						catch(e){ /* console.log("Could not log view to Clicky. Error: " + e); */ }
						//$("#reservation_message").delay(5000).slideUp("slow");
					}
					else{
						$("#reservation_errors").empty();
						var mes = $("<p></p>").html("The server returned the following errors!");
						var list = $("<ul></ul>");
						$.each(data_return.errors,function(i,item){
							li = $("<li></li>").html(item);
							list.append(li);
						});
						$("#reservation_errors").append(mes).append(list).show();
						$.scrollTo("#reservation_errors");
						try{
							clicky.log('/tickets/submit#error','Reservation error','pageview');
						}
						catch(e){ /* console.log("Could not log view to Clicky. Error: " + e); */ }
					}
					$('#reservation_form input').removeAttr("disabled"); /* re-enable the form */
					$('#loadingAnimatin').hide();
				}
			});
			return false;
		}
		$(document).ready(function(){
			$('.custom_radio_check').customInput();
			$('select.styled_select').customStyle();
			/*$('#input_submit_reservation').click(submit_reservation);*/
			$('#reservation_form').submit(submit_reservation);
			$('#reservation_errors, .errors, #reservation_message').hide();		
		});
		</script>
		<style type="text/css">
			.clear{
				width: 10px;
				clear: both;
				padding: 0 !important;
			}
			div.form-container div:not(.loading div){
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
				margin-left: 120px !important;
			}
			div.custom-checkbox{
				padding: 0 !important;
			}
			div.form-container label em{
				font-size: 100% !important;
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
			select.error ~ .customStyleSelectBox{
				border-color: #C00; background-color: #FEF;
			}
			div.ticket_label{
				display: inline-block;
				margin: 0 10px 0 0 !important;
				float: right; 
				color: #6A6A6A;
			}
			#reservation_message{
				background-color: #EEFFEE;
				border: 2px solid #66CC66;
				margin: 0 0 10px;
				padding: 5px 10px;
			}
			#reservation_message, #reservation_errors{
				display: none;
			}
			#reservation_message.visible_override, #reservation_errors.visible_override{
				display: block;
			}
			#input_container_show_location .custom-radio, #input_container_show_location .radio_container label{
				max-width: 550px;
			}
			
			#loadingAnimatin {
				left: 150px;
				position: relative;
				top: -24px;
				display: none;
			}
			#news_message{
				background-color: #EEFFEE;
				border: 2px solid #66CC66;
				margin: 0 0 10px;
				padding: 5px 10px;
			}
			.twitter-share-button{
				margin-left: 10px;
				vertical-align: middle;
			}
		</style>
		<style type="text/css">
			.custom-checkbox, .custom-radio { 
				position: relative; 
				margin-left: 0 !important;
				display: inline-block;
			}
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
			
			.custom-radio label.disabled, .custom-radio label.checked.disabled, .custom-radio label.hover.disabled, .custom-radio label.focus.disabled{
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

			div.errors, div.form-container div.errors { margin: 0 0 10px 0; padding: 5px 10px; border: #FC6 2px solid; background-color: #FFC; }
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
			
			p.note { font-size: 12px; margin: 5px 0 0 170px; }

		</style>
		<link href="/res/styles/loading-30.css" type="text/css" rel="stylesheet" />
			<!-- CUSTOM CSS/LINK HERE -->
	<?php
	}


	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		if($this->has_flag(0,"submit")){
			require "ticketprocess.php";
			$this->reservation_show_name = $this->getShowNameFromEventId($_POST['input_selected_event']);
			if(isset($_GET['ajax_request'])){
				$return = array();
				if($success){
					$return["status"] = "success";
					if(count($errors) > 0){
						$return['errors'] = $errors;
					}		
				}
				else{
					$return["status"] = "error";
					$return["errors"] = $errors;
				}
				$return['show_name'] = $this->reservation_show_name;
				$return['show_hashtag'] = $this->makeHashtag($this->reservation_show_name);
				echo json_encode($return);
				return false;
			}
			$this->reservation_status = $success;
			$this->reservation_details = $errors;
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
	function getContent(){
		$this->sql = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$this->sql){
			echo "Error connecting to database!";
			logError("page.tickets.php",__LINE__,"Error connecting to database!",mysql_error(),time(),false);
		}
		else{
			mysql_select_db(DB_NAME, $this->sql);
			
			$showquery = "SELECT events.event_id, shows.show_name, events.event_date, events.event_capacity, events.ticket_close, events.closed, (SELECT COALESCE(SUM(reservations.ticket_quantity),0) FROM reservations WHERE reservations.event_id=events.event_id) AS tickets_sold FROM events INNER JOIN shows ON events.show_id=shows.show_id WHERE events.active='1' AND events.archived='0' ORDER BY events.event_date";
			$show_response = mysql_query($showquery,$this->sql);
			if(!$show_response){
				echo "Error retrieving available events!";
				logError("page.tickets.php",__LINE__,"Error retrieving available events!",mysql_error(),time(),false);
			}
			else{
				//Get the errors/messages to display
				ob_start();
				echo '<div id="reservation_errors" class="errors"></div>';
				if($this->has_flag(0,"submit")){
					if($this->reservation_status == true){
						/*echo '<div id="reservation_message" class="visible_override"><strong>Reservation successful!</strong><br />Thank you for reservation! You will receive a confirmation email with further information.</div>';
						echo '<div id="reservation_errors" class="errors"></div>';*/
						echo '<div id="reservation_message" class="visible_override"><strong>Reservation successful!</strong><p>Thank you for reservation! You will receive a confirmation email with further information.</p>
				<p>Let your friends know and make them jealous! <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://floridaplayers.org/tickets" data-text="I just reserved my tickets for #FloridaPlayers Presents '.$this->reservation_show_name.'!" data-via="florida_players" data-hashtags="'.$this->makeHashtag($this->reservation_show_name).'" data-size="large" data-count="none" data-dnt="true">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script></p></div>';
						echo '<div id="reservation_errors" class="errors"></div>';
					}
					else{
						echo '<div id="reservation_message"></div>';
						echo '<div id="reservation_errors" class="errors visible_override"><p>The server returned the following errors!</p><ul>';
						foreach($this->reservation_details as $error){
							echo "<li>{$error}</li>";
						}
						echo '</ul></div>';
					}
				}
				else{ ?>
					<div id="reservation_errors" class="errors"></div>
					<div id="reservation_message"></div>
				<?php }
				$messages = ob_get_contents();
				ob_clean();
				//Finished getting errors/messages
				
				//Time to start displaying available shows
				$no_shows = true;
				if(mysql_num_rows($show_response) > 0){
					while($row = mysql_fetch_assoc($show_response)){
						$event_id = $row['event_id'];
						$input_id = "input_event_{$row['event_id']}";
						$event_name = $row['show_name'];
						$date = date( 'l, j F Y, \a\t g:i A',strtotime( $row['event_date'] ));
						$remaining = $row['event_capacity'] - $row['tickets_sold'];
						$is_closed = (strtotime( $row['ticket_close'] ) < time()) || ($row['closed'] == true);
						$disabled = ($is_closed || $remaining <= 0)?"disabled=\"disabled\"":"";
						
						if(strtotime( $row['event_date'] ) <= time()) continue; //No need to display past shows
						$no_shows = false;
						
						$show_label = "";
						$input_class = "custom_radio_check";
						if(!$is_closed && $remaining > 0){
							$show_label = "<div class=\"ticket_label normal\">{$remaining} " . (($remaining == 1)?"ticket":"tickets") . " remaining</div>";
						}
						elseif(!$is_closed && $remaining <= 0){
							$show_label = "<div class=\"ticket_label soldout\">SOLD OUT</div>";
							$input_class .= " soldout";
						}
						elseif($is_closed && $remaining > 0){
							$show_label = "<div class=\"ticket_label closed remaining\">CLOSED &mdash; $remaining " . (($remaining == 1)?"ticket":"tickets") . " remaining</div>";
							$input_class .= " closed";
						}
						elseif($is_closed && $remaining <= 0){
							$show_label = "<div class=\"ticket_label soldout\">SOLD OUT</div>";
							$input_class .= " soldout";
						}
						
						echo "<div class=\"radio_container\"><input class=\"$input_class\" name=\"input_selected_event\" id=\"$input_id\" value=\"$event_id\" type=\"radio\" $disabled /> <label for=\"$input_id\">$event_name &mdash; $date</label>$show_label</div>\n								";
					}
				}
				else{
					$no_shows = true;
				}
				$available_shows = ob_get_contents();
				ob_end_clean();
				
				
				?>
				<h1>Tickets</h1>
				<article>
					<section>
						
						<?php if(time() < 1397768400 ){ ?>
						<div id="news_message">We've added a few more tickets for <em>Florida Players Presents Spring Awakening</em>. Extra tickets for the Saturday and Sunday performances will be made available Thursday at 5:00pm.</div>
						<?php } ?>
						<p>Welcome to the online ticketing system for Florida Players. Tickets will become available for reservation approximately two weeks prior to opening night of each show.</p>
						<p>Some shows may be sold out or closed. If a show is listed as closed, it means that the online signup period has ended but that there are still tickets available. If a show is sold out or closed, PLEASE join us at the theatre 30 minutes before the start of the show and our house manager will add you to the waiting list. For various reasons, some patrons are unable to attend despite reserving tickets. If you show up to a performance without a reservation, you more than likely will be able to see it. ALSO, check back the monday before opening week of the show, as many times more seats are able to be added at this date.</p>
						<p>Please note that ONLY 2 TICKETS may be reserved by each individual for the entire run of the show. This will be managed manually, so any extras will be removed and the actual amount reserved will only be 2 tickets. This is strictly enforced. If you reserve more than 2 tickets under the same name, you are not guaranteed to seat any more than 2 people.</p>
					</section>
				</article>
				<div class="form-container">
					<?php 
					echo $messages; 
					if(!$no_shows){
						?>
						<form action="/tickets/submit" method="post" id="reservation_form">
							<fieldset>
								<legend>Notice</legend>
								<p>More tickets for events may be released closer to the event date. If shows appear to be sold out, please check back later as more tickets may be added at a later date. </p>
							</fieldset>
							<fieldset>
								<legend>Reservation Details</legend>
								<?php
								if($no_shows == true){?>
									<label>Shows</label><p>There are no shows currently open for reservations</p>
								<?php
								}
								else{
								?>
									<div id="input_container_show_location" class="controlset" >
									<label class="controlset">Show</label>
										<?php echo $available_shows;  ?>
									</div>
									<div id="input_container_ticket_quanity">
										<label for="input_ticket_quanity">Ticket quantity</label>
										<select id="input_ticket_quanity" name="input_ticket_quantity" class="styled_select">
										<?php
											for($x=1;$x<=MAX_TICKET_RESERVATION;$x++){
												echo "<option value=\"$x\">$x".(($x==1)?" Ticket":" Tickets")."</option>";
											}
										?>
										</select>
									</div>
								<?php } ?>
							</fieldset>
							<fieldset>
								<legend>Contact Information</legend>
								<?php
								if($this->usr->is_logged_in()){ 
									$this->usr->get_info();
									?>
									<div id="input_container_preferred_contact" class="controlset" >
									<label class="controlset">Preferred Contact</label>
										<div class="radio_container"><input class="custom_radio_check" name="input_preferred_contact" id="input_preferred_mylogin" value="1" type="radio" /> <label for="input_preferred_mylogin">Use my log in information &mdash; <?php echo ucwords($this->usr->get_user_info("first_name") . " " . $this->usr->get_user_info("last_name")); ?></label></div>
										<div class="radio_container"><input class="custom_radio_check" name="input_preferred_contact" id="input_preferred_enterinfo" value="2" type="radio" /> <label for="input_preferred_enterinfo">Use the following information</label></div>
									</div>
									<?php
									$this->text_input("First name","first_name",false,"");
									$this->text_input("Last name","last_name",false);
									$this->text_input("Email address","email_address",false);
								}
								else{
									$this->text_input("First name","first_name",true,"");
									$this->text_input("Last name","last_name",true);
									$this->text_input("Email address","email_address",true);
								}?>
							</fieldset>

							<?php /*<fieldset>
								<legend>VIP Reservations</legend>
								<p>Florida Players is now offering VIP reservations! For $3 for a single ticket, or $5 for two tickets, a VIP reservation will guarantee seating in the first three rows. To upgrade your tickets, complete your reservation, then email <span class="email"><?php echo $this->str_rot('reservations@floridaplayers.org?subject=VIP Reservation',17); ?></span> with your name and the email used for the reservation. </p>
							</fieldset> */ ?>

							<input type="submit" value="Submit reservation" id="input_submit_reservation" />
							<div id="loadingAnimatin" class="loading windows8">
								<div class="wBall" id="wBall_1">
									<div class="wInnerBall"></div>
								</div>
								<div class="wBall" id="wBall_2">
									<div class="wInnerBall"></div>
								</div>
								<div class="wBall" id="wBall_3">
									<div class="wInnerBall"></div>
								</div>
								<div class="wBall" id="wBall_4">
									<div class="wInnerBall"></div>
								</div>
								<div class="wBall" id="wBall_5">
									<div class="wInnerBall"></div>
								</div>
							</div>
						</form>
						
					<?php
					}
					else{ ?>
						<section>No events are open for reservations at the time! Check back later.</section>
					<?php } ?>
				</div>
				<script type="text/javascript">
					$(document).ready(function(){
						$('.email').each(function(){
							var text = $(this).html();
							var address = text.replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+9)?c:c-26);});
							var display = address.replace(/\?.*$/,'');
							$(this).html('<a href="mailto:'+address+'">'+display+'</a>');
						});
					});
				</script>
				<?php
			}
		}
	}
	function text_input($label,$name,$required = false,$value = "",$size = null){
		$id = "input_$name";
		
		$size_echo = "";
		if(isset($size) && $size >= 1){
			$size_echo = "size=\"$size\"";
		}
		?>
		<div id="input_container_<?php echo $name; ?>"><label for="<?php echo $id; ?>"><?php echo $label; if($required) echo " <em>*</em>"; ?></label> <input id="<?php echo $id; ?>" type="text" name="<?php echo $id; ?>" value="<?php echo $value; ?>" <?php echo $size_echo; ?> /></div>
	<?php
	}
	function has_flag($pos,$flag){
		return (isset($this->request) && isset($this->request["flags"][$pos]) && $this->request["flags"][$pos] == $flag) === true;
	}
	function has_input_at($pos){
		return (isset($this->request) && isset($this->request["flags"][$pos]));
	}
	function get_input_at($pos){
		if(isset($this->request) && isset($this->request["flags"][$pos])) return $this->request["flags"][$pos];
		else return null;
	}
	function str_rot($s, $n = 13) {
		static $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$n = (int)$n % 26;
		if (!$n) return $s;
		if ($n == 13) return str_rot13($s);
		for ($i = 0, $l = strlen($s); $i < $l; $i++) {
			$c = $s[$i];
			if ($c >= 'a' && $c <= 'z') {
				$s[$i] = $letters[(ord($c) - 71 + $n) % 26];
			} else if ($c >= 'A' && $c <= 'Z') {
				$s[$i] = $letters[(ord($c) - 39 + $n) % 26 + 26];
			}
		}
		return $s;
	}
	
	function getShowNameFromEventId($eventId){
		if(!isset($_POST['input_selected_event'])){ 
			return false;
		}
		else $event_id = cut($eventId,11);
		if(preg_match("/[^0-9]/",$event_id) && $event_id != ""){
			return false;
		}
		
		$sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$statement = $sql->prepare('SELECT DISTINCT shows.show_name FROM shows INNER JOIN events ON events.show_id=shows.show_id WHERE events.event_id=:eid');
		$statement->execute(array(':eid'=>$eventId));
		if(($result = $statement->fetch(PDO::FETCH_ASSOC)) !== FALSE){
			$show_name = $result['show_name'];
			return $show_name;
		}
		return false;
	}
	
	function makeHashtag($showName){
		$name = str_replace(' ','',$showName);
		$hashtag = strtolower('fp'.$name);
		return $hashtag;
	}
}
?>