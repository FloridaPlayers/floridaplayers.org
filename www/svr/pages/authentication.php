<?php

require_once "require.php";
require_once "emailvalidate.php";

session_start();

function cleanInput($input,$minLength,$maxLength){
	if(!is_null($input)){
		$value = $input;
		$value = @strip_tags($value);	
		$value = @stripslashes($value);
		$value = trim($value);	
		$value = @substr($value,$maxLenth);
		$value = trim($value);	/*redundant so as to remove spaces
		that may have been placed in the middle of a string */
		if(strlen($value) < $minLength){
			return false;
		}
		else if(!empty($value)){
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
function reportInputError($input,$minLength,$maxLength){
	if(!is_null($input)){
		$value = $input;
		$value = @strip_tags($value);	
		$value = @stripslashes($value);
		$value = trim($value);	
		$value = @substr($value,$maxLenth);
		$value = trim($value);	/*redundant so as to remove spaces
		that may have been placed in the middle of a string */
		if(strlen($value) < $minLength){
			return "The input is shorter than the minimum length of $minLength characters";
		}
		else if(!empty($value)){
			return "Something is wrong with the server!";
		}
		else{
			logError($_SERVER['SCRIPT_NAME'],__LINE__,"User provided input that was highly contaminated","No error",time());
			return "The input is contaminated!";
		}
	}
	else{
		return "The input provided is empty!";
	}
}

class User{
	var $dbReturn = null;
	var $sqlCon = null;
	var $authenticated = false;
	var $loggedIn = false;
	function debug($mes){
		echo $mes . "<br />";
	}
	function User(){
		$this->sqlCon = @mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not connect to MySQL server.",mysql_error(),time());
		@mysql_select_db(DB_NAME) or logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not select database (".DB_NAME.").",mysql_error(),time());
		
		if($this->authenticated == false){
			$this->loggedIn = $this->authenticate();
			$this->authenticated = true;
		}
	}
	function is_logged_in(){
		if($this->authenticated == false){
			$this->loggedIn = $this->authenticate();
			$this->authenticated = true;
		}
		return $this->loggedIn;
	}
	function get_info(){
		if($this->is_logged_in()){
			$infoQuery = "SELECT uid, email, first_name, last_name, permissions FROM users WHERE uid='{$_SESSION['USER_ID']}' LIMIT 1;";
			$infoQueryResponse = mysql_query($infoQuery,$this->sqlCon);
			
			if(!$infoQueryResponse) {
				logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not check user session! Query: \"$infoQuery\"",mysql_error(),time(),false);
				return false;
			}
			else{
				$this->dbReturn = mysql_fetch_assoc($infoQueryResponse);
				return true;
			}
		}
	}
			
	function authenticate(){
		if(!isset($_SESSION['USER_ID']) || (trim($_SESSION['USER_ID'])=='')) { 
			return false;
			//
		}
		else{
			$userID = $_SESSION['USER_ID'];
				
			$infoQuery = "SELECT session FROM users WHERE uid='{$userID}' LIMIT 1;";
			$infoQueryResponse = mysql_query($infoQuery,$this->sqlCon);
			if(!$infoQueryResponse) {
				logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not check user session! Query: \"$infoQuery\"",mysql_error(),time(),false);
				return false;
			}
			
			$info = mysql_fetch_assoc($infoQueryResponse);
			
			if($_SESSION['AUTH_KEY'] === $info['session']){
				
				session_regenerate_id();
				$authKey = newAuthKey();
				$oldAuth = $_SESSION['AUTH_KEY'];
				$_SESSION['AUTH_KEY'] = $authKey;
				
				$updateAuthSQL = "UPDATE users SET session='{$authKey}' WHERE uid='{$userID}' LIMIT 1";
				$updateAuthQueryResponse = mysql_query($updateAuthSQL,$this->sqlCon);
				if(!$updateAuthQueryResponse){
					logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not insert user's auth key into table! Query: \"{$updateAuthSQL}\"",mysql_error(),time(),false);
					$_SESSION['AUTH_KEY'] = $oldAuth;
				}
				return true;
			}
			else{
				return false;
			}
		}
	}
	function get_user_info($info){
		//echo $this->dbReturn['first_name'];
		if($this->dbReturn != null){
			if(array_key_exists($info,$this->dbReturn)){
				return $this->dbReturn[$info];
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	function set_user_info($col,$val){
		$userID = $_SESSION['USER_ID'];
		
		$updateSQL = "UPDATE users SET {$col}='{$val}' WHERE uid='{$userID}' LIMIT 1";
		$updateQueryResponse = mysql_query($updateSQL,$this->sqlCon);
		if(!$updateQueryResponse){
			logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not update user info! Column: \"{$col}\", Value: \"{$val}\" Query: \"{$updateAuthSQL}\"",mysql_error(),time(),false);
			return false;
		}
		$this->get_info();
		return true;
	}
	function register_user($email,$password,$fname,$lname){
		$return = array("success" => 0,"errors" => array());
		
		if($this->is_logged_in()){
			$return["success"] = 0;
			array_push($return["errors"],"User is already logged in.");
			return $return;
		}
		if(!validEmail($email = cleanInput($email,0,500))){
			$return["success"] = 0;
			array_push($return["errors"],"The email address provided is not valid.");
			return $return;
		}
		if(($password = cleanInput($password,6,40)) == false || ($fname = cleanInput($fname,0,100)) == false || ($lname = cleanInput($lname,0,100)) == false){
			$return["success"] = 0;
			$inv = "";
			if($password === false) $inv .= "password";
			if($fname === false) $inv .= ((strlen($inv) == 0)?"":", ")."first name";
			if($lname === false) $inv .= ((strlen($inv) == 0)?"":", ")."last name";
			array_push($return["errors"],"The following inputs appear to be invalid: $inv.");
			return $return;
		}
		if(!preg_match("/^[\w-]*$/",$fname) || !preg_match("/^[\w-]*$/",$lname)){
			$return["success"] = 0;
			array_push($return["errors"],"Your first name may only consist of alphanumeric characters or hyphens!");
			return $return;
		}
		$email = strtolower(mysql_real_escape_string($email));
		$password = mysql_real_escape_string($password);
		$encrypted = sha1(md5($password) . "" . md5($email));
		$firstName = mysql_real_escape_string($fname);
		$lastName = mysql_real_escape_string($lname);
		
		$userCheckQuery = "SELECT email FROM users WHERE email='$email' LIMIT 1;";
		$userCheckQueryResponse = mysql_query($userCheckQuery,$this->sqlCon);
		if(mysql_num_rows($userCheckQueryResponse) > 0){
			$conflict = mysql_fetch_assoc($userCheckQueryResponse);
			if($conflict['email'] == $email){
				$return["success"] = 0;
				array_push($return["errors"],"An account already exists associated with this email!");
				return $return;
			}
			if(mysql_num_rows($userCheckQueryResponse) > 1){
				$return["success"] = 0;
				array_push($return["errors"],"We're sorry, but something has gone wrong!");
				logError($_SERVER['SCRIPT_NAME'],__LINE__,"Registration check has yeilded more than one user with similar registration data! Query: \"$userCheckQuery\"","No error; IP: $_SERVER[REMOTE_ADDR]",time());
				return $return;
			}
		}
		else{
			$updateQuery="INSERT INTO users (email,password,first_name,last_name) VALUES ('$email','$encrypted','$firstName','$lastName');";
			$updateQueryResponse = mysql_query($updateQuery,$this->sqlCon);
			if(!$updateQueryResponse){
				logError($_SERVER['SCRIPT_NAME'],__LINE__,"An error occurred while registering a new user! Query: \"$updateQuery\"",mysql_error(),time(),false);

				$return["success"] = 0;
				array_push($return["errors"],"An error occurred while trying to create your account! Oh no! Please try again.");
				return $return;
			}
			else{
				$uidValue = mysql_insert_id($this->sqlCon);
				if($uidValue > 0 && $uidValue != false){
					$this->give_credentials($uidValue); 
					$return["success"] = 1;
					return $return;
				}
			}
		}
		return $return;
	}
	function log_in($email,$password){
		$return = array("success" => 0,"errors" => array());
		
		if($this->is_logged_in()){
			$return["success"] = 1;
			array_push($return["errors"],"User is already logged in.");
			return $return;
		}
		
		if(($email = cleanInput($email,0,500)) == false){
			$return["success"] = 0;
			array_push($return["errors"],"The email provided is invalid!");
			return $return;
		}
		if(($password = cleanInput($password,8,40)) == false){
			$return["success"] = 0;
			array_push($return["errors"],"The password provided is invalid!");
			return $return;
		}
		
		if(!isset($_SESSION)){ //Fix for error: "Notice: A session had already been started - ignoring session_start()"
			session_start();
		}  
		session_unset();
		
		$email = strtolower(mysql_real_escape_string($email));
		$password = mysql_real_escape_string($password);
		$encrypted = sha1(md5($password) . "" . md5($email));


		$loginQuery = "SELECT uid,password,last_attempt,attempt_count,disabled FROM users WHERE email='$email' LIMIT 1;";
		$loginQueryResponse = mysql_query($loginQuery,$this->sqlCon);
		if(!$loginQueryResponse) {
			logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not log in user. Query: \"$loginQuery\"",mysql_error(),time());
			$return["success"] = 0;
			array_push($return["errors"],"An error occurred while trying to log in!");
			return $return;
		}
		else if(mysql_affected_rows() == 0){
			$return["success"] = 0;
			array_push($return["errors"],"Username or password is incorrect!");
			return $return;
			// Username was wrong, but we don't tell the
			// user as this information could be exploited 
		}
		else{
			$currentTime = date("Y-m-d H:i:s");
			$loginResult = mysql_fetch_assoc($loginQueryResponse);
			if($loginResult['disabled'] == 1 && (strtotime($loginResult['last_attempt']) + DISABLED_ACCOUNT_PERIOD) > time()){
				$seconds = (strtotime($loginResult['last_attempt']) + DISABLED_ACCOUNT_PERIOD) - time();
				$minutes = floor($seconds / 60);
				$remainingSeconds = $seconds % 60;
				$minString = ($minutes > 1) ? "$minutes minutes, " : (($minutes == 0) ? "" : "$minutes minute, ");
				$secString = ($seconds > 1) ? "$remainingSeconds seconds" : (($seconds == 0) ? "" : "$remainingSeconds second ");
				showError("You must wait $minString $secString before you may try to log in!");
			}
			else{
				$uidValue = $loginResult['uid'];
				if($loginResult['password'] != $encrypted){
					$attemptCount = (int)$loginResult['attempt_count'] + 1;
					$accountDisabled = 0;
					if($attemptCount >= DISABLED_ACCOUNT_TRIES){
						$accountDisabled = 1;
					}
					$attemptCount = 0;
					$updateSQL = "UPDATE users SET last_attempt='$currentTime',disabled='$accountDisabled',attempt_count='$attemptCount' WHERE uid='$uidValue' LIMIT 1;";
					$updateQueryResponse = mysql_query($updateSQL,$this->sqlCon);
					if(!$updateQueryResponse){
						logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not log user's log-in attempt into users table! Query: \"$updateSQL\"",mysql_error(),time(),false);
					}

					if($attemptCount >= DISABLED_ACCOUNT_TRIES){
						$seconds = DISABLED_ACCOUNT_PERIOD;
						$minutes = floor($seconds / 60);
						$remainingSeconds = $seconds % 60;
						$minString = ($minutes > 1) ? "$minutes minutes " : (($minutes == 0) ? "" : "$minutes minute ");
						$secString = ($seconds > 1) ? "$remainingSeconds seconds" : (($seconds == 0) ? "" : "$remainingSeconds second ");

						array_push($return["errors"],"You have exceeded your maximum number of log in attempts!<br />You must wait $minString $secString before you may try to log in!");
					}
					
					$return["success"] = 0;
					array_push($return["errors"],"Username or password is incorrect!");
					// Password was wrong, but we don't tell the
					// user as this information could be exploited
					return $return;
				}
				else{

					$updateSQL = "UPDATE users SET last_login='$currentTime',last_attempt='$currentTime',disabled='0',attempt_count='0' WHERE uid='$uidValue' LIMIT 1;";
					$updateQueryResponse = mysql_query($updateSQL,$this->sqlCon);
					if(!$updateQueryResponse){
						logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not log user's log-in into users table! Query: \"$updateSQL\"",mysql_error(),time(),false);
					}
					
					$this->give_credentials($uidValue);
					$return["success"] = 1;
					return $return;
					
					
				}
			}
		}
		return $return; 
	}
	function give_credentials($uidValue){
		session_regenerate_id();
		$authKey = newAuthKey();
		$_SESSION['USER_ID']=$uidValue;
		$_SESSION['AUTH_KEY']=$authKey;
		
		$updateAuthSQL = "UPDATE users SET session='{$authKey}' WHERE uid='{$uidValue}' LIMIT 1";
		$updateAuthQueryResponse = mysql_query($updateAuthSQL,$this->sqlCon);
		if(!$updateAuthQueryResponse){
			logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not insert user's auth key into table! Query: \"{$updateAuthSQL}\"",mysql_error(),time(),false);
		}
		
		session_write_close();
	}
	function log_out(){
		if(!$this->is_logged_in()){
			//header("Location: /");
			unset($_SESSION['USER_ID']);
			unset($_SESSION['AUTH_KEY']);
			// just in case
		}
		//Start session
		if (!isset ($_COOKIE[ini_get('session.name')])) {
			session_start();
		}

		unset($_SESSION['USER_ID']);
		unset($_SESSION['AUTH_KEY']);
		
		//header("Location: /");
	}
}
?>