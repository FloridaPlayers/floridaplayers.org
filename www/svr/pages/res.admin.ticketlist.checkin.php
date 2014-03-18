<?php
try{
	$sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
}
catch(PDOException $e){
	logError("res.admin.ticketlist.checkin.php",__LINE__,"Error connecting to database!",$e->getMessage(),time(),false);
	return false;
}
	

if(!isset($_GET['reservation_id']) || !isset($_GET['checked_in'])){
	echo json_encode(array('status'=>'fail','message'=>'Invalid request'));
	return;
}
	
$reservationId = substr($_GET['reservation_id'],0,10);
if($reservationId == null || $reservationId == "" || preg_match("/[^\d{1,10}]/",$reservationId)){
	echo json_encode(array('status'=>'fail','message'=>'Invalid request'));
	return;
}

$checkedInStatus = $_GET['checked_in'];
if($checkedInStatus !== 'true' && $checkedInStatus !== 'false'){
	echo json_encode(array('status'=>'fail','message'=>'Invalid request'));
	return;
}

if($checkedInStatus === 'true'){
	$checkedInStatus = 1;
}
else{
	$checkedInStatus = 0;
}

try{
	$infoStmt = $sql->prepare('UPDATE reservations SET checked_in=:checked WHERE reservation_id=:rid');
	$infoStmt->bindParam(':checked',$checkedInStatus,PDO::PARAM_INT);
	$infoStmt->bindParam(':rid',$reservationId,PDO::PARAM_INT);
	$infoStmt->execute();
	echo json_encode(array('status'=>'success','message'=>'Woot! ' . "$checkedInStatus $reservationId"));
}
catch(PDOException $e){
	logError("res.admin.ticketlist.download.php",__LINE__,"Error retrieving available events!",$e->getMessage(),time(),false);
	echo json_encode(array('status'=>'fail','message'=>'Something went wrong!'));
	return;
}



?>