<?php
require_once "authentication.php";
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
			tr.checkedIn{
				background: #9FFCAD;
			}
			td.edit{
				width: 110px;
			}
		</style>
		<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript">
		$(document).ready(function(){
			$('.reservation_checkin').click(function(){
				var checked = $(this).is(':checked');
				var resId = $(this).val();
				var checkbox = $(this);
				$.ajax({
					url:'/admin/ticketlist/checkin',
					data:'reservation_id='+resId+'&checked_in='+((checked)?'true':'false'),
					dataType:'json',
					success:function(data,status,jqxhr){
						if(data.status == 'success'){
							if(checked){
								checkbox.parents('tr').addClass('checkedIn');
							}
							else{
								checkbox.parents('tr').removeClass('checkedIn');
							}
						}
						else{
							alert(data.message);
						}
					}
				});
			});
			<?php if($this->usr->is_logged_in() && $this->usr->get_user_info("permissions") >= 1){ ?>
			$('.vip_edit').click(function(){
				//var checked = $(this).is(':checked');
				resEditButton = $(this);
				resId = $(this).attr('data-reservation-id');
				resRow = $(this).parents('tr');
				resVipDisplay = $('.vip',resRow);
				resTicketQuantity = $('.quantity',resRow).text();
				resIsVip = ($(this).attr('data-is-vip') == 'true');
				//var checkbox = $(this);
				$.ajax({
					url:'/admin/ticketlist/editvip',
					data:'reservation_id='+resId+'&make_vip='+((!resIsVip)?'true':'false'),
					dataType:'json',
					success:function(data,status,jqxhr){
						if(data.status == 'success'){
							resIsVip = !resIsVip; 
							resEditButton.prop('value',(resIsVip)?resEditButton.attr('data-remove-vip'):resEditButton.attr('data-add-vip'));
							resEditButton.attr('data-is-vip',(resIsVip)?'true':'false');
							resVipDisplay.html((resIsVip)?resTicketQuantity:'');
						}
						else{
							alert(data.message);
						}
					}
				});
			});
			<?php } ?>
		});
		</script>
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
		
		$this->usr->get_info();
		if(!$this->usr->is_logged_in() || $this->usr->get_user_info("permissions") == false || (int)$this->usr->get_user_info("permissions") < 1){
			if(!$this->has_input_at(1)){
				header("Location: /home");
				return false;
			}
			$ticketlistKey = substr($this->get_input_at(1),0,16);
			if($ticketlistKey == null || $ticketlistKey == "" || preg_match("/[^a-f0-9]/",$ticketlistKey)){
				header("Location: /home");
				return false;
			}
			try{
				$keyCheck = $this->sql->prepare('SELECT aid FROM ticketlist_access WHERE access_key=:key AND event_id=:eid LIMIT 1');
				$keyCheck->bindParam(':key',$ticketlistKey,PDO::PARAM_STR);
				$keyCheck->bindParam(':eid',$requestedShow,PDO::PARAM_INT);
				$keyCheck->execute();
				$result = $keyCheck->fetch(PDO::FETCH_ASSOC);
				if(!isset($result) || !$result || !isset($result['aid'])){
					header("Location: /home");
					print_r($result);
					return false;
				}
			}
			catch(PDOException $e){
				logError("page.admin.tickets.list.php",__LINE__,"Error checking if ticketlist key is valid!",$e->getMessage(),time(),false);
				return false;
			}
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
			$reservationsStmt = $this->sql->prepare('SELECT r.reservation_id, CASE WHEN r.email_address IS NULL THEN u.email ELSE r.email_address END AS email, CASE WHEN r.first_name IS NULL THEN u.first_name ELSE r.first_name END AS fname, CASE WHEN r.last_name IS NULL THEN u.last_name ELSE r.last_name END AS lname, sum(r.ticket_quantity) AS quantity, sum(r.ticket_quantity * r.vip) AS vip, r.checked_in FROM reservations AS r LEFT JOIN users AS u ON r.user_id=u.uid WHERE r.event_id=:eid GROUP BY r.email_address,u.email ORDER BY lname,fname');
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
					<thead>
						<tr>
							<th>&#9745;</th>
							<th>Name</th>
							<th>Quantity</th>
							<th>VIP</th>
							<?php if($this->usr->is_logged_in() && $this->usr->get_user_info("permissions") >= 1){ ?> 
								<th>Edit</th>
							<?php } ?>
						</tr>
					</thead>
				<?php while ($row = $this->reservationStmt->fetch()) { ?>
					<tr <?php if($row['checked_in']){echo 'class="checkedIn"'; }?>>
						<td>
							<input type="checkbox" class="reservation_checkin" value="<?php echo $row['reservation_id']; ?>" <?php if($row['checked_in']){ echo 'checked="checked"'; } ?> />
						</td>
						<td>
							<?php echo ucwords($row['fname']." ".$row['lname']); ?>
						</td>
						<td class="quantity">
							<?php echo $row['quantity']; ?>
						</td>
						<td class="vip">
							<?php if($row['vip'] > 0){ echo $row['vip']; } ?>
						</td>
						<?php if($this->usr->is_logged_in() && $this->usr->get_user_info("permissions") >= 1){ ?> 
						<td class="edit">
							<input type="button" class="vip_edit" data-reservation-id="<?php echo $row['reservation_id']; ?>" data-add-vip="Make VIP" data-remove-vip="Remove VIP" data-is-vip="<?php echo (($row['vip'] > 0)?'true':'false'); ?>" value="<?php echo (($row['vip'] > 0)?'Remove VIP':'Make VIP'); ?>" />
						</td>
						<?php } ?>
					</tr>
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