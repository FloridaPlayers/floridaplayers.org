<?php
require SERVER_RES_DIR.'facebook-sdk/facebook.php';
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

		$events = $facebook->api('/152160421492556/events','GET');
		
		$outputEvents = array();
		foreach($events['data'] as $event){
			$eventTime = strtotime($event['start_time']);
			if($eventTime >= time() - (60 * 60 * 5)){ //If the start time is at the latest, 5 hours ago. (So, anytime in the future, or the past 5 hours).
				$tempEvent = array();
				$tempEvent['name'] = $event['name'];
				$time = date('l, F jS \a\t g:ia',$eventTime);
				if(isset($event['end_time'])){
					$endTime = strtotime($event['end_time']);
					if(date('l',$eventTime) != date('1',$endTime)){
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
				
				$outputEvents[] = $tempEvent; //append the event
			}
		}
		header('Content-Type: application/json');
		echo json_encode(array('events'=>$outputEvents));

	} catch(FacebookApiException $e) {
	}   
}

/*print_r($_GET);

function get_data($url) {
	$ch = curl_init();
	$timeout = 50;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

  $app_id = '520558364705082';
  $app_secret = 'a55fd443dcc60d35e9c93bb540b7919b';

  $token_url = "https://graph.facebook.com/oauth/access_token?" .
    "client_id=" . $app_id .
    "&client_secret=" . $app_secret .
    "&grant_type=client_credentials";
	

  $app_access_token = get_data($token_url);
  
//388426151189208
  $apprequest_url ='https://graph.facebook.com/152160421492556/events?'.$app_access_token;
  //$apprequest_url ='https://graph.facebook.com/388426151189208/events?'.$app_access_token;

	echo $apprequest_url;
  $result = get_data($apprequest_url);
  
  
  echo $result;
*/
/*require_once("facebook-sdk/facebook.php");

date_default_timezone_set('America/Los_Angeles');

function display_events()
{
    $app_id = '520558364705082';
    $secret = 'a55fd443dcc60d35e9c93bb540b7919b';
    $page_id = '152160421492556';

    $config = array();
    $config['appId'] = $app_id;
    $config['secret'] = $secret;
    $config['fileUpload'] = false; // optional

    $facebook = new Facebook($config);

    $fql = 'SELECT 
                eid, 
                name, 
                pic_square, 
                creator,
                start_time,
                end_time
            FROM event 
            WHERE eid IN 
                (SELECT eid 
                    FROM event_member 
                    WHERE uid='.$page_id.'
                ) 
            ORDER BY start_time ASC
    ';

    $ret_obj = $facebook->api(array(
        'method' => 'fql.query',
        'query' => $fql,
    ));

	echo 'hi';
    $html = '';                            
    foreach($ret_obj as $key)
    {
        $facebook_url = 'https://www.facebook.com/event.php?eid=' . $key['eid'];

        $start_time = date('M j, Y \a\t g:i A', $key['start_time']);
        $end_time = date('M j, Y \a\t g:i A', $key['end_time']);

        $html .= '
            <div class="event">
                <a href="'.$facebook_url.'">
                    <img src="'.$key['pic_square'].'" />
                </a>
                <span>
                    <a href="'.$facebook_url.'">
                        <h2>'.$key['name'].'</h2>
                    </a>
                    <p class="time">'.$start_time.'</p>
                    <p class="time">'.$end_time.'</p>
                </span>
            </div>
        ';
    }

    echo $html;
}
display_events();*/
?>