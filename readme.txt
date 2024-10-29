=== Custom Admin-Bar Favorites ===  
Contributors: CodeAndReload  
Donate link: http://www.codeandreload.com/wp-plugins/admin-bookmarks/#donate  
Tags: admin-bar, favorite, favorites, bookmark, bookmarks, link, wpadminbar, shortcut, shortcuts  
Requires at least: 3.1  
Tested up to: 3.2.1  
Stable tag: 0.1  


Allows admins other users with a specially-defined user-capability to define a custom menu on the admin-bar of his or her favorite shortcuts.

== Description ==

Allows admins other users with a specially-defined user-capability to define a custom menu on the admin-bar of his or her favorite shortcuts.


== Installation ==

Installation is simple and straight-forward:

1. Unzip `admin-bookmarks.zip` into to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.


== Frequently Asked Questions ==


= What capability do non-Editors need to have and manage a menu? =

The role needs to have the 'manage_personal_menu' capability.


= How can you assign the 'manage_personal_menu' capability to a role? =

There are many great plugins for assigning a capability to a role.  However, you can manually add the role with the following lines of code:

	    `global $wp_roles;`
	    `$wp_roles->add_cap("editor", "manage_personal_menu", true);`

The above line adds the capability to the Editor role.  Replace 'editor' with whatever role you wish to assign the capability.


= Why can't users with the 'manage_personal_menu' capability manage their bookmarks in the same way an administrator can? =

The user must have the 'edit_theme_options' capability to be able to edit a navigational menu. Without that capability, he or she will not be able to use the menus interface and must use a different interface instead.


= Why are there menus labeled 'favoritesBar' followed by a number? =

Each user gets a favorites bar menu followed by their User ID. Editing a favorites bar with a User ID not your own will effectively be editing a different user's menu.


== Screenshots ==

1. This is the Favorites menu in the admin bar.
2. Editing the menu with the administrator interface.
3. Editing the menu with the non-administrator interface.


== Changelog ==

= 0.1 =  
* Initial public release.

== Upgrade Notice ==

= 0.1 =  
* Initial public release.


== Support ==

Technical support for this plugin will be provided via the WordPress plugin forum.  Additional support may be
available at [plugin's homepage](http://www.codeandreload.com/wp-plugins/admin-bookmarks/ "Admin Bookmarks at Code
and Reload").
