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
			#boardList li span.info-item{
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
		<h1>2015-2016 Executive Board</h1>
		<article>
			<section>
				<ul id="boardList">
					<li>
						<a name="ad"></a>
						<img src="/res/images/exec-board/quimby.png" />
						<span class="name info-item">Andrew Quimby</span>
						<span class="position info-item">Artistic Director</span>
						<span class="email info-item"><?php echo $this->str_rot('artisticdirector@floridaplayers.org',17); ?></span>
						<p>Andrew is a third year Theatre (BA) and Economics undergraduate at UF. He has thoroughly enjoyed his time working in the UF School of Theatre and Dance and hopes to pass that enjoyment onto as many other Gators as possible. If you need to contact to him, email him at <span class="email"><?php echo $this->str_rot('choctaw10@gmail.com',17); ?></span> or the email above.</p>
                    </li>
					<li>
						<a name="aad"></a>
						<?php //<img src="/res/images/exec-board/ashley.png" /> ?>
						<span class="name info-item">Kacey Musson</span>
						<span class="position info-item">Assistant Artistic Director</span>
						<span class="email info-item"><?php echo $this->str_rot('asst.artisticdirector@floridaplayers.org',17); ?></span>
                        <p>Kacey is a Junior BFA Acting Major, and outside studies of Film and Media. Questions and information regarding Mentor-Mentee, conflicts, workshops, general production and audition info, and any other questions you may have can be reverted to her! She's in the theater hallway all the time, and welcomes e-mails, calls, and texts.</p>
                    </li>
					<li>
						<a name="pm"></a>
						<img src="/res/images/exec-board/lauren-kennedy.png" />
						<span class="name info-item">Lauren Kennedy</span>
						<span class="position info-item">Production Manager</span>
						<span class="email info-item"><?php echo $this->str_rot('productionmanager@floridaplayers.org',17); ?></span>
						<p>Lauren Kennedy is elated to serve as Production Manager for Florida Players. This is her second year on the executive board and her third year at UF. Lauren is majoring in Tourism, Event, and Recreation Management, and yes, that IS a mouthful. In her spare time, Lauren bakes too much, attempts to learn to penny board inside her apartment, and hangs out in the SoTD hallway. Contact her if you have questions regarding stage management, design, tech crew, house management, show proposals, or getting involved in a production in any way.</p>
                    </li>
					<li>
						<a name="tres"></a>
						<img src="/res/images/exec-board/melanie.png" />
						<span class="name info-item">Melanie Sholl</span>
						<span class="position info-item">Treasurer</span>
						<span class="email info-item"><?php echo $this->str_rot('treasurer@floridaplayers.org',17); ?></span>
						<p>Melanie is a sophomore BFA Acting student and Arts in Healthcare Certificate candidate.  She is so excited to be serving on the executive board for such a wonderful organization!  Please contact with any questions regarding the Florida Players budget or if you have a good pun you'd like to share. She treasures you!</p>
                    </li>
					<li>
						<a name="pub"></a>
						<?php //<img src="/res/images/exec-board/veronica.png" /> ?>
						<span class="name info-item">Veronica Cinibulk</span>
						<span class="position info-item">Publicist</span>
						<span class="email info-item"><?php echo $this->str_rot('publicist@floridaplayers.org',17); ?></span>
						<p>Veronica Cinibulk is a third-year Psychology major and Spanish minor who fell in love with theatre a long, long time ago and still keeps coming back for more. In her free time she writes a lot, reads a lot, tries to learn new languages, and travels. Contact her with any questions regarding the publicity team, poster and program designs, other publicity questions, or just to talk!</p>
                    </li>
					<li>
						<a name="ec"></a>
						<img src="/res/images/exec-board/michael.png" />
						<span class="name info-item">Michael Ortiz</span>
						<span class="position info-item">Special Events Coordinator</span>
						<span class="email info-item"><?php echo $this->str_rot('specialevents@floridaplayers.org',17); ?></span>
						<p>Michael Ortiz is a third year BFA Acting and Business Administration minor. This is his first year on the FP Executive Board. He has big plans for FP special events this year and can't wait to see his fellow players party their way through college together. Contact him if you have any questions/suggestions regarding special events!</p>
                    </li>
					<li>
						<a name="sec"></a>
						<img src="/res/images/exec-board/summer.png" />
						<span class="name info-item">Summer Pliskow</span>
						<span class="position info-item">Secretary</span>
						<span class="email info-item"><?php echo $this->str_rot('secretary@floridaplayers.org',17); ?></span>
						<p>Summer is a sophomore BFA Acting major and is beyond excited to be your secretary! She has loved being an active member and performer in Florida Players and cannot wait to bring that enthusiasm to the Executive Board. She will be sending you all the lovely emails about important dates, reminders and meeting info so be sure to look out for them! However, she also loves to get emails as well, so feel free to contact her with any questions you may have regarding the point system, meeting minutes, the FP bulletin board or if you wish to be on the Florida Players mailing list! </p>
                    </li>
					<li>
						<a name="hist"></a>
						<img src="/res/images/exec-board/mako.png" />
						<span class="name info-item">Mako Horikoshi</span>
						<span class="position info-item">Historian</span>
						<span class="email info-item"><?php echo $this->str_rot('historian@floridaplayers.org',17); ?></span>
						<p>Mako is a sophomore BA in theatre (she used to be a psych major, but then, she was like "nawwww, theatre"), and she is super excited to be the historian!! She loves Florida Players and theatre in general with a strong passion. She has previously acted in Florida Players' productions of Antigone and Mulier Ridiculam.</p>
					</li>
                    <li>
                        <a name="outreach"></a>
                        <img src="/res/images/exec-board/kelsa.png" />
                        <span class="name info-item">Kelsa Kuchera</span>
                        <span class="position info-item">Outreach Liaison</span>
                        <span class="email info-item"><?php echo $this->str_rot('outreach@floridaplayers.org',17); ?></span> 
                        <p>Kelsa Kuchera is hyped to be the Florida Players' first Gator Growl, Homecoming and outreach Liaison!  A Sophomore at UF, Kelsa is double majoring in English and Theatre and minoring in the Theories and Politics of Sexuality.  In her vast amounts of free time she enjoys drawing, riding her bike, and watering her succulents.  Contact her if you are interested in skits, getting involved with homecoming, or just want to let the gator growl!</p>
                    </li>
					<li>
						<a name="web"></a>
						<img src="/res/images/exec-board/webmaster.png" />
						<span class="name info-item">Marcus Ball</span>
						<span class="position info-item">Webmaster</span> <!-- Grand Master of the Web -->
						<span class="email info-item"><?php echo $this->str_rot('webmaster@floridaplayers.org',17); ?></span>
						<p>Marcus is a 5th Computer Science major. He's been with Florida Players since his freshman year and is so happy to have the opportunity to work with such a great organization. Contact him if something has gone horribly wrong with the website or if you want to talk about geek stuff. </p>
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
                
                $('.unrot').each(function(){
					var text = $(this).text();
					var address = text.replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+9)?c:c-26);});
					$(this).text(address);
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