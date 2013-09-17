<?php

class Page{
	var $request = null;

	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Calendar";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<link rel="stylesheet" type="text/css" href="/res/styles/frontierCalendar/jquery-frontier-cal-1.3.2.css" />
		<link type="text/css" href="/res/styles/fp_theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
		<script type="text/javascript" src="/res/scripts/jquery.min.js"></script>
		<script type="text/javascript" src="/res/scripts/jquery-ui-1.8.21.custom.min.js"></script>
		<script type="text/javascript" src="/res/scripts/lib/jshashtable-2.1.js"></script>
		<script type="text/javascript" src="/res/scripts/frontierCalendar/jquery-frontier-cal-1.3.2.min.js"></script>
		<script type="text/javascript" src="/res/scripts/jquery-qtip-1.0.0-rc3140944/jquery.qtip-1.0.js"></script>
		
		<script type='text/javascript'>
		function format_date(d){
			var days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
			var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
			var hour = d.getHours() - 3;
			var ampm = "am";
			if(hour < 11) ampm = "am";
			if(hour >= 12) ampm = "pm";
			
			if(hour == 0) hour = 12;
			else if(hour > 12) hour = hour - 12;
			
			var min = "";
			if(d.getMinutes() < 10) min = "0" + d.getMinutes();
			else min = d.getMinutes();
			return days[d.getDay()] + ", " + d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear() + ", at " + hour + ":" + min + " " + ampm;
		}
		function this_month(){
			var d = new Date();
			var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
			return months[d.getMonth()] + " " + d.getFullYear();
		}
		
		$(document).ready(function(){	
			/**
			 * Initializes calendar.
			 */
			var jfcalplugin = $("#calendar").jFrontierCal({
				date: new Date(),
				applyAgendaTooltipCallback: myApplyTooltip,
				dragAndDropEnabled: false
			}).data("plugin");
			jfcalplugin.loadICalSource("#calendar","/getcalendar","text");
			$("#dateSelect").val(this_month());
			/**
			 * Use reference to plugin object to a specific year/month
			 */
			$("#dateSelect").bind('change', function() {
				var selectedDate = $("#dateSelect").datepicker('getDate');
				var dtArray = selectedDate.split("-");
				var year = selectedDate.getFullYear();
				// jquery datepicker months start at 1 (1=January)		
				var month = selectedDate.getMonth();
				// strip any preceeding 0's		
				//month = month.replace(/^[0]+/g,"")		
				//var day = dtArray[2];
				// plugin uses 0-based months so we subtrac 1
				//jfcalplugin.showMonth("#calendar",year,parseInt(month-1).toString());
				jfcalplugin.showMonth("#calendar",year,month);
			});	
			/**
			 * Initialize previous month button
			 */
			$("#BtnPreviousMonth").button();
			$("#BtnPreviousMonth").click(function() {
				jfcalplugin.showPreviousMonth("#calendar");
				var calDate = jfcalplugin.getCurrentDate("#calendar");
				calDate.setDate(1); //Bad things happen on the 31st of each month if this isn't here. Thank god I was testing this on July 31st. 
				$("#dateSelect").datepicker('setDate',calDate);
				return false;
			});
			/**
			 * Initialize next month button
			 */
			$("#BtnNextMonth").button();
			$("#BtnNextMonth").click(function() {
				jfcalplugin.showNextMonth("#calendar");
				var calDate = jfcalplugin.getCurrentDate("#calendar");
				calDate.setDate(1); //Bad things happen on the 31st of each month if this isn't here. Thank god I was testing this on July 31st. 
				$("#dateSelect").datepicker('setDate',calDate);
				return false;
			});
			$("#dateSelect").datepicker( {
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				dateFormat: 'MM yy',
				beforeShow: function(input, inst) {
					var dateString = $(this).val();
					var options = new Object();
					if (dateString.length > 0) {
						options.defaultDate = $.datepicker.parseDate("dd-" + $(this).datepicker("option", "dateFormat"), "01-" + dateString);
					}
					inst.dpDiv.addClass("ui-monthpicker");
					return options;
				},
				onClose: function(dateText, inst) {
					var month = inst.dpDiv.find(".ui-datepicker-month").val();
					var year = inst.dpDiv.find(".ui-datepicker-year").val();
					$(this).datepicker("setDate", new Date(year, month, 1));
					inst.dpDiv.removeClass("ui-monthpicker");
					jfcalplugin.showMonth("#calendar",year,month);
				},
			});
			
		});
		function myApplyTooltip(divElm,agendaItem){

			// Destroy currrent tooltip if present
			if(divElm.data("qtip")){
				divElm.qtip("destroy");
			}
			
			var displayData = "";
			
			var title = agendaItem.title;
			var startDate = agendaItem.startDate;
			var endDate = agendaItem.endDate;
			var allDay = agendaItem.allDay;
			var data = agendaItem.data;
			displayData += "<b>" + title+ "</b><br />";
			if(allDay){
				displayData += "(All day event)<br /><br />";
			}else{
				displayData += format_date(startDate) + "<br /><br />";
			}
			/*for (var propertyName in data) {
				displayData += "<b>" + propertyName + ":</b> " + data[propertyName] + "<br>"
			}*/
			
			var desc = data["DESCRIPTION"].replace(/\\n/gi,"<br />").replace(/\\,/g,",");
			
			displayData += "<strong>Location:</strong> " + data["LOCATION"] + "<br />";
			displayData += "<strong>Description:</strong> " + desc + "<br />";
			// apply tooltip
			divElm.qtip({
				content: displayData,
				position: {
					corner: {
						tooltip: "bottomMiddle",
						target: "topMiddle"			
					},
					adjust: { 
						mouse: true,
						x: 0,
						y: -15
					},
					target: "mouse"
				},
				show: { 
					when: { 
						event: 'mouseover'
					}
				},
				style: {
					border: {
						width: 5,
						radius: 10
					},
					padding: 10, 
					textAlign: "left",
					tip: true,
					name: "dark",
					fontFamily: "OpenSansRegular",
					fontSize: 12
				}
			});

		};
		</script>
		<style type="text/css">
		#ui-datepicker-div.ui-monthpicker {
			padding: 0.2em;
		}
		#ui-datepicker-div.ui-monthpicker .ui-datepicker-calendar {
			display: none;
		}
		#toolbar{
			border: 0px solid #000;
		}
		#toolbar button, #toolbar input{
			border: 2px solid #6A6A6A;
			font-family: AurulentSansRegular;
			font-size: 14px;
			padding: 3px 3px 5px;
		}
		#toolbar input{ padding: 3px 3px 5px; }
		#toolbar button{ padding: 3px 7px 5px; font-weight: normal; }
		#toolbar .ui-button-text-only .ui-button-text{ padding: 0 !important; }
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
		<div id="toolbar" class="ui-widget-header ui-corner-all" style="padding:3px; vertical-align: middle; white-space:nowrap; overflow: hidden;">
			<button id="BtnPreviousMonth">Previous Month</button>
			<button id="BtnNextMonth">Next Month</button>
			&nbsp;&nbsp;&nbsp;
			Date: <input type="text" id="dateSelect" size="20" value="" />
		</div>
		<div id="calendar"></div>
		<!-- CONTENT HERE -->
		<?php
	}
}
?>