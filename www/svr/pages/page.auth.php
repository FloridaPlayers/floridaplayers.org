<?php
require_once "authentication.php";


class Page{
	var $request = null;
	var $usr;
	
	var $bodyContent = null; 
	
	
	var $registrationOpen = false; //Only hides the form, doesn't actually shut off functionality
	function Page($request){
		$this->request = $request;
		$this->usr = new User();
	}

	//Return string containing the page's title. 
	function getTitle(){
		echo "Log in";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
			<style type="text/css">
			.half-container:not(.full){
				display: inline-block;
				vertical-align: top;
				width: 49%;
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				-webkit-box-sizing:border-box;
				padding: 0 10px 0 0;
				margin: 0 !important;
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
	<?php
	}
	
	function getOverrideTemplate(){
		if($this->lastFlag("iframe")){
			
			if(is_good_file("./svr/res/auth-iframe-template.html")){
				//echo file_get_contents("/srv/res/auth-iframe-template.html");
				return file_get_contents("./svr/res/auth-iframe-template.html");
			}
			else{
				die("Cannot find templace file! Panic!");
			}
		}
		return false;
	}

	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		if($this->hasFlag(0,"logout")){
			$this->usr->log_out();
			
			
			$this->bodyContent .= "<h1>Logged out</h1>";
			if(!$this->lastFlag("iframe")){
				$this->bodyContent .= "<script type=\"text/javascript\"> function fwd(){ window.location = \"/home\"; } setTimeout('fwd()', 1000); </script>";
			}
			//header("Location: /home/loggedout");
			//return false; 
		}
		else if($this->hasFlag(0,"register")){
			if($this->hasFlag(1,"success")){
				if($this->usr->is_logged_in()){
					$this->bodyContent .= "<h1>Registration successful!</h1>
					<script type=\"text/javascript\"> function fwd(){ window.location = \"/home\"; } setTimeout('fwd()', 1000); </script>";
				}
				else{
					header("Location: /home" . $this->add_iframe());
					return false;
				}
			}
			else{
				$regVars = $this->getValues($_POST,array("input_first_name","input_last_name","input_email","input_password","input_password_conf"));
				if($regVars === false){
					$this->bodyContent .= $this->errorTemplate("You must fill out all of the registration form!");
				}
				else{
					//echo "two";
					if($regVars["input_password"] !== $regVars["input_password_conf"]){
						$this->bodyContent .= $this->errorTemplate("Your password and the confirmation do not match!");
					}
					else{
						$regInfo = $this->usr->register_user($regVars["input_email"],$regVars["input_password"],$regVars["input_first_name"],$regVars["input_last_name"]);
						if($regInfo["success"] === 1){
							//$this->bodyContent .= "<h1>Registration successful!</h1>";
							header("Location: /auth/register/success" . $this->add_iframe());
							return false;
						}
						else{
							$temp = "";
							foreach($regInfo["errors"] as $error){
								$temp .= $error . "<br />";
							}
							$this->bodyContent .= $this->errorTemplate($temp);
						}
					}
				}
			}
		}
		else if($this->hasFlag(0,"login")){
			if($this->hasFlag(1,"success")){
				if($this->usr->is_logged_in()){
					$this->bodyContent .= "<h1>Log in successful!</h1>
					<script type=\"text/javascript\"> function fwd(){ window.location = \"/home\"; } setTimeout('fwd()', 1000); </script>";
				}
				else{
					header("Location: /auth" . $this->add_iframe());
					return false;
				}
			}
			else{
				$regVars = $this->getValues($_POST,array("input_email","input_password"));
				if($regVars === false){
					$this->bodyContent .= $this->errorTemplate("You must fill out all of the log in form!");
				}
				else{
					$regInfo = $this->usr->log_in($regVars["input_email"],$regVars["input_password"]);
					if($regInfo["success"] === 1){
						//$this->bodyContent .= "<h1>Log in successful!</h1>";
						header("Location: /auth/login/success" . $this->add_iframe());
						return false; 
					}
					else{
						$temp = "";
						foreach($regInfo["errors"] as $error){
							$temp .= $error . "<br />";
						}
						$this->bodyContent .= $this->errorTemplate($temp);
					}
				}
			}
		}
		else if($this->usr->is_logged_in()){
			header("Location: /home");
			return false;
		}
		return true;
	}


