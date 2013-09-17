<?php
require_once "authentication.php";
require_once "showhelper.php";

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
function sanitize($value,$strip_tags = false){
	$value = trim($value);
	if(get_magic_quotes_gpc()){
		$value = stripslashes($value);
	}
	if(!is_numeric($value)){ // only need to do this part for strings
		$text = @mysql_real_escape_string($value);
		if($text === FALSE){  // we must not be connected to mysql, so....
			$text = mysql_escape_string($value);
		}
		if($strip_tags){
			$text = strip_tags($text);
		}
		$value = "'$text'";
	}
   return($value);
} 


$usr = $GLOBALS['USER']; //Get the user from router.php
$usr->get_info();
if($usr->get_user_info("permissions") < 1){
	header("Location: /home");
	die();
}

$sql = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
if(!$sql){
	echo "Error connecting to database!<br />" . mysql_error();
	die();
}
mysql_select_db(DB_NAME, $sql);

if(isset($_GET['get_show_list'])){
	$result = mysql_query("SELECT show_id, show_name FROM shows ORDER BY show_id DESC;");
	if(!$result){
		echo "Error! " . mysql_error();
		die();
	}
	while($row = mysql_fetch_array($result)){?>
		<div class="show_list_item" id="show_<?php echo $row['show_id']; ?>">
			<?php echo $row['show_name'] . "\n"; ?>
		</div><?php
	}
}
elseif(isset($_GET['get_show_info'])){
	if(!isset($_GET['show_id'])){
		die("No show ID provided!");
	}
	$show_id = $_GET['show_id'];
	if(preg_match("/[^0-9]/",$show_id)){
		die("Invalid show ID!");
	}
	$result = mysql_query("SELECT * FROM shows WHERE show_id='$show_id' LIMIT 1;");
	if(!$result){
		echo "Error! " . mysql_error();
		die();
	}
	$row = mysql_fetch_array($result);
	?>
	
	<form name="edit_show_form" action="" onsubmit="return false;">
		<input type="hidden" id="show_id" name="show_id" value="<?php echo $row['show_id']; ?>" />
		<fieldset>
			<legend>Show Details</legend>
			<?php 
			text_input("Show name","show_name",true,$row['show_name']);
			text_input("Abbreviation","show_abbr",true,$row['show_abbr'],10);
			text_input("Show term","show_term",true,$row['show_term'],6);
			text_input("Show year","show_year",true,$row['show_year'],4);
			?>
			
			<div id="input_container_show_location" class="controlset" >
				<label class="controlset">Location <em>*</em></label>
				<div class="radio_container"><input name="input_show_location" id="input_show_location_squitieri" value="1" type="radio" <?php if($row['location'] == 1) echo "checked=\"checked\""; ?> /> <label for="input_show_location_squitieri">Squitieri Studio Theatre</label></div>
				<div class="radio_container"><input name="input_show_location" id="input_show_location_nadine" value="2" type="radio" <?php if($row['location'] == 2) echo "checked=\"checked\""; ?> /> <label for="input_show_location_nadine">Nadine McGuire Black Box</label></div>
			</div>	
		</fieldset>
		
		<fieldset>
			<legend>Descriptions</legend>
			<div>
				<label for="desc">Byline</label>
				<textarea id="input_show_byline" name="input_show_byline" cols="40" rows="3" ><?php echo $row['byline']; ?></textarea>
			</div>
			<div>
				<label for="info">Director's comment</label>
				<textarea class="rich" id="input_show_director" name="input_show_director" cols="50" rows="5"><?php echo $row['director']; ?></textarea>
			</div>
			<div>
				<label for="info">Synopsis</label>
				<textarea class="rich" id="input_show_synopsis" name="input_show_synopsis" cols="50" rows="5"><?php echo $row['synopsis']; ?></textarea>
			</div>
		</fieldset>
		<div id="submit_options">
			<div id="save_div">
				<input type="submit" id="save_edit_button" value="Save changes"></input>
			</div>
			<div id="cancel_div">
				<input type="submit" id="cancel_edit_button" value="Cancel"></input>
			</div>
		</div>
	</form>
	<?php
}
elseif(isset($_GET['get_show_template'])){
	?>
	
	<form name="edit_show_form" action="" onsubmit="return false;">
		<input type="hidden" id="show_id" name="show_id" value="<?php echo $row['show_id']; ?>" />
		<fieldset>
			<legend>Show Details</legend>
			<?php 
			text_input("Show name","show_name",true,"");
			text_input("Abbreviation","show_abbr",true,"",10);
			text_input("Show term","show_term",true,"",6);
			text_input("Show year","show_year",true,"",4);
			?>
			
			<div id="input_container_show_location" class="controlset" >
				<label class="controlset">Location <em>*</em></label>
				<div class="radio_container"><input name="input_show_location" id="input_show_location_squitieri" value="1" type="radio" /> <label for="input_show_location_squitieri">Squitieri Studio Theatre</label></div>
				<div class="radio_container"><input name="input_show_location" id="input_show_location_nadine" value="2" type="radio" /> <label for="input_show_location_nadine">Nadine McGuire Black Box</label></div>
			</div>	
		</fieldset>
		
		<fieldset>
			<legend>Descriptions</legend>
			<div>
				<label for="desc">Byline</label>
				<textarea id="input_show_byline" name="input_show_byline" cols="40" rows="3" ></textarea>
			</div>
			<div>
				<label for="info">Director's comment</label>
				<textarea class="rich" id="input_show_director" name="input_show_director" cols="50" rows="5"></textarea>
			</div>
			<div>
				<label for="info">Synopsis</label>
				<textarea class="rich" id="input_show_synopsis" name="input_show_synopsis" cols="50" rows="5"></textarea>
			</div>
		</fieldset>
		<div id="submit_options">
			<div id="save_div">
				<input type="submit" id="save_new_button" value="Save"></input>
			</div>
			<div id="cancel_div">
				<input type="submit" id="cancel_new_button" value="Cancel"></input>
			</div>
		</div>
	</form>
	<?php
}
elseif(isset($_GET['submit_show_edit'])){
	$errors = array();
	$error = false;
	if(!isset($_POST['show_id'])){ 
		array_push($errors,"There was an error with the form! Try refreshing the page.");
		$error = true;
	}
	else $show_id = $_POST['show_id'];
	if(preg_match("/[^0-9]/",$show_id) && $show_id != ""){
		array_push($errors,"Invalid show ID!");
		$error = true;
	}
	
	if(!isset($_POST['show_name']) || ($show_name = $_POST['show_name']) == ""){
		array_push($errors,"Show name cannot be empty!");
		$error = true;
	}
	if(!isset($_POST['show_abbr']) || ($show_abbr = $_POST['show_abbr']) == ""){
		array_push($errors,"Show abbreviation cannot be empty!");
		$error = true;
	}
	if(preg_match("/[^\w_-]/",strtolower($show_abbr))){
		array_push($errors,"Only alphanumeric characters, underscore, and hyphen are allowed for show abbreviation!");
		$error = true;
	}
	
	if(!isset($_POST['show_term']) || ($show_term = $_POST['show_term']) == ""){
		array_push($errors,"Show term cannot be empty!");
		$error = true;
	}
	$show_term = strtolower($show_term);
	if($show_term == "spring") $show_term = "Spring";
	elseif($show_term == "fall") $show_term = "Fall";
	elseif($show_term == "summer") $show_term = "Summer";
	else{
		array_push($errors,"Show term must be a valid semester period (fall,spring,...)!");
		$error = true;
	}
	
	if(!isset($_POST['show_year']) || ($show_year = $_POST['show_year']) == ""){
		array_push($errors,"Show year cannot be empty!");
		$error = true;
	}
	if(!preg_match("/\d{4}/",$show_year)){ // HACK: Fix for year 10,000+ 
		array_push($errors,"Show year must be a valid four digit number!");
		$error = true;
	}
	
	if(!isset($_POST['location']) || ($location = $_POST['location']) == ""){
		array_push($errors,"Location cannot be empty!");
		$error = true;
	}
	if(!preg_match("/\d{1,}/",$location)){
		array_push($errors,"Location must be a number!");
		$error = true;
	}
	
	if(isset($_POST['byline'])) $byline = $_POST['byline'];
	else $byline = ""; 
	if(isset($_POST['director'])) $director = $_POST['director'];
	else $director = "";
	if(isset($_POST['synopsis'])) $synopsis = $_POST['synopsis'];
	else $synopsis = "";
	
	$show_id = sanitize($show_id);
	$show_name = sanitize($show_name, true);
	$show_abbr = sanitize($show_abbr, true);
	$show_year = sanitize($show_year);
	$show_term = sanitize($show_term);
	$location = sanitize($location);
	$byline = sanitize($byline);
	$director = sanitize($director);
	$synopsis = sanitize($synopsis);
	
	$return = array();
	if(!$error){
		$querysql = "UPDATE shows SET show_name=$show_name, show_abbr=$show_abbr, show_term=$show_term, show_year=$show_year, location=$location, byline=$byline, director=$director, synopsis=$synopsis WHERE show_id=$show_id;";
		$query_response = mysql_query($querysql,$sql);
		if(!$query_response){
			$return["status"] = "error";
			$return["errors"] = array(mysql_error());
		}
		else{
			$return["status"] = "success";
		}
	}
	else{
		$return["status"] = "error";
		$return["errors"] = $errors;
	}
	echo json_encode($return);
}
elseif(isset($_GET['submit_new_show'])){
	$errors = array();
	$error = false;
	
	if(!isset($_POST['show_name']) || ($show_name = $_POST['show_name']) == ""){
		array_push($errors,"Show name cannot be empty!");
		$error = true;
	}
	if(!isset($_POST['show_abbr']) || ($show_abbr = $_POST['show_abbr']) == ""){
		array_push($errors,"Show abbreviation cannot be empty!");
		$error = true;
	}
	if(preg_match("/[^\w_-]/",strtolower($show_abbr))){
		array_push($errors,"Only alphanumeric characters, underscore, and hyphen are allowed for show abbreviation!");
		$error = true;
	}
	
	if(!isset($_POST['show_term']) || ($show_term = $_POST['show_term']) == ""){
		array_push($errors,"Show term cannot be empty!");
		$error = true;
	}
	$show_term = strtolower($show_term);
	if($show_term == "spring") $show_term = "Spring";
	elseif($show_term == "fall") $show_term = "Fall";
	elseif($show_term == "summer") $show_term = "Summer";
	else{
		array_push($errors,"Show term must be a valid semester period (fall,spring,...)!");
		$error = true;
	}
	
	if(!isset($_POST['show_year']) || ($show_year = $_POST['show_year']) == ""){
		array_push($errors,"Show year cannot be empty!");
		$error = true;
	}
	if(!preg_match("/\d{4}/",$show_year)){ // HACK: Fix for year 10,000+ 
		array_push($errors,"Show year must be a valid four digit number!");
		$error = true;
	}
	
	if(!isset($_POST['location']) || ($location = $_POST['location']) == ""){
		array_push($errors,"Location cannot be empty!");
		$error = true;
	}
	if(!preg_match("/\d{1,}/",$location)){
		array_push($errors,"Location must be a number!");
		$error = true;
	}
	
	if(isset($_POST['byline'])) $byline = $_POST['byline'];
	else $byline = ""; 
	if(isset($_POST['director'])) $director = $_POST['director'];
	else $director = "";
	if(isset($_POST['synopsis'])) $synopsis = $_POST['synopsis'];
	else $synopsis = "";
	
	//$show_id = sanitize($show_id); //I have no idea why this was here.
	$show_name = sanitize($show_name, true);
	$show_abbr = sanitize($show_abbr, true);
	$show_year = sanitize($show_year);
	$show_term = sanitize($show_term);
	$location = sanitize($location);
	$byline = sanitize($byline);
	$director = sanitize($director);
	$synopsis = sanitize($synopsis);
	
	$checksql = "SELECT show_id FROM shows WHERE show_abbr=$show_abbr LIMIT 1;";
	$check_response = mysql_query($checksql,$sql);
	if(mysql_num_rows($check_response) > 0){
		array_push($errors,"The show abbreviation $show_abbr has already been used.");
		$error = true;
	}
	
	
	$return = array();
	if(!$error){
		$querysql = "INSERT INTO shows (show_name, show_abbr, show_term, show_year, location, byline, director, synopsis) VALUES ($show_name, $show_abbr, $show_term, $show_year, $location, $byline, $director, $synopsis);";
		$query_response = mysql_query($querysql,$sql);
		if(!$query_response){
			$return["status"] = "error";
			$return["errors"] = array(mysql_error());
			logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not insert new show!",mysql_error(),time(),false);
		}
		else{
			$idquery = "SELECT show_id FROM shows WHERE show_abbr=$show_abbr LIMIT 1;";
			$id_response = mysql_query($idquery,$sql);
			$id = -1;
			if(!$id_response){
				logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not retrieve show_id after creating new show!",mysql_error(),time(),false);
			}
			else{ 
				$row = mysql_fetch_assoc($id_response);
				$id = $row['show_id'];
			}
			$return["status"] = "success";
			$return["id"] = $id; 
		}
	}
	else{
		$return["status"] = "error";
		$return["errors"] = $errors;
	}
	echo json_encode($return);
}
elseif(isset($_GET['get_event_list'])){
	$event_list_query = "SELECT events.event_id, events.show_id, events.event_date, events.active, events.event_capacity, events.ticket_close, shows.show_name, (SELECT COALESCE(SUM(reservations.ticket_quantity),0) FROM reservations WHERE reservations.event_id=events.event_id) AS tickets_sold FROM events INNER JOIN shows ON events.show_id=shows.show_id WHERE events.archived='0' ORDER BY events.event_date";
	$list_result = mysql_query($event_list_query,$sql);
	if(!$list_result){
		echo "Error! " . mysql_error();
		die();
	}
	while($row = mysql_fetch_array($list_result)){
		$phpdate = strtotime( $row['event_date'] );
		$event_date = date( 'l, j F Y, \a\t g:i A', $phpdate );

		?>
		<div class="show_list_item" id="event_<?php echo $row['event_id']; ?>">
			<div class="event_data">
				<h4><?php echo $row['show_name']; ?></h4>
				<span class="show_date"><?php echo $event_date; ?></span>
			</div>
			<div class="event_controls">
				<span><?php echo $row['tickets_sold']."/".$row['event_capacity']; ?></span>
				<input type="button" value="Export" />
				<input type="button" value="View" />
				<input type="button" value="Edit" />
			</div>
			<div class="clear"></div>
		</div><?php
	}
}
elseif(isset($_GET['get_event_template'])){
	$season_info = get_current_season();
	
	$show_query = "SELECT show_id,show_name FROM shows WHERE show_term='{$season_info['current']['term']}' AND show_year='{$season_info['current']['year']}'";
	$show_response = mysql_query($show_query,$sql);
	if(!$show_response){
		die("Error fetching show list!");
	}
	if(mysql_num_rows($show_response) == 0){
		$show_query = "SELECT show_id,show_name FROM shows WHERE show_term='{$season_info['previous']['term']}' AND show_year='{$season_info['previous']['year']}';";
		$show_response = mysql_query($show_query,$sql);
		if(!$show_response){
			die("Error fetching show list!");
		}
		if(mysql_num_rows($show_response) == 0){
			die("Cannot find a current season!");
		}
	}
	?>
	<fieldset>
		<legend>New event information</legend>
		<div id="input_container_event_name">
			<label for="input_event_name">Show name</label>
			<select id="input_event_name" name="input_event_name" class="styled_select"><?php
			while($row = mysql_fetch_assoc($show_response)){
				echo "<option value=\"{$row['show_id']}\">{$row['show_name']}</option>";
			}
			?>
			</select>
		</div>
		<div id="input_container_event_date">
			<label for="input_event_date">Event date</label>
			<input type="text" id="input_event_date" name="input_event_date" class="input_datetime"></input>
		</div>
		<div id="input_container_event_close">
			<label for="input_close_date">Ticket close date</label>
			<input type="text" id="input_close_date" name="input_close_date" class="input_datetime"></input>
			<p class="note">This is the time and date when reservations will be disabled.</p>
		</div>
		<div>
			<label class="controlset">Active</label>
			<div class="checkbox_container controlset">
				<input type="checkbox" value="active" id="input_event_active" class="styled_checkbox" name="input_event_active">
				<label for="input_event_active"></label>
			</div>
			<p class="note">Reservations are open when active</p>
		</div>
		<div id="input_container_seat_quantity">
			<label for="input_seat_quanity">Available seats</label>
			<input type="text" id="input_seat_quantity" name="input_seat_quantity" size="3" value="1" ></input>
			<div id="seat_quanitity_slider"></div>
		</div>
	</fieldset>
	<input type="submit" value="Save new event" class="save_button" id="save_new_event_button" />
<?php
}
elseif(isset($_GET['submit_new_event'])){
	$errors = array();
	$error = false;
	
	$id_error = false;
	if(!isset($_POST['show_id'])){ 
		array_push($errors,"You must select a valid show!");
		$error = true;
		$id_error = true;
	}
	else $show_id = $_POST['show_id'];
	if(preg_match("/[^0-9]/",$show_id) && $show_id != ""){
		array_push($errors,"Invalid show ID!");
		$error = true;
		$id_error = true;
	}
	if(!$id_error){
		$showidquery = "SELECT show_name FROM shows WHERE show_id='{$show_id}'";
		$showidresponse = mysql_query($showidquery,$sql);
		if(mysql_num_rows($showidresponse) == 0){
			array_push($errors,"No shows were found matching the selected show ID!");
			$error = true;
		}
	}
	
	if(!isset($_POST['event_date']) || ($event_date = $_POST['event_date']) == ""){
		array_push($errors,"Event date cannot be empty!");
		$error = true;
	}
	if(!preg_match("/\d{1,}/",$event_date)){
		array_push($errors,"Invalid event date specified!");
		$error = true;
	}
	
	$event_date = date("Y-m-d G:i:s",$event_date);
	if(!isset($_POST['event_close']) || ($event_close = $_POST['event_close']) == ""){
		$event_close = $event_date;
	}
	else{
		if(!preg_match("/\d{1,}/",$event_close)){
			array_push($errors,"Invalid event close date specified!");
			$error = true;
		}
		$event_close = date("Y-m-d G:i:s",$event_close);
	}
	if(!isset($_POST['event_active']) || ($event_active = $_POST['event_active']) == ""){
		$event_active = 0; 
	}
	else{
		if($event_active == "true") $event_active = 1;
		elseif($event_active == "false") $event_active = 0;
		elseif($event_active == "1") $event_active = 1;
		elseif($event_active == "0") $event_active = 0;
	}
	
	if(!isset($_POST['event_capacity']) || ($event_capacity = $_POST['event_capacity']) == ""){
		array_push($errors,"Event capacity must be specified!");
		$error = true;
	}
	if(!preg_match("/\d{1,3}/",$event_capacity)){
		array_push($errors,"Invalid event capacity specified!");
		$error = true;
	}
	
	$return = array();
	if(!$error){
		$show_id = sanitize($show_id);
		//Dates and event_active shouldn't need to be sanitized 
		//as they are set in this script.
		$event_capacity = sanitize($event_capacity);
	
		$submitquery = "INSERT INTO events (show_id, event_date, ticket_close, active, event_capacity) VALUES ($show_id,'$event_date','$event_close','$event_active','$event_capacity');";
		$submitresponse = mysql_query($submitquery,$sql);
		if(!$submitresponse){
			$return["status"] = "error";
			$return["errors"] = array(mysql_error());
			logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not insert new event!",mysql_error(),time(),false);
		}
		else{
			$return["status"] = "success"; 
		}
	}
	else{
		$return["status"] = "error";
		$return["errors"] = $errors;
	}
	echo json_encode($return);
}
mysql_close($sql);
?>