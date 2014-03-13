<?php
try{
	$sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
}
catch(PDOException $e){
	logError("res.admin.ticketlist.download.php",__LINE__,"Error connecting to database!",$e->getMessage(),time(),false);
	return false;
}
	
if(!isset($_GET['event'])){
	echo "no input";
	return false;
}
$requestedShow = substr($_GET['event'],0,10);
if($requestedShow == null || $requestedShow == "" || preg_match("/[^\d{1,10}]/",$requestedShow)){
	echo "Invalid request";
	return false;
}

try{
	$infoStmt = $sql->prepare('SELECT e.event_id, s.show_name, e.event_date FROM events AS e INNER JOIN shows AS s ON e.show_id=s.show_id WHERE e.event_id=:eid');
	$infoStmt->execute(array(':eid'=>$requestedShow));
	
}
catch(PDOException $e){
	logError("res.admin.ticketlist.download.php",__LINE__,"Error retrieving available events!",$e->getMessage(),time(),false);
	return false;
}

if($infoStmt->rowCount() === 1){
	$eventData = $infoStmt->fetch();
}

try{
	$reservationsStmt = $sql->prepare('SELECT r.reservation_id, CASE WHEN r.email_address IS NULL THEN u.email ELSE r.email_address END AS email, CASE WHEN r.first_name IS NULL THEN u.first_name ELSE r.first_name END AS fname, CASE WHEN r.last_name IS NULL THEN u.last_name ELSE r.last_name END AS lname, sum(r.ticket_quantity) AS quantity, sum(r.ticket_quantity * r.vip) AS vip, r.checked_in FROM reservations AS r LEFT JOIN users AS u ON r.user_id=u.uid WHERE r.event_id=:eid GROUP BY r.email_address,u.email ORDER BY lname,fname');
	$reservationsStmt->execute(array(':eid'=>$requestedShow));
}
catch(PDOException $e){
	logError("res.admin.ticketlist.download.php",__LINE__,"Error retrieving reservations list!",$e->getMessage(),time(),false);
	return false;
}



if($reservationsStmt->rowCount() > 0){
	header("Expires: 0");
	header("Cache-control: private");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Description: File Transfer");
	header("Content-Type: application/vnd.ms-excel"); 
	
	$echoThis = str_replace(" ","_",$eventData['show_name']) . "_" . date('D\_j\-M\-Y\_g\-ia',strtotime($eventData['event_date']));

	header('Content-disposition: attachment; filename="' .$echoThis. '.csv"');
	unset($echoThis);
	header("Pragma: no-cache"); 
	header("Expires: 0"); 

	$cr = "\n";
	$data = "First Name" . ',' . "Last Name" . ',' . "Ticket Amount" . ',VIP,' . "Email Address" . $cr;

	while ($row = $reservationsStmt->fetch()) {
		$data .= ucwords($row['fname']) . ',' . ucwords($row['lname']) . ',' . $row['quantity'] . ',' . (($row['vip'] > 0)?$row['vip']:'') . ',' . $row['email'] . $cr;
	}
	unset($sql,$row,$result,$usertable);
	echo $data;
	unset($data,$id);
} 

?>