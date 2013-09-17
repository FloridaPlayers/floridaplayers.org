<?php

class Page{
	var $request = null;
	var $usr;
	var $sql = null;
	
	var $eventData = null;
	var $reservationStmt = null;
	function Page($request){
		$this->request = $request;
		$this->usr = $GLOBALS['USER']; //Get the user from router.php
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Ticket Lists";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<style type="text/css">
			tr:nth-child(odd) { background:#fff; }
			tr:nth-child(even) { background:#eee; }
			table{
				border: 0 solid #000;
			}
			tr:hover{
				background: #d5dbf8;
			}
		</style>
			<!-- CUSTOM CSS/LINK HERE -->
	<?php
	}


	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		try{
			$this->sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		}
		catch(PDOException $e){
			logError("page.admin.tickets.list.php",__LINE__,"Error connecting to database!",$e->getMessage(),time(),false);
			return false;
		}
			
		if(!$this->has_input_at(0)){
			echo "no input";
			//header("Location: /home");
			return false;
		}
		$requestedShow = substr(strtolower($this->get_input_at(0)),0,10);
		if($requestedShow == null || $requestedShow == "" || preg_match("/[^\d{1,10}]/",$requestedShow)){
			header("Location: /shows");
			return false;
		}
		
		try{
			$infoStmt = $this->sql->prepare('SELECT e.event_id, s.show_name, e.event_date FROM events AS e INNER JOIN shows AS s ON e.show_id=s.show_id WHERE e.event_id=:eid');
			$infoStmt->execute(array(':eid'=>$requestedShow));
			
		}
		catch(PDOException $e){
			logError("page.admin.tickets.list.php",__LINE__,"Error retrieving available events!",$e->getMessage(),time(),false);
			return false;
		}
		
		if($infoStmt->rowCount() === 1){
			$this->eventData = $infoStmt->fetch();
		}
		
		try{
			$reservationsStmt = $this->sql->prepare('SELECT r.reservation_id, CASE WHEN r.email_address IS NULL THEN u.email ELSE r.email_address END AS email, CASE WHEN r.first_name IS NULL THEN u.first_name ELSE r.first_name END AS fname, CASE WHEN r.last_name IS NULL THEN u.last_name ELSE r.last_name END AS lname, sum(r.ticket_quantity) AS quantity FROM reservations AS r LEFT JOIN users AS u ON r.user_id=u.uid WHERE r.event_id=:eid GROUP BY r.email_address,u.email ORDER BY lname,fname');
			$reservationsStmt->execute(array(':eid'=>$requestedShow));
			$this->reservationStmt = $reservationsStmt;
		}
		catch(PDOException $e){
			logError("page.admin.tickets.list.php",__LINE__,"Error retrieving reservations list!",$e->getMessage(),time(),false);
			return false;
		}

		return true; 
	}

	/**
	 * If desired, you may override the template being used
	 * return a string containing the directory path, or false for no override.
	 **/
	function getOverrideTemplate(){
		return false;
		//return SERVER_RES_DIR.'template.html';
	}


	//Return nothing; print out the page. 
	function getContent(){
		?>
		<h1><?php echo $this->eventData['show_name']; ?></h1>
		<article>
			<section>
				<?php
				$dateString = date('l, j M Y \a\t g:i a',strtotime($this->eventData['event_date']));
				?>
				<div style="text-align:center"><strong><?php echo "Ticket RSVP for ".$this->eventData['show_name']." on ".$dateString; ?></strong><br /><a href="/admin/ticketlist/download?event=<?php echo $this->eventData['event_id']; ?>">Download spreadsheet</a></div>
				<table width="100%">
				<?php while ($row = $this->reservationStmt->fetch()) { ?>
					<tr><td><input type="checkbox"></input></td><td><?php echo ucwords($row['fname']." ".$row['lname']); ?></td><td><?php echo $row['quantity']; ?></td></tr>
				<?php } ?>
				</table>
			</section>
		</article>
		<?php
	}
	
	function has_flag($pos,$flag){
		return (isset($this->request) && isset($this->request["flags"][$pos]) && $this->request["flags"][$pos] == $flag) === true;
	}
	function has_input_at($pos){
		return (isset($this->request) && isset($this->request["flags"][$pos]));
	}
	function get_input_at($pos){
		if(isset($this->request) && isset($this->request["flags"][$pos])) return $this->request["flags"][$pos];
		else return null;
	}
}
?>