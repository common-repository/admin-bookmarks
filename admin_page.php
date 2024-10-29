<?php

// search and replace 'wpBarFavorites_' with something.


function wpBarFavorites_create_menu() {
  wpBarFavorites_add_submenu_page('tools.php','Admin Bar Favorites Options', 'Admin Bar Favorites Options', 'manage_personal_menu', "wpBarFavorites-options");
}


function createMenuObj() {
	$myFlag = false;
//	print_r($_GET);	

    $currentBar = "favoritesBar".wp_get_current_user()->ID;
	
    if ( !is_nav_menu( $currentBar )) {
	   wp_create_nav_menu($currentBar );	
	}

	$mymenu = wp_get_nav_menu_object($currentBar);	
	$menuID = (int) $mymenu->term_id;

	$my_id = (int) $_GET['menuObject'];
	$myPost = get_post($my_id, "OBJECT");
	
	
	if (isset($_GET['mode']) && $_GET['mode'] == "delete" && isset($_GET['menuObject'])   ) {
		wp_delete_post($_GET['menuObject']);
		echo "<br /><br />deleting menu-nav #" . $_GET['menuObject'] . ".";
		
		return true;
	} else 	if (isset($_GET['mode']) && $_GET['mode'] == "reorder" && isset($_GET['menuObject'])) {
    	$items = wp_get_nav_menu_items($currentBar  , $args = array());		
		echo "<br /><br />there currently are " .count($items). " menu favorites in your Favorites bar.";
		echo "<form name='input' action='"  .admin_url("tools.php").  "' method='get'>";
		echo "New position number for this favorite: <input type='text' name='reordernum' /><br />";
		echo "New title for this favorite: <input type='text' name='newtitle' />";		
		echo "<input type='hidden' name='menuObject' value='" .$_GET['menuObject']. "' />";
		echo "<input type='hidden' name='mode' value='do_reorder' />";
		echo "<input type='hidden' name='page' value='wpBarFavorites-options' />";		
		echo "<br /><input type='submit' value='Submit' />";
		echo "</form>";
		return true;
	} else 	if (isset($_GET['mode']) && $_GET['mode'] == "do_reorder" && isset($_GET['menuObject'])  ) {
	      $menu = wp_get_nav_menu_object($currentBar);
	      
	      $postData = array(
     	      'ID' => $_GET['menuObject'],
      	  );           
      	  
      	  if (isset($_GET['reordernum'])){
      	  	if (is_numeric(trim($_GET['reordernum']))){
			  echo "<br />reordering favorite #" . $_GET['menuObject'] ." to position #". $_GET['reordernum'] .".";	      	  	
     	      $postData['menu_order'] = $_GET['reordernum'];
      	  	} else {
//			  	echo "<br />error: the new position provided is <i>not</i> a number.";
				echo "<br />the menu position for this item has been left unchanged.";
      	  	}
      	  } else {
			// 
      	  }
      	  
      	  if (isset($_GET['newtitle'])){
      	  	if(trim($_GET['newtitle'])){
     	      $postData['post_title'] = $_GET['newtitle'];
     	      echo "<br />Your favorite has been renamed: " . $_GET['newtitle'];
      	  	} else {
      	  		// new post-title left blank.
      	  		echo "<br />the title for your favorite in unchanged.";
      	  	}
      	  }
	      
			      
		wp_update_post( $postData );
		
/*		
		$itemData =  array(
		    'menu-item-position'  => $_GET['reordernum'],
			'menu_item_db_id' => $_GET['menuObject'],
		  );
		  		
		wp_update_nav_menu_item($menuID, $_GET['menuObject'], $itemData);
*/
		return true;
		
	} else if (isset($_GET['mode']) && $_GET['mode'] == "list") {
    	$items = wp_get_nav_menu_items($currentBar  , $args = array());
		$link = admin_url("tools.php")."?page=wpBarFavorites-options&menuObject=";
    	
    	foreach ($items as $item){
			echo "<hr />";    	
    		echo "" . compareReturn($item->post_title, $item->title);
    		echo "<br />menu position: " . $item->menu_order;
    		echo "<br /><a href='". $link .$item->ID ."&mode=reorder'>reorder/rename " .compareReturn($item->post_title, $item->title). "</a>";
    		echo "<br /><a href='". $link .$item->ID ."&mode=delete'>remove " .compareReturn($item->post_title, $item->title). "</a>";
    		
//    		echo "<pre>";
//    		print_r($item);
//    		echo "</pre>";
    	}
    	
		echo "<hr /><form name='input' action='"  .admin_url("tools.php").  "' method='get'>";
		echo "Create a new custom bookmark:<br />";
		echo "URL: <input type='text' name='url' /><br />";
		echo "Title: <input type='text' name='title' /><br />";
		echo "<input type='hidden' name='menuObject' value='0' />";
//		echo "<input type='hidden' name='mode' value='do_reorder' />";
		echo "<input type='hidden' name='page' value='wpBarFavorites-options' />";		
		echo "<br /><input type='submit' value='Submit' />";
		echo "</form>";    	
		return true;
	} else if (isset($_GET['url']) && isset($_GET['title'])  || isset($_GET['menuObject'])){
	
	} else {
	 	return;
	}
	
if (isset($myPost->ID) && trim(isset($myPost->ID))  ){
	
//	echo "!<pre>";
//	print_r($myPost);
//	echo "!</pre>";
	$itemData =  array(
    	'menu-item-object-id' => $myPost->ID,
	    'menu-item-parent-id' => 0,
	    'menu-item-position'  => 0,
   		'menu-item-object' => $myPost->post_type,
	    'menu-item-type'      => 'post_type',
	    'menu-item-status'    => 'publish'
	  );
	 wp_update_nav_menu_item($menuID, 0, $itemData);
	$myFlag = true;	 
} else {

	$taxonomies=get_taxonomies('','names'); 
	$term = '';
	foreach ($taxonomies as $taxonomy ) {
	  if (!$term) {
		  $term = (get_term($my_id, $taxonomy));
	  }
	}

//	print_r($term);
	
	if ($term && !$term->errors) {	
		$itemData =  array(
	    	'menu-item-object-id' => $term->term_id,
		    'menu-item-parent-id' => 0,
		    'menu-item-position'  => 0,
	   		'menu-item-object' => $term->taxonomy,
		    'menu-item-type'      => 'taxonomy',
		    'menu-item-status'    => 'publish'
	  );
	 wp_update_nav_menu_item($menuID, 0, $itemData);	
	$myFlag = true;	 
	}
} 

if (!$myFlag){
	$mymenu = wp_get_nav_menu_object($currentBar);
	$menuID = (int) $mymenu->term_id;
	
	$itemData =  array(
	    'menu-item-parent-id' => 0,
	    'menu-item-position'  => 0,
   		'menu-item-type' => 'custom',
	    'menu-item-status'    => 'publish',
		'menu-item-title' => compareReturn($_GET['title'], "no title"), 
		'menu-item-url' => $_GET['url'],    
	  );
	 wp_update_nav_menu_item($menuID, 0, $itemData);
	$myFlag = true;	 	 
}

if ($myFlag){
	echo "<br /><a href='http://" .$_GET['url'] ."'> " .compareReturn($_GET['title'], "no title") . "</a> has been added to your " .__("Favorites"). " menu.";
}

	return $myFlag;


/*  
          'menu-item-type' => 'custom/taxonomy/post_type',
*/ 

 
}


