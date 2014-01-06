<?php
require_once('./svr/res/magpierss/rss_fetch.inc');
require_once('./svr/res/gcal.php');
class Page{
	var $request = null;
	var $rssFeed = null;
	var $calFeed = null;
	function Page($request = array()){
		$this->request = $request;
	}

	//Return string containing the page's title. 
	function getTitle(){
		echo "Home";
	}

	//Page specific content for the <head> section.
	function customHead(){?>

			<!--<link href="/res/styles/orbit.css" rel="stylesheet" type="text/css">-->
			<link href="/res/styles/nivo/nivo-slider.css" type="text/css" rel="stylesheet" />
			<link href="/res/styles/nivo/themes/default/default.css" type="text/css" rel="stylesheet" />
			<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
			<!--<script src="/res/scripts/jquery.orbit.min.js" type="text/javascript"></script>-->
			<script src="/res/scripts/jquery.nivo.slider.pack.js" type="text/javascript"></script>
			<!--<script src="/res/scripts/jquery.tweetable.js" type="text/javascript"></script>-->
			<script type="text/javascript">
				$(window).load(function() {
					/*$('#slideshow').orbit({
						animation: 'horizontal-push',               // fade, horizontal-slide, vertical-slide, horizontal-push
						animationSpeed: 800,             // how fast animations are
						timer: true,                     // true or false to have the timer
						advanceSpeed: 4000,              // if timer is enabled, time between transitions
						pauseOnHover: false,             // if you hover pauses the slider
						startClockOnMouseOut: false,     // if clock should start on MouseOut
						startClockOnMouseOutAfter: 1000, // how long after MouseOut should the timer start again
						directionalNav: true,            // manual advancing directional navs
						captions: true,                  // do you want captions?
						captionAnimation: 'slideOpen',        // fade, slideOpen, none
						captionAnimationSpeed: 800,      // if so how quickly should they animate in
						bullets: false,                  // true or false to activate the bullet navigation
						afterSlideChange: function(){}   // empty function
					});*/
					$('#slider').nivoSlider({
						effect: 'sliceDown,slideInLeft', // Specify sets like: 'fold,fade,sliceDown'
						slices: 15, // For slice animations
						boxCols: 8, // For box animations
						boxRows: 4, // For box animations
						animSpeed: 700, // Slide transition speed
						pauseTime: 5000, // How long each slide will show
						startSlide: 0, // Set starting Slide (0 index)
						directionNav: true, // Next & Prev navigation
						controlNav: false, // 1,2,3... navigation
						controlNavThumbs: false, // Use thumbnails for Control Nav
						pauseOnHover: true, // Stop animation while hovering
						manualAdvance: false, // Force manual transitions
						prevText: 'Prev', // Prev directionNav text
						nextText: 'Next', // Next directionNav text
						randomStart: false, // Start on a random slide
						beforeChange: function(){}, // Triggers before a slide transition
						afterChange: function(){}, // Triggers after a slide transition
						slideshowEnd: function(){}, // Triggers after all slides have been shown
						lastSlide: function(){}, // Triggers when last slide is shown
						afterLoad: function(){} // Triggers when slider has loaded
					});
					//$('#tweets').tweetable({username: 'florida_players',limit: 3,time: true});
				});
			</script>
			<style type="text/css">
				.nivo-controlNav{
					padding: 0 !important;
				}
				.nivoSlider {
					position:relative;
					background:url("/res/styles/nivo/themes/default/loading.gif") no-repeat 50% 50%;
					height: 300px;
				}
				.nivoSlider img {
					position:absolute;
					top:0px;
					left:0px;
					display:none;
				}
				.nivoSlider a {
					border:0;
					display:block;
				}
			</style>
			<style type="text/css">
				div.orbit{
					width: 900px;
					height: 300px;
				}
				
				#news h2{
					margin-bottom: 10px;
				}
				article.newsItem {
					border-bottom: 1px solid #EEEEEE;
					margin: 0 0 10px;
					padding: 0;
				}
				.newsItem span.newsPublished{
					font-size: 10px;
					position: relative;
					top: -4px;
				}
				.newsItem h3{
					font-family: ColaboratethinRegular;
					font-size: 20px;
					margin: 0;
				}
				.newsItem h3 a{
					color: #606060;
				}
				.newsItem p:first-of-type{
					margin-top: 6px;
				}
				.newsItem p, .newsItem ul{
					color: #6A6A6A;
					font-family: OpenSansRegular;
					font-size: 14px;
				}
				
				
				/* twitter styles */
				small {font-style:italic; }

