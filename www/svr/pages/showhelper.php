<?php
function get_current_season(){
	$return = array();
	
	$month = date('n');
	if($month > 6) $current_season = 'Fall';
	else $current_season = 'Spring';
	$year = date('Y');
	
	$return["current"] = array("term" => $current_season, "year" => $year);
	
	/* upcoming season */
	if($current_season == 'Fall'){
		$return['next']['term'] = 'Spring';
		$return['next']['year'] = $year + 1;
	}
	elseif($current_season == 'Spring'){
		$return['next']['term'] = 'Fall';
		$return['next']['year'] = $year;
	}
	
	/* previous season */
	if($current_season == 'Fall') $current_season = 'Spring';
	else{
		$current_season = 'Fall';
		$year = $year - 1;
	}
	$return["previous"] = array("term" => $current_season, "year" => $year);
	
	
	
	return $return;
}
?>