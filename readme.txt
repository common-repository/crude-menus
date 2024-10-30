=== CRUDE Menus ===
Contributors: RobertGillmer
Tags: menus, create menus, programmitically create menus
Requires at least: 4.9.0
Tested up to: 4.9.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows an admin to create navigation menus by simply pasting a list of comma-separated values into a text box.

== Description ==

I've often worked on sites with very complex navigation menus.  It can be difficult to set up and maintain menus with 50+ links.  It usually involves  looking for the post you're trying to add, not finding it, remembering that the post is *actually* in the "article" custom post type so now you gotta go to that meta box and find it there, then drag-and-drop it to where you need it and and and...

Wouldn't it be super if you could just put together a list of links and titles in something like Excel, paste that list into a textbox, and have WordPress create a menu based off of that?

Well, now you can!

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Navigate to Appearances / CRUDE Menus.
1. To create a menu, enter a unique menu name and the comma-separated list of pages you want to put as links in this menu.

== Frequently Asked Questions ==

= Why do you call it "CRUDE Menus?" =

CRUD is a common acronym in the database world - Create, Read, Update, Delete.  This plugin will also allow for Exporting menus (slated for a future release), so I expanded upon the CRUD acronym.

= How do I tell this new menu where I want it to display? =

You'll still have to go to Appearance / Menus, or set the location through the Customizer.

= But wait, aren't you worried that people will think your plugin is crude? =

Who says crude inherently means bad?  Just ask the Clampetts.

= What can be imported for a menu item? =

Link is mandatory; you can also import CSS class(es), Link Target (whether it opens in another tab), and a custom title.

= What about integration with Nav Menu Roles, WP Mega Menu, Ubermenu, etc? =

You'd have to set that manually as well, through Appearance / Menus.  Menus created via CRUDE Menus will be standard WordPress menus, and will be able to be configured with other plugins just as if you had created them manually.  This plugin is designed to make creating large menus easier, but can't bring in data for anything other than stock WordPress menu functions.

= Can I use this to update existing navigation menus? =

That's planned for a future release.  I've got a three-year-old daughter, time got away from me. :)

== Screenshots ==

1. The Create Menu screen.  Note the textbook, where you can paste a comma-separated list of links, CSS classes, link targets, and labels.

== Changelog ==
= 1.0 =
* Initial release