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
		echo "The Executive Board";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
		<style type="text/css">
			ul#boardList{
				list-style: none outside none;
				padding: 0;
				margin: 10px 0;
			}
			#boardList li{
				display: block;
				width: 400px;
				font-family: OpenSansRegular;
				font-size: 18px;
				font-weight: normal;
				color: #6A6A6A;
				text-overflow: ellipsis;
				float: left;
				margin: 5px;
				padding: 10px;
			}
			#boardList li span{
				display: block;
			}
			#boardList li span.name{
				color: #4764FA;
			}
			#boardList li span.position, #boardList li span.email{
				font-size: 14px;
			}
			#boardList li span.email a{
				color: #6A6A6A;
			}
			section{
				padding: 0px;
			}
		</style>
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('.email').each(function(){
					var text = $(this).html();
					var address = text.replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+9)?c:c-26);});
					$(this).html('<a href="mailto:'+address+'">'+address+'</a>');
				});
			});
		</script>
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
		<h1>2013-2014 Executive Board</h1>
		<article>
			<section>
				<ul id="boardList">
					<li>
						<span class="name">Megan Wicks</span>
						<span class="position">Artistic Director</span>
						<span class="email"><?php echo $this->str_rot('artisticdirector@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<span class="name">Kevin Roost</span>
						<span class="position">Assistant Artistic Director</span>
						<span class="email"><?php echo $this->str_rot('asst.artisticdirector@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<span class="name">Kimberly Yeoman</span>
						<span class="position">Production Manager</span>
						<span class="email"><?php echo $this->str_rot('productionmanager@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<span class="name">Katina White</span>
						<span class="position">Treasurer</span>
						<span class="email"><?php echo $this->str_rot('treasurer@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<span class="name">Lauren Killer</span>
						<span class="position">Publicist</span>
						<span class="email"><?php echo $this->str_rot('publicist@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<span class="name">Christian Allison</span>
						<span class="position">Assistant Publicist</span>
						<span class="email"><?php echo $this->str_rot('asst.publicist@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<span class="name">Drew Bryan</span>
						<span class="position">Secretary</span>
						<span class="email"><?php echo $this->str_rot('secretary@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<span class="name">Casey Henshaw</span>
						<span class="position">Special Events Coordinator</span>
						<span class="email"><?php echo $this->str_rot('specialevents@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<span class="name">Anthony Bido</span>
						<span class="position">Historian</span>
						<span class="email"><?php echo $this->str_rot('historian@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<span class="name">Marcus Ball</span>
						<span class="position">Webmaster</span> <!-- Grand Master of the Web -->
						<span class="email"><?php echo $this->str_rot('webmaster@floridaplayers.org',17); ?></span>
					</li>
				</ul>
				<div class="clear"></div>
			</section>
			<section>
				<p>For general questions or comments, you may email us at <span class="email"><?php echo $this->str_rot('info@floridaplayers.org',17); ?></span>.</p>
			</section>
		</article>
		<?php
	}
	function str_rot($s, $n = 13) {
		static $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$n = (int)$n % 26;
		if (!$n) return $s;
		if ($n == 13) return str_rot13($s);
		for ($i = 0, $l = strlen($s); $i < $l; $i++) {
			$c = $s[$i];
			if ($c >= 'a' && $c <= 'z') {
				$s[$i] = $letters[(ord($c) - 71 + $n) % 26];
			} else if ($c >= 'A' && $c <= 'Z') {
				$s[$i] = $letters[(ord($c) - 39 + $n) % 26 + 26];
			}
		}
		return $s;
	}
}
?>