<?php
require_once "authentication.php";
require SERVER_RES_DIR.'facebook-sdk/facebook.php';
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
		echo "Facebook stuff";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
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
		
		if(isset($_GET['code'])){
			$conf = parse_ini_file(SERVER_INI_FILE,true);

			//initializing keys
			$facebook = new Facebook(array(
				'appId'  => $conf['facebook']['appid'],
				'secret' => $conf['facebook']['secret']
			));
			
			/*$user = $facebook->getUser();
			if($user){*/
			
				$conf['facebook']['access_code'] = $facebook->getAccessToken();
				
				//echo 'accessToken = ' . $facebook->getAccessToken();
				
				$this->write_php_ini($conf,SERVER_INI_FILE);
			//}
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
		$conf = parse_ini_file(SERVER_INI_FILE,true);

		//initializing keys
		$facebook = new Facebook(array(
			'appId'  => $conf['facebook']['appid'],
			'secret' => $conf['facebook']['secret']
		));
		
		if(isset($conf['facebook']['access_code'])){
			//echo 'token: ' . $conf['facebook']['access_code'];
			$facebook->setAccessToken($conf['facebook']['access_code']);
			$facebook->setExtendedAccessToken();
		}
		
		$user = $facebook->getUser();
		if($user){
			 try {

				$user_profile = $facebook->api('/152160421492556/events','GET');
				//echo "Name: " . $user_profile['name'];
				print_r($user_profile);

			} catch(FacebookApiException $e) {
				// If the user is logged out, you can have a 
				// user ID even though the access token is invalid.
				// In this case, we'll get an exception, so we'll
				// just ask the user to login again here.
				$login_url = $facebook->getLoginUrl(); 
				echo 'Please <a href="' . $login_url . '">login.</a>';
			}   
		}
		else{
			$login_url = $facebook->getLoginUrl();
			echo 'Please <a href="' . $login_url . '">login.</a>';
		}
		// if($facebook->getSession()) {
			// $user = $facebook->getUser();
		// }
		// else{
			// $loginUrl = "https://graph.facebook.com/oauth/authorize?type=user_agent,offline_access&display=page&client_id={$conf['facebook']['appid']}&redirect_uri=http://apps.facebook.com/CANVAS URL/
			// &scope=user_photos";
			// echo '<a href="' . $loginUrl . '">Log in to Facebook</a>';
		// }
	}
	
	function write_php_ini($array, $file){
		$res = array();
		foreach($array as $key => $val){
			if(is_array($val))
			{
				$res[] = "[$key]";
				foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
			}
			else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
		}
		$this->safefilerewrite($file, implode("\r\n", $res));
	}
	function safefilerewrite($fileName, $dataToSave){    
		if ($fp = fopen($fileName, 'w')){
			$startTime = microtime();
			do{            
				$canWrite = flock($fp, LOCK_EX);
			   // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
			   if(!$canWrite) usleep(round(rand(0, 100)*1000));
			} while ((!$canWrite)and((microtime()-$startTime) < 1000));

			//file was locked so now we can store information
			if ($canWrite)
			{            
				fwrite($fp, $dataToSave);
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		}
	}
}
?>