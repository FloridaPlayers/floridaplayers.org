<?php
class Page{
	var $request = null;
	function Page(){
	}
	function Page($request){
		$this->request = $request;
	}
	//Return string containing the page's title. 
	function getTitle(){
		echo "A test page";
	}

	//Page specific content for the <head> section.
	function customHead(){?>

			<link rel="stylesheet" href="/res/styles/orbit.css">
			<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
			<script src="/res/scripts/jquery.orbit.min.js" type="text/javascript"></script>
			<script type="text/javascript">
				$(window).load(function() {
					$('#slideshow').orbit({
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
					});
				});
			</script>
			<style type="text/css">
				div.orbit{
					width: 900px;
					height: 300px;
				}
			</style>
	<?php
	}


	//Return nothing; print out the page. 
	function getContent(){
		?>
		<article>
			<section>
				<h1>An article!<h1>
				<p>A GLUEEE GLUUUEEE GLUEE</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam convallis convallis erat id molestie. Suspendisse nec magna non erat sodales consequat. Nam a metus leo, a egestas turpis. Etiam ac lorem placerat sem bibendum pellentesque. Vestibulum id feugiat diam. Curabitur at adipiscing urna. Pellentesque gravida lobortis volutpat. Vivamus non dui risus, varius pulvinar odio. Suspendisse nec sapien vitae massa mattis congue eget quis sem. Integer sed leo massa. Curabitur risus mauris, accumsan nec ultrices sed, egestas at diam. Donec eleifend, felis in rhoncus venenatis, ipsum ligula tempor tellus, in mollis neque enim sed turpis. Curabitur commodo est lorem, vitae interdum sapien. </p>
			</section>
			<section>
				<h1>An article!<h1>
				<p>A GLUEEE GLUUUEEE GLUEE</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam convallis convallis erat id molestie. Suspendisse nec magna non erat sodales consequat. Nam a metus leo, a egestas turpis. Etiam ac lorem placerat sem bibendum pellentesque. Vestibulum id feugiat diam. Curabitur at adipiscing urna. Pellentesque gravida lobortis volutpat. Vivamus non dui risus, varius pulvinar odio. Suspendisse nec sapien vitae massa mattis congue eget quis sem. Integer sed leo massa. Curabitur risus mauris, accumsan nec ultrices sed, egestas at diam. Donec eleifend, felis in rhoncus venenatis, ipsum ligula tempor tellus, in mollis neque enim sed turpis. Curabitur commodo est lorem, vitae interdum sapien. </p>
			</section>
			<section class="full">
				<h1>An article!<h1>
				<p>A GLUEEE GLUUUEEE GLUEE</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam convallis convallis erat id molestie. Suspendisse nec magna non erat sodales consequat. Nam a metus leo, a egestas turpis. Etiam ac lorem placerat sem bibendum pellentesque. Vestibulum id feugiat diam. Curabitur at adipiscing urna. Pellentesque gravida lobortis volutpat. Vivamus non dui risus, varius pulvinar odio. Suspendisse nec sapien vitae massa mattis congue eget quis sem. Integer sed leo massa. Curabitur risus mauris, accumsan nec ultrices sed, egestas at diam. Donec eleifend, felis in rhoncus venenatis, ipsum ligula tempor tellus, in mollis neque enim sed turpis. Curabitur commodo est lorem, vitae interdum sapien. </p>
			</section>
		</article>
		<?php
	}
}
?>