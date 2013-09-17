<?php

class Page{
	var $request = null;
	var $isGallery = false;
	var $sql = null;
	var $errorStatus = 0;
	var $errorType = null;
	var $galleryID = 0;
	var $galleryTitle = null;
	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		if($this->galleryTitle != null){
			echo $this->galleryTitle . ' | Gallery';
		}
		else{
			echo "Media";
		}
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<script type="text/javascript" src="/res/scripts/jquery.min.js"></script>
		<?php
		if($this->isGallery){ ?>
			<script type="text/javascript" src="/res/scripts/gallerificPlus.js"></script>
			<script type="text/javascript" src="/res/scripts/jquery.opacityrollover.js"></script>
			<script type="text/javascript" src="/res/scripts/jquery.scrollTo-1.4.2-min.js"></script>
			<link rel="stylesheet" type="text/css" href="/res/styles/gallerificPlus.css" />
			<style type="text/css">
			section.navigation{
				width: 300px !important;
				float: left;
			}
			section.content{
				width: 520px !important;
				float: right;
			}
			
			div.slideshow img{
				max-width: 510px;
			}
			</style>
		<?php 
		}
		else{ ?>
			<style type="text/css">
			section.semester-container{
				padding: 0 !important;
			}
			.sliderlink {
				overflow:hidden;
				/* fix ie overflow issue */
				position:relative;
				box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
				display: inline-block;
				height: 200px;
				margin: 5px;
				width: 200px;
			}
			.sliderlink ul {
				position:relative;
				left:0;
				top:0;
				list-style:none;
				margin:0;
				padding:0;          
			}
			.sliderlink li {
				position: absolute;
				top: 0;
				left: 0;
				z-index: 8;
			}
			.sliderlink li.active {
				z-index:10;
			}
			.sliderlink li.last-active {
				z-index:9;
			}
			.sliderlink li img {
				padding: 0px;
				width: 200px;
				height: 200px;
			}
			.slidera{
				width: 200px;
				height: 200px;
				display: block;
				position: absolute;
				z-index: 20;
			}
			figcaption {
				background-color: rgba(0, 0, 0, 0.4);
				bottom: 0;
				color: #FFFFFF;
				padding: 3px 5px;
				position: absolute;
				width: 100%;
				z-index: 15;
			}
			</style>
			<script type="text/javascript">		
			var active_sliding = null;
			function slideSwitch(slider) {
				if($('li',slider).length > 1){
					var $active = $('li.active',slider);
					if ($active.length == 0) $active = $('li:last',slider);

					var $next =  $active.next().length ? $active.next()
						: $('li:first',slider);

					$active.addClass('last-active');
						
					$next.css({opacity: 0.0})
						.addClass('active')
						.stop().animate({opacity: 1.0}, 500, function() {
							$active.removeClass('active last-active');
						});
				}
			}
			function startSlide(obj){
				if(!$(obj).is(":animated")){
					slideSwitch(obj);
					active_sliding = setInterval( function(){slideSwitch(obj)}, 1500 );
				}
			}
			function endSlide(obj){
				if(active_sliding){
					clearInterval(active_sliding);
				}
			}

			$(function() {
				$('.sliderlink').hover(function(){startSlide(this)},function(){endSlide(this)});
			});
			</script>
		<?php
		}
	}


	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		try{
			$this->sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
			if($this->has_input_at(0)){
				$this->isGallery = true;
				$gallery = $this->get_input_at(0);
				if(preg_match('/[\w\d-_]+/',$gallery)){
					$statement = $this->sql->query('SELECT album_id,album_title FROM albums WHERE album_url=\''.$gallery.'\' LIMIT 1;');
					if($statement->rowCount() == 0){
						$this->errorStatus = 404;
						$this->errorType = "notfound";
						header('HTTP/1.0 404 Not Found');
						return true;
					}
					$result = $statement->fetch();
					$this->galleryID = $result['album_id'];
					$this->galleryTitle = $result['album_title'];
				}
				else{
					$this->errorStatus = 404;
					$this->errorType = "invalid";
					header('HTTP/1.0 404 Not Found');
				}
			}
		}
		catch(PDOException $e){
			$this->sql = false;
			//echo $e->getMessage();
			//return false;
		}
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
		if($this->sql === false){?>
			<article>
				<section>
					<h2>Error</h2>
					<p>Unable to connect to database!</p>
				</section>
			</article>
		<?php }
		if($this->errorStatus == 404){?>
			<article>
				<section>
					<h2>404 - Album not found</h2>
					<p><?php if($this->errorType == "notfound") { echo "Unable to find the requested album!"; } elseif($this->errorType == "invalid") { echo "Invalid album requested"; } else { echo "An unknown error occurred while trying to find that gallery."; } ?></p>
				</section>
			</article>
		<?php }
		if($this->isGallery === true){ ?>
			<article>
				<section id="navigation" class="navigation">
					<ul class="thumbs noscript">
						<?php
						$sql_query = 'SELECT * FROM images WHERE album_id=\''.$this->galleryID.'\';';
						foreach ($this->sql->query($sql_query) as $image)
						{
							if($image['hide_in_album'] == false){
								$display = '/res/uploads/'.$image['img_file'].'_l'.$image['img_ext'];
								$thumb = '/res/uploads/'.$image['img_file'].'_s'.$image['img_ext'];
								$original = '/res/uploads/'.$image['img_file'].'_o'.$image['img_ext'];
								$title = ($image['img_title'] == null)?"":$image['img_title'];
								$desc = ($image['img_desc'] == null)?"":$image['img_desc'];
								echo "<li><a href=\"{$display}\" original=\"{$original}\" title=\"{$title}\" description=\"{$desc}\"><img src=\"{$thumb}\" alt=\"{$title}\" /></a></li>";
							}
						}
						?>
					</ul>
				</section>
				<section id="gallery" class="content">
					<div id="controls" class="controls"></div>
					<div id="slideshow" class="slideshow"></div>
					<div id="details" class="embox">
						<div id="download" class="download"><a id="download-link">Download Original</a></div>
						<div id="image-title" class="image-title"></div>
						<div id="image-desc" class="image-desc"></div>
					</div>
				</section>
				<div class="clear"></div>
			</article>
			<script type="text/javascript">			
			$(document).ready(function() {
				var gallery = $('#gallery').galleriffic('#navigation', {
					delay:                    2000,
					numThumbs:                12,
					preloadAhead:             10,
					imageContainerSel:        '#slideshow',
					controlsContainerSel:     '#controls',
					titleContainerSel:        '#image-title',
					descContainerSel:         '#image-desc',
					downloadLinkSel:          '#download-link',
					fixedNavigation:	       true,
					galleryKeyboardNav:	       true,
					autoPlay:			       false,
					syncTransitions:           true,
					enableTopPager:            false,
					fixedNavigation:           true,
					defaultTransitionDuration: 900,
					onSlideChange:             function(prevIndex, nextIndex) {
						// 'this' refers to the gallery, which is an extension of $('#thumbs')
						this.find('ul.thumbs').children()
							.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
							.eq(nextIndex).fadeTo('fast', 1.0);
					},
					onPageTransitionOut:       function(callback) {
						this.fadeTo('fast', 0.0, callback);
					},
					onPageTransitionIn:        function() {
						this.fadeTo('fast', 1.0);
					}
				});
				var onMouseOutOpacity = 0.67;
				$('ul.thumbs li').opacityrollover({
					mouseOutOpacity:   onMouseOutOpacity,
					mouseOverOpacity:  1.0,
					fadeSpeed:         'fast',
					exemptionSelector: '.selected'
				});
				gallery.onFadeOut = function() {
					$('#details').fadeOut('fast');
				};
				
				gallery.onFadeIn = function() {
					$('#details').fadeIn('fast');
				};
				
				$(window).bind('hashchange', function (e) {
					//$.scrollTo($('#gallery'));
					e.preventDefault();
					return false;
				});
			});
			</script>
			<?php
		}
		else{
			echo '<article>';
			$sql_query = 
			   'SELECT i.img_id, i.album_id, i.img_file, i.img_ext, i.img_title, a.album_title, a.album_url, a.album_year AS album_year, a.album_semester AS album_semester FROM images AS i
				INNER JOIN albums AS a
				ON a.album_id=i.album_id 
				WHERE i.album_thumb=\'1\'
				UNION ALL
				SELECT NULL, album_id, NULL, NULL, NULL, album_title, album_url, album_year, album_semester
				FROM albums
				WHERE album_id NOT IN
						(
						SELECT album_id
						FROM images WHERE album_thumb=\'1\'
						)
				ORDER BY album_year DESC, album_semester ASC, album_id ASC';
			$term = null;
			//$new_term = false;
			$alid = null;
			//$new_al = false;
			foreach($this->sql->query($sql_query) as $album){
				$albumTitle = htmlentities($album['album_title']);
				if($album['album_id'] != $alid && $alid != null){
					echo "</ul><div class=\"clear\"></div></figure>";
				}
				if($album['album_semester'].$album['album_year'] != $term && $term != null){
					echo '</section>';
				}
				if($album['album_semester'].$album['album_year'] != $term){
					$term = $album['album_semester'].$album['album_year'];
					echo '<section class="semester-container"><h2>'.ucwords($album['album_semester']).' '.$album['album_year'].'</h2>';
				}
				if($album['album_id'] != $alid){
					$alid = $album['album_id'];
					echo "<figure class=\"sliderlink\"><figcaption>{$albumTitle}</figcaption><a href=\"/media/{$album['album_url']}\" class=\"slidera\"></a><ul>";
				}

				$img_title = htmlentities(($album['img_title'] == null)?$album['album_title']:$album['img_title']);
				if($album['img_file'] != null){
					echo "<li><img src=\"/res/uploads/{$album['img_file']}_sq{$album['img_ext']}\" alt=\"{$img_title}\" /></li>";
				}
				else{
					echo "<li><img src=\"".NO_SHOW_IMAGE_DEFAULT."\" alt=\"{$img_title}\" /></li>";
				}
			}
			if($alid != null) echo '</ul><div class="clear"></div></div>';
			if($term != null) echo '</section>';
			echo '</article>';
		}
		
	}
	function has_input_at($pos){
		return (isset($this->request) && isset($this->request["flags"][$pos]));
	}
	function get_input_at($pos){
		if(isset($this->request) && isset($this->request["flags"][$pos])) return $this->request["flags"][$pos];
		else return null;
	}
}
?>