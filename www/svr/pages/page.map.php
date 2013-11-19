<?php

class Page{
	var $request = null;

	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Maps";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
		<script charset="UTF-8" type="text/javascript" src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0"></script>
		<style type="text/css">
			#map_div{
				position: absolute;
				width: 720px;
				height: 500px;
			}
			article{
				height: 500px;
			}
			
			.blocklink{
				display: inline-block;
				width: 120px;
				height: 120px;
				margin: 10px 0 0 0;
			}
			.blocklink span{
				color: #ffffff !important;
				font-family: opensansregular;
				font-size: 12px;
				padding: 100px 8px 0;
				display: block;
			}
			
			.blocklink{ background: #3B5998 url('/res/images/icons/magnify-icon.png') no-repeat scroll top left; }
			
			/* map styles */
			.MicrosoftMap .Infobox .infobox-title{
				color: #3366BB !important;
				font-size: 18px !important;
				font-weight: normal !important;
				font-family: OpenSansRegular !important;
			}
			.MicrosoftMap .OverlaysTL > div:not(.NavBar_modeSelectorControlContainer){
				background-color: #fff !important;
			}
			.NavBar_modeSelectorControlContainer .NavBar_dropIconContainer, .NavBar_modeSelectorControlContainer .NavBar_typeButtonLabel{
				color: #5077BB !important;
			}
			.MicrosoftMap .Infobox{
				box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.5) !important;
			}
			.NavBar_modeSelectorControlContainer{	
				background: url('/res/images/layout/map-menu-right.png') no-repeat scroll top right transparent !important;
			}
			/*.MicrosoftMap img[src$="ecn.dev.virtualearth.net/mapcontrol/v7.0/i/BingTheme/pins/pin_userdark.png"] ~ div{
				cursor: pointer;
			}*/
		</style>
		<script type="text/javascript">
			var map, nadineInfobox, phillipsInfobox;
			function showNadine(){
				if(map != null && nadineInfobox != null){
					var location = new Microsoft.Maps.Location(29.646120,-82.346651);
					map.setView({center: location,zoom: 17});
					nadineInfobox.setOptions({ visible:true });
				}
			}
			function showPhillips(){
				if(map != null && phillipsInfobox != null){
					var location = new Microsoft.Maps.Location(29.635391,-82.36927);
					map.setView({center: location,zoom: 17});
					phillipsInfobox.setOptions({ visible:true });
				}
			}
			function addPins(){
				
				var locationa = new Microsoft.Maps.Location(29.646120,-82.346651);
				var nadinepin = new Microsoft.Maps.Pushpin(locationa, {text: '2'}); 
				
				nadineInfobox = new Microsoft.Maps.Infobox(nadinepin.getLocation(), 
					{title: 'Nadine McGuire Theatre and Dance Pavillion', 
					 description: 'The Nadine McGuire Theatre and Dance Pavillion is home to the Constans Theatre mainstage, the black box, as well as several other studios.', 
					 visible: false, 
					 width: 350,
					 offset: new Microsoft.Maps.Point(0,25)});

				Microsoft.Maps.Events.addHandler(nadinepin, 'click', function(){ nadineInfobox.setOptions({ visible:true });});
				Microsoft.Maps.Events.addHandler(map, 'viewchange', function(){ nadineInfobox.setOptions({ visible:false });});
				map.entities.push(nadinepin);
				map.entities.push(nadineInfobox);
				
				var locationb = new Microsoft.Maps.Location(29.635391,-82.36927);
				var phillipspin = new Microsoft.Maps.Pushpin(locationb, {text: '1'}); 
					
				phillipsInfobox = new Microsoft.Maps.Infobox(phillipspin.getLocation(), 
					{title: 'Phillips Center for the Performing Arts', 
					 description: 'The Phillips Center for the Performing Arts is home to the mainstage and black box theatres.', 
					 visible: false, 
					 width: 360,
					 offset: new Microsoft.Maps.Point(0,25)});

				Microsoft.Maps.Events.addHandler(phillipspin, 'click', function(){ phillipsInfobox.setOptions({ visible:true });});
				Microsoft.Maps.Events.addHandler(map, 'viewchange', function(){ phillipsInfobox.setOptions({ visible:false });});
				map.entities.push(phillipspin);
				map.entities.push(phillipsInfobox);
			}
			function GetMap(){
				var mapOptions = {
					credentials: "Ar141tGozqfl8-gOtha0ZvuHI1T0rAAufYHjeadOmEsrnICmodGks7et4Mi8ZVXu",
					center: new Microsoft.Maps.Location(29.643315, -82.358024),
					mapTypeId: Microsoft.Maps.MapTypeId.aerial,
					zoom: 15
				}
				map = new Microsoft.Maps.Map(document.getElementById("map_div"), mapOptions);
				addPins();
				<?php
				if($this->has_input_at(0)){
					$requested_location = strtolower($this->get_input_at(0));
					global $THEATER_LOCATIONS;
					if($requested_location == $THEATER_LOCATIONS['1']['short']) echo "showPhillips();";
					elseif($requested_location == $THEATER_LOCATIONS['2']['short']) echo "showNadine();";
				}
				?>
			}
			function displayInfobox(e)
			 {
				pinInfobox.setOptions({ visible:true });
			 }                    

			 function hideInfobox(e)
			 {
				pinInfobox.setOptions({ visible: false });
			 }
			
			$(document).ready(function(){
				GetMap();
				$("#body").each(function() {
					var obj = $(this);
					var objHeight = 0;
					$.each($(this).children(':not(.MicrosoftMap *):not(aside)'), function(){
						objHeight += $(this).outerHeight();
					});
					$(obj).height(objHeight);
				});
				$('#phillips_link').click(function(){ showPhillips(); return false; });
				$('#nadine_link').click(function(){ showNadine(); return false; });
			});

		</script>
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
		<h1>Map and Locations</h1>
		<aside class="small">
			<ul>
				<li><a href="/map/squitieri" class="blocklink" id="phillips_link" title="Phillips Center location"><span>Phillips Center</span></a></li>
				<li><a href="/map/nadine" class="blocklink" id="nadine_link" title="Nadine McGuire Theatre location"><span>Nadine McGuire</span></a></li>
			</ul>
		</aside>
		<article>
			<div id='map_div'></div>
		</article>
		<?php
	}
	
	function has_flag($pos,$flag){
		return (isset($this->request) && isset($this->request["flags"][$pos]) && $this->request["flags"][$pos] == $flag) === true;
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