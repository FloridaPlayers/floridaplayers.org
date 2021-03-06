<?php

class Page{
	var $request = null;
	var $show_data = null;
	var $cast_data = null;
	var $sql = null;
	var $test = 4;

	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		//if($show_data != null) {}
		if($this->show_data != null && isset($this->show_data['show_name']) && $this->show_data['show_name'] !== ""){
			echo $this->show_data['show_name'];
		}
		else echo "Show information";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<style type="text/css">
			#byline{
				font-style: italic;
			}
			.sep{
				margin: 0 0 20px;
			}
			#show_img{
				text-align: right;
			}
			#show_img img{
				max-width: 400px;
			}
			
			#show_img{
				/* background: url('/res/images/layout/photo-placeholder.png');
				-moz-background-size:100% 100%; /* Firefox 3.6 */
				background-size:100% 100%;
				background-repeat:no-repeat;*/
			}
			
			.cnc ul{
				list-style: none outside none;
				padding: 0;
				margin: 0;
				font-size: 13px;
			}
			.cnc ul li{
				width: 100%;
				clear: both;
				padding: 5px 0;
			}
			.cnc ul li:not(:last-child){
				border-bottom: 1px solid #eee;
			}
			.cnc ul li span.name{
				display: block;
				width: 49%;
				float: left;
			}
			.cnc ul li span.role{
				display: block;
				width: 49%;
				float: right;
			}
			
			a.showTicketLink{
				color: #FFFFFF !important;
				display: block;
				height: 25px;
				padding: 10px;
				width: 180px;
				background-color: #0E3EAA;
				font-family: AurulentSansRegular;
				font-size: 18px;
				font-weight: normal;
				text-overflow: ellipsis;
				margin: 20px 0 0;
			}
		</style>
		<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			/* $(document).ready(function(){
				if($
				$('#show_img').height($('#show_info').height() + 10);
			}); */
		</script>
		<?php
		if($this->show_data['open'] != null){ ?>
		<link href="/res/libraries/video-js/video-js.css" rel="stylesheet" />
		<script src="/res/libraries/video-js/video.js"></script>
		<style type="text/css">
		#promo div.video-js{
			margin: 0 auto;
		}
		section#promo {
			margin-bottom: 20px;
		}
		</style>
		<script type="text/javascript">
		$(document).ready(function(){
			_V_("promo-video").ready(function(){
				var myPlayer = this;
				var myFunc = function(){
					var myPlayer = this;
					try{
						clicky.log(window.location.pathname+'#video','Video play','click');
					}
					catch(e){ /* console.log("Could not log view to Clicky. Error: " + e); */ }
				};
				myPlayer.addEvent("play", myFunc);
			});
		});
		</script>
		<?php
		} ?>
	<?php
	}


	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		$this->sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		$this->sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		if(!$this->has_input_at(0)){
			header("Location: /shows");
			return false;
		}
		$requested_show = substr(strtolower($this->get_input_at(0)),0,20);
		if($requested_show == null || $requested_show == "" || preg_match("/[^\w_-]/",$requested_show)){
			header("Location: /shows");
			return false;
		}
		
		$show_query = "SELECT shows.show_id, shows.show_name, shows.show_abbr, shows.show_term, shows.show_year, shows.byline, shows.location, shows.image, shows.director, shows.synopsis, shows.fine_print, shows.promo, e.open, e.close, e.active FROM shows LEFT JOIN (SELECT show_id, MAX(event_date) AS close, MIN(event_date) AS open, active FROM events GROUP BY show_id) AS e ON shows.show_id = e.show_id WHERE shows.show_abbr='$requested_show' LIMIT 1";
		try{
			$show_response = $this->sql->query($show_query);
			if($show_response->rowCount() === 1){
				$this->show_data = $show_response->fetch(PDO::FETCH_ASSOC);
			}
			else{
				header("Location: /shows");
				return;
			}
		}
		catch(PDOException $e){
			logError("page.show.php",__LINE__,"Error retrieving available events!",$e->getMessage(),time(),false);
			echo 'Something went wrong while trying to access this show\'s information!';
			return;
		}
		
		$cast_query = "SELECT person_name AS name, role, type, position FROM cast_and_crew WHERE show_id='{$this->show_data['show_id']}' ORDER BY type, position ASC;";
		
		try{
			$cast_response = $this->sql->query($cast_query);
			if($cast_response->rowCount() > 0){
				$this->cast_data = $cast_response;
			}
		}
		catch(PDOException $e){
			logError("page.show.php",__LINE__,"Error retrieving cast list! Query: \"$cast_query\".",$e->getMessage(),time(),false);
			echo 'Something went wrong while trying to access this show\'s information!';
			return;
		}
			
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
		<h1><?php echo $this->show_data['show_name']; ?></h1>
		<article>
			<?php if($this->show_data['promo'] != null){ ?>
			<section id="promo">
				<?php echo $this->show_data['promo']; ?>
			</section>
			<?php } ?>
			<section id="show_info" class="sep half"><?php
				if($this->show_data['promo'] != null){
					echo "<h2>{$this->show_data['show_name']}</h2>";
				}
				?>
				<div id="byline" class="sep"><?php echo $this->utf8_urldecode($this->show_data['byline']); ?></div>
				<?php
				if($this->show_data['open'] != null){
					$opentime = strtotime($this->show_data['open']);
					$closetime = strtotime($this->show_data['close']);
					$datestr = 'l, F jS, Y';
					$drop = true;
					if(date('Y',$opentime) === date('Y',time())){
						$datestr = 'l, F jS';
						$drop = false;
					}
					$open = date($datestr,$opentime);
					$close = date($datestr,$closetime);
					echo "$open to ";
					if($drop) echo "<br />";
					echo "$close<br />";
				}
				global $THEATER_LOCATIONS;
				if(array_key_exists($this->show_data['location'],$THEATER_LOCATIONS)){
					$data = $THEATER_LOCATIONS[$this->show_data['location']];
					echo "Presented at the <a href=\"/map/{$data['short']}\">{$data['name']}</a>";
				}
                //If the show is both active (tickets are open), 
                //  and the show hasn't already passed,
                //  then we can show a link to reserve tickets.
				if($this->show_data['active'] == true && strtotime($this->show_data['close']) > time()){
					echo "<a href=\"/tickets\" class=\"showTicketLink\">Reserve tickets</a>";
				}
			?></section>
			<section id="show_img" class="sep half"><?php
				if($this->show_data['image'] != null){
					echo "<img src=\"{$this->show_data['image']}\" alt=\"{$this->show_data['show_name']}\" />";
				}
				else{
					//echo "<img src=\"".NO_SHOW_IMAGE_DEFAULT."\" alt=\"{$this->show_data['show_name']}\" />";
				}
			?>
			</section>
			<?php
			 if($this->show_data['director'] != null){ ?>
			<section <?php if($this->show_data['synopsis'] != null){ echo 'class="half"'; } ?>>
				<h2>Director's Note</h2>
				<?php echo $this->show_data['director']; ?>
			</section>
			<?php }
			if($this->show_data['synopsis'] != null){ ?>
			<section <?php if($this->show_data['director'] != null){ echo 'class="half"'; } ?>>
				<h2>Show Synopsis</h2>
				<?php echo $this->show_data['synopsis']; ?>
			</section>
			<?php }
			if($this->cast_data != null){
				$is_cast = false;
				$new = true;
				while($crew_member = $this->cast_data->fetch(PDO::FETCH_ASSOC)){
					if($crew_member['type'] == 'cast' && $new){
						$is_cast = true;
						$new = false; ?>
						<section class="cast cnc">
							<h2>Cast</h2>
							<ul>
						<?php
					}
					if($crew_member['type'] == 'design' && ($is_cast || $new)){
						if(!$new){ ?>
								</ul>
								<div class="clear"></div>
							</section>
							<?php
							$new = false;
						}
						if($is_cast){ ?>
							<section class="crew cnc">
								<h2>Crew</h2>
								<ul>
							<?php
							$is_cast = false;
						}
					}
					echo "<li><span class=\"name\">{$crew_member['name']}</span><span class=\"role\">{$crew_member['role']}</span><div class=\"clear\"></div></li>";
				}
				?>
				</ul>
				<div class="clear"></div>
			</section>
			<?php } 
			if($this->show_data['fine_print'] != null){ ?>
			<section id="fine_print">
				<?php echo $this->show_data['fine_print']; ?>
			<section>
			<?php
			}
			?>
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
	function utf8_urldecode($str) {
		$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
		return html_entity_decode($str,null,'UTF-8');;
	}
}
?>