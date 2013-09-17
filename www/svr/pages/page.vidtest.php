<?php

class Page{
	var $request = null;
	var $usr;
	function Page($request){
		$this->request = $request;
		$this->usr = $GLOBALS['USER']; //Get the user from router.php
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "TITLE";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
			

			<link href="/res/libraries/video-js/video-js.css" rel="stylesheet">
			<script src="/res/libraries/video-js/video.js"></script>


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
		?>
		

		<video id="promo-video" class="video-js vjs-default-skin" controls preload="auto" width="640" height="360" poster="/res/images/video-posters/maple-and-vine.png" data-setup="{}">
			<source src="/res/videos/maple-and-vine-trailer.mp4" type='video/mp4'>
			<source src="/res/videos/maple-and-vine-trailer.ogv" type="video/ogg" />
			<source src="/res/videos/maple-and-vine-trailer.webm" type='video/webm'>
		</video>
		<?php
	}
}
?>