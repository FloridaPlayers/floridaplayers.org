<?php
require_once "authentication.php";
class Page{
	var $request = null;
	var $sql = null;
	var $usr;
	
	private $activeShows;
	private $showKeys;
	function Page($request){
		$this->request = $request;
		$this->usr = $GLOBALS['USER']; //Get the user from router.php
		$this->usr->get_info();
		if($this->usr->get_user_info("permissions") < 1){
			header("Location: /home");
			die();
		}
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Manage Ticketlists";
	}

	//Page specific content for the <head> section.
	function customHead(){?>

			<!-- CUSTOM CSS/LINK HERE -->
	<?php
	}


	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		try{
			$sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		}
		catch(PDOException $e){
			logError("res.admin.ticketlist.manage.php",__LINE__,"Error connecting to database!",$e->getMessage(),time(),false);
			return false;
		}
		
		try{
			$activeShowQuery = $sql->prepare('SELECT events.event_id, shows.show_name, events.event_date, events.event_capacity FROM events INNER JOIN shows ON events.show_id=shows.show_id WHERE events.active=\'1\' AND events.archived=\'0\' AND events.event_date >= NOW() ORDER BY events.event_date');
			$activeShowQuery->execute();
			$this->activeShows = $activeShowQuery->fetchAll(PDO::FETCH_ASSOC);
			
			$this->showKeys = array();
			$ticketListKeyInsert = $sql->prepare('INSERT INTO ticketlist_access(access_key,event_id) VALUES (:key,:eid)');
			foreach($this->activeShows as $show){
				$key = substr(sha1(time() . $show['show_name'] . $show['event_id'] . 'somebadasssalt or something'),0,16);
				$this->showKeys[$show['event_id']] = $key;
				$ticketListKeyInsert->bindParam(':key',$key,PDO::PARAM_STR);
				$ticketListKeyInsert->bindParam(':eid',$show['event_id'],PDO::PARAM_INT);
				$ticketListKeyInsert->execute();
				
				if($ticketListKeyInsert->rowCount() == 0){
					echo "Unable to insert key for {$show['event_id']}!";
				}
			}
		}
		catch(PDOException $e){
			logError("res.admin.ticketlist.manage.php",__LINE__,"Error inserting ticketlist key!",$e->getMessage(),time(),false);
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
	function getContent(){?>
		<table><?php
		foreach($this->activeShows as $show){
			$dateString = date('l, j M Y \a\t g:i a',strtotime($show['event_date'])); ?>
			<tr><td><a href="/admin/ticketlist/<?php echo $show['event_id']; ?>/<?php echo $this->showKeys[$show['event_id']]; ?>">Ticket list</a> for <?php echo $show['show_name'] . ' on ' . $dateString; ?></td></tr>
			<?php
		}
		?>
		</table><?php
	}
}
?>