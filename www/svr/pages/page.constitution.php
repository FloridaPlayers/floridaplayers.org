<?php

class Page{
	var $request = null;
    private $conf;
    private $constitutionData;

	function __construct($request){
		$this->request = $request;
        $this->conf = parse_ini_file(SERVER_INI_FILE,true);
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Florida Player's Constitution";
	}

	//Page specific content for the <head> section.
	function customHead(){?>

			<!-- CUSTOM CSS/LINK HERE -->
	<?php
	}


	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
        $repoCommits = json_decode($this->get_data('https://api.github.com/repos/FloridaPlayers/Documents/commits?path=Constitution.md'), true);
        $requestFailed = !(isset($repoCommits[0]) && isset($repoCommits[0]['sha']));
        $mostRecentSha = (!$requestFailed) ? $repoCommits[0]['sha'] : '';
        
        $cacheInfo = $this->loadArrayFromCache();
        
        if($cacheInfo['sha'] == $mostRecentSha || $requestFailed){ //No new commits
            $this->constitutionData = $cacheInfo['data'];
        }
        else{
            $commitDataUrl = $repoCommits[0]['commit']['tree']['url'];
            $repoCommits = ''; //Dump this memory! 
            $commitData = json_decode($this->get_data($commitDataUrl), false);
            
            foreach($commitData->tree as $fileInfo){
                if($fileInfo->path == 'Constitution.md'){
                    $constitutionDataUrl = $fileInfo->url;
                    
                    $constitutionData = json_decode($this->get_data($constitutionDataUrl));
                    $constitutionMarkdown = base64_decode($constitutionData->content);
                    
                    require_once 'Parsedown.php';
                    $this->constitutionData = Parsedown::instance()->text($constitutionMarkdown);
                    
                    $toCache = array('sha' => $mostRecentSha, 'data' => $this->constitutionData);
                    $this->writeArrayToCache($toCache);
                    break;
                }
            }
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
		<article>
            <?php echo $this->constitutionData; ?>
        </article>
		<?php
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

    function writeArrayToCache($data){
        $file = SERVER_CACHE_DIR . 'Constitution.cache';
        //safeFileRewrite($file,json_encode($data,JSON_PRETTY_PRINT));
        $this->safeFileRewrite($file,json_encode($data));
    }

    function loadArrayFromCache(){
        $file = SERVER_CACHE_DIR . 'Constitution.cache';
        return json_decode(file_get_contents($file),true);
    }
    
    //https://davidwalsh.name/curl-download
    function get_data($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "Florida-Players-webmaster@floridaplayers.org");
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
?>