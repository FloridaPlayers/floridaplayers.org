<?php

class Page{
	var $request = null;
	var $sql = null;
	
	var $membership_status = null;
	var $membership_details = array();

	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Join Florida Players";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
			<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
			<script src="/res/scripts/jquery.customselect.js" type="text/javascript"></script>
			<script src="/res/scripts/jquery.scrollTo-1.4.2-min.js" type="text/javascript"></script>
			<script type="text/javascript">
			function submit_membership(event){
				event.preventDefault();
				$('#membership_errors').hide();
				$('.errors').hide();
				$('.error').removeClass('error'); 
				$('input',this).attr("disabled", "disabled"); /* disable the form */
				$('#loadingAnimatin').show();
			
				var error = false;
				var errors = new Array();
				
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
				
				var major = $("input#input_major").val();
				if (major == "" || major == null) {
					$('#input_container_major input, #input_container_major label').addClass("error");
					errors.push("Please enter your major area of study.");
					error = true; 
				}
				
				id_re = /\d{1,2}/;
				year_level = $('#input_year_level').val();
				if(year_level == null || year_level == "" || !id_re.test(year_level)){
					$('#input_container_year_level select, #input_container_year_level label').addClass("error");
					errors.push("Please select a valid year in school.");
					error = true; 
				}
				
				
				var dataString = "input_year_level="+escape(year_level);
				dataString += "&input_first_name="+escape(first_name)+"&input_last_name="+escape(last_name)+"&input_email_address="+escape(email_address)+"&input_major="+major;
				
				var phone_number = $("input#input_phone_number").val();
				if (last_name != "" || last_name != null) {
					dataString += "&input_phone_number="+phone_number;
				}
				
				if(error){
					$("#membership_errors").empty();
					var mes = $("<p></p>").html("The following errors occurred:");
					var list = $("<ul></ul>");
					for(i=0;i<errors.length;i++){
						li = $("<li></li>").html(errors[i]);
						list.append(li);
					}
					$("#membership_errors").append(mes).append(list).show();
					$.scrollTo("#membership_errors");
					$('#membership_form input').removeAttr("disabled"); /* re-enable the form */
					$('#loadingAnimatin').hide();
					return false;
				}
				
				//alert(dataString);
				$.ajax({
					type: "POST",
					url: "/about/membership/submit?ajax_request",
					data: dataString,
					success: function(data, textStatus, jqXHR) {
						var data_return = $.parseJSON(data);
						if(data_return.status == "success"){
							$("#membership_message").html("<strong>Membership registration successful!</strong><br />Thank you for joining Florida Players!").show();
							$.scrollTo("#membership_message");
							$('#membership_form').hide(500);
							
							if(data_return.errors != null){
								$("#membership_errors").empty();
								var mes = $("<p></p>").html("The server returned the following errors!");
								var list = $("<ul></ul>");
								$.each(data_return.errors,function(i,item){
									li = $("<li></li>").html(item);
									list.append(li);
								});
								$("#membership_errors").append(mes).append(list).show();
							}
							try{
								clicky.log('/about/membership/submit#success','Membership registration success','pageview');
							}
							catch(e){ /* console.log("Could not log view to Clicky. Error: " + e); */ }
							$("#membership_message").delay(5000).slideUp("slow");
						}
						else{
							$("#membership_errors").empty();
							var mes = $("<p></p>").html("The server returned the following errors!");
							var list = $("<ul></ul>");
							$.each(data_return.errors,function(i,item){
								li = $("<li></li>").html(item);
								list.append(li);
							});
							$("#membership_errors").append(mes).append(list).show();
							$.scrollTo("#membership_errors");
							try{
								clicky.log('/about/membership/submit#error','Membership registration error','pageview');
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
				$('select.styled_select').customStyle();
				$('#membership_form').submit(submit_membership);
				$('#reservation_errors:not(.visible_override), .errors:not(.visible_override), #reservation_message:not(.visible_override)').hide();		
			});
			</script>
			<style type="text/css">
			table {
				border-collapse: collapse;
				font-size: 13px;
				width: 100%;
				margin: 20px 0 0 0;
			}
			tr {
				margin: 2px 0;
			}
			tr:nth-child(2n+1) td {
				background: none repeat scroll left top #EEEEEE;
			}
			td {
				border: 0 solid #000000;
				margin: 0;
				padding: 3px 0 3px 5px;
			}
			td.points ~ td.action{
				border-left: 4px solid #fff;
			}
			.points {
				padding: 0 4px;
			}
			.points sup{
				font-style: italic;
				font-size: 9px;
			}
			</style>
			
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
			#membership_message{
				background-color: #EEFFEE;
				border: 2px solid #66CC66;
				margin: 0 0 10px;
				padding: 5px 10px;
			}
			#membership_message, #membership_errors{
				display: none;
			}
			#membership_message.visible_override, #membership_errors.visible_override{
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
		</style>
		<style type="text/css">

			/* General styles */
			/* body { margin: 0; padding: 0; font: 80%/1.5 Arial,Helvetica,sans-serif; color: #111; background-color: #FFF; } */
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
		<link href="/res/styles/loading-30.css" type="text/css" rel="stylesheet" />
	<?php
	}


	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		//$this->sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		
		if($this->has_flag(0,"submit")){
			require "membershipprocess.php";
			
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
				echo json_encode($return);
				return false;
			}
			$this->membership_status = $success;
			$this->membership_details = $errors;
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
		?>
		<h1>Join Florida Players</h1>
		<article>
			<section class="half">
				<h2>Membership</h2>
				<p>Florida Players membership is based on student involvement. Any involvement in Florida Players earns non-voting membership.</p>
				<p>In order to become a voting Florida Players member, you must earn 10 points, 3 of which must come from actively participating in a production. Once you earn voting membership, you must earn 5 points each semester thereafter, with 2 points from productions, to retain membership. The table at
    right shows the point distribution for the most common activities involved
    in Florida Players. (Note:&nbsp;Points <u>do not</u> roll over between semesters.) The Florida Players
    secretary keeps track of  point totals.</p>
	
				<p>Production points are earned by attending strike, build, load in, crew, and house management. <em>(*) Denotes production points</em></p>
			</section>
			<section class="half">
				<h2>Points</h2>
				<table>
					<tr>
						<td class="action">Producing</td> <td class="points">3*</td><td class="action">Directing</td> <td class="points">5*</td>
					</tr>
					<tr>
						<td class="action">Stage Management</td> <td class="points">3*</td><td class="action">Asst. Directing</td> <td class="points">4*</td>
					</tr>
					<tr>
						<td class="action">Asst. Stage Management</td> <td class="points">3*</td><td class="action">Musical Directing</td> <td class="points">4*</td>
					</tr>
					<tr>
						<td class="action">Scenic Design</td> <td class="points">4*</td><td class="action">Lighting Design</td> <td class="points">4*</td>
					</tr>
					<tr>
						<td class="action">Costume Design</td> <td class="points">4*</td><td class="action">Projection Design</td> <td class="points">4*</td>
					</tr>
					<tr>
						<td class="action">Sound Design</td> <td class="points">4*</td><td class="action">Choreography</td> <td class="points">3*</td>
					</tr>
					<tr>
						<td class="action">Asst. Design</td> <td class="points">2*</td><td class="action">Acting</td> <td class="points">3*</td>
					</tr>
					<tr>
						<td class="action">Committee Participation</td> <td class="points">3<sup>max</sup></td><td class="action">Light Board Operator</td> <td class="points">2*</td>
					</tr>
					<tr>
						<td class="action">Sound Board Operator</td> <td class="points">2*</td><td class="action">Run Crew</td> <td class="points">2*</td>
					</tr>
					<tr>
						<td class="action">House Managing</td> <td class="points">2*</td><td class="action">Mentoring</td> <td class="points">2</td>
					</tr>
					<tr>
						<td class="action">Community Outreach</td> <td class="points">1</td><td class="action">Participating in a Build</td> <td class="points">2*</td>
					</tr>
					<tr>
						<td class="action">Participating in a Strike</td> <td class="points">2*</td><td class="action">Social Outings</td> <td class="points">1</td>
					</tr>
					<tr>
						<td class="action">Meeting Attendance</td> <td class="points">1</td>
					</tr>
				</table>
			</section>
			<section>
				<h2>Join now!</h2>
				
				<div class="form-container">
					<?php
					if($this->has_flag(0,"submit")){
						if($this->membership_status == true){
							echo '<div id="membership_message" class="visible_override"><strong>Membership registration successful!</strong><br />Thank you for joining Florida Players!</div>';
							echo '<div id="membership_errors" class="errors"></div>';
						}
						else{
							echo '<div id="membership_message"></div>';
							echo '<div id="membership_errors" class="errors visible_override"><p>The server returned the following errors!</p><ul>';
							foreach($this->membership_details as $error){
								echo "<li>{$error}</li>";
							}
							echo '</ul></div>';
						}
					}
					else{ ?>
						<div id="membership_errors" class="errors"></div>
						<div id="membership_message"></div>
					<?php } ?>
				
					<form action="/about/membership/submit" method="post" id="membership_form">
						<fieldset>
							<legend>Your Information</legend>
							<?php
								$this->text_input("First name","first_name",true,"");
								$this->text_input("Last name","last_name",true);
								$this->text_input("Email address","email_address",true);
								$this->text_input("Phone number","phone_number",false);
								$this->text_input("Major","major",true);
							?>
							<div id="input_container_year_level">
								<label for="input_year_level">Year <em>*</em></label>
								<select id="input_year_level" name="input_year_level" class="styled_select">
									<option disabled="disabled" selected="selected">Select one</option>
									<option value="1">Freshman</option>
									<option value="2">Sophomore</option>
									<option value="3">Junior</option>
									<option value="4">Senior</option>
									<option value="5">Graduate</option>
									<option value="0">Other</option>
								</select>
							</div>
						</fieldset>
						<input type="submit" value="Join Florida Players" id="input_submit_membership" />
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
				</div>
			</section>
		</article>
		<?php
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
}
?>