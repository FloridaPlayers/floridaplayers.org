<?php
require_once "config.php";

function logError($script,$line,$description, $error, $time,$die = true){
	$data = "File:        $script (Line: $line)\nDescription: ".$description."\nError:       ".$error."\nTime:        ".date('l, j F Y, \a\t g:i:s:u A',$time)."\n--------------------------------\n";
	file_put_contents("./svr/errors.log", $data, FILE_APPEND);
	if($die){
		die();
	}
}

function newAuthKey(){
	$keyLength = 32;
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&*";
   
    //mt_srand(microtime() * 1337);
	/* create the seed */
    
    $key = '';
    $charcount = strlen($chars);
    
    for ($x=0;$keyLength>$x;$x++){
        $key .= $chars{mt_rand(0, $charcount - 1)};
    }
    
    return $key;
}

?>