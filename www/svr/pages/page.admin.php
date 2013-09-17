<?php

class Page{
	var $request = null;
	var $usr;
	function Page($request = array()){
		$this->request = $request;
		$this->usr = $GLOBALS['USER']; //Get the user from router.php
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Admin Panel";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<style type="text/css">
			ul#resourceList{
				list-style: none outside none;
				padding: 0;
				margin: 10px 0;
			}
			#resourceList li{
				display: block;
				width: 200px;
				height: 45px;
				background-color: #0e3eaa;
				font-family: AurulentSansRegular;
				font-size: 18px;
				font-weight: normal;
				color: #fff;
				text-overflow: ellipsis;
				float: left;
				margin: 5px;
			}
			#resourceList li a{
				display: block;
				width: 180px;
				height: 25px;
				color: #fff;
				padding: 10px;
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
		$this->usr->get_info();
		if($this->usr->get_user_info("permissions") < 1){
			header("Location: /home");
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
		<article>
			<section>
				<ul id="resourceList">
					<li><a href="/admin/shows">Manage Shows</a></li>
					<li><a href="/admin/events">Manage Events</a></li>
					<li><a href="/admin/photos">Upload photos</a></li>
				</ul>
				<div class="clear"></div>
			</section>
		</article>
		<?php
	}
}
?>