// CHANGE THE LINES BELOW AT YOUR OWN RISK
// THE SKY WILL FALL ON YOUR HEAD

add_action('admin_menu', 'wpBarFavorites_create_menu');

if ( ! function_exists( 'wpBarFavorites_plugin_options' ) ){
function wpBarFavorites_plugin_options() {
	global $wpBarFavorites_page_title;
	global $wpBarFavorites_page_parent;
	$page = $_GET["page"];
	echo '<div class="wrap">';
	echo "<div class='icon32 icon_$page' id='icon-options-general'><br/></div>";
	echo '<h2>' . $wpBarFavorites_page_title[$page] . '</h2>';
	echo '</div>';
////	echo '<table class="form-table"><tr><td>';
	echo "<br /><a href='" .get_bloginfo("url"). "/wp-admin/plugin-install.php?tab=search&mc_find_plugins=TRUE'>" .__("Find more plugins by this author"). "</a>";
////	echo "</td></tr></table>";
	if (!createMenuObj()){
	
	$list=get_taxonomies('','names'); 
	
	
	echo "<br /><br />This is a list of available classes for styling your admin-menu:<br />some of these may not be relevant.<br /><br />";
	echo ".favbar_taxonomy<br />";
	foreach ($list as $taxonomy ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;.favbar_".ucfirst ($taxonomy)."<br />";
	}	
	
	$list=get_post_types('','names'); 
	
	echo "<br />.favbar_post_type<br />";
	foreach ($list as $posttype ) {
		echo "&nbsp;&nbsp;&nbsp&nbsp;.favbar_".ucfirst ( $posttype)."<br />";
	}	
	
	echo "<br />.favbar_custom<br />";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;.favbar_Custom<br />";
	echo "<br />";
		
//	echo "</pre>";
	}
	wpBar_footer_info();
}
}

if ( ! function_exists( 'wpBarFavorites_add_submenu_page' ) ){
function wpBarFavorites_add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function="", $icon_url="", $position=""){
	global $wpBarFavorites_page_title;
	global $wpBarFavorites_page_parent;
////	$temp_array = array("".$menu_slug => $page_title);
////	$wpBarFavorites_page_title = array_merge($wpBarFavorites_page_title, $temp_array);
	$wpBarFavorites_page_title[$menu_slug] = $page_title;
	$parent_slug = explode(",", $parent_slug);
	foreach ($parent_slug as $parent_slugX) {
		if($parent_slugX) {
			add_submenu_page($parent_slugX, $page_title, $menu_title, $capability, $menu_slug, "wpBarFavorites_plugin_options");
		} else {
			add_menu_page($page_title, $menu_title, $capability, $menu_slug, "wpBarFavorites_plugin_options", $icon_url, $position);
		}
	}
}
}

function wpBar_footer_info(){
	echo "<hr />";
	if (!current_user_can("manage_personal_menu")){
		echo "<br />You can add menu items to your ". __("Favorites") . " menu.";		
	} else {
		echo "<br />You must use the plugin's favorite manager linked below to manage your favorites.";
	}
	echo "<br />";
	
	
	
	if (current_user_can("edit_theme_options" )){
	    $currentBar = "favoritesBar".wp_get_current_user()->ID;	
	    if (is_nav_menu( $currentBar )) {
		   wp_create_nav_menu($currentBar );	
		}	    
		$mymenu = wp_get_nav_menu_object($currentBar);
		echo "You can manage your menu <a href='" .admin_url('nav-menus.php?action=edit&menu=') . $mymenu->term_id. "'>here</a>.";
	} else {
		echo "You can manage your menu <a href='" .admin_url('tools.php?page=wpBarFavorites-options&mode=list'). "'>here</a>.";
	}

}

?>