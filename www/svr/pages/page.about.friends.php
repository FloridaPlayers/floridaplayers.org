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
		echo "Our Friends";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
			<style type="text/css">
			h3{
				font-family: ColaboratethinRegular;
				font-size: 20px;
				margin: 0;
			}
			ul{
				list-style-type: none;
				margin: 0;
				padding: 0;
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
		<h1>Friends of Florida Players</h1>
		<article>
			<section>
				<h2>On Campus</h2>
				<ul>
					<li>
						<h3><a href="http://www.theatrestrikeforce.org">Theatre Strike Force</a></h3>
						<p>Theatre Strike Force is the improv and sketch comedy student organization at UF. With regular performances and ever increasing membership, TSF has become one of the largest and most popular collegiate comedy groups in the nation. Although we're best known for our &quot;short-form&quot; improv (popularized by the show &ldquo;Whose Line Is It, Anyway&rdquo;), we also perform long-form improv, musical improv, and Saturday Night Live-style sketch comedy. Many of our alumni have moved on to Chicago and Los Angeles, joining the ranks of the best improvisers in the country. We offer beginner classes in improv to anyone whose desire is to have some fun.</p>
					</li>
					<li>
						<h3><a href="http://www.danceinasuitcase.org">Dance in a Suitcase</a></h3>
						<p>Dance in a Suitcase is a student-run organization designed to expose BFA Dance Majors at the University of Florida to a wide range of learning environments and professional experiences. This organization strives to expose the University of Florida School of Theater and Dance students to other schools, prospective students and educational opportunities outside the city of Gainesville. We aim to challenge the barriers that segregate this country&rsquo;s diverse artistic communities and foster innovative partnerships that reduce the geographical and cultural isolation of artists from their peers and public communities. Our goal is to bring in guest artists and travel to dance festivals around the country in order to enhance our education and awareness of the entire dance field.</p>
					</li>
					<!--<li>
						<h3><a href="http://www.facebook.com/group.php?gid=152097864756#/group.php?gid=152097864756&amp;v=info">Fight Club</a></h3>
						<p>The purpose of Fight Club is to learn and teach proper and safe techniques for stage combat and to raise awareness of the role stage combat plays in the unfolding of a dramatic narrative. We define stage combat is an artistic presentation of violence that &nbsp;is designed to be safe for the performers and to aid in telling the story of the play or theatrical event. Any interested, enrolled University of Florida student may be a member.</p>
					</li>-->
					<li>
						<h3><a href="//www.facebook.com/groups/173081640926/">Volaticus - Aerial Dance Club</a></h3>
						<p>Volaticus is a Latin derivative for &ldquo;take flight&rdquo;; fitting. Volaticus was established to help foster an atmosphere that allows freedom of artistic expression in the form of Aerial Dance. This program  recognizes the goal to bring art above the ground in a new era and does  so with teaching techniques on the silk and trapeze. This environment resembles a &ldquo;melting pot&rdquo; including dance, art, theatre, digital design, music, etc. The organization will choreograph and put on  performances that allow students to be hands on in every part. Student  have the opportunity to &ldquo;reach new heights&rdquo; in the Aerial Dance student organization.</p>
					</li>
					<li>
						<h3><a href="http://arts.ufl.edu/welcome/td/">UF School of Theatre and Dance</a></h3>
						<p>The fundamental purpose and primary responsibility of the School, through its various degree programs, is the education and training of the next generation of artists, scholars, and teachers, enabling them to compete successfully in the professional world. Education and training are inseparable as the School aims for the closest possible  union between academic and applied knowledge, theory and practice, experience and reflection, within a thoroughly integrated curriculum that is sensitive both to the practical needs of an ever-changing  marketplace and to the intellectual needs of the individual student. Stage and classroom are engaged in constant mutual exchange.</p>
					</li>
				</ul>
			</section>
			<section>
				<h2>Around Gainesville</h2>
				<ul>
					<li>
						<h3><a href="http://www.acrosstown.org">Acrosstown Repertory Theatre</a></h3>
						<p>Since its inception in 1980, the Acrosstown Repertory Theatre has provided Gainesville and Alachua County with a unique and innovative cultural experience. There are many opportunities for exploring acting, writing, directing, and production for many diverse sectors of our community, especially for those who otherwise might not enjoy such opportunities.</p>
					</li>
					<li>
						<h3><a href="http://www.gcplayhouse.org">Gainesville Community Playhouse</a></h3>
						<p>GCP is one of the oldest community theaters in the State of Florida, having produced our first show in 1927. Each year our season consists of six shows - usually three musicals and three dramas or comedies.</p>
					</li>
					<li>
						<h3><a href="http://www.gainesvillejaycees.com">Gainesville Jaycees</a></h3>
						<p>The Gainesville Jaycees is a youth organization dedicated to community service. One of its major fundraisers for charities, the annual Haunted House, is now in its fourteenth year. The Haunted House draws volunteer actors from UF, Santa Fe, and Gainesville's various high schools. Lest you may think that it is a cute event for kids, one of the aims of the House is to provide a local scare-fest comparable to Universal's  Halloween Horror Nights--though not in budget, nevertheless a night of equal terror. Each Halloween, the Haunted House needs dedicated people to build, crew, and terrify the patrons into the wee hours of the night.</p>
					</li>
					<li>
						<h3><a href="http://www.thehipp.org">The Hippodrome State Theatre</a></h3>
						<p>The mission of the Hippodrome is to explore the truth of the human  experience and the human spirit through the examination and  presentation of dramatic work. The theater enjoys recognition as one of the leading regional theaters in the country, with the primary goal of providing the best season of  theater for audiences throughout the state.</p>
					</li>
					<li>
						<h3><a href="http://www.thievesguilde.org/Index2.php">Thieves' Guilde</a></h3>
						<p>The Thieves' Guilde is a volunteer acting troupe that performs at local fairs in Gainesville and specializes in stage combat. They are widely  known for their human chess board, the highlight of each fair. They are dedicated to safe combat techniques, bringing legends to life, and above all, putting on a great show.</p>
					</li>
				</ul>
			</section>
		</article>
		<?php
	}
}
?>