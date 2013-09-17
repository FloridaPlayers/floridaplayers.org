<?php
require_once "config.php";
//require_once "./svr/pages/authentication.php";

//$MENU_ITEMS = $GLOBALS['MENU_ITEMS'];




function build_nav($sub){
	nav_comp_top();
	$MENU_ITEMS = $GLOBALS['MENU_ITEMS']; 
	$sel = get_nav_selected($sub);
	$usr = $GLOBALS['USER']; //Get the user from router.php
	$usr->get_info();
	//$user_perm = 0; //Replace this later when login is implemented
	$user_perm = $usr->get_user_info("permissions");
	foreach($MENU_ITEMS as $menu){ 
		if($menu["perm"] <= $user_perm && ((isset($menu["hide"]) && $menu["hide"] != 1) || !isset($menu["hide"]))){
			$menu_sub = $menu["sub"];
			$is_selected = ($menu_sub == $sel);
			$href = "/".$menu_sub; 
			/*echo "\t\t\t\t<li ";
			if($is_selected){ echo "class=\"selected\""; }
			echo "><a href=\"$href\" title=\"$menu[title]\">$menu[label]</a></li>\n";*/
			nav_comp_li($is_selected,$href,$menu['title'],$menu['label']);
		}
	}
	nav_comp_bottom();
}
function get_nav_selected($sub){
	$MENU_ITEMS = $GLOBALS['MENU_ITEMS'];
	foreach($MENU_ITEMS as $s){
		if($sub == $s["sub"] || (isset($s['mask']) && in_array($sub,$s['mask']))){ //If the file equals an available sub, for example /home would match home. (page.home.php)
			return $s["sub"];
		}
	}
	return MENU_DEFAULT; //no sub could be found, place in resources. 
	//Note, this shouldn't happen, as it would break naming convention. 
}

function nav_comp_top(){?>

		<nav>
			<ul>
<?php
}
function nav_comp_bottom(){?>

			</ul>
		</nav><?php
}
function nav_comp_li($sel,$href,$title,$label){ ?>
				<li <?php if($sel){ echo "class=\"selected\""; } ?>><a href="<?php echo $href; ?>" title="<?php echo $title; ?>"><?php echo $label; ?></a></li><?php echo "\n";
}


function build_meta_message(){
	$usr = $GLOBALS['USER']; //Get the user from router.php
	if($usr->is_logged_in()){
		$usr->get_info(); //Populates $usr with information, before it can be fetched. 
		$fname = ucwords(strtolower($usr->get_user_info("first_name")));
		echo "Welcome, $fname! <a href=\"/auth/logout\">Log out</a>";
	}
	else{
		echo "Welcome! <a href=\"/auth\">Register/Log in</a>";
	}
}

function place_trackers(){ 
	if(!preg_match('/[\w\.-]+\.local$/',$_SERVER['HTTP_HOST'])){
	?>
		<script src="//static.getclicky.com/js" type="text/javascript"></script>
		<script type="text/javascript">try{ clicky.init(100570566); }catch(e){}</script>
		<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/100570566ns.gif" style="visibility: hidden;"		/></p></noscript>
<?php } }
?>