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
		echo "Points";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<style type="text/css">
		section{
			text-align: center;
		}
		</style>
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
		<h1>Points</h1>
		<article>
			<section>
				<iframe frameborder="1" marginwidth="0" marginheight="0" border="0" style="border:0;margin:0;width:800px;height:600px;" src="https://spreadsheets.google.com/pub?key=0Aqv87wQ26byedDd2RzV4bUZzVFI5cFJzci1Bb0FyZ3c&hl=en&output=html" scrolling="yes" allowtransparency="true"></iframe>
			</section>
			<a href="http://spreadsheets.google.com/pub?key=0Aqv87wQ26byedDd2RzV4bUZzVFI5cFJzci1Bb0FyZ3c&amp;output=html" target="_blank">Open Points Table in New Window</a></div></td>
		</article>
		<?php
	}
}
?>