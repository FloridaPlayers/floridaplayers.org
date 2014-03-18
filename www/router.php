<?php
require_once "config.php";
require_once "./svr/pages/authentication.php";
require_once "builder.php";

putenv ('TMPDIR=/svr/tmp');
date_default_timezone_set('America/New_York');

error_reporting (E_ALL);

$PageObject = null;
$GlobalUser = new User();

/** 
 * Calls the getTitle() function from page templates, then echos
 * the resulting title for the page's <title> tag. 
 **/
function displayTitle(){
	try{
		global $PageObject;
		if($PageObject != null){
			printf(TITLE_FORMAT,$PageObject->getTitle());
		}
		else{
			printf(TITLE_FORMAT,Page::getTitle());
		}
	}
	catch(Exception $e){
		echo ERROR_TITLE;
	}
}
/** 
 * Check's whether the file specified is valid and exists 
 **/
function is_good_file($file){
	return file_exists($file) && is_file($file);
}
/** 
 * Takes an array of keys and returns a pattern for finding the
 * matching tempate tags. Returned in the form of:
 *   /{(Key1|Key2|...)}/
 * This is used to find the template keys, {Key1}, {key2}, etc.
 **/
function keys_to_tag_pattern($keys,$ignorecase = true){
	$ret = "/{(";
	foreach($keys as $key){
		$ret .= $key . "|";
	}
	$ret = substr($ret,0,-1);
	$ret .= ")}/";
	if($ignorecase) $ret .= "i";
	return $ret;
}
/**
 * Once a valid page file has been imported, display the final webpage.
 * $template: A string containing the default template. 
 * $funcArray: An array with keys that correspond to keys in the template
 * 		and values that correspond to valid functions, or arrays that can
 *		be used in the call_user_func method. 
 *		Example: array("Key" => "myfunc"), will replace the {Key} value in
 *		the template with the output of myfunc. 
 **/
function display_page($template,$funcArray,$request = array()){
	if(!isset($GLOBALS['USER'])){
		global $GlobalUser;
		$GLOBALS['USER'] = $GlobalUser;
	}
	if(!class_exists("Page")){
		not_found();
		return;
	}
	ob_start();
	$page = new Page($request);
	global $PageObject;
	$PageObject = $page;
	if(method_exists($page,"preloadPage")){
		/* See if the page has the preloadPage function.
		 * This will run before anything else if present.
		 * if it returns false, the script will end. */
		$preload = call_user_func(array($page,"preloadPage")); 
		if($preload === false){
			ob_end_flush();
			return; 
		}
	}
	if(method_exists($page,"getOverrideTemplate")){	
		/* See if the page has a template to override the
		 * default template. */
		$newTemplate = call_user_func(array($page,"getOverrideTemplate")); 
		if($newTemplate !== false) $template = $newTemplate;
	}
	$template_keys = array_keys($funcArray);
	$pattern = keys_to_tag_pattern($template_keys);
	//echo $pattern;
	$matches = preg_split($pattern,$template,null,PREG_SPLIT_DELIM_CAPTURE);

	foreach($matches as $part){
		if(array_key_exists($part,$funcArray)){
			if(array_key_exists("args",$funcArray[$part])){
				if(array_key_exists("page",$funcArray[$part]) && $funcArray[$part]["page"] === true){
					call_user_func_array(array($page,$funcArray[$part]["func"]),$funcArray[$part]["args"]);
				}
				else{
					call_user_func_array($funcArray[$part]["func"],$funcArray[$part]["args"]);
				}
			}
			else{
				if(array_key_exists("page",$funcArray[$part]) && $funcArray[$part]["page"] === true){
					call_user_func(array($page,$funcArray[$part]["func"]));
				}
				else{
					call_user_func($funcArray[$part]["func"]);
				}
			}
		}
		else{
			echo $part;
		}
	}
	ob_end_flush();
}

