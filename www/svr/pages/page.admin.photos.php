<?php
require_once "authentication.php";
class Page{
	var $request = null;
	var $sql = null;
	var $usr;
	function Page($request){
		$this->request = $request;
		$this->usr = $GLOBALS['USER']; //Get the user from router.php
		$this->usr->get_info();
		if($this->usr->get_user_info("permissions") < 1){
			header("Location: /home");
			die();
		}
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Photo Manager";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
			<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
			<script src="/res/scripts/multiupload.js" type="text/javascript"></script>
			<script type="text/javascript">
			var config = {
				support : "image/jpg,image/png,image/jpeg,image/gif",		// Valid file formats
				form: "demoFiler",					// Form ID
				dragArea: "dragAndDropFiles",		// Upload Area ID
				uploadUrl: "/do/upload.php",		// Server side upload url
				onComplete: function(uploadedIds){ 
					post_to_url('/admin/photos/edit',{'photos':uploadedIds.toString()},'post');
				}
			}
			$(document).ready(function(){
				initMultiUploader(config);
			});
			
			function post_to_url(path, params, method) {
				method = method || "post"; // Set method to post by default, if not specified.

				// The rest of this code assumes you are not using a library.
				// It can be made less wordy if you use one.
				var form = document.createElement("form");
				form.setAttribute("method", method);
				form.setAttribute("action", path);

				for(var key in params) {
					if(params.hasOwnProperty(key)) {
						var hiddenField = document.createElement("input");
						hiddenField.setAttribute("type", "hidden");
						hiddenField.setAttribute("name", key);
						hiddenField.setAttribute("value", params[key]);

						form.appendChild(hiddenField);
					 }
				}

				document.body.appendChild(form);
				form.submit();
			}
			</script>
			
			<style type="text/css">
			.uploadArea{ min-height:300px; height:auto; border:1px dotted #ccc; padding:10px; cursor:move; margin-bottom:10px; position:relative;}
			.uploadArea h1{ color:#ccc; width:100%; z-index:0; text-align:center; vertical-align:middle; position:absolute; top:25px;}
			.dfiles{ clear:both; border:1px solid #ccc; background-color:#E4E4E4; padding:3px;  position:relative; height:25px; margin:3px; z-index:1; width:97%; opacity:0.6; cursor:default;}
			.invalid { border:1px solid red !important; }
			.buttonUpload { display:inline-block; padding: 4px 10px 4px; text-align: center; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); background-color: #0074cc; -webkit-border-radius: 4px;-moz-border-radius: 4px; border-radius: 4px; border-color: #e6e6e6 #e6e6e6 #bfbfbf; border: 1px solid #cccccc; color:#fff; }
			.progress img{ margin-top:7px; margin-left:24px; }
			.uploadArea h5{ padding:0px; margin:0px; width:95%; line-height:25px; }
			.uploadArea h5, .uploadArea h5 img {  float:left;  }
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
		<h1>Upload Photos</h1>
		<div id="dragAndDropFiles" class="uploadArea">
			<h2>Drop Images Here</h2>
		</div>
		<form name="demoFiler" id="demoFiler" enctype="multipart/form-data">
		<input type="file" name="multiUpload" id="multiUpload" multiple />
		<input type="submit" name="submitHandler" id="submitHandler" value="Upload" class="buttonUpload" />
		</form>
		<div class="progressBar">
			<div class="status"></div>
		</div>
		<?php
	}
}
?>