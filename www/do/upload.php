<?php
error_reporting(0);

require_once "../config.php";
define('UPLOAD_DIR','.'.IMAGE_UPLOAD_DIR); 
//The config is stored relative to the root as './res/uploads/', so this is adding a period
//so as to go down a directory because of the location of this script (in "./do/"). 

function create_file_name($file){
	$pi = pathinfo($file);
	$name = $pi['filename'];
	return 'fp_'.uniqid().'_'.preg_replace('/[^\w\d-]/','_',$name);
}
function validate_uploaded_image($file){
	$error = false;
	$image = null;
	$ext = null;
	if(!empty($file)){ //The photo was uploaded
		if($file['error'] != UPLOAD_ERR_OK){
			$error = 'Server encountered an error while attempting to upload your Photo.';
		}
		elseif(!is_uploaded_file($file['tmp_name'])){ //Make sure the file was actually uploaded, not something from the local filesystem
			$error = 'The photo specified is not valid.';
		}
		else{ //Load the file into memory to validate further
			$image_info = @getimagesize($file['tmp_name']);
			if(!$image_info){
				$error = 'The photo specified is not valid.';
			}
			else{
				$image_type = $image_info[2];
				$image_handler = get_image_handlers($image_type);
				if(!$image_handler){
					$error = 'The photo is of an unsupported file type.';
				}
				else{
					$image = call_user_func($image_handler['loader'], $file['tmp_name']);
					$ext = $image_type; 
					if($image === FALSE){
						$error = 'Unable to open image.';
					}
				}
			}
		}
	}
	return array('error'=>$error,'image'=>&$image,'ext'=>$ext);
}
//* img: source image, already loaded using imagecreatefrom(jpeg/gif/png)
//* img_dir: directory to save new image (ex: "img_dir/")
//* img_name: name of new image (ex: "fp_0001")
//* img_suffix: suffix for thumb (ex: "_th");
//* img_type: valid IMAGETYPE_JPEG/_GIF/_PNG
//* img_width: width of new thumbnail
//* upscale: true/false - If image width is less than img_width, enlarge it (true). 
//* file will be "img_dir/fp_0001_th.[ext]"
function create_thumbnail(&$img,$img_dir,$img_name,$img_suffix,$img_type,$img_width,$upscale = false){
	$handler = get_image_handlers($img_type);
	if(!$handler) return false;
	//$image = call_user_func($handler['loader'], $file); //Calls imagecreatefromjpeg/gif/png
	$old_x = imagesx($img);
	$old_y = imagesy($img);
	
	if($old_x >= $img_width || ($old_x < $img_width && $upscale)){
		$new_width = $img_width;
		$new_height = floor($old_y * ($new_width / $old_x));
	}
	else{
		$new_width = $old_x;
		$new_height = $old_y;
	}
	

	$new_image = imagecreatetruecolor($new_width,$new_height);
	if($img_type == IMAGETYPE_PNG){
		imagealphablending($new_image, false);
		imagesavealpha($new_image, true);
	}
	imagecopyresampled($new_image,$img,0,0,0,0,$new_width,$new_height,$old_x,$old_y);
	
	$img_ext = $handler['ext'];
	$save = call_user_func($handler['saver'],$new_image,"{$img_dir}{$img_name}{$img_suffix}{$img_ext}");
	if(!$save){
		return false;
	}
	imagedestroy($new_image);
	//imagedestroy($image); //removed due to reference $img object
	return true;
}
function create_thumbnail_square(&$img,$img_dir,$img_name,$img_suffix,$img_type,$side_length){
	$handler = get_image_handlers($img_type);
	if(!$handler) return false;
	//$image = call_user_func($handler['loader'], $file); //Calls imagecreatefromjpeg/gif/png
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
	
	$new_image = imagecreatetruecolor((int)$new_w,(int)$new_h);
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
	//imagedestroy($image); //removed due to reference $img object
	return true;
}
function resave_original(&$img,$img_dir,$img_name,$img_suffix,$img_type){
	$handler = get_image_handlers($img_type);
	if(!$handler) return false;
	
	if($img_type == IMAGETYPE_PNG){
		imagealphablending($img, false);
		imagesavealpha($img, true);
	}
	
	$img_ext = $handler['ext'];
	if($img_type == IMAGETYPE_GIF){
		$save = call_user_func($handler['saver'],$img,"{$img_dir}{$img_name}{$img_suffix}{$img_ext}");
	}
	else{
		$save = call_user_func($handler['saver'],$img,"{$img_dir}{$img_name}{$img_suffix}{$img_ext}",$handler['max_quality']);
	}
	if(!$save){
		return false;
	}
	return true;
}
function get_image_handlers($image_type){
	$image_handers = array(
		IMAGETYPE_JPEG=>array('loader'=>'imagecreatefromjpeg','saver'=>'imagejpeg','ext'=>'.jpg','max_quality'=>100),
		IMAGETYPE_GIF=>array('loader'=>'imagecreatefromgif','saver'=>'imagegif','ext'=>'.gif'),
		IMAGETYPE_PNG=>array('loader'=>'imagecreatefrompng','saver'=>'imagepng','ext'=>'.png','max_quality'=>9)
	);
	if(array_key_exists($image_type, $image_handers)){
		return $image_handers[$image_type];
	}
	return false; 
}
function database_connect($host,$db,$user,$pwd){
	try {
		$sql = new PDO("mysql:host=$host;dbname=$db", $user, $pwd);
		return array(true,&$sql); //by reference because I -think- it'll help memory usage
    }
	catch(PDOException $e){
		return array(false,$e->getMessage());
    }
}
function insert_image(&$sql,$name,$ext){
	$h = get_image_handlers($ext);
	if(!$h) return false;
	$extension = $h['ext'];
	$count = $sql->exec("INSERT INTO images(img_file,img_ext) VALUES ('$name','$extension')");
	if($count !== 0){
		return $sql->lastInsertId();
	}
	return false;
}
function delete_image(&$sql,$id){
	$count = $sql->exec("DELETE FROM images WHERE img_id='$id' LIMIT 1;");
	return ($count == 1);
}

