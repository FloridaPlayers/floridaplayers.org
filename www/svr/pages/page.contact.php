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
		
		<style type="text/css">
			ul#boardList{
				list-style: none outside none;
				padding: 0;
				margin: 10px 0;
			}
			#boardList li{
				display: block;
				width: 380px;
				font-family: OpenSansRegular;
				font-size: 18px;
				font-weight: normal;
				color: #6A6A6A;
				text-overflow: ellipsis;
				float: left;
				margin: 10px;
				padding: 15px;
				
				background-color: #eeeeee;
				box-shadow: 0 1px 3px rgba(34, 25, 25, 0.4);
				-moz-box-shadow: 0 1px 2px rgba(34,25,25,0.4);
				-webkit-box-shadow: 0 1px 3px rgba(34, 25, 25, 0.4);
			}
			#boardList li span{
				display: block;
				padding-left: 75px;
			}
			#boardList img {
				float: left;
				width: 64px;
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
			#boardList li p{
				background-color: #FFFFFF;
				background-origin: border-box;
				color: #6A6A6A;
				font-family: OpenSansRegular;
				font-size: 14px;
				margin: 15px -15px -15px;
				padding: 15px;
			}
			section{
				padding: 0px;
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
		<h1>2013-2014 Executive Board</h1>
		<article>
			<section>
				<ul id="boardList">
					<li>
						<img src="/res/images/exec-board/megan.jpg" />
						<span class="name">Megan Wicks</span>
						<span class="position">Artistic Director</span>
						<span class="email"><?php echo $this->str_rot('artisticdirector@floridaplayers.org',17); ?></span>
						<p>Megan is honored to be Artistic Director of Florida Players for the 2013-2014 school year. In the past two years, she has been involved with the executive board of FP as Secretary and Special Events coordinator and has acted in Children's Hour, Othello, Jesus Hopped the A Train, Iceland Play, Maple and Vine, and Mulier Ridiculum. She enjoys vegan food, the Insanity workout, and tea. Art is the portal to the soul, and Florida Player strives to provide opportunities to express yourself and your passion....contact me to get involved! 
Contact for: General questions regarding Florida Players, Master Class</p>
					</li>
					<li>
						<img src="/res/images/exec-board/kevin.jpg" />
						<span class="name">Kevin Roost</span>
						<span class="position">Assistant Artistic Director</span>
						<span class="email"><?php echo $this->str_rot('asst.artisticdirector@floridaplayers.org',17); ?></span>
						<p>Kevin is beyond appreciative to be your Assistant Artistic Director and will be happy to serve you in any way possible. He has been seen in Floyd Collins, Shotgun Party, A New Brain, Too Much Light Makes the Baby Go Blind, and will be directing Change Provided as a part of the New Works Festival. If you really want him to like you, feed him Reese's, sing him Newsies, or bring him a puppy to play with. Thanks for playing! </p>
					</li>
					<li>
						<img src="/res/images/exec-board/kimberly.jpg" />
						<span class="name">Kimberly Yeoman</span>
						<span class="position">Production Manager</span>
						<span class="email"><?php echo $this->str_rot('productionmanager@floridaplayers.org',17); ?></span>
						<p>Kimberly Yeoman (BFA Scenic Design) is super excited to be on the Florida Players Board in her final year at UF. Previous Florida Players credits include Scenic Design for 'New Works Festival 2012' and 'Jeffrey,' and Props Design for 'Jesus Hopped the A Train.' 
Kimberly is in charge of the shows that are produced within Florida Players, so please contact her with any show-related concerns or if you want to get involved with things like:
Tech Crews
Auditions
Design
Stage Management
House Management
Show Proposals
Etc.</p>
					</li>
					<li>
						<img src="/res/images/exec-board/katina.jpg" />
						<span class="name">Katina White</span>
						<span class="position">Treasurer</span>
						<span class="email"><?php echo $this->str_rot('treasurer@floridaplayers.org',17); ?></span>
						<p>Katina is enthralled to be your treasurer. Her first experience with the Florida Players was light board operator for Jeffrey. She enjoys kittens, long walks on the beach, managing the money, and creating SARs. Please contact her for any Florida Players financial concerns or if you are interested in joining the finance committee. </p>
					</li>
					<li>
						<img src="/res/images/exec-board/lauren.jpg" />
						<span class="name">Lauren Killer</span>
						<span class="position">Publicist</span>
						<span class="email"><?php echo $this->str_rot('publicist@floridaplayers.org',17); ?></span>
					</li>
					<li>
						<img src="/res/images/exec-board/christian.jpg" />
						<span class="name">Christian Allison</span>
						<span class="position">Assistant Publicist</span>
						<span class="email"><?php echo $this->str_rot('asst.publicist@floridaplayers.org',17); ?></span>
						<p>Christian is new to the executive board of Florida Players. In addition to graphic design, he enjoys theatre, music and more tea than can be considered healthy. As Assistant Publicist, he works primarily with the visual aspect of the production, creating posters, pamphlets, and anything else that can be used to promote the organization and its shows! Anyone with any interest in the Publicity team may contact him or the Senior Publicist, Lauren Killer.</p>
					</li>
					<li>
						<img src="/res/images/exec-board/drew.jpg" />
						<span class="name">Drew Bryan</span>
						<span class="position">Secretary</span>
						<span class="email"><?php echo $this->str_rot('secretary@floridaplayers.org',17); ?></span>
						<p>Drew Bryan is a junior studying Theatre performance and Telecommunications. Her passions include acting, elephants, and good books</p>
					</li>
					<li>
						<img src="/res/images/exec-board/casey.jpg" />
						<span class="name">Casey Henshaw</span>
						<span class="position">Special Events Coordinator</span>
						<span class="email"><?php echo $this->str_rot('specialevents@floridaplayers.org',17); ?></span>
						<p>Casey Henshaw is a second year Event Management major at the University of Florida. Her minor is in theatre, and she has been dancing and doing theatre almost her entire life. She is so excited to be a part of the Executive Board this year, and if you are interested in helping with or participating in any kind of special event, she would love to hear from you!</p>
					</li>
					<li>
						<img src="/res/images/exec-board/bido.jpg" />
						<span class="name">Anthony Bido</span>
						<span class="position">Historian</span>
						<span class="email"><?php echo $this->str_rot('historian@floridaplayers.org',17); ?></span>
						<p>Anthony Bido is a junior BFA acting major. He's been a member of Florida Players for the past two years working mostly behind the scenes. He enjoys lights and shiny things. Contact him for archive information and photo calls.</p>
					</li>
					<li>
						<img src="/res/images/exec-board/webmaster.jpg" />
						<span class="name">Marcus Ball</span>
						<span class="position">Webmaster</span> <!-- Grand Master of the Web -->
						<span class="email"><?php echo $this->str_rot('webmaster@floridaplayers.org',17); ?></span>
						<p>Marcus is a junior Computer Science major. He's been with Florida Players since his freshman year and is so happy to have the opportunity to work with such a great organization. Contact him if something has gone horribly wrong with the website or if you want to talk about geek stuff. </p>
						<!--
						Wow. Such professional.
						
						─────────▄──────────────▄
						────────▌▒█───────────▄▀▒▌
						────────▌▒▒▀▄───────▄▀▒▒▒▐
						───────▐▄▀▒▒▀▀▀▀▄▄▄▀▒▒▒▒▒▐
						─────▄▄▀▒▒▒▒▒▒▒▒▒▒▒█▒▒▄█▒▐
						───▄▀▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▀██▀▒▌        4
						──▐▒▒▒▄▄▄▒▒▒▒▒▒▒▒▒▒▒▒▒▀▄▒▒▌
						──▌▒▒▐▄█▀▒▒▒▒▄▀█▄▒▒▒▒▒▒▒█▒▐    very rfc 1149.5
						─▐▒▒▒▒▒▒▒▒▒▒▒▌██▀▒▒▒▒▒▒▒▒▀▄▌
						─▌▒▀▄██▄▒▒▒▒▒▒▒▒▒▒▒░░░░▒▒▒▒▌                           wow
						─▌▀▐▄█▄█▌▄▒▀▒▒▒▒▒▒░░░░░░▒▒▒▐
						▐▒▀▐▀▐▀▒▒▄▄▒▄▒▒▒▒▒░░░░░░▒▒▒▒▌
						▐▒▒▒▀▀▄▄▒▒▒▄▒▒▒▒▒▒░░░░░░▒▒▒▐
						─▌▒▒▒▒▒▒▀▀▀▒▒▒▒▒▒▒▒░░░░▒▒▒▒▌         many bytes
						─▐▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▐
						──▀▄▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▄▒▒▒▒▌
						────▀▄▒▒▒▒▒▒▒▒▒▒▄▄▄▀▒▒▒▒▄▀
						───▐▀▒▀▄▄▄▄▄▄▀▀▀▒▒▒▒▒▄▄▀
						──▐▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▀▀
						
						So webmaster
						-->

					</li>
				</ul>
				<div class="clear"></div>
			</section>
			<section>
				<p>For general questions or comments, you may email us at <span class="email"><?php echo $this->str_rot('info@floridaplayers.org',17); ?></span>.</p>
			</section>
		</article>
		<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
		<script src="/res/scripts/masonry.pkgd.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.email').each(function(){
					var text = $(this).html();
					var address = text.replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+9)?c:c-26);});
					$(this).html('<a href="mailto:'+address+'">'+address+'</a>');
				});
				
				$('#boardList').masonry({
					  itemSelector: 'li',
					  isFitWidth: true
				});
			});
		</script>
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