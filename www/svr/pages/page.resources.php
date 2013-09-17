<?php

class Page{
	var $request = null;

	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Resources";
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
			<section>This will be more organized and nicer later.</section>
			<section>
				<ul id="resourceList">
					<li><a href="/res/docs/constitution.pdf">Constitution</a></li>
					<li><a href="/map">Maps</a></li>
					<li><a href="/points">Points</a></li>
					<li><a href="/res/docs/New-Works-Proposal-Guidelines-2013.pdf">Proposal Guidelines</a></li>
					<li><a href="/res/docs/MLP-Matching-2013.pdf">Mentor/Mentee Form</a></li>
				</ul>
				<div class="clear"></div>
			</section>
		</article>
		<?php
	}
}
?>