<?php
require_once "authentication.php";
class Page{
	var $request = null;
	var $sql = null;
	var $usr;
	var $errors = null;
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
		echo "Edit photos";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<script src="/res/scripts/jquery.min.js" type="text/javascript"></script>
		<script src="/res/scripts/jquery.customselect.js" type="text/javascript"></script>
		<script src="/res/scripts/custom-radio-checkbox.jquery.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$('select.styled_select').customStyle();
				$('.styled_checkbox').customInput();
				$('#new_album_info_container').hide();
				//$('.errors').hide();
				
				$("#input_existing_album").change(function(){ 
					var message_index;
					message_index = $("#input_existing_album").val(); 
					if(message_index == "_new_album"){
						$('#new_album_info_container').show();
					}
					else{
						$('#new_album_info_container').hide();
					}
				});
			});
			
		</script>
		<style type="text/css">
			table#photo_data_table td{
				vertical-align: top;
			}
			table#photo_data_table td div.checkbox_container, table#photo_data_table td div.custom-checkbox{
				padding: 0 !important;
			}
			table#photo_data_table{
				margin: 0 auto;
			}
			table#photo_data_table td{
				padding: 0 10px;
			}
			table#photo_data_table .custom-checkbox{
				margin-left: auto !important;
				margin-right: auto !important;
				width: 26px;
			}
		</style>
		<style type="text/css">
			#show_bar{
				width: 250px;
				float: left;
			}
			#show_info{
				
			}
			#show_info_container{
				display: none;
				width: 600px;
				float: right;
			}
			.clear{
				width: 10px;
				clear: both;
			}
			div.form-container div{
				padding: 5px 0;
			}
			div.form-container input{
				border: 2px solid #6A6A6A;
				font-family: AurulentSansRegular;
				font-size: 14px;
				padding: 3px;
			}
			div.form-container label{
				color: #6A6A6A;
				font-family: AurulentSansRegular;
				font-size: 15px;
				font-weight: normal;
			}
			div.radio_container{
				margin-left: 120px !important;
			}
			div.form-container label em{
				font-size: 100% !important;
			}
			div.form-container textarea{
				border: 2px solid #6A6A6A;
				font-family: AurulentSansRegular;
				font-size: 13px;
				padding: 3px;
			}
			
			.custom-checkbox, .custom-radio { position: relative; margin-left: 0 !important; }
			/* input, label positioning */
			.custom-checkbox input, .custom-radio input {
				position: absolute;
				left: 2px;
				top: 3px;
				margin: 0;
				z-index: 0;
			}

			.custom-checkbox label, .custom-radio label {
				display: block;
				position: relative;
				z-index: 1;
				font-size: 1.3em;
				padding-right: 1em;
				line-height: 1;
				padding: .5em 0 .5em 30px;
				margin: 0 0 .3em;
				cursor: pointer;
			}
			.custom-checkbox label {
				background: url('/res/images/layout/checkbox.png') no-repeat; 
			}
			.custom-radio label { 
				background: url('/res/images/layout/radio.png') no-repeat; 
			}
			.custom-checkbox label, .custom-radio label {
				background-position: -10px -10px;
			}

			.custom-checkbox label.hover,
			.custom-checkbox label.focus,
			.custom-radio label.hover,
			.custom-radio label.focus {
				background-position: -10px -110px;
			}

			.custom-checkbox label.checked, 
			.custom-radio label.checked {
				background-position: -10px -210px;
			}

			.custom-checkbox label.checkedHover, 
			.custom-checkbox label.checkedFocus {
				background-position: -10px -310px;
			}

			.custom-checkbox label.focus, 
			.custom-radio label.focus {
				outline: 1px dotted #ccc;
			}
			
			
			span.customStyleSelectBox { 
				border: 2px solid #6A6A6A;
				font-family: AurulentSansRegular;
				font-size: 14px;
				padding: 3px 5px 3px 10px;
			} 
			span.customStyleSelectBox.changed { 
				/* background-color: #f0dea4; */
			} 
			.customStyleSelectBoxInner { 
				background:url('/res/images/layout/downarrow.png') no-repeat center right; 
				padding: 0 7px 0 0;
			}
			select:focus ~ span.customStyleSelectBox{
				background-color: #FFC;
				border-color: #FC6;
			}
			.styled_select{
				cursor: pointer;
			}
		</style>
		
		<style type="text/css">

		/* General styles */
		/* body { margin: 0; padding: 0; font: 80%/1.5 Arial,Helvetica,sans-serif; color: #111; background-color: #FFF; } */
		h2 { margin: 0px; padding: 10px; font-family: Georgia, "Times New Roman", Times, serif; font-size: 200%; font-weight: normal; color: #FFF; background-color: #CCC; border-bottom: #BBB 2px solid; }
		p#copyright { margin: 20px 10px; font-size: 90%; color: #999; }

		/* Form styles */
		div.form-container { margin: 10px; padding: 5px; background-color: #FFF; /* border: #EEE 1px solid; */ }

		p.legend { margin-bottom: 1em; }
		p.legend em { color: #C00; font-style: normal; }

		div.errors { margin: 0 0 10px 0; padding: 5px 10px; border: #FC6 2px solid; background-color: #FFC; }
		div.errors p { margin: 0; }
		div.errors p em { color: #C00; font-style: normal; font-weight: bold; }

		div.form-container form p { margin: 0; }
		div.form-container form p.note { margin-left: 170px; font-size: 90%; color: #333; }
		div.form-container form fieldset { margin: 10px 0; padding: 10px; border: #DDD 1px solid; }
		div.form-container form legend { font-weight: bold; color: #666; }
		div.form-container form fieldset div { padding: 0.25em 0; }
		div.form-container label { margin-right: 10px; padding-right: 10px; width: 100px; display: block; float: left; text-align: right; position: relative; }
		div.form-container label.error, 
		div.form-container span.error { color: #C00; }
		div.form-container label em { position: absolute; right: 0; font-size: 120%; font-style: normal; color: #C00; }
		div.form-container input.error { border-color: #C00; background-color: #FEF; }
		div.form-container input:focus,
		div.form-container input.error:focus, 
		div.form-container textarea:focus {	background-color: #FFC; border-color: #FC6; }
		div.form-container div.controlset label, 
		div.form-container div.controlset input { display: inline; float: none; }
		div.form-container div.controlset label.controlset { display: block; float: left; }
		div.form-container div.controlset div { margin-left: 170px; }
		div.form-container div.buttonrow { margin-left: 180px; }

		</style>
	<?php
	}


	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		$this->errors = array();
		if(!isset($_POST['photos']) && !isset($_GET['submit'])){
			header('Location: /admin/photos');
			return false;
		}
		try {
			$photo_ids = $_POST['photos'];
			$match = preg_match('/(\d{1,11},?){0,}(\d{1,11})/',$photo_ids);
			// '/(\d{1,11},?){0,}(\d{1,11})/' Will match '80', '69,70', '1000,2,3', etc with only properly place commas. 
			// {1,11} is based on the current SQL database structure with a max length of 11 for the ID field.
			if($match === 0 || $match === false){
				return false;
			}
			
			$this->sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
			
			if(isset($_GET['submit'])){
				if(!isset($_POST['input_existing_album'])){
					array_push($this->errors,"You must select an album for the photos!");
					return true;
				}
				
				$album_id = $_POST['input_existing_album'];
				if($_POST['input_existing_album'] == '_new_album'){
					
					if(!isset($_POST['input_new_album_name'])){
						array_push($this->errors,"You must enter a name for the new album!");
						return true;
					}
					elseif(!isset($_POST['input_new_album_url'])){
						array_push($this->errors,"You must enter a URL for the new album!");
						return true;
					}
					elseif(!isset($_POST['input_new_album_semester'])){
						array_push($this->errors,"You must select a semester for the album!");
						return true;
					}
					elseif(!isset($_POST['input_new_album_year'])){
						array_push($this->errors,"You must enter a year for the album!");
						return true;
					}
					
					$album_name = $_POST['input_new_album_name'];
					$album_url = strtolower($_POST['input_new_album_url']);
					$album_semester = strtolower(trim($_POST['input_new_album_semester']));
					$album_year = $_POST['input_new_album_year'];
					
					if(!preg_match('/[\w\d-_]{1,35}/',$album_url)){
						array_push($this->errors,"The album URL may only contain alphanumeric characters, underscores and hyphens.");
						return true;
					}
					if(!in_array($album_semester,array('fall','spring','summer'))){
						array_push($this->errors,"You must select a valid semester for the album!");
						return true;
					}
					if(!preg_match('/\d{4}/',$album_year)){
						array_push($this->errors,"You must enter a valid year for the album!");
						return false;
					}
					
					try{
						$count = $this->sql->exec("INSERT INTO albums(album_title,album_url,album_semester,album_year) VALUES ('{$album_name}','{$album_url}','{$album_semester}','{$album_year}')");
						if($count != 1){
							array_push($this->errors,"Unable to create new album.");
							return true;
						}
						else{
							$album_id = $this->sql->lastInsertId();
						}
					}
					catch(PDOException $e){
						array_push($this->errors,"Database error: " . $e->getMessage());
						return true;
					}
				}
				
				$stmt = $this->sql->prepare('UPDATE images SET album_id=:album_id, img_title=:img_title, img_desc=:img_desc, album_thumb=:album_thumb WHERE img_id=:img_id');
				$stmt->bindParam(':album_id', $album_id, PDO::PARAM_INT);
				$stmt->bindParam(':img_title', $img_title, PDO::PARAM_STR);
				$stmt->bindParam(':img_desc', $img_desc, PDO::PARAM_STR);
				$stmt->bindParam(':album_thumb', $album_thumb, PDO::PARAM_BOOL);
				$stmt->bindParam(':img_id', $img_id, PDO::PARAM_INT);
				
				$fetch = $this->sql->prepare('SELECT img_file, img_ext FROM images WHERE img_id=:img_id LIMIT 1');
				$fetch->bindParam(':img_id',$img_id, PDO::PARAM_INT);
				
				foreach($_POST['photo_info'] as $image){
					$img_title = $image['title'];
					$img_desc = $image['desc'];
					$album_thumb = (isset($image['thumb']) && $image['thumb'] == 'active');
					$img_id = $image['id'];
					if(is_numeric($img_id)){
						$stmt->execute();
					}
					if($album_thumb){
						if($fetch->execute() > 0){
							$idata = $fetch->fetch();
							$save_thumb = $this->create_thumbnail_square(IMAGE_UPLOAD_DIR.$idata['img_file'].'_o'.$idata['img_ext'],IMAGE_UPLOAD_DIR,$idata['img_file'],'_sq',$idata['img_ext'],300);
							if(!$save_thumb){
								array_push($this->errors,'Unable to create album thumbnail.');
							}
						}
					}
				}
				
				//header("Location: /admin/photos?success");
				echo 'Images saved successfully! <a href="/home">Home</a> <a href="/admin/photos">Upload more</a>';
				return false;
				
			}
			
		}
		catch(PDOException $e){
			echo $e->getMessage();
			return false;
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
		?>
		<article>
			<?php
			if($this->errors != null && count($this->errors) > 0){
				?> <div id="show_errors" class="errors"> <?php
				foreach($this->errors as $er){
					echo "<p>$er</p>";
				}
				?> </div> <?php
			} 
			?>
			<form name="edit_photos_form" action="/admin/photos/edit?submit" method="post">
				<div class="form-container">
					<input type="hidden" name="photos" value="<?php echo $_POST['photos']; ?>" />
					<fieldset>
						<legend>Album Information</legend>
						<div id="input_container_existing_album">
							<label for="input_existing_album">Album</label>
							<select id="input_existing_album" name="input_existing_album" class="styled_select">
								<option disabled="disabled" selected="selected"></option>
								<option value="_new_album">New Album</option>
								<?php
								$term = null;
								$new_term = false;
								foreach($this->sql->query('SELECT * FROM albums ORDER BY album_year DESC, album_semester DESC') as $album){
									if($album['album_semester'].$album['album_year'] != $term){
										if($term != null) echo '</optgroup>';
										$new_term = true;
										$term = $album['album_semester'].$album['album_year'];
										echo "<optgroup label=\"{$album['album_semester']} {$album['album_year']}\">";
									}
									echo "<option value=\"{$album['album_id']}\">{$album['album_title']}</option>";
								}
								if($term != null) echo '</optgroup>';
								?>
							</select>
						</div>
						<div id="new_album_info_container">
							<div id="input_container_new_album_name">
								<label for="input_new_album_name">Album name</label>
								<input type="text" id="input_new_album_name" name="input_new_album_name" />
							</div>
							<div id="input_container_new_album_name">
								<label for="input_new_album_name">Album URL</label>
								<input type="text" id="input_new_album_url" name="input_new_album_url" maxlength="35" />
								<p class="note">Only alphanumeric characters, underscores and hyphens allowed. 35 characters max.</p>
							</div>
							<div id="input_container_new_album_semester">
								<label for="input_new_album_semester">Semester</label>
								<select id="input_new_album_semester" name="input_new_album_semester" class="styled_select">
									<option value="_null" disabled="disabled" selected="selected"></option>
									<option value="spring">Spring</option>
									<option value="fall">Fall</option>
									<!-- <option value="summer">Summer</option> -->
								</select>
							</div>
							<div id="input_container_new_album_year">
								<label for="input_new_album_year">Year</label>
								<input type="text" id="input_new_album_year" name="input_new_album_year" value="<?php echo date('Y'); ?>" maxlength="4" size="4" />
							</div>
						</div>
					</fieldset>
					<table id="photo_data_table">
						<thead>
							<tr>
								<th>Photo</th>
								<th>Title</th>
								<th>Description</th>
								<th><small>Use as album thumbnail</small></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$photo_ids = $_POST['photos']; //We validated this in the preload
							$x = 0;
							foreach($this->sql->query('SELECT * FROM images WHERE img_id IN ('.$photo_ids.');') as $row){ 
								$alt = ($row['img_title'] == "")?"Image":$row['img_title'];
								?>
								<tr>
									<?php echo "<td><img src=\"/res/uploads/{$row['img_file']}_th{$row['img_ext']}\" alt=\"{$alt}\" /><input type=\"hidden\" name=\"photo_info[{$x}][id]\" value=\"{$row['img_id']}\" /></td><td><input type=\"text\" name=\"photo_info[{$x}][title]\" /></td><td><textarea name=\"photo_info[{$x}][desc]\"></textarea></td>"; ?>
									<td><div class="checkbox_container controlset">
										<input type="checkbox" name="photo_info[<?php echo $x; ?>][thumb]" value="active" class="styled_checkbox" id="photo_thumb_check_<?php echo $x; ?>" />
										<label for="photo_thumb_check_<?php echo $x; ?>"></label>
									</div></td>
								</tr>
								<?php
								$x += 1;
							}
							?>
						</tbody>
					</table>
					<input type="submit" value="Save" />
				</div>
			</form>
		</article>
		<?php
	}
	function create_thumbnail_square($filename,$img_dir,$img_name,$img_suffix,$img_type,$side_length){
		$handler = $this->get_image_handlers($img_type);
		if(!$handler) return false;
		$img = call_user_func($handler['loader'], $filename); //Calls imagecreatefromjpeg/gif/png
		
		$orig_w = imagesx($img);
		$orig_h = imagesy($img);
		
		$new_w = $side_length;
		$new_h = $side_length;
			
		$w_ratio = ($new_w / $orig_w);
		$h_ratio = ($new_h / $orig_h);
		
		if ($orig_w > $orig_h ) {//landscape
			$crop_w = round($orig_w * $h_ratio);
			$crop_h = $new_h;
			$src_x = ceil( ( $orig_w - $orig_h ) / 2 );
			$src_y = 0;
		} elseif ($orig_w < $orig_h ) {//portrait
			$crop_h = round($orig_h * $w_ratio);
			$crop_w = $new_w;
			$src_x = 0;
			$src_y = ceil( ( $orig_h - $orig_w ) / 2 );
		} else {//square
			$crop_w = $new_w;
			$crop_h = $new_h;
			$src_x = 0;
			$src_y = 0;	
		}

		$new_image = imagecreatetruecolor($new_w,$new_h);
		if($img_type == IMAGETYPE_PNG){
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
		}
		imagecopyresampled($new_image,$img,0,0,$src_x,$src_y,$crop_w,$crop_h,$orig_w,$orig_h);
		
		$img_ext = $handler['ext'];
		$save = call_user_func($handler['saver'],$new_image,"{$img_dir}{$img_name}{$img_suffix}{$img_ext}");
		if(!$save){
			return false;
		}
		imagedestroy($new_image);
		imagedestroy($img); 
		return true;
	}
	function get_image_handlers($image_type){
		$image_handers = array(
			'.jpg'=>array('loader'=>'imagecreatefromjpeg','saver'=>'imagejpeg','ext'=>'.jpg','max_quality'=>100),
			'.gif'=>array('loader'=>'imagecreatefromgif','saver'=>'imagegif','ext'=>'.gif'),
			'.png'=>array('loader'=>'imagecreatefrompng','saver'=>'imagepng','ext'=>'.png','max_quality'=>9)
		);
		if(array_key_exists($image_type, $image_handers)){
			return $image_handers[$image_type];
		}
		return false; 
	}
}
?>