	//Return nothing; print out the page. 
	function getContent(){
		?><article><?php
		if($this->bodyContent !== null){
			echo $this->bodyContent;
		}
		if($this->hasFlag(0,"login") && $this->hasFlag(1,"success") || $this->hasFlag(0,"register") && $this->hasFlag(1,"success") || $this->hasFlag(0,"logout")){
			return;
		}
		if(($this->isntFlag(0,array("login","register")) || $this->hasFlag(0,"register")) && $this->registrationOpen){ ?>
			<div class="form-container half-container<?php if(!($this->isntFlag(0,array("login","register")) || $this->hasFlag(0,"login"))) echo " full"; ?>">
				<h1>Register!</h1>
				<form action="/auth/register<?php echo $this->add_iframe(); ?>" method="post">
					<fieldset>
					<?php 
					$this->text_input("First name","first_name",true,$this->input("input_first_name"));
					$this->text_input("Last name","last_name",true,$this->input("input_last_name"));
					$this->text_input("Email","email",true,$this->input("input_email"));
					$this->text_input("Password","password",true,"",0,true);
					$this->text_input("Confirm password","password_conf",true,"",0,true);
					?>
					</fieldset>
					<input class="submitbutton" type="submit" value="Register" />
				</form>
			</div><?php
		}
		if($this->isntFlag(0,array("login","register")) || $this->hasFlag(0,"login")){ ?>
			<div class="form-container half-container<?php if(!($this->isntFlag(0,array("login","register")) || $this->hasFlag(0,"register"))) echo " full"; ?>">
				<h1>Log in!</h1>
				<form action="/auth/login<?php echo $this->add_iframe(); ?>" method="post">
					<fieldset>
					<?php 
					$this->text_input("Email","email",true,$this->input("input_email"));
					$this->text_input("Password","password",true,"",0,true);
					?>
					</fieldset>
					<input class="submitbutton" type="submit" value="Log in" />
				</form>
			</div><?php
		}?>
		</article>
		
		<?php
	}
	
	function getValues($post,$keyarray){
		$ret = array();
		foreach($keyarray as $key){
			if(isset($post[$key]) && trim($post[$key]) != ""){
				$ret[$key] = $post[$key];
			}
			else{
				return false;
			}
		}
		return $ret;
	}
	
	function errorTemplate($message){
		return "<section class=\"error full\">$message</section>";
	}
	
	function hasFlag($pos,$flag){
		return (isset($this->request) && isset($this->request["flags"][$pos]) && $this->request["flags"][$pos] == $flag) === true;
	}
	function isntFlag($pos,$flags = array()){
		if(isset($this->request) && isset($this->request["flags"][$pos])){
			$test = $this->request["flags"][$pos];
			foreach($flags as $flag){
				if($flag === $test){
					return false;
				}
			}
		}
		return true;
	}
	
	function lastFlag($flag){
		if(isset($this->request) && isset($this->request["flags"])){
			$flags = $this->request["flags"];
			if(count($flags) > 0){
				if($flags[count($flags) - 1] == $flag){
					return true;
				}
			}
		}
		return false;
	}
	
	function is_good_file($file){
		return file_exists($file) && is_file($file);
	}
	
	function add_iframe(){
		if($this->lastFlag("iframe")) return "/iframe";
		return "";
	}
	
	/**
	 * Gets the $_POST var with index $name, or returns "" if null.
	 **/
	function input($name){
		if(isset($_POST[$name])){
			$t = $this->simpleCleanInput($_POST[$name]);
			if($t !== false){
				return $t;
			}
		}
		return "";
	}
	
	function simpleCleanInput($input){ //Renamed simple because of conflict with authentication.php
		if(!is_null($input)){
			$value = $input;
			$value = @strip_tags($value);	
			$value = @stripslashes($value);
			$value = trim($value);
			if(!empty($value)){
				return $value;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	function text_input($label,$name,$required = false,$value = "",$size = null,$is_password = false){
		$id = "input_$name";
		$type = "text";
		if($is_password) $type = "password";
		
		$size_echo = "";
		if(isset($size) && $size >= 1){
			$size_echo = "size=\"$size\"";
		}
		?>
		<div id="input_container_<?php echo $name; ?>"><label for="<?php echo $id; ?>"><?php echo $label; if($required) echo " <em>*</em>"; ?></label> <input id="<?php echo $id; ?>" type="<?php echo $type; ?>" name="<?php echo $id; ?>" value="<?php echo htmlentities($value); ?>" <?php echo $size_echo; ?> /></div>
	<?php
	}
}
?>