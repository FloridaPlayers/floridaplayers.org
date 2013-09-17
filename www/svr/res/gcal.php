<?php 	
function displayFeed($feedBase,$displayAllFutureEvents,$noEventsText,$errorText,$maxEvents = 0) {

	//set timezone to get good "date()"
	date_default_timezone_set('America/New_York');

	if (date("I") == 1) {
		//daylight savings time
		$time_adjust = "-04:00";
	} else {
		//not daylight savings time
		$time_adjust = "-05:00";
	}
	
	$today_now_really = date("Y-m-d\TH:i:s", time()) . "-00:00";
	
	$one_month_from_today_really = mktime(date("H"), date("i"), date("s"), date("m")+1, date("d"), date("Y"));
	$one_month_from_today_really = date("Y-m-d\TH:i:s", $one_month_from_today_really) . "-00:00";
	
	//three hours before right now
	$one_month_in_seconds_2 = 60 * 60 * 24 * 31;
	$today_now = gmdate("Y-m-d\Th:i:s", time() - $one_month_in_seconds_2);
	$today_now = $today_now . $time_adjust;

	//one month from today in daylights savings time
	$one_month_in_seconds = 60 * 60 * 24 * 31;
	$one_month_from_today = gmdate("Y-m-d\Th:i:s", time() + $one_month_in_seconds);
	$one_month_from_today = $one_month_from_today . $time_adjust;
	
	if ($displayAllFutureEvents) {
		//Displays events according to certain settings
		//Find info about this here: http://code.google.com/apis/calendar/docs/2.0/reference.html
    	$feed = $feedBase . 
        "public/full?" . "&" .
		"orderby=" . "starttime" . "&" .
		"sortorder=" . "ascending" . "&" .
		"singleevents=" . "true" . "&" . 
        "futureevents=" . "true";
		
	} else {
		//Displays events according to certain settings
		//Find info about this here: http://code.google.com/apis/calendar/docs/2.0/reference.html
    	$feed = $feedBase . 
        "public/full?" . "&" .
		"orderby=" . "starttime" . "&" .
		"sortorder=" . "ascending" . "&" .
		"singleevents=" . "true" . "&" . 
		//"ctz=" . "America%2FPhoenix" . "&" .
        "start-min=" . $today_now . "&" .
        "start-max=" . $one_month_from_today;
	}
		
		//"futureevents=" . "true";
		//"ctz=" . "America%2FNew_York" . "&" . (doesn't work...?)
		
	
	//set confirmed according to google
	$confirmed = 'http://schemas.google.com/g/2005#event.confirmed';


	
	//new code
	$GLOBALS['error132'] = false;
	function customError($errno, $errstr)  
	  {
			$GLOBALS['error132'] = true;
	  }
	//new code

	set_error_handler("customError");//new code
	
    $doc = new DOMDocument(); 
    $doc->load( $feed );
	
	restore_error_handler();//new code

    $entries = $doc->getElementsByTagName( "entry" ); 
	
	$i = 0;
    foreach ( $entries as $entry ) { 

        $status = $entry->getElementsByTagName( "eventStatus" ); 
        $eventStatus = $status->item(0)->getAttributeNode("value")->value;
		
		$timesCheck = $entry->getElementsByTagNameNS("http://schemas.google.com/g/2005","when" ); 
		$endTimeCheck = $timesCheck->item(0)->getAttributeNode("endTime")->value;
		
		$endTimeCheck = date("Y-m-d\TH:i:s",strtotime($endTimeCheck)) . "-00:00";
		
		//echo $today_now_really . " is less " . $endTimeCheck . " equal "; 
		if (strtotime($today_now_really) <= strtotime($endTimeCheck) && strtotime($one_month_from_today_really) >= strtotime($endTimeCheck) ) {
			//echo "yes";
			$keepIt = true;
		} else {
			//echo "no";
			$keepIt = false;
		}
		//echo "<br /><br />";
		
		

        if ($eventStatus == $confirmed) {
            $titles = $entry->getElementsByTagName( "title" ); 
            $title = $titles->item(0)->nodeValue;
			
			$contents = $entry->getElementsByTagName( "content" ); 
            $content = $contents->item(0)->nodeValue;

            $times = $entry->getElementsByTagNameNS("http://schemas.google.com/g/2005","when" ); 
			
			
			$startTime = $times->item(0)->getAttributeNode("startTime")->value;
			//echo $startTime;
			//echo "<br />";
			$endTime = $times->item(0)->getAttributeNode("endTime")->value;
			//echo $endTime;
			//echo "<br />";
			
			$places = $entry->getElementsByTagNameNS("http://schemas.google.com/g/2005","where"); 	
			$where = $places->item(0)->getAttributeNode("valueString")->value;
       

			/*
			$title    - $stuffArray[i][0]
			$content  - $stuffArray[i][1]
			startTime - $stuffArray[i][2][u]
			$endTime  - $stuffArray[i][3]
			$where    - $stuffArray[i][4]
			*/
			
			/*echo "{";
			echo count($stuffArray);
			echo "}";*/
			
			/*
			echo "{ ";
			echo " (" . $i . ") ";
			for ($n=0; $n<=count($stuffArray)-1; $n++) {
  				echo $stuffArray[$n][0];
				echo "/";
  			}
			echo " }";
			*/
			
			$foundMatch = false;
			if(!isset($stuffArray)) $stuffArray = array();
			for ($n=0; $n<=count($stuffArray)-1; $n++) {
  				
				if ($title == $stuffArray[$n][0]) {
					$foundMatch = true;
					//$matchKey = $n;
					array_push($stuffArray[$n][1],$startTime);
				}
				
  			}
		
			if (!$foundMatch && $keepIt) {
				$stuffArray[$i][0]    = $title;
				$stuffArray[$i][1][0] = $startTime;
				$stuffArray[$i][2]    = $endTime;
				$stuffArray[$i][3]    = $where;
				$stuffArray[$i][4]    = $content;
				
				$i++;
			}
			
			if($maxEvents > 0 && $i >= $maxEvents){
				break; 
			}
			//$i++;
        }
		
    }
	/* Added the !isset. If this becomes broken, that may be the cause */
	/* old version is just "if (count($stuffArray) == 0) {" */
	if (!isset($stuffArray) || count($stuffArray) == 0) {
		if ($GLOBALS['error132']) {
			//error!
			echo $errorText;
		} else {
			//no upcoming events
			echo $noEventsText;
		}
	} else {
		
	
	
	//ECHO LIST
	for ($n=0; $n<=count($stuffArray)-1; $n++) {
	
		//easier names
		$title2 = $stuffArray[$n][0];
		$startTimeArray2 = $stuffArray[$n][1];
		$endTime2 = $stuffArray[$n][2];
		$where2 = $stuffArray[$n][3];
		$content2 = $stuffArray[$n][4];
	
		//echo "<p align='center' style='font-style:italic'>";
		echo "<li>";
		
		echo '<span class="eventTitle">' . $title2 . '</span>';
		echo '<span class="eventTime">';
		
		
		
		
		if (count($startTimeArray2) == 1) {
			
            $day = date( "l, F jS", strtotime( $startTimeArray2[0] ));
			$start_time = date( "g:ia", strtotime( $startTimeArray2[0] ));
			$end_time = date( "g:ia", strtotime( $endTime2 ));
			
			$eventLength = strtotime( $endTime2 ) - strtotime( $startTimeArray2[0] );
			
			echo $day;
			echo ("<br />");
			
			//0 MINUTE EVENT (just echo startTime)
			if ( $eventLength == 0 ) {
				echo ($start_time);
				echo ("<br />");
				
			//ALL DAY EVENT (don't echo hours)
			} else if ( $eventLength == 86400 ) {
				//
			//NORMAL EVENT (echo hours)
			} else {
				echo ($start_time . " - " . $end_time);
				echo ("<br />");
			}
			
		} else {
			
			/*
			$eventLength = strtotime( $endTime2 ) - strtotime( $startTimeArray2[0] );
			//ALL DAY REPEATING EVENT (don't echo hours)
			if ( $eventLength == 86400 ) {
				$allDay = true;
			} else {
				$allDay = false;
			}
			*/
		
			//reset array
			$repeatArray = array();
		
			$i = 0;
			//looping for every "time" available
			for ($n2=0; $n2<=count($startTimeArray2)-1; $n2++) {
			
				$day = date( "l, F jS", strtotime( $startTimeArray2[$n2] ));
				$start_time = date( "g:ia", strtotime( $startTimeArray2[$n2] ));
				
				if ( strlen($startTimeArray2[$n2]) == 10 ) {
					$start_time = "badEgg";
				}
				
				
				$foundMatch2 = false;
			
				for ($n3=0; $n3<=count($repeatArray)-1; $n3++) {
				
					if ($day == $repeatArray[$n3][0]) {
						$foundMatch2 = true;
						//$magicKey = $n3;
						array_push($repeatArray[$n3][1],$start_time);
					}
				
				}
			
				if (!$foundMatch2) {
					$repeatArray[$i][0]    = $day;
					$repeatArray[$i][1][0] = $start_time;
					
					$i++;
				}

			}
			
			
			
			
			//write out array just created
			for ($s=0; $s<=count($repeatArray)-1; $s++) {
			
				echo $repeatArray[$s][0];
				

				$addedAt = false;
				for ($s2=0; $s2<=count($repeatArray[$s][1])-1; $s2++) {
					
					if ($repeatArray[$s][1][$s2] == "badEgg") {
						
						//DO NOTHING!
						
					} else {
					
						if ($s2 == 0 || !$addedAt) {
							echo " at ";
							$addedAt = true;
						}
						
						echo $repeatArray[$s][1][$s2];					
						
						if ( $s2+2 <= count($repeatArray[$s][1])-1 ) {
							echo ", ";
						} else if ( $s2+1 <= count($repeatArray[$s][1])-1 ) {
							echo " and ";
						}
					}
						

				
				}
				
				echo "<br />";
			
			}
			
			
		} //end of date craziness
		
	echo "</span>";
		
			//match searchs for links (case-insensitive)
			
			
			//all searchs for Nadine McGuire Pavilion
			$findNadine[0]   = "g15";
			$findNadine[1]   = "g14";
			$findNadine[2]   = "g13";
			$findNadine[3]   = "g12";
			$findNadine[4]   = "g11";
			$findNadine[5]   = "g10";

			$findNadine[6]   = "g-15";
			$findNadine[7]   = "g-14";
			$findNadine[8]   = "g-13";
			$findNadine[9]   = "g-12";
			$findNadine[10]  = "g-11";
			$findNadine[11]  = "g-10";
			$findNadine[12]  = "nadine";
			$findNadine[13]  = "constans";
			$findNadine[14]  = "ufsotd";
			$findNadine[15]  = "university of florida school of theatre and dance";
			
			//all search for phillips center
			$findPhillips[0]   = "phillips";
			$findPhillips[1]   = "cpa";
			$findPhillips[2]   = "squitieri";
			
			$foundNadine = false;
			for ($z=0; $z<=count($findNadine)-1; $z++) {
				$pos = stripos($where2, $findNadine[$z]);
				if ($pos !== false) {
					$foundNadine = true;
				}
			}
			
			$foundPhillips = false;
			for ($z=0; $z<=count($findPhillips)-1; $z++) {
				$pos = stripos($where2, $findPhillips[$z]);
				if ($pos !== false) {
					$foundPhillips = true;
				}
			}
			
			if ($where2 == "") {
				//leave blank b/c it is blank
			} else if ($foundNadine) {
				//nadine gets priority over phillips
				//found nadine or constans in string so set link to nadine
				echo ( '<a class="location" href="http://newfp.local/map/nadine">' );
				echo ($where2);
				echo ("</a>");
				
			} else if ($foundPhillips) {
			
				//found phillips in string so set link to phillips
				echo ( '<a class="location" href="http://newfp.local/map/squitieri">' );
				echo ($where2);
				echo ("</a>");
				
			} else {
			
				//found nothing in string so set to general google map
				//echo ( '<a class="location" href="http://www.bing.com/maps/?q=' . $where2 . '">' );
				echo ($where2);
				//echo ("</a>");
				
			}
		echo '<p class="eventDesc">'.$content2.'</p>';
		echo "</li>";
	
	}
	}
}
?>

