=== Facebook Profile Wordpress plugin ===
Author: Claude Vedovini
Contributors: cvedovini
Donate link: http://vedovini.net/plugins
Tags: Facebook,profile
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: 1.0.5 


== Description ==

This plugin enables you to create a Facebook profile tab featuring your blog.


== Installation ==

This plugin follows the [standard WordPress installation method](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins):

1. Upload the `facebook-profile-theme` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Log in to the Facebook Developer application: http://www.facebook.com/developers/
1. Create a new application, more info: http://developers.facebook.com/get_started.php
1. In the `Advanced` tab, set the application type to `website`
1. In the `Canvas` tab, set the `Canvas Callback URL` to your blog's URL and `Render Method` to `FBML`
1. In the `Profiles` tab, give a name to your profile tab (your blog's name for example) and set the `Tab URL` field to `index.php` 
 
To add your blog to your profile tab:
1. Go to the application profile page
1. Click on the `Go to Application` button
1. Authorize the application
1. Click on the `Add profile tab` button at the top of the page 

You will then have a new profile tab showing your last posts to people visiting your profile.


== Screenshots ==

1. Screenshot Facebook profile tab 


== Changelog ==

= version 1.0.5 =
- fixing application authorization issue following Facebook new SDK release

= version 1.0.4 =
- various fixes in documentation

= version 1.0.0 =
- Initial release
