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
						<a name="ad"></a>
						<img src="/res/images/exec-board/debbie.png" />
						<span class="name">Debbie Maciel</span>
						<span class="position">Artistic Director</span>
						<span class="email"><?php echo $this->str_rot('artisticdirector@floridaplayers.org',17); ?></span>
						<p>Debbie Maciel is humbled by and thrilled to be the Artistic Director of Florida Players’s 2014-2015 season! A Dance BFA candidate she has grown up in the School of Theatre & Dance halls as a 3 year member of Florida Players, Dance in a Suitcase & as a student ambassador for the College of Fine Arts. You may have seen in her in various dance shows as well as the play Blood Wedding, musical Guys & Dolls, and FP’s very own Mulier Ridiculum. Debbie loves making art, rapping alongside her Signs of Life family, and a really smart pun. She wants you to join her for a fun and compelling season. Contact with: any questions regarding Florida Players and Master Classes.</p>
					</li>
					<li>
						<a name="aad"></a>
						<img src="/res/images/exec-board/ashley.png" />
						<span class="name">Ashley Vonada</span>
						<span class="position">Assistant Artistic Director</span>
						<span class="email"><?php echo $this->str_rot('asst.artisticdirector@floridaplayers.org',17); ?></span>
						<p>Ashley is a second year student studying theater and chemical engineering, and she was last seen in Antigone in the spring. She is both thrilled and honored to be on the Executive board as the assistant artistic director. If you have any questions, need help with anything (literally anything at all), or just want to talk theater or nerdy things, feel free to contact her!</p>
					</li>
					<li>
						<a name="pm"></a>
						<img src="/res/images/exec-board/chaz.png" />
						<span class="name">Chaz May</span>
						<span class="position">Production Manager</span>
						<span class="email"><?php echo $this->str_rot('productionmanager@floridaplayers.org',17); ?></span>
						<p>Chaz May (BFA Musical Theatre) is honored to be the Florida Players Production Manager in his sophomore year at UF. This is Chaz's second year on the executive board of Florida Players. Chaz is in charge of the shows that are produced within Florida Players, so please contact him with any show-related concerns or if you want to get involved with things like: Tech Crews, Auditions, Design, Stage Management, House Management, Show Proposals, Etc.</p>
					</li>
					<li>
						<a name="tres"></a>
						<img src="/res/images/exec-board/katina.png" />
						<span class="name">Katina White</span>
						<span class="position">Treasurer</span>
						<span class="email"><?php echo $this->str_rot('treasurer@floridaplayers.org',17); ?></span>
						<p>Katina is enthralled to be your treasurer. Her first experience with the Florida Players was light board operator for Jeffrey. She enjoys kittens, long walks on the beach, managing the money, and creating SARs. Please contact her for any Florida Players financial concerns or if you are interested in joining the finance committee. </p>
					</li>
					<li>
						<a name="pub"></a>
						<img src="/res/images/exec-board/lauren.png" />
						<span class="name">Lauren Elizabeth-Killer</span>
						<span class="position">Publicist</span>
						<span class="email"><?php echo $this->str_rot('publicist@floridaplayers.org',17); ?></span>
						<p>Lauren Killer is a third year English and Theatre major, with a minor in mass communications. Essentially, she's crazy. This is her second year serving on the Florida Players executive board, and her third year working with this mah-velous organization. When not acting on the Florida Players stage or working behind the scenes, she is trying to be Tina Fey, doing a downward dog, or crafting her little heart out. Check out her live and online comedy show “Just Kidding,” coming to an interwebs near you.</p>
					</li>
					<li>
						<a name="apub"></a>
						<img src="/res/images/exec-board/veronica.png" />
						<span class="name">Veronica Michelle</span>
						<span class="position">Assistant Publicist</span>
						<span class="email"><?php echo $this->str_rot('asst.publicist@floridaplayers.org',17); ?></span>
						<p>Veronica is very excited to serve as Publicist for Florida Players during her last year at UF. She is studying both theatre and advertising, and has been a member of Florida Players since her freshman year. Spreading the word about this wonderful organization and all the opportunities it offers is a task she will do with pride. Contact her if interested in joining the Publicity team.</p>
					</li>
					<li>
						<a name="sec"></a>
						<img src="/res/images/exec-board/sam.png" />
						<span class="name">Samantha Stone</span>
						<span class="position">Secretary</span>
						<span class="email"><?php echo $this->str_rot('secretary@floridaplayers.org',17); ?></span>
						<p>Sam is a sophomore Journalism/English double major and is beyond excited to be joining the Florida Players board! Sam loves acting, and you may have most recently seen her in the Florida Players production of Chatroom. In addition to theatre, she enjoys writing, reading, and generally goofing off. Contact her if you have any questions about points, becoming a member, or any other secretarial thing your heart desires.</p>
					</li>
					<!--<li>
						<img src="" />
						<span class="name"></span>
						<span class="position">Special Events Coordinator</span>
						<span class="email"><?php echo $this->str_rot('specialevents@floridaplayers.org',17); ?></span>
						<p></p>
					</li>-->
					<li>
						<a name="hist"></a>
						<img src="/res/images/exec-board/mako.png" />
						<span class="name">Mako Horikoshi</span>
						<span class="position">Historian</span>
						<span class="email"><?php echo $this->str_rot('historian@floridaplayers.org',17); ?></span>
						<p>Mako is a sophomore BA in theatre (she used to be a psych major, but then, she was like "nawwww, theatre"), and she is super excited to be the historian!! She loves Florida Players and theatre in general with a strong passion. She has previously acted in Florida Players' productions of Antigone and Mulier Ridiculam.</p>
					</li>
					<li>
						<a name="web"></a>
						<img src="/res/images/exec-board/webmaster.png" />
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
						
						So webczar
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