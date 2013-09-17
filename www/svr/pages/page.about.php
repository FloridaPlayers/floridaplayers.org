<?php

class Page{
	var $request = null;

	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "About us";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<style type="text/css">
			.blocklink{
				display: inline-block;
				width: 120px;
				height: 120px;
				margin: 0 0 5px 0;
			}
			
			#facebook_link{ background: #3B5998 url('/res/images/icons/facebook.png') no-repeat scroll top left; }
			#constitution_link { background: #0e3eaa url('/res/images/icons/constitution.png') no-repeat scroll top left; }
			#contact_link { background: #0e3eaa url('/res/images/icons/contact.png') no-repeat scroll top left; }
			#calendar_link { background: #0e3eaa url('/res/images/icons/calendar.png') no-repeat scroll top left; }
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
			<aside class="small">
				<ul>
					<li><a href="/res/docs/constitution.pdf" class="blocklink" id="constitution_link" title="View our constitution"></a></li>
					<!-- <li><a href="/calendar" class="blocklink" id="calendar_link" title="View our calendar"></a></li> -->
					<li><a href="/about/contact" class="blocklink" id="contact_link" title="Contact Us"></a></li>
					<li><a href="http://www.facebook.com/FloridaPlayers" class="blocklink" id="facebook_link" title="Visit us on Facebook"></a></li>
				</ul>
			</aside>
			<article>
				<section>
					<h2>About Florida Players</h2>
					<p>Florida Players is a student-run theater company that provides opportunities for students to explore the world of theatre and showcase their works in doing so. Florida Players is open to all University of Florida students, regardless of their major and is sponsored by UF Student Government. Florida Players offers students opportunities in all aspects of theatre, including direction, design, performance, and playwriting, as well as leadership positions. Florida Players is also an umbrella organization to Floridance, a dance company.</p>
				</section>
				<section>
					<h2>A Brief History of Florida Players</h2>
					<p>Florida Players is a student-run organization dedicated to producing shows, studying theatre and fostering an appreciation of the performing arts at the University of Florida. The organization was created in 1932 at the University of Florida. Members performed shows in any available location and rehearsals were in vacant classrooms. In the 1950&apos;s, The Costume shop was located underneath the University Auditorium stage and the costumes were stored next to where the organ pipes used to be on the third level. The stage scenery was constructed in a now non-existent cattle barn near what is now the College of Architecture, and was then carried to the performance location on the East side of 13th street at the P.K. Yonge school, now known as Norman Hall. In the 1960&apos;s, Florida Players received its own home in the H.P. Constans Theatre. The number of productions, as well as the number of performances, increased over the years.</p>
					<p>In 1979 two of the Florida Players productions were chosen out of a state competition for the American College Theatre Festival to represent the University of Florida at the regional competition held in Athens, Georgia. One of these shows was then chosen to go to the Kennedy Center in Washington, D.C. Six universities in the United States were chosen from a field of more than six hundred entries. In 1979 the Florida Players were chosen to perform at the regional level in Greensboro, North Carolina and they were chosen as the first alternate to again go to the Kennedy Center. In 1980 the Florida Players were again chosen through a state A.C.T.F. with twelve schools competing to go to the regionals in Auburn, Alabama. In 1985 the Florida Players were chosen to host the Regional American College Theatre Festival.</p>
					<p>It was during the 1980&apos;s that Florida Players separated from the UF Department of Theatre and Dance. The building and all of the group&apos;s equipment were given to the department. Since then, Florida Players has used various other spaces, most recently the Squitieri Black Box, and the Nadine McGuire Black Box. </p>
				</section>
			</article>
			<div class="clear"></div> 
		<?php
	}
}
?>