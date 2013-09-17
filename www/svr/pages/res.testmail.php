<?php
require_once('./svr/res/swiftmailer/lib/swift_required.php');

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
		  ->setTo(array('marcus.ball@live.com' => 'FP Webmaster'))
		  ->setBody($html,'text/html')
		  ->addPart($txt, 'text/plain')
		  ;
		$transport = Swift_MailTransport::newInstance();
		$mailer = Swift_Mailer::newInstance($transport);
		$numSent = $mailer->send($message);
	}
	catch(PDOException $e){
		logError($_SERVER['SCRIPT_NAME'],__LINE__,"Could not fetch event information for confirmation email.",$e->getMessage(),time());
		return false;
	}
}

sendConfirmationMail('marcus.ball@live.com','Marcus Ball',1,22);

/*
//putenv('TMPDIR=./svr/cache');

$message = Swift_Message::newInstance()

  // Give the message a subject
  ->setSubject('Ticket confirmation')

  // Set the From address with an associative array
  ->setFrom(array('do-not-reply@floridaplayers.org' => 'Florida Players'))

  // Set the To addresses with an associative array
  ->setTo(array('marcus.ball@live.com' => 'FP Webmaster'))

  // Give it a body
  ->setBody('Here is the message itself')

  // And optionally an alternative body
  ->addPart('<q>Here is the message itself</q>', 'text/html')
  ;
  
  // Create the Transport
$transport = Swift_MailTransport::newInstance();

// Create the Mailer using your created Transport
$mailer = Swift_Mailer::newInstance($transport);

$numSent = $mailer->send($message);

printf("Sent %d messages\n", $numSent); */
?>