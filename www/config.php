<?php
require_once 'serverconfig.php';

define('SERVER_RES_DIR','./svr/res/');
define('SERVER_PAGE_DIR','./svr/pages/');
define('TEMPLATE_FILE',SERVER_RES_DIR.'template.html');

define('IMAGE_UPLOAD_DIR','./res/uploads/'); //Relative to "./do/upload.php"
define('IMAGE_THUMB_WIDTH',100);
define('IMAGE_SQUARE_THUMB_WIDTH',75);
define('IMAGE_LARGE_WIDTH',700);


define('LIVE_PAGE_PREFIX','page');
define('RESOURSE_PAGE_PREFIX','res');
//
// Include this in a file name to make it viewable as an item in the server root.
// It must still include the LIVE_PAGE_PREFIX at the beginning of the file name.
// Example: "page.root.special.php" would be accessible at example.com/special.
// This essentially allows you to not need a separate menu sub for single files.
//
define('ROOT_PAGE_PREFIX','root');


//The title for each page, formatted for printf. 
define('TITLE_FORMAT','%s | Florida Players');
//Title to use in case an error occurs while trying to generate the title. 
define('ERROR_TITLE','Florida Players');


// Disable account after too many unsuccessful logins
define('DISABLED_ACCOUNT_PERIOD',60 * 5); 
// How many unsuccessful logins before an account is disabled
define('DISABLED_ACCOUNT_TRIES',5);


//Pages are requested in the form of /sub/file, which translates to [LIVE_PAGE_PREFIX].sub.file.php
$MENU_ITEMS = array(
	array("sub" => "home","label" => "Home","title" => "Home", "perm" => 0),
	array("sub" => "about","label" => "About Us","title" => "About Florida Players", "perm" => 0),
	array("sub" => "shows","label" => "Shows","title" => "See our current and past productions", "perm" => 0, "mask" => array("show")), //Mask is a list of subs that will be shown under this menu shortcut
	array("sub" => "tickets","label" => "Tickets","title" => "Reserve tickets for a show", "perm" => 0),
	array("sub" => "media","label" => "Media","title" => "Florida Players media", "perm" => 0),
	array("sub" => "resources","label" => "Resources","title" => "Resources", "perm" => 0),
	array("sub" => "admin","label" => "Admin","title" => "Admin", "perm" => 1)
	);
$MENU_ALIASES = array("sub" => "show","alias" => "shows");
define('MENU_DEFAULT','resources'); 


// The most tickets someone may reserve for an event
define('MAX_TICKET_RESERVATION',2);
// Whether to enforce strictly enforce one reservation per person
define('ENFORCE_ONE_RESERVATION',false);
// If not enforcing one reservation, give a ceiling limit per person
define('RESERVATION_CEILING',6);


//The image to display if no show picture is available (if the database returns NULL)
define('NO_SHOW_IMAGE_DEFAULT','/res/images/show-icons/no-picture.png');

//The key of the primary array should be equal to the id of the sub-array
$THEATER_LOCATIONS = array(
	"1" => array("id" => "1","name" => "Squitieri Studio Theatre","short" => "squitieri"),
	"2" => array("id" => "2","name" => "Black Box Theatre in the McGuire Pavilion","short" => "mcguire")
);

//old urls and the new paths to forward them to
$PAGE_FORWARDING = array(
	'index.php' => '/home',
	'about.php' => '/about',
	'contact.php' => '/about/contact',
	'friends.php' => '/about/friends',
	'gallery.php' => '/media',
	'maps.php' => '/map',
	'points.php' => '/points',
	'ticketing.php' => '/tickets',
	'shows.php' => '/shows',
	'contact' => '/about/contact',
	'board' => '/about/contact'
);

//The location for the text file containing the template for ticket confirmation emails (Plain text)
define('MESSAGE_TICKET_CONFIRM_TEXT','./svr/res/messages/ticketConfirmation.txt');
//The location for the text file containing the template for ticket confirmation emails (HTML)
define('MESSAGE_TICKET_CONFIRM_HTML','./svr/res/messages/ticketConfirmation.html.txt');

define('MESSAGE_MEMBERSHIP_CONFIRM_TEXT','./svr/res/messages/membershipConfirmation.txt');
define('MESSAGE_MEMBERSHIP_CONFIRM_HTML','./svr/res/messages/membershipConfirmation.html.txt');

define('SERVER_INI_FILE',SERVER_RES_DIR.'fpconfig.ini');
?>