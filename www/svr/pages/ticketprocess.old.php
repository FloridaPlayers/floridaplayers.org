<?php
require_once "emailvalidate.php";
require_once('./svr/res/swiftmailer/lib/swift_required.php');

function cut($str,$len){
	return substr($str,0,$len);
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

function sendConfirmationMail($email,$name,$quantity,$event_id){
	try {
		$sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e){
		logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not select database (".DB_NAME.").",$e->getMessage(),time());
		return false;
	}
	
	try{
		$edStatement = $sql->prepare('SELECT e.event_date, s.show_name AS name, s.location, s.show_abbr AS url FROM events AS e INNER JOIN shows AS s ON e.show_id=s.show_id WHERE e.event_id=:eid LIMIT 1');
		$edStatement->execute(array(':eid'=>$event_id));
		
		if($edStatement->rowCount() == 0){
			return false; //We were unable to find show information
		}
		
		$showData = $edStatement->fetch();
		
		$txt = file_get_contents(MESSAGE_TICKET_CONFIRM_TEXT); //Get the plain-text template
		$html = file_get_contents(MESSAGE_TICKET_CONFIRM_HTML); //Get the plain-text template
		
		list($txt,$html) = preg_replace('/^\s*\#.*?$/','',array($txt,$html)); //Remove comment lines
		
		$dateString = date('l, F jS \a\t g:i a',strtotime($showData['event_date']));
		$ticketString = ($quantity == 1)?$quantity . ' ticket':$quantity . ' tickets';
		
		global $THEATER_LOCATIONS;
		$locationUrl = SITE_DOMAIN.'/map/'.$THEATER_LOCATIONS[$showData['location']]['short'];
		$locationName = $THEATER_LOCATIONS[$showData['location']]['name'];
		
		$showUrl = SITE_DOMAIN.'/show/'.$showData['url'];
		
		list($txt,$html) = str_replace(
			array('{Name}','{TicketString}','{ShowName}','{ShowDate}','{ShowLocation}','{ShowUrl}','{ShowLocationUrl}'),
			array($name,$ticketString,$showData['name'],$dateString,$locationName,$showUrl,$locationUrl),
			array($txt,$html));
			
		
		
		$message = Swift_Message::newInstance()
		  ->setSubject('Ticket confirmation')
		  ->setFrom(array('do-not-reply@floridaplayers.org' => 'Florida Players'))
		  ->setTo(array($email => $name))
		  ->setBody($html,'text/html')
		  ->addPart($txt, 'text/plain')
		  ;
		$transport = Swift_MailTransport::newInstance();
		$mailer = Swift_Mailer::newInstance($transport);
		$numSent = $mailer->send($message);
		if($numSent == 0){
			return false;
		}
	}
	catch(PDOException $e){
		logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not fetch event information for confirmation email.",$e->getMessage(),time());
		return false;
	}
	return true;
}



$errors = array();
$error = false;
$success = false;

$sql = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
if(!$sql){
	array_push($errors,"Error connecting to database!");
	$error = true;
}
else{
	mysql_select_db(DB_NAME, $sql);

	$remaing_capacity = 0;
	$valid_event = true;

	$id_error = false;
	if(!isset($_POST['input_selected_event'])){ 
		array_push($errors,"You must select a valid event!");
		$error = true;
		$id_error = true;
		$valid_event = false;
	}
	else $event_id = cut($_POST['input_selected_event'],11);
	if(preg_match("/[^0-9]/",$event_id) && $event_id != ""){
		array_push($errors,"Invalid event ID!");
		$error = true;
		$id_error = true;
		$valid_event = false;
	}
	if(!$id_error){
		$eventidquery = "SELECT events.event_date, events.event_capacity, events.ticket_close, (SELECT COALESCE(SUM(reservations.ticket_quantity),0) FROM reservations WHERE reservations.event_id=events.event_id) AS tickets_sold FROM events WHERE events.active='1' AND events.archived='0' AND events.event_id='{$event_id}' LIMIT 1;";
		$eventidresponse = mysql_query($eventidquery,$sql);
		if(mysql_num_rows($eventidresponse) == 0){
			array_push($errors,"No open events were found matching the selected event.");
			$error = true;
			$valid_event = false;
		}
		else{
			$row = mysql_fetch_assoc($eventidresponse);
			$remaining = $row['event_capacity'] - $row['tickets_sold'];
			$is_closed = (strtotime( $row['ticket_close'] ) < time());
			
			$remaining_capacity = $remaining;
			
			if(strtotime( $row['event_date'] ) <= time()){
				array_push($errors,"It's too late to register for that.");
				$error = true;
				$valid_event = false;
			}
			elseif($remaining <= 0){
				array_push($errors,"The selected event is sold out.");
				$error = true;
				$valid_event = false;
			}
			elseif($is_closed){
				array_push($errors,"Reservations for this event have been closed.");
				$error = true;
				$valid_event = false;
			}
		}
	}

	if(!isset($_POST['input_ticket_quantity']) || ($ticket_quantity = cut($_POST['input_ticket_quantity'],2)) == ""){
		array_push($errors,"You must select a ticket quantity!");
		$error = true;
	}
	elseif(!preg_match("/\d{1,2}/",$ticket_quantity) || $ticket_quantity < 1 || $ticket_quantity > MAX_TICKET_RESERVATION){
		array_push($errors,"Invalid ticket quantity specified!");
		$error = true;
	}
	elseif($valid_event && $remaining_capacity < $ticket_quantity){
		array_push($errors,"There are not enough tickets remaining! You requested $ticket_quantity, but there ".(($remaining_capacity == 1)?"is":"are")." only $remaining_capacity left.");
		$error = true;
	}
	$use_account = false;
	$account_id = 0;
	$user_email = null;
	if(isset($_POST['input_preferred_contact']) && $_POST['input_preferred_contact'] == "1"){
		$user = new User();
		if(!$user->is_logged_in()){
			array_push($errors,"You must be logged in to reserve using account information.");
			$error = true;
		}
		else{
			$user->get_info();
			$account_id = $user->get_user_info('uid');
			$user_email = $user->get_user_info('email');
			$first_name = $user->get_user_info('first_name');
			$last_name = $user->get_user_info('last_name');
			$use_account = true;
		}
	}
	else{
		if(!isset($_POST['input_first_name']) || ($first_name = cut($_POST['input_first_name'],50)) == ""){
			array_push($errors,"Please enter your first name.");
			$error = true;
		}
		if(!isset($_POST['input_last_name']) || ($last_name = cut($_POST['input_last_name'],50)) == ""){
			array_push($errors,"Please enter your last name.");
			$error = true;
		}
		if(!isset($_POST['input_email_address']) || ($user_email = cut($_POST['input_email_address'],500)) == ""){
			array_push($errors,"Please enter your email address.");
			$error = true;
		}
		elseif(!validEmail($user_email)){
			array_push($errors,"The email address provided is invalid!");
			$error = true;
		}
	}

	$event_id = sanitize($event_id);
	$ticket_quantity = sanitize($ticket_quantity);
	if($use_account) $account_id = sanitize($account_id);
	if(isset($first_name)) $first_name = strtolower(sanitize($first_name,true));
	if(isset($last_name)) $last_name = strtolower(sanitize($last_name,true));
	$user_email = strtolower(sanitize($user_email,true));


	$reservation_check_query = ""; 
	if($use_account) $reservation_check_query = "SELECT COALESCE(SUM(ticket_quantity),0) AS reserved FROM reservations WHERE (email_address=$user_email OR user_id=$account_id) AND event_id=$event_id";
	else $reservation_check_query = "SELECT COALESCE(SUM(ticket_quantity),0) AS reserved FROM reservations WHERE email_address=$user_email AND event_id=$event_id";
	$reservation_check_response = mysql_query($reservation_check_query,$sql);

	if(!$reservation_check_response){
		array_push($errors,"An unexpected error has occurred!");
		$error = true;
		logError("ticketprocess.php",__LINE__,"Error checking for reservations!",mysql_error(),time(),false);
	}
	else{
		$row = mysql_fetch_assoc($reservation_check_response);
		$quantity = $row['reserved'];
		if(ENFORCE_ONE_RESERVATION){
			if($quantity >= MAX_TICKET_RESERVATION){
				array_push($errors,"You have already reached your maximum number of reservations for this event.");
				$error = true;
			}
			elseif(($quantity + $ticket_quantity) > MAX_TICKET_RESERVATION){
				array_push($errors,"Your request will cause you to exceed the maximum number of reservations for an event!");
				$error = true;
			}
		}
		else{
			if($quantity >= RESERVATION_CEILING){
				array_push($errors,"You have already reached your maximum number of reservations for this event.");
				$error = true;
			}
			elseif(($quantity + $ticket_quantity) > RESERVATION_CEILING){
				array_push($errors,"Your request will cause you to exceed the maximum number of reservations for an event!");
				$error = true;
			}
		}
	}
	if(!$error){
		if($use_account) $reservation_insert_query = "INSERT INTO reservations (event_id, user_id, ticket_quantity) VALUES ($event_id,$account_id,'$ticket_quantity');";
		else $reservation_insert_query = "INSERT INTO reservations (event_id, first_name, last_name, email_address, ticket_quantity) VALUES ($event_id, $first_name, $last_name, $user_email, $ticket_quantity);";
		
		$reservation_insert_response = mysql_query($reservation_insert_query,$sql);
		if(!$reservation_insert_response){
			array_push($errors,"There was an error saving your request! \"".mysql_error()."\"");
			$error = true;
		}
		else{
			$mail = sendConfirmationMail($user_email,$first_name . ' ' . $last_name,$ticket_quantity,$event_id);
			if(!$mail){
				array_push($errors,"Your reservation was successful, but we were unable to send the confirmation email!");
				$error = true;
			}
			$success = true;
		}
	}
}


?>