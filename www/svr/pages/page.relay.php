<?php

class Page{
	var $request = null;
	var $sql = null;
	var $errorStatus = 0;
	var $errorType = null;
	var $relayAlbum = 17;
	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo 'Relay for Life Photos';
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<script type="text/javascript" src="/res/scripts/jquery.min.js"></script>
	
		<link rel="stylesheet" type="text/css" href="/res/libraries/shadowbox/shadowbox.css">
		<script type="text/javascript" src="/res/libraries/shadowbox/shadowbox.js"></script>
		<script type="text/javascript">
		Shadowbox.init();
		</script>
	
		
		<style type="text/css">
		h1{
			background: url("/res/images/layout/header-bg2-relay.png") no-repeat scroll left bottom transparent !important;
		}
		
		section.semester-container{
			padding: 0 !important;
		}
		#photos li {
			overflow:hidden;
			/* fix ie overflow issue */
			position:relative;
			box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
			display: inline-block;
			height: 200px;
			margin: 5px;
			width: 200px;
		}
		#photos ul {
			position:relative;
			left:0;
			top:0;
			list-style:none;
			margin:0;
			padding:0;          
		}
		#photos img {
			padding: 0px;
			width: 200px;
			height: 200px;
		}
		#photos a{
			width: 200px;
			height: 200px;
			display: block;
			position: absolute;
			z-index: 20;
		}
		#sb-title a{
			color: #fff;
			text-decoration: none;
			font-family: OpenSansRegular;
		}
		</style>
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
			$this->sql = false;
			//echo $e->getMessage();
			//return false;
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
		if($this->sql === false){?>
			<article>
				<section>
					<h2>Error</h2>
					<p>Unable to connect to database!</p>
				</section>
			</article>
		<?php }
		elseif($this->errorStatus == 404){?>
			<article>
				<section>
					<h2>404 - Album not found</h2>
					<p><?php if($this->errorType == "notfound") { echo "Unable to find the requested album!"; } elseif($this->errorType == "invalid") { echo "Invalid album requested"; } else { echo "An unknown error occurred while trying to find that gallery."; } ?></p>
				</section>
			</article>
		<?php }
		else{ ?>
			<h1>Relay for Life</h1>
			<article>
				
				<?php
					$sql_query = 'SELECT * FROM images WHERE album_id=\''.$this->relayAlbum.'\';';
					$return = $this->sql->query($sql_query);
					if($return->rowCount() == 0){
						echo 'There are no photos yet!';
					}
					else{ ?>
						<section id="photos">
							<ul class="photo">
								<?php
								//$sql_query = 'SELECT * FROM images WHERE album_id=\''.$this->relayAlbum.'\';';
								
								while($image = $return->fetch(PDO::FETCH_ASSOC)){
									if($image['hide_in_album'] == false){
										$display = '/res/uploads/'.$image['img_file'].'_l'.$image['img_ext'];
										$thumb = '/res/uploads/'.$image['img_file'].'_sq'.$image['img_ext'];
										$original = '/res/uploads/'.$image['img_file'].'_o'.$image['img_ext'];
										//$title = ($image['img_title'] == null)?"":$image['img_title'];
										//$desc = ($image['img_desc'] == null)?"":$image['img_desc'];
										echo "<li><a href=\"{$display}\" rel=\"shadowbox[relay]\" title=\"<a href='{$original}'>Download full size image</a>\"><img src=\"{$thumb}\" alt=\"\" /></a></li>";
									}
								}
								?>
							</ul>
						</section
					<?php } ?>
				<div class="clear"></div>
			</article>
			<?php
		}
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