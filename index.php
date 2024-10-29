<?php
/*
Plugin Name: Custom Admin-Bar Favorites
Plugin URI: http://codeandreload.com
Description: This plugin allows each administrator and others users with a specially-defined user-capability to define a custom menu on the admin-bar of his or her favorite shortcuts.
Author: Robert Wise
Version: 0.1
Author URI: http://codeandreload.com/wp-plugins/admin-bookmarks
*/

 include("admin_page.php");

if(!function_exists("codeAndReloadLink")){
	add_action("plugins_loaded","codeAndReloadLink");
	function codeAndReloadLink(){
		if( isset($_GET["mc_find_plugins"]) && trim($_GET["mc_find_plugins"])){
			$_POST["type"] = "author";
			$_POST["s"] = "CodeAndReload";
			$_POST["search"] = "Search Plugins";
		}
	}
}


function my_new_admin_bar_menu() {
    global $wp_admin_bar;
    
    if(!current_user_can("manage_personal_menu")){
		return false;        
    }
    
    $currentBar = "favoritesBar".wp_get_current_user()->ID;
    
	$items = wp_get_nav_menu_items($currentBar  , $args = array());
	$mymenu = wp_get_nav_menu_object($currentBar);
 
 
 	if (current_user_can("edit_theme_options" )){
	    $wp_admin_bar->add_menu( array(
	            'parent' => false, // parent ID or false to make it root menu
	            'id' => 'favoritesBar', // Menu ID should be unique.
	            'title' => __('Favorites'), // Menu / Sub0menu title
	            'href' => admin_url('nav-menus.php?action=edit&menu=') . $mymenu->term_id , // Menu URL
	            'meta' => array( 
	            	)
	            )
	    );
	} else {
	    $wp_admin_bar->add_menu( array(
	            'parent' => false, // parent ID or false to make it root menu
	            'id' => 'favoritesBar', // Menu ID should be unique.
	            'title' => __('Favorites'), // Menu / Sub0menu title
	            'href' => admin_url('tools.php?page=wpBarFavorites-options&mode=list'), // Menu URL
	            'meta' => array( 
	            	)
	            )
	    );	
	}

	$deleteLink = array();
	$deleteKey = array();	
	if (count($items) > 0 && isset($items[0])) {
		global $wp_query;
		for ($i=0; $i<count($items); $i++){
			$isCurrent = "";				
		    if (($items[$i]->object_id == $wp_query->queried_object_id) || ("http://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] == $items[$i]->url)) {
		    	$deleteLink[] = ($items[$i]->ID);
		    	$deleteKey[] = $i;
				$isCurrent = "favbar_current ";
		    }
		
		
		    $wp_admin_bar->add_menu(array( 
		    	'parent' => 'favoritesBar',
		    	'id' => 'fav-add' . $items[$i]->ID,
		    	'title' => compareReturn($items[$i]->post_title, $items[$i]->title),
		    	'href' => $items[$i]->url,
		    	'meta' => array( 
            	'class' => $isCurrent . "favbar_" . $items[$i]->type . " " . "favbar_" . str_replace(" ", "_", $items[$i]->type_label),
            	)
		    ));
		}
	}
	
	for ($x=0; $x<count($deleteLink); $x++){
		if (count($deleteLink)>1  ){
			$myTitle = "Remove favorite #" . (1+$deleteKey[$x]);		
			$reorderTitle = "Reorder favorite #" . (1+$deleteKey[$x]);
		} else {
			$myTitle = "Remove this favorite";
			$reorderTitle = "Reorder this favorite";			
		}
		
		if (current_user_can("edit_theme_options" )) {
			$reorderUrl = admin_url('nav-menus.php?action=edit&menu=') . $mymenu->term_id;
		} else {
			$reorderUrl = admin_url('tools.php?page=wpBarFavorites-options') . "&menuObject=" . $deleteLink[$x] . "&mode=reorder";
		}
		
		$wp_admin_bar->add_menu(array( 
				'parent' => 'favoritesBar',
				'id' => 'fav-reorder' . $deleteKey[$x],
				'title' => $reorderTitle,
		    	'href' => $reorderUrl,
				'meta' => array( 
					'class' => "favbar_reorder favbar_reorder$x",
				)
			));
		
		
	}	
	
	for ($x=0; $x<count($deleteLink); $x++){
		if (count($deleteLink)>1  ){
			$myTitle = "Remove favorite #" . (1+$deleteKey[$x]);		
			$reorderTitle = "Reorder favorite #" . (1+$deleteKey[$x]);					
		} else {
			$myTitle = "Remove this favorite";
			$reorderTitle = "Reorder this favorite";			
		}
		
		$wp_admin_bar->add_menu(array( 
				'parent' => 'favoritesBar',
				'id' => 'fav-remove' . $deleteKey[$x],
				'title' => $myTitle,
		    	'href' => admin_url('tools.php?page=wpBarFavorites-options') . "&menuObject=" . $deleteLink[$x] . "&mode=delete",
				'meta' => array( 
					'class' => "favbar_remove favbar_remove$x",
				)
			));
	}	
	
	if (!count($deleteLink)){
	global $admin_title, $title;
    $wp_admin_bar->add_menu(array( 
    	'parent' => 'favoritesBar', 
    	'id' => 'fav-add', 
    	'title' => __('Add to favorites+'), 
    	'href' => admin_url('tools.php?page=wpBarFavorites-options') . "&menuObject=" . $wp_query->queried_object_id ."&url=" . str_replace("&", "%26", ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) . "&title=" . str_replace(" ","%20",trim(wp_title("",false))) . @str_replace(" ", "%20", $title),
    	'meta' => array(
    		'class' => "addFav"
    	) )
    	);
    	
    }
 
}
add_action( 'wp_before_admin_bar_render', 'my_new_admin_bar_menu' );

function compareReturn($a, $b, $c='', $type = true){
	if (trim($a) && isset($type)){
		return $a;
	} else if (trim((string) $b)){
		return $b;		
	} else if (trim($c)){
		return $c;		
	} else {
		return ("no title");
	}

}

add_action( 'init', 'addMBCap' );

function addMBCap(){
	if ( is_user_logged_in() ) {
		global $wp_roles;
		$wp_roles->add_cap("administrator", "manage_personal_menu", true );
//		$wp_roles->add_cap("editor", "manage_personal_menu", true );		
	    add_action( 'admin_print_styles', 'favbarStyle_stylesheet' );
	    add_action('wp_print_styles', 'favbarStyle_stylesheet');
	}
}


    function favbarStyle_stylesheet() {    
            wp_register_style('favbarStyle', WP_PLUGIN_URL . '/admin-bookmarks/plugin.css');
            wp_enqueue_style( 'favbarStyle');
	}


/// http://new2wp.com/snippet/register-and-create-new-nav-menus-with-default-menu-items/
/// http://www.acousticwebdesign.net/wordpress/how-to-create-wordpress-3-navigation-menus-in-your-theme-or-plugin-code/
/// wp_update_nav_menu_item(
/// http://hitchhackerguide.com/2011/02/12/wp_update_nav_menu_item/

?>