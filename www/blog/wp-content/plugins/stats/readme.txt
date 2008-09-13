=== WordPress.com Stats ===
Contributors: skeltoac, mdawaffe
Tags: stats, statistics
Requires at least: 2.1
Tested up to: 2.6-alpha
Stable tag: 1.2.1

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
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. It will ask you to enter your WordPress.com API key, do so.
1. Sit back and wait a few minutes for your stats to come rolling in.

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
