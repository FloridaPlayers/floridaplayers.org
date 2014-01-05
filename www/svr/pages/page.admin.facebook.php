<?php
require_once "authentication.php";
require SERVER_RES_DIR.'facebook-sdk/facebook.php';
class Page{
	var $request = null;
	var $sql = null;
	var $usr;
	
	private $facebook;
	private $conf;
	
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
		
		set_time_limit(15);
		
		$this->conf = parse_ini_file(SERVER_INI_FILE,true);
		
		$this->facebook = new Facebook(array(
			'appId'  => $this->conf['facebook']['appid'],
			'secret' => $this->conf['facebook']['secret']
		));
		
		if(isset($_GET['code'])){
			$this->facebook->setExtendedAccessToken();
			$this->writeNewAccessCode();
		}
		
		if(isset($this->conf['facebook']['access_code'])){
			//echo 'token: ' . $this->conf['facebook']['access_code'];
			$this->facebook->setAccessToken($this->conf['facebook']['access_code']);
			$this->facebook->setExtendedAccessToken();
		}
		
		if($this->facebook->getUser() !== 0 && !isset($this->conf['facebook']['access_code'])){
			$this->writeNewAccessCode();
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
		if($this->requiresReauthorization()){ ?>
			Please <a href="<?php echo $this->getReauthorizationUrl(); ?>">log in</a>.
		<?php } else { ?>
			<?php
			$data = $this->getMyInfo();
			if($data === false){ ?>
				Please <a href="<?php echo $this->getReauthorizationUrl(); ?>">log in</a>.
			<?php }
			else{
				echo 'Authorized by ' . $data['name'] . '<br />';
				echo '<a href="'.$this->getLogoutUrl().'">Log out</a>';
			}
			?>
		<?php }
	}
	
	function writeNewAccessCode(){
		$this->conf['facebook']['access_code'] = $this->facebook->getAccessToken();
		$this->writePhpIni($this->conf,SERVER_INI_FILE);
	}
	
	function requiresReauthorization(){
		if($this->facebook->getUser() == false){
			return true;
		}
		return false;
	}
	
	function getReauthorizationUrl(){
		return $this->facebook->getLoginUrl();
	}
	
	function getLogoutUrl(){
		return $this->facebook->getLogoutUrl();
	}
	
	function getMyInfo(){
		try {
			$user_profile = $this->facebook->api('/me','GET');
			//echo "Name: " . $user_profile['name'];
			return $user_profile;

		} catch(FacebookApiException $e) {
			// If the user is logged out, you can have a 
			// user ID even though the access token is invalid.
			// In this case, we'll get an exception, so we'll
			// just ask the user to login again here.
			return false;
		}   
	}
	
	function writePhpIni($array, $file){
		$res = array();
		foreach($array as $key => $val){
			if(is_array($val))
			{
				$res[] = "[$key]";
				foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
			}
			else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
		}
		$this->safeFileRewrite($file, implode("\r\n", $res));
	}
	function safeFileRewrite($fileName, $dataToSave){    
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