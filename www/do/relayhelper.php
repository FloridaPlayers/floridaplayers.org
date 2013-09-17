<?php
function relay_create_file_name($file){
	$pi = pathinfo($file);
	$name = $pi['filename'];
	return 'relay_for_life_'.uniqid().'_'.preg_replace('/[^\w\d-]/','_',$name);
}

function relay_insert_image(&$sql,$name,$ext){
	$h = get_image_handlers($ext);
	if(!$h) return false;
	$extension = $h['ext'];
	$count = $sql->exec("INSERT INTO images(img_file,img_ext,album_id) VALUES ('$name','$extension','17')");
	if($count !== 0){
		return $sql->lastInsertId();
	}
	return false;
}

function relay_upload(&$sql,&$image,$img_type,&$error){
	$img_name = relay_create_file_name($_FILES['file']['name']);
	$img_id = relay_insert_image($sql,$img_name,$img_type); 
	if($img_id === false){
		$error = 'Unable to save image to database.';
		return false;
	}
	else{
		$saved = true;
		$saved = $saved & create_thumbnail_square($image,UPLOAD_DIR,$img_name,"_s",$img_type,IMAGE_SQUARE_THUMB_WIDTH,false);
		$saved = $saved & create_thumbnail($image,UPLOAD_DIR,$img_name,"_th",$img_type,IMAGE_THUMB_WIDTH,false);
		$saved = $saved & create_thumbnail_square($image,UPLOAD_DIR,$img_name,'_sq',$img_type,300);
		$saved = $saved & create_thumbnail($image,UPLOAD_DIR,$img_name,"_l",$img_type,IMAGE_LARGE_WIDTH,false);
		$saved = $saved & resave_original($image,UPLOAD_DIR,$img_name,"_o",$img_type);
		if(!$saved){
			$error = 'Unable to save image files.';
			delete_image($img_id);
			return false;
		}
		else{
			return $img_id;
		}
	}
	return false;
}

?>