function get_request_hierarchy($get_request){
	//$keys = array_keys($get_request); 
	//$keys = clean_keys($keys);
	
	$get_request = strtolower(preg_replace("#[^\w-_/]#","",$get_request));
	$keys = explode("/",$get_request);
	$keys = clean_keys($keys);
	
	$temp_files = glob(SERVER_PAGE_DIR . "*.[pP][hH][pP]");
	$files = array();
	foreach($temp_files as $file){
		array_push($files,strtolower(basename($file)));
	}
	
	$c = count($keys);
	$file = $keys[0];
	$cut = 1;
	for($i=1;$i<$c;$i++){
		$append = ".{$keys[$i]}";
		if(in_array(LIVE_PAGE_PREFIX . ".{$file}$append.php",$files) || in_array(RESOURSE_PAGE_PREFIX . ".{$file}$append.php",$files)){
			$file .= $append;
		}
		else{
			$cut = $i;
			break;
		}
	}

	if(in_array(LIVE_PAGE_PREFIX . ".{$file}.php",$files)){
		$filename = LIVE_PAGE_PREFIX . ".{$file}.php";
		$resource = false;
	}
	elseif(in_array(RESOURSE_PAGE_PREFIX . ".{$file}.php",$files)){
		$filename = RESOURSE_PAGE_PREFIX . ".{$file}.php";
		$resource = true;
	}
	else{
		not_found();
	}
	
	$hierarchy = array();
	$hierarchy["file"] = $filename;
	$hierarchy["flags"] = array_slice($keys,$cut);
	$hierarchy["sub"] = $keys[0];
	
	return $hierarchy;
	
	// $hierarchy = array();
	// if(count($keys) > 0){
		// if(is_valid_sub($keys[0]) || is_root_file($keys[0])){
			// if(is_valid_sub($keys[0])){
				// if(count($keys) > 1 && is_good_file(SERVER_PAGE_DIR.LIVE_PAGE_PREFIX.'.'.$keys[0].'.'.$keys[1].'.php')){
					// $hierarchy["sub"] = $keys[0];
					// $hierarchy["page"] = $keys[1];
					// $hierarchy["flags"] = array_slice($keys,2);
				// }
				// else if(is_good_file(SERVER_PAGE_DIR.LIVE_PAGE_PREFIX.'.'.$keys[0].'.php')){
					// $hierarchy["page"] = $keys[0];
					// $hierarchy["flags"] = array_slice($keys,1);
				// }
				// else{
					// not_found();
				// }
			// }
			// else{
				// $hierarchy["sub"] = ROOT_PAGE_PREFIX;
				// $hierarchy["page"] = $keys[0];
				// $hierarchy["flags"] = array_slice($keys,1);
			// }
		// }
	// }
	// return $hierarchy;
}
function clean_path($path){
	$matchVal = preg_match('#^(?\'path\'(?:[/a-zA-Z0-9-_])+)(?:.*?)$#',$path,$matches);
	if($matchVal === 0 || $matchVal === false){
		return '/';
	}
	return $matches['path'];
}
function clean_keys($keys){
	$cleaned = array();
	foreach($keys as $key){
		if(($clean_key = validate_input($key)) != false){
			array_push($cleaned,$clean_key);
		}
	}
	return $cleaned;
}

function is_valid_sub($item){
	$MENU_ITEMS = $GLOBALS['MENU_ITEMS'];
	foreach($MENU_ITEMS as $menu){ 
		if($menu["sub"] == $item){
			return true;
		}
	}
	return false;
}

function is_root_file($item){
	return is_good_file(SERVER_PAGE_DIR.LIVE_PAGE_PREFIX.'.'.ROOT_PAGE_PREFIX.'.'.$item.'.php');
}

function validate_input($item){
	if(preg_match("/([A-za-z0-9-_]+)/",$item,$match) == 0) return false;
	return $match[0];
}

function not_found(){
	header('HTTP/1.1 404 Not Found');
	die("404");
}

function starts_with($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}


function load_page($page,$template){
	$file = SERVER_PAGE_DIR.LIVE_PAGE_PREFIX.'.'.$page.'.php';
	if(is_good_file($file)){
		require_once($file);
		display_page($template,array("Title" => array("func" => "displayTitle"),"MetaMessage" => array("func" => "build_meta_message"),"Content" => array("func" => "getContent","page" => true),"HeadResources" => array("func" => "customHead","page" => true),"Navigation" => array("func" => "build_nav","args" => array($page)),"Trackers" => array("func"=>"place_trackers")),array());
	}
	else{
		not_found();
	}
}

//For handling things like forwarding old urls to new urls
function handle_special_request($path){
	global $PAGE_FORWARDING; //Get forwarding array from config.php
	if(array_key_exists(strtolower($path),$PAGE_FORWARDING)){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: {$PAGE_FORWARDING[$path]}");
	}
}

function parse_path(){
	//http://stackoverflow.com/questions/16388959/url-rewriting-with-php
	$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
	$uri = '/' . trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );
	$uri = urldecode( $uri );
	
	return $uri;
}
	

/**
 * ##################
 * Script begins here
 * ##################
 **/


if(!is_good_file(TEMPLATE_FILE)){
	die("Cannot find templace file! Panic!");
}
$template = file_get_contents(TEMPLATE_FILE);

$path = parse_path();

if(isset($path) && !$path == '' && $path != '/'){
	handle_special_request($path);
	$request = get_request_hierarchy(clean_path($path));

	if(isset($request["file"])){
		$file = SERVER_PAGE_DIR.$request['file'];
		if(is_good_file($file)){
			global $GlobalUser;
			$GLOBALS['USER'] = $GlobalUser;
			require_once($file);
			if(starts_with($request['file'],LIVE_PAGE_PREFIX)){
				display_page($template,array("Title" => array("func" => "displayTitle"),"MetaMessage" => array("func" => "build_meta_message"),"Content" => array("func" => "getContent","page" => true),"HeadResources" => array("func" => "customHead","page" => true),"Navigation" => array("func" => "build_nav","args" => array($request['sub'])),"Trackers" => array("func"=>"place_trackers")),$request);
			}
			else{
				//Do something else if it's a resource
			}
		}
		else{
			not_found();
		}
	}
	else{
		load_page("home",$template);
	}
}
else{
	load_page("home",$template);
}


?>
		