$error = null;
$success = false;
$image_val = null;
$db_init = database_connect(DB_HOST,DB_NAME,DB_USER,DB_PASSWORD);
if($db_init[0] === true){ //Connection successful
	$sql = &$db_init[1]; //Assign the connection variable
	if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
		//list($error,$image,$ext) = validate_uploaded_image($_FILES['file']);
		//print_r(validate_uploaded_image($_FILES['file']));
		//echo "three<br />";
		$isRelay = isset($_GET['relay']);
		
		$d = validate_uploaded_image($_FILES['file']);
		$error = $d['error'];
		$image = &$d['image'];
		$img_type = $d['ext'];
		if($error === false && $img_type != null){
			if($isRelay){ /* Special functions for Relay for Life photo booth event */
				include 'relayhelper.php';
				$relayReturn = relay_upload($sql,$image,$img_type,$error);
				if($relayReturn !== false){
					$success = true;
					$image_val = $relayReturn; //returns the image_id
				}
			}
			else{
				$img_name = create_file_name($_FILES['file']['name']);
				$img_id = insert_image($sql,$img_name,$img_type); 
				if($img_id === false){
					$error = 'Unable to save image to database.';
				}
				else{
					$saved = true;
					$saved = $saved & create_thumbnail_square($image,UPLOAD_DIR,$img_name,"_s",$img_type,IMAGE_SQUARE_THUMB_WIDTH,false);
					$saved = $saved & create_thumbnail($image,UPLOAD_DIR,$img_name,"_th",$img_type,IMAGE_THUMB_WIDTH,false);
					$saved = $saved & create_thumbnail($image,UPLOAD_DIR,$img_name,"_l",$img_type,IMAGE_LARGE_WIDTH,false);
					$saved = $saved & resave_original($image,UPLOAD_DIR,$img_name,"_o",$img_type);
					if(!$saved){
						$error = 'Unable to save image files.';
						delete_image($img_id);
					}
					else{
						$success = true;
						$image_val = $img_id;
					}
				}
			}
			imagedestroy($image);
		}
	}
	$sql = null; //close the connection
}
else{
	$success = false;
	$error = $db_init[1];
}
echo json_encode(array('success'=>$success,'error'=>$error,'image'=>$image_val,'index'=>((isset($_POST['index']))?$_POST['index']:-1)));
?>