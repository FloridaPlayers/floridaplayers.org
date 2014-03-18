<?php
require SERVER_RES_DIR.'facebook-sdk/facebook.php';
$conf = parse_ini_file(SERVER_INI_FILE,true);
set_time_limit(25);

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
	$file = SERVER_CACHE_DIR . 'FacebookEventCache.cache';
	//safeFileRewrite($file,json_encode($data,JSON_PRETTY_PRINT));
	safeFileRewrite($file,json_encode($data));
}

function loadArrayFromCache(){
	$file = SERVER_CACHE_DIR . 'FacebookEventCache.cache';
	return json_decode(file_get_contents($file),true);
}
function debug($message){
	echo $message . "<br />";
}

function orderMyEvents($a, $b) {
	return $a['timestamp'] - $b['timestamp'];
}

debug("beginning script");
debug("initializing facebook variable");

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

debug('initializing facebook user');
$user = $facebook->getUser();
if($user){
	try {
		$eventCache = loadArrayFromCache();
		if(!isset($eventCache['ignored'])){
			$eventCache['ignored'] = array();
		}
		if(!isset($eventCache['event_data'])){
			$eventCache['event_data'] = array();
		}
		if(!isset($eventCache['wall_posts'])){
			$eventCache['wall_posts'] = array();
			$eventCache['wall_posts']['data'] = array();
		}
		if(!isset($eventCache['page_events'])){
			$eventCache['page_events'] = array();
			$eventCache['page_events']['data'] = array();
		}
		
		$events = null;
		if(isset($eventCache['page_events']) && isset($eventCache['page_events']['data_acquired'])){
			if(((time() - $eventCache['page_events']['data_acquired']) / 3600.0) <= 6.0){ //If data is less than 2 hours old
				debug("using cached events");
				$events = $eventCache['page_events'];
			}
		}
		if($events == null){
			debug("loading events");
			$events = $facebook->api('/152160421492556/events','GET');
			$eventCache['page_events'] = $events;
			$eventCache['page_events']['data_acquired'] = time();
		}
		$eventIds = array();
		foreach($events['data'] as $event){
			$eventIds[] = $event['id'];
		}
		
		//Get the page-posted posts from the wall
		$posts = null;
		if(isset($eventCache['wall_posts']) && isset($eventCache['wall_posts']['data_acquired'])){
			if(((time() - $eventCache['wall_posts']['data_acquired']) / 3600.0) <= 2.0){ //If data is less than 2 hours old
				debug("using cached posts");
				$posts = $eventCache['wall_posts'];
			}
		}
		if($posts == null){ //Not using cached
			debug("loading posts");
			$posts = $facebook->api('/152160421492556/posts','GET');
			$eventCache['wall_posts']['data_acquired'] = time();
		}
		$postedEvents = array();
		foreach($posts['data'] as $post){
			if(isset($post['link'])){
				if(preg_match('#facebook\.com/events/(\d{15})/.*?#',$post['link'],$matches)){ //Find the IDs of events posted by the page.
					$eventId = $matches[1];
					if(isset($eventCache['wall_posts']['data']) && !array_key_exists($eventId,$eventCache['wall_posts']['data'])){
						$eventCache['wall_posts']['data'][$eventId] = array('link'=>$post['link']);
					}
					
					if(!in_array($eventId,$eventIds) || !isset($eventCache['ignored']) || !in_array($eventId,$eventCache['ignored'])){ //Make sure this is not a duplicate event, and that it is not an ignored event
						try{
							$eventIds[] = $eventId; //Append this event id.
							if(isset($eventCache['event_data']) && array_key_exists($eventId,$eventCache['event_data'])){
								$cachedEvent = $eventCache['event_data'][$eventId];
								if(((time() - $cachedEvent['data_acquired']) / 3600.0) <= 6.0){ //If the data is less than 6 hours old
									$events['data'][] = $cachedEvent;
									debug("Using cached value for $eventId");
									continue; //We don't need to go fetch the data, so let's continue to the next item in the loop
								}
							}
							debug("Fetching data for $eventId");
							$appendEvent = $facebook->api('/'.$eventId,'GET');
							$appendEvent['data_acquired'] = time();
							$events['data'][] = $appendEvent;
							
							$eventCache['event_data'][$eventId] = $appendEvent;
						}
						catch(FacebookApiException $e) {}  
					}
					else{
						debug("ignoring {$eventId}");
					}
				}
			}
		}
		
		$outputEvents = array();
		foreach($events['data'] as $event){
			$eventTime = strtotime($event['start_time']);
			if($eventTime >= time() - (60 * 60 * 5)){ //If the start time is at the latest, 5 hours ago. (So, anytime in the future, or the past 5 hours).
				$tempEvent = array();
				$tempEvent['name'] = $event['name'];
				$timeStr = (isset($event['end_time']))?'l, M jS \f\r\o\m g:ia':'l, M jS \a\t g:ia';
				$time = date($timeStr,$eventTime);
				if(isset($event['end_time'])){
					$endTime = strtotime($event['end_time']);
					if(date('l',$eventTime) != date('l',$endTime)){
						$time .= ' to ' . date('l \a\t g:ia',strtotime($event['end_time']));
					}
					else{
						$time .= ' to ' .  date('g:ia',strtotime($event['end_time']));
					}
				}
				$tempEvent['time'] = $time;
				
				global $THEATER_LOCATIONS;
				
				if(stristr($event['location'],$THEATER_LOCATIONS['1']['short']) !== false){
					$tempEvent['location'] = '<a class="location" href="/map/'.$THEATER_LOCATIONS['1']['short'].'">'.$event['location'].'</a>';
				}
				elseif(stristr($event['location'],$THEATER_LOCATIONS['2']['short']) !== false){
					$tempEvent['location'] = '<a class="location" href="/map/'.$THEATER_LOCATIONS['2']['short'].'">'.$event['location'].'</a>';
				}
				else{
					$tempEvent['location'] = $event['location'];
				}
				$tempEvent['url'] = 'http://www.facebook.com/events/'.$event['id'].'/';
				$tempEvent['timestamp'] = $eventTime;
				
				$outputEvents[] = $tempEvent; //append the event
			}
			else{
				debug("adding {$event['id']} to ignore list");
				$eventCache['ignored'][] = $event['id'];
				if(array_key_exists($event['id'],$eventCache['event_data'])){
					debug("removing {$event['id']} from cache");
					unset($eventCache['event_data'][$event['id']]);
				}
			}
		}
		
		writeArrayToCache($eventCache);
		
		usort($outputEvents, 'orderMyEvents');
		
		header('Content-Type: application/json');
		echo json_encode(array('events'=>$outputEvents));

	} catch(FacebookApiException $e) {
	}   
}

?>