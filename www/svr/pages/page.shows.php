<?php

class Page{
	var $request = null;
	var $sql = null;

	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Shows";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<style type="text/css">
			.show_item_inner{
				display: inline-block;
				vertical-align: top;
			}
			.show_item{
				margin: 5px 10px;
				padding: 7px 7px 2px;
			}
			.show_item:hover{
				background-color: #eeeeee;
				border-radius: 7px;
			}
			.show_item_name{
				margin: 0px;
				font-weight: normal;
			}
			/*.show_item a{
				color: #4764FA;
				text-decoration: none; 
			}
			.show_item a:hover{
				text-decoration: underline; 
			}*/
			.show_item img{
				border-radius: 3px 3px 3px 3px;
				box-shadow: 0 0 2px;
				padding: 5px;
				background-color: #ffffff;
				width: 150px;
			}
			.show_item .details{
				display: inline-block;
				margin: 0 15px;
				max-width: 570px;
				vertical-align: top;
			}
			.show_item aside{
				max-width: 100px;
				float: right;
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
		$this->sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		$this->sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if(!$this->sql){
			echo "Error connecting to database!";
			logError("page.shows.php",__LINE__,"Error connecting to database!",mysql_error(),time(),false);
		}
		else{
			require_once "showhelper.php";
			global $THEATER_LOCATIONS;
			$season_info = get_current_season();
			
			$current_season_ids = array();
			
			$upcoming_show_query = "SELECT shows.show_id, shows.show_name, shows.show_abbr, shows.show_term, shows.show_year, shows.byline, shows.location, shows.image, e.close FROM shows LEFT JOIN (SELECT show_id, MAX(ticket_close) AS close FROM events WHERE active='1' GROUP BY show_id) AS e ON shows.show_id = e.show_id WHERE show_term='{$season_info['next']['term']}' AND show_year='{$season_info['next']['year']}'";
			try{
				$upcoming_show_response = $this->sql->query($upcoming_show_query);
			}
			catch(PDOException $e){
				die("Error fetching show list! " . $e->getMessage());
			}
			
			
			ob_start();
			if($upcoming_show_response->rowCount()  > 0){	
			?>
				<section class="full nopad">
					<h1>Upcoming season</h1>
					<div class="upcoming_season_container">
					<?php
					while($show = $upcoming_show_response->fetch(PDO::FETCH_ASSOC)){ 
						array_push($current_season_ids,$show['show_id']);
						?>
						<div class="show_item" id="<?php echo $show['show_abbr']; ?>_show_item">
							<div class="show_item_image_container show_item_inner">
								<img src="<?php if(isset($show['image']) && ($show['image'] != null || $show['image'] != "")){ echo $show['image']; }else{echo NO_SHOW_IMAGE_DEFAULT;} ?>" alt="<?php echo $show['show_name']; ?>" class="show_item_picture" />
							</div>
							<div class="details">
								<h3 class="show_item_name"><a href="<?php echo "/show/{$show['show_abbr']}"; ?>"><?php echo $show['show_name']; ?></a></h3>
								<div><?php echo $this->utf8_urldecode($show['byline']); ?></div>
								<div><?php if(array_key_exists($show['location'],$THEATER_LOCATIONS)){ $data = $THEATER_LOCATIONS[$show['location']]; echo "Located at the <a href=\"/map/{$data['short']}\">{$data['name']}</a>"; } ?></div>
							</div>
							<aside>
								<?php
								$close = $show['close'];
								if(strtotime($close) > time()){
									echo "<a href=\"/tickets\">Get tickets</a>";
								}
								?>
							</aside>
							<div class="clear"></div>
						</div>
					<?php }
					?>
					</div>
				</section>
			<?php 
			}
			$upcoming_season=ob_get_contents();
			ob_clean();
			//End: get the current season
			
			
			//Get the current season
			//$show_query = "SELECT show_id,show_name,show_abbr,byline,location,image FROM shows WHERE show_term='{$season_info[current][term]}' AND show_year='{$season_info[current][year]}'";
			$show_query = "SELECT shows.show_id, shows.show_name, shows.show_abbr, shows.show_term, shows.show_year, shows.byline, shows.location, shows.image, e.close FROM shows LEFT JOIN (SELECT show_id, MAX(ticket_close) AS close FROM events WHERE active='1' GROUP BY show_id) AS e ON shows.show_id = e.show_id WHERE show_term='{$season_info['current']['term']}' AND show_year='{$season_info['current']['year']}'";
			try{
				$show_response = $this->sql->query($show_query);
				
				if($show_response->rowCount() == 0){
					$show_query = "SELECT shows.show_id, shows.show_name, shows.show_abbr, shows.show_term, shows.show_year, shows.byline, shows.location, shows.image, e.close FROM shows LEFT JOIN (SELECT show_id, MAX(ticket_close) AS close FROM events WHERE active='1' GROUP BY show_id) AS e ON shows.show_id = e.show_id WHERE show_term='{$season_info['previous']['term']}' AND show_year='{$season_info['previous']['year']}'";
					$show_response = $this->sql->query($show_query);
				}
			}
			catch(PDOException $e){
				die("Error fetching show list! " . $e->getMessage());
			}
			
			ob_start();
			if($show_response->rowCount()  > 0){	
			?>
				<section class="full nopad">
					<h1>Current season</h1>
					<div class="current_season_container">
					<?php
					while($show = $show_response->fetch(PDO::FETCH_ASSOC)){ 
						array_push($current_season_ids,$show['show_id']);
						?>
						<div class="show_item" id="<?php echo $show['show_abbr']; ?>_show_item">
							<div class="show_item_image_container show_item_inner">
								<img src="<?php if(isset($show['image']) && ($show['image'] != null || $show['image'] != "")){ echo $show['image']; }else{echo NO_SHOW_IMAGE_DEFAULT;} ?>" alt="<?php echo $show['show_name']; ?>" class="show_item_picture" />
							</div>
							<div class="details">
								<h3 class="show_item_name"><a href="<?php echo "/show/{$show['show_abbr']}"; ?>"><?php echo $show['show_name']; ?></a></h3>
								<div><?php echo $this->utf8_urldecode($show['byline']); ?></div>
								<div><?php if(array_key_exists($show['location'],$THEATER_LOCATIONS)){ $data = $THEATER_LOCATIONS[$show['location']]; echo "Located at the <a href=\"/map/{$data['short']}\">{$data['name']}</a>"; } ?></div>
							</div>
							<aside>
								<?php
								$close = $show['close'];
								if(strtotime($close) > time()){
									echo "<a href=\"/tickets\">Get tickets</a>";
								}
								?>
							</aside>
							<div class="clear"></div>
						</div>
					<?php }
					?>
					</div>
				</section>
			<?php 
			}
			$current_season=ob_get_contents();
			ob_clean();
			//End: get the current season
			
			
			//Get the previous seasons
			$helper_query = "";
			if(count($current_season_ids) > 0){
				for($x=0;$x<count($current_season_ids);$x++){
					$sid = $current_season_ids[$x];
					if($x > 0) $helper_query .= " AND ";
					$helper_query .= "show_id <> '$sid'";
				}
			}
			$past_show_query = "SELECT show_id,show_name,show_abbr,show_term,show_year,byline,location,image FROM shows";
			if($helper_query != "") $past_show_query .= " WHERE $helper_query";
			$past_show_query .= "ORDER BY show_year DESC, show_term ASC;";
			try{
				$past_show_response = $this->sql->query($past_show_query);
			}
			catch(PDOException $e){
				echo 'Error fetching previous seasons\' information! '.$e->getMessage();
			}
			if($past_show_response->rowCount() > 0){ ?>
				<section class="full nopad">
					<h1>Previous seasons</h1>
					<div class="previous_season_container">
					<?php
					while($show = $past_show_response->fetch(PDO::FETCH_ASSOC)){ 
						array_push($current_season_ids,$show['show_id']);
						?>
						<div class="show_item" id="<?php echo $show['show_abbr']; ?>_show_item">
							<img src="<?php if(isset($show['image']) && ($show['image'] != null || $show['image'] != "")){ echo $show['image']; }else{echo NO_SHOW_IMAGE_DEFAULT;} ?>" alt="<?php echo $show['show_name']; ?>"/>
							<div class="details">
								<h3 class="show_item_name"><a href="<?php echo "/show/{$show['show_abbr']}"; ?>"><?php echo $show['show_name']; ?></a></h3>
								<div><?php echo $this->utf8_urldecode($show['byline']); ?></div>
								<div><?php if(array_key_exists($show['location'],$THEATER_LOCATIONS)){ $data = $THEATER_LOCATIONS[$show['location']]; echo "Located at the <a href=\"/map/{$data['short']}\">{$data['name']}</a>"; } ?></div>
							</div>
							<aside>
								<?php echo $show['show_term'] . " " . $show['show_year']; ?>
							</aside>
							<div class="clear"></div>
						</div>
					<?php }
					?>
					</div>
				</section> <?php
			}
			//End: get the previous seasons
			
			$previous_season=ob_get_contents();
			ob_clean();
			
			if($upcoming_season !== FALSE) echo $upcoming_season;
			if($current_season !== FALSE) echo $current_season;
			if($previous_season !== FALSE) echo $previous_season;
		}
	}
	function utf8_urldecode($str) {
		$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
		return html_entity_decode($str,null,'UTF-8');;
	}
}
?>