				#tweets {
					width:220px;
					background: #ccc none repeat;
					-moz-border-radius: 0 0 6px 6px;
					border-radius: 0 0 6px 6px; 
					padding: 0 10px;
				}

				#tweets li {
					padding: 5px;
					color:#606060;
					border-bottom:1px solid #e0e0e0;
					border-top:1px solid #c0c0c0;
					line-height:150%;
				}

				#tweets li.tweet_content_0 {
					border-top:0px none;
				}
				#tweets li:last-child{
					border-bottom:0px none;	
				}

				#tweets .hash { color:#FFF; } 
				#tweets .reply { color:#FFF; } 
				
				#tweets p{
					margin: 0;
					font-size: 13px;
				}
				
				ul.tweetList{
					list-style: none outside none;
					padding: 0;
				}
				h3#tweetHeader, #upcomingEvents h3{
					border-radius: 6px 6px 0 0;
				}
				
				#eventsList li{
					padding: 5px;
					color:#606060;
					border-bottom:1px solid #e0e0e0;
					border-top:1px solid #c0c0c0;
					line-height:150%;
					font-size: 13px;
				}
				#eventsList{
					width: 220px;
					background: #ccc none repeat;
					-moz-border-radius: 0 0 6px 6px;
					border-radius: 0 0 6px 6px; 
					padding: 0 10px !important;
					display: block;
				}
				#eventsList .eventDesc{
					margin: 0;
					font-size: 13px;
				}
				#eventsList .eventTime{
					font-size: 10.5px;
					display: block; 
				}
				#eventsList .eventTitle{
					display: block;
					font-weight: bold;
				}
				
				#eventsList .noEvents{
					display: none;
				}
				#eventsList .loadingEvents{
					height: 60px;
					text-align: center;
				}
				#eventsList .loadingEvents .loading{
					margin: 10px auto 0;
					width: 30px;
				}
			</style>
			<script type='text/javascript'>
				$(document).ready(function(){
					eventsList = $('#eventsList');
					$.ajax('getevents',{
						cache: false,
						success: function(data, status, jqXHR){
							events = data.events;
							
							if(events != null && events.length > 0){
								eventsList.empty();
								
								var tempLi;
								//for(var event in events){
								$.each(events, function(key,event){
									tempLi = $('<li></li>');
									eventTitle = $('<a></a>').addClass('eventTitle').html(event.name).attr({'href':event.url,'target':'_blank'});
									tempLi.append(eventTitle);
									eventTime = $('<span></span>').addClass('eventTime').html(event.time);
									tempLi.append(eventTime);
									eventLocation = $('<span></span>').addClass('eventLocation').html(event.location);
									tempLi.append(eventLocation);
									
									eventsList.append(tempLi);
								});
							}
							else{
								$('.loadingEvents',eventsList).remove();
								$('.noEvents',eventsList).show();
							}
						},
						error: function(data, status, jqXHR){
							$('.loadingEvents',eventsList).remove();
							$('.noEvents',eventsList).show();
						}
					});
								
				});
			</script>
			<link href="/res/styles/loading-30.css" type="text/css" rel="stylesheet" />
	<?php
	}
	
	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		define('MAGPIE_CACHE_DIR', './svr/cache');
		$this->rssFeed  = fetch_rss('http://blog.floridaplayers.org/rss');
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
					<div id="slideshow-container">
						<div class="slider-wrapper theme-default">
							<div id="slider" class="nivoSlider">
								<!-- <a href="http://floridaplayers.org/tickets"><img src="/res/images/header/altruists.png" alt=""/></a> -->
								<a href="http://floridaplayers.org/show/mercy-seat"><img src="/res/images/header/chatroom-header.png" alt="Chatroom"/></a>
								<a href="http://floridaplayers.org/show/seminar"><img src="/res/images/header/antigone-header.png" alt="Antigone"/></a>
								<a href="http://floridaplayers.org/show/nwf-f13"><img src="/res/images/header/spring-awakening-header.png" alt="Spring Awakening"/></a>
								<a href="http://floridaplayers.org/show/jeffrey"><img src="/res/images/header/jeffrey.png" alt=""/></a>
								<a href="http://floridaplayers.org/show/picasso"><img src="/res/images/header/picasso.png" alt="" /></a>
								<a href="http://floridaplayers.org/show/iceland"><img src="/res/images/header/iceland.png" alt="" /></a>
							</div>
						</div>
						<div id="htmlcaption" class="nivo-html-caption">
							<strong>This</strong> is an example of a <em>HTML</em> caption with <a href="#">a link</a>.
						</div>
						<!--<div id="slideshow">
							<img src="/res/images/header/new_website.png" />
							<div class="textslide" style="">
								<h1>Orbit does content now.</h1>
							</div>
							<img src="/res/images/sample/pic1.jpg" data-caption="#htmlCaption"/>
							<img src="/res/images/sample/pic2.jpg" />
							<img src="/res/images/sample/pic3.jpg" />
							<img src="/res/images/sample/pic4.jpg" />
							<img src="/res/images/sample/pic5.jpg" />
							<img src="/res/images/sample/pic6.jpg" />
							<img src="/res/images/sample/pic7.jpg" />
						</div>
						<span class="orbit-caption" id="htmlCaption">I'm a badass caption</span>-->
						
					</div>
					<aside>	
						<section id="upcomingEvents">
							<h3>Upcoming events</h3>
							<ul id="eventsList">
								<li class="noEvents">No upcoming events at this time.  See <a href="/calendar">the calendar</a> for future happenings.</li>
								<li class="loadingEvents">
									<span>Loading events...</span>
									<div class="loading windows8">
										<div class="wBall" id="wBall_1">
											<div class="wInnerBall"></div>
										</div>
										<div class="wBall" id="wBall_2">
											<div class="wInnerBall"></div>
										</div>
										<div class="wBall" id="wBall_3">
											<div class="wInnerBall"></div>
										</div>
										<div class="wBall" id="wBall_4">
											<div class="wInnerBall"></div>
										</div>
										<div class="wBall" id="wBall_5">
											<div class="wInnerBall"></div>
										</div>
									</div>
									
								</li>
							</ul>
						</section>
						<!--<section>
							<h3 id="tweetHeader"><a href="http://www.twitter.com/florida_players" title="Follow us on Twitter!">@florida_players</a></h3>
							<div id="tweets"></div>
						</section>-->
						
					</aside>
					<article>
						<section>
							<h2>Welcome!</h2>
							<p>Florida Players is a student-run theater company that provides opportunities for students to explore the world of theatre and showcase their works in doing so. Florida Players is open to all University of Florida students, regardless of their major and is sponsored by UF Student Government. Florida Players offers students opportunities in all aspects of theatre, including direction, design, performance, and playwriting, as well as leadership positions.</p>
						</section>
						<section>
						</section>
						<section id="news">
							<h2>Recent news</h2>
							<?php
							//echo 'Site: ', $this->rssFeed->channel['title'], '<br />';
							$max = 3; $x = 0;
							foreach ($this->rssFeed->items as $item ) {
								$title = $item['title'];
								$url   = $item['link'];
								$body = $item['summary'];
								$published = $item['date_timestamp'];
								$date = date("l, j F Y, \a\\t g:i a",$published);
								echo "<article class=\"newsItem\"><h3><a href=$url>$title</a></h3>$body<span class=\"newsPublished\">Published on $date</span></article>";
								$x += 1;
								if($x > $max) break;
							}
							?>
							<span id="moreNews"><a href="<?php echo $this->rssFeed->channel['link']; ?>">More news</a></span>
						</section>
					</article>
					<div class="clear"></div>
		<?php
	}
}
?>