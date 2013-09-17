<?php

$file = 'http://www.google.com/calendar/ical/webmaster%40floridaplayers.org/public/basic.ics';
readfile($file);
/*
//Initialize the variables
$cache_file = null;
$script_file = null;
$is_windows = false;

//Determine the system being used.
//This is only to make debugging easier, since different variables are needed
//if I'm testing it (on Windows) or if it's running live (Linux)
$system = php_uname('s');
if(stristr($system,"windows") !== false) $is_windows = true;
if($is_windows){
	$script_file = "C:/Users/Marcus/My Web/newfp/www/svr/res/download-calendar.pl";
	$cache_file = "C:/Users/Marcus/My Web/newfp/www/svr/res/calendar.cache";
}
else{
	$script_file = "./svr/res/download-calendar.pl";
	$cache_file = "./svr/res/calendar.cache";
}


//Okay real script time
//If the cache file isn't present, just download the live file
//Otherwise show the cache file

if(ob_get_length() > 0)  ob_clean();  // These are needed
header("Connection: close");  // so that we can close the connection
ob_start();                   // before we execute the cache script at the end

if(!file_exists($cache_file)){
	echo "not from cache";
	$file = 'http://www.google.com/calendar/ical/webmaster%40floridaplayers.org/public/basic.ics';
	readfile($file);
}
else{
	echo "from cache";
	readfile($cache_file);
}

$size = ob_get_length();
header("Content-Length: $size");
ob_end_flush(); // Strange behaviour, will not work
flush();        // Unless both are called !

//Now we download the file and cache it.
//It's redundant if the file doesn't exist, but unless something breaks, that would only happen once.
if($is_windows) shell_exec("C:/Perl/Strawberry/perl/bin/perl.exe \"$script_file\"");
else exec("perl $script_file");*/
?>

