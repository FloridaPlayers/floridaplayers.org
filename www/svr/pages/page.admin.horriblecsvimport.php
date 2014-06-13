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
			$this->sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		}
		catch(PDOException $e){
			logError("res.admin.ticketlist.manage.php",__LINE__,"Error connecting to database!",$e->getMessage(),time(),false);
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
		<article>
		<form method="post">
			<input type="hidden" name="submit_import" value="much" />
			<div>Event Id: <input type="number" name="event_id" /></div>
			<div>CSV:
			<textarea name="import_content"></textarea>
			</div>
			<input type="submit" value="import" />
		<form>
		
		<section><?php
			if(isset($_POST['submit_import'])){
				try{
					$event_id = $_POST['event_id'];
					$raw_csv = $_POST['import_content'];
					$rows = explode(';',$raw_csv);
					$to_add = array();
					foreach($rows as $row){
						$details = explode(',',strtolower($row));
						if(count($details) < 3) continue;
						$name = explode(' ',trim($details[0]));
						$first_name = $name[0];
						$last_name = ' ';
						if(isset($name[1])){
							$last_name = $name[1];
						}
						
						$email = $details[1];
						if(trim($email) == ''){
							$email = implode('-',array('manual-insert',implode('-',$name),$event_id));
						}
						$new = array('first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'quantity'=>$details[2]);
						$to_add[] = $new;
					}

					$insertQuery = $this->sql->prepare('INSERT INTO reservations (event_id, user_id, first_name, last_name, email_address, ticket_quantity) VALUES (:eid, null, :fn, :ln, :eaddr, :quant)');
					echo '<pre>';
					
					foreach($to_add as $insert){
						$insertQuery->execute(array(':eid'=>$event_id,':fn'=>$insert['first_name'],':ln'=>$insert['last_name'],':eaddr'=>$insert['email'],':quant'=>$insert['quantity']));
						if($insertQuery->rowCount() == 1){ 
							echo "Successfully inserted reservation for {$insert['first_name']} {$insert['last_name']} ({$insert['quantity']} tickets)<br />";
						}
						else{
							echo "Unable to insert reservation for {$insert['first_name']} {$insert['last_name']} ({$insert['quantity']} tickets) - {$insert['email']}<br />";
							print_r($this->sql->errorInfo());
						}
					}
					echo '</pre>';
				}
				catch(PDOException $e){
					logError("res.admin.horriblecsvimport.php",__LINE__,"Error inserting new reservations!",$e->getMessage(),time(),false);
					return false;
				}
			}
		?>
		</section>
		</article>
		<?php
	}
}
?>