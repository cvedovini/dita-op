=== WordPress.com Stats ===
Contributors: skeltoac, mdawaffe, automattic
Tags: stats, statistics
Requires at least: 2.7
Tested up to: 2.9
Stable tag: 1.6

You can have simple, concise stats with no additional load on your server by plugging into WordPress.com's stat system.

== Description ==

There are hundreds of plugins and services which can provide statistics about your visitors. However I found that even though something like Google Analytics provides an incredible depth of information, it can be overwhelming and doesn't really highlight what's most interesting to me as a writer. That's why Automattic created its own stats system, to focus on just the most popular metrics a blogger wants to track and provide them in a clear and concise interface. 

Installing this stats plugin is much like installing Akismet, all you need is to put in your [API Key](http://wordpress.com/api-keys/ "You can get a free API key from WordPress.com") and the rest is automatic.

Once it's running it'll begin collecting information about your pageviews, which posts and pages are the most popular, where your traffic is coming from, and what people click on when they leave. It'll also add a link to your dashboard which allows you to see all your stats on a single page. Less is more.

Finally, because all of the processing and collection runs on our servers and not yours, it doesn't cause any additional load on your hosting account. In fact, it's one of the fastest stats system, hosted or not hosted, that you can use.

== Screenshots ==

1. Your stats are displayed in a frame on your own blog's dashboard. There are graphs and several sections of charts below. You will need to be logged in at WordPress.com to see the stats. If you see a login box here, use your WordPress.com login.

2. Each post has its own graph.

3. You can add other WordPress.com users to the list of people allowed to see your stats.

== Installation ==

Installing should be a piece of cake and take fewer than five minutes.

1. Upload `stats.php` to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. It will ask you to enter your WordPress.com API key, do so.
4. Sit back and wait a few minutes for your stats to come rolling in.

== Frequently Asked Questions ==

= Can I keep using existing stat systems like Mint, Google Analytics, and Statcounter? =

Of course, nothing we do conflicts with any of those systems. We're just (hopefully) faster.

= How long before I start seeing stats? =

It may take as long as 20 minutes the first time you use it. After that they should update every 3 minutes or so.

= Does it count my own hits? =

It does not count the hits of logged in users.

= What if the stats don't start showing up? Do I need anything special in my theme? =

Yes, your theme must have a call to `<?php wp_footer(); ?>` at the very bottom right before the `</body>` tag.

= Can I hide the smiley? =

Sure, just use `display:none`. Try this code in your stylesheet:

`img#wpstats{display:none}`

= Is it compatible with WP-Cache? =

The plugin collects stats via a javascript call, so as long as the JS call is on the page stats will be collected just fine, whether the page is cached or not.

= Can I use the same API key on multiple blogs? =

Just like with Akismet, you're welcome to use the same API key on multiple blogs. In fact our interface is optimized for quickly switching between stats for multiple blogs.

== Changes ==

= 1.6 =
* Add shortlink generator. Now wp.me shortlinks are available on the Edit Post screen from a button next to View Post.

= 1.5.4 =
* Work around core API change in plugins_url. Different code for 2.7. Fixes missing charts in 2.7.*. No changes for 2.8+.

= 1.5.3 =
* Restore backward compatibility for WordPress 2.7. Fixes "Call to undefined function plugin_dir_url()..."

= 1.5.2 =
* Fix dashboard chart missing due to omitted line of code.

= 1.5.1 =
* Include <a href="http://teethgrinder.co.uk/open-flash-chart/">Open Flash Chart</a> SWF. Faster and more reliable than proxying it. Should fix missing graph for many users.
* Move change log out of source code.
* Fixed an XMLRPC encoding issue that resulted in "malformed" error when entering API key. Thanks to Oscar Reixa for helping.

= 1.5 =
* Kill iframes.
* Use blog's role/cap system to allow local users to view reports. (No more switcher.)
* Thanks to Stefanos Kofopoulos for helping to debug encoding issues.

= 1.4 =
* Added gmt_offset setting to blog definition. (Stats in your time zone.)

= 1.3.8 =
* Fixed "Missing API Key" error appearing in place of more helpful errors. Hat tip: Walt Ritscher.

= 1.3.7 =
* If blog dashboard is https, stats iframe should be https.

= 1.3.6 =
* fopen v wp_remote_fopen CSV fix from A. Piccinelli

= 1.3.5 =
* Compatibility with WordPress 2.7

= 1.3.4 =
* Compatibility with WordPress 2.7

= 1.3.3 =
* wpStats.update_postinfo no longer triggered by revision saves (post_type test)


