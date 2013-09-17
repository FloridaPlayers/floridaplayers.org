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
		$value = strip_tags($value);
	}
   return($value);
} 

function sendConfirmationMail($email,$name){
		
	$txt = file_get_contents(MESSAGE_MEMBERSHIP_CONFIRM_TEXT); //Get the plain-text template
	$html = file_get_contents(MESSAGE_MEMBERSHIP_CONFIRM_HTML); //Get the plain-text template
	
	list($txt,$html) = preg_replace('/^\s*\#.*?$/','',array($txt,$html)); //Remove comment lines
	
	
	list($txt,$html) = str_replace(
		array('{Name}'),
		array($name),
		array($txt,$html));
		
	
	
	$message = Swift_Message::newInstance()
	  ->setSubject('Membership confirmation')
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
	return true;
}

$errors = array();
$error = false;
$success = false;

try {
	$sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
	logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not select database (".DB_NAME.").",$e->getMessage(),time(),false);
	array_push($errors,"Error connecting to database!");
	$error = true;
}


$remaing_capacity = 0;
$valid_event = true;


$user_email = "";
$major = "";
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

if(!isset($_POST['input_major']) || ($major = cut($_POST['input_major'],100)) == ""){
	array_push($errors,"Please enter your major area of study.");
	$error = true;
}

$year_level = -1;
if(!isset($_POST['input_year_level'])){ 
	array_push($errors,"You must select a year in school!");
	$error = true;
}
else $year_level = cut($_POST['input_year_level'],1);
if(preg_match("/[^0-5]/",$year_level) && $year_level != ""){
	array_push($errors,"Invalid year in school!");
	$error = true;
}

if(isset($_POST['input_phone_number'])){
	$phone_number = cut($_POST['input_phone_number'],15);
	if(preg_match("/[^0-9\-\.\(\)\s]/",$phone_number)){
		array_push($errors,"Invalid phone number!");
		$error = true;
	}
}
else{
	$phone_number = null;
}

$year_level = sanitize($year_level);
if(isset($first_name)) $first_name = strtolower(sanitize($first_name,true));
if(isset($last_name)) $last_name = strtolower(sanitize($last_name,true));
$user_email = strtolower(sanitize($user_email,true));
if(isset($phone_number)) $phone_number = sanitize($phone_number);
$major = sanitize($major);

if(!$error){
	/* if($use_account) $reservation_insert_query = "INSERT INTO reservations (event_id, user_id, ticket_quantity) VALUES ($event_id,$account_id,'$ticket_quantity');";
	else $reservation_insert_query = "INSERT INTO reservations (event_id, first_name, last_name, email_address, ticket_quantity) VALUES ($event_id, $first_name, $last_name, $user_email, $ticket_quantity);";
	*/
	
	$reservationInsert = null;
	try{
		$reservationInsert = $sql->prepare('INSERT INTO members (first_name, last_name, email_address, phone_number, major, year, date_added) VALUES (:fname, :lname, :email, :phone, :major, :year, :now);');
		$reservationInsert->execute(array(':fname'=>$first_name,':lname'=>$last_name,':email'=>$user_email,':phone'=>$phone_number,':major'=>$major,':year'=>$year_level,':now'=>date("Y-m-d H:i:s")));
	}
	catch(PDOException $e){
		logError("membershipprocess.php",__LINE__,"Error saving membership!",$e->getMessage(),time(),false);
		array_push($errors,'There was an error saving your membership request!');
		$error = true;
	}
	if($reservationInsert != null){
		$mail = sendConfirmationMail($user_email,ucwords($first_name . ' ' . $last_name));
		if(!$mail){
			array_push($errors,"Your membership request was successful, but we were unable to send the confirmation email!");
			$error = true;
		}
		$success = true;
	}
}


?>