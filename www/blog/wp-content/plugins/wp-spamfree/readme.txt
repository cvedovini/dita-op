=== WP-SpamFree ===
Contributors: WebGeek
Donate link: http://www.hybrid6.com/spamfree-donate
Tags: spam, antispam, anti-spam, comments, comment, wp-spamfree, plugin, security, wordpress, javascript, contact, form
Requires at least: 2.0.2
Tested up to: 2.6
Stable tag: trunk

Powerful anti-spam plugin that virtually eliminates comment spam. Finally, you can enjoy a spam-free WordPress blog! Includes contact form.

== Description ==

An extremely powerful anti-spam plugin for WordPress that virtually eliminates comment spam, including trackback and pingback spam. Finally, you can enjoy a spam-free WordPress blog! Includes spam-free contact form feature as well.

= A Powerful Weapon Against Comment Spam =
Comment spam has been a huge problem for bloggers since the inception of blogs, and it just doesn't seem to go away. The worst kind, and most prolific, is automated spam that comes from bots. Well, finally there is an effective solution, without CAPTCHA's, challenge questions, or other inconvenience to site visitors. **WP-SpamFree virtually eliminates automated comment spam from bots, including trackback and pingback spam.**

= New Features =
* Now with a drop-in **spam-free contact form!** Simple and effective. Just add a tag `<!--spamfree-contact-->` to pages where you want a contact form (using the HTML editing tab), and you're done. Easy to use - no configuration necessary.
* Now can display your blocked spam stats on your blog. Simply insert the code into your theme and you can show off how much spam you're not getting anymore.

= Key Features =
1. Virtually eliminates automated comment spam from bots. It works like a firewall to ensure that your commenters are in fact, human.
2. A counter on your dashboard to keep track of all the spam it's blocking. The numbers will show how effective this plugin is.
3. No CAPTCHA's, challenge questions or other inconvenience to site visitors - it works silently in the background.
4. Includes drop-in spam-free contact form. Easy to use - no configuration necessary.
5. No false positives, which leads to fewer frustrated readers, and less work for you.
6. You won't have to waste valuable time sifting through a spam queue anymore, because there won't be much there, if anything.
7. Powerful trackback and pingback spam protection.
8. Easy to install - truly plug and play. Just upload and activate. (Installation Status on the plugin admin page to let you know if plugin is installed correctly.)
9. The beauty of this plugin is the methods of blocking spam. It takes a different approach than most and stops spam at the door.
10. The code has an extremely low bandwidth overhead and won't slow down your blog (very light database access), unlike some other anti-spam plugins.
11. Completely compatible with all cache plugins, including WP Cache and WP Super Cache. Not all anti-spam plugins can say that.
12. Display your blocked spam stats on your blog.

= Background =
Before I developed this plugin, our team and clients experienced the same frustration you do with comment spam on your blog. Every blog we manage had comment moderation enabled, Akismet and various other anti-spam plugins installed, but we still had a ton of comments tagged as spam by Akismet that we had to sort through. This wasted a lot of valuable time, and we all know, time is money. We needed a solution.

Comment spam stems from an older problem - automated spamming of email contact forms on web sites. I developed a successful fix for this a while ago, and later applied it to our WordPress blogs. It was so effective, that I decided to add a few modifications and turn it into a WordPress plugin to be freely distributed. Blogs we manage used to get an excessive number of spam comments show up on the Akismet Spam page each day - now the daily average is zero spam comments.

To further the development of this plugin, I now study thousands and thousands of potential spam comments from many test blogs and contributors. I use a special diagnostic version of the plugin, which provides much more information on each of these spam comments than what is shown in WordPress. By analyzing patterns and behaviors consistent with spam, I can continually improve the plugin and ensure future accuracy.

= How It Works =
Most of the spam hitting your blog originates from bots. Few bots can process JavaScript (JS). Few bots can process cookies. Fewer still, can handle both. In a nutshell, this plugin uses a dynamic combo of JavaScript and cookies to weed out the humans from spambots, preventing 99%+ of automated spam from ever getting to your site. Almost 100% of web site visitors will have these turned on by default, so this type of solution works silently in the background, with no inconveniences. There may be a few users (less than 2%) that have JavaScript and/or cookies turned off by default, but they will be prompted to simply turn those back on to post their comment. Overall, the few might be inconvenienced because they have JS and cookies turned off will be far fewer than the 100% who would be annoyed by CAPTCHA's, challenge questions, and other validation methods.

Some would argue that using JS and cookies is too simplistic an approach. Traditionally, programmers prefer using some type of basic AI to fight bots by trying to figure out if a comment is spam. While that isn't a bad idea, when used alone this method falls short because no machine AI can ever accurately judge whether a comment is spam - many spam comments get through that could easily have been stopped, and there are many false positives where non-spam comments get flagged as spam. Others may argue that some spammers have programmed their bots to read JavaScript, etc. In reality, the percentage of bots with these capabilities is still extremely low - less than 1%. It's simply a numbers game. Statistics tell us that an effective solution would involve using a technology that few bots can handle, therefore eliminating their ability to spam your site. The important thing in fighting spam is that we create a solution that can reduce spam noticeably and improve the user experience, and a 99%+ reduction in spam would definitely make a difference for most bloggers and site visitors.

Even so, it's important to know that the particular JS and cookies solution used in WP-SpamFree has evolved quite a bit, and is no longer simple at all. There are now two layers of protection, a JavaScript/Cookies Layer, and an Algorithmic Layer. Even if bot authors could engineer a way to break through the JavaScript/Cookies Layer, the Algorithmic Layer would still stop 95% of the spam that the JavaScript Layer blocks. (I'm working to make this 100% for fully redundant protection.) This JavaScript Layer utilizes randomly generated keys, and is algorithmically enhanced to ensure that spambots won't beat it. The powerful Algorithmic Layer is what eliminates trackback/pingback spam, and much human spam as well. And, it does all that without hindering legitimate comments and trackbacks. The bottom line, is that this plugin just plain works, and is a **powerful weapon against spam**.

= Blogging Without Spam =
How does it feel to blog without being bombarded by automated comment spam? If you're happy with WP-SpamFree, please let others know by giving it a rating!

== Installation ==

= Installation Instructions =
1. After downloading, unzip file and upload the enclosed `wp-spamfree` directory to your WordPress plugins directory: `/wp-content/plugins/`.

2. As always, **activate** the plugin on your WordPress plugins page.

3. Check to make sure the plugin is installed properly. Many support requests for this plugin originate from improper installation and can be easily prevented. To check proper installation status, go to the WP-SpamFree page in your Admin. It's a submenu link on the Plugins page. Go the the 'Installation Status' area near the top and it will tell you if the plugin is installed correctly. If it tells you that the plugin is not installed correctly, please double-check what directory you have installed WP-SpamFree in, delete any WP-SpamFree files you have uploaded to your server, re-read the Installation Instructions, and start the Installation process over from step 1. If it is installed correctly, then move on to the next step.

4. Select desired configuration options. Due to popular request, I've added the option to block trackbacks and pingbacks if the user feels they are excessive. I'd recommend not doing this, but the choice is yours.

5. If you are using front-end anti-spam plugins (CAPTCHA's, challenge questions, etc), be sure they are disabled since there's no longer a need for them, and these could likely conflict. (Back-end anti-spam plugins like Akismet are fine, although unnecessary.)

You're done! Sit back and see what it feels like to live without comment spam!

= Upgrading from Version 1.0 =
Simply undo any edits you made to your `header.php` and `wp-comments-post.php` files when installing Version 1.0. Then install the most recent version!

= For Best Results =
WP-SpamFree was created specifically to stop automated comment spam (which accounts for over 99% of comment spam), and recently we have added some features that help combat human comment spam, as well as trackback/pingback spam. Unfortunately, no plugin can perfectly detect human comment spam. As other experts will tell you, the most effective strategy for blocking spam involves applying a variety of techniques. For best results, enable comment moderation, and if you desire a backup, feel free to use Akismet, as the two plugins are compatible.

= Displaying Stats on Your Blog =
Want to show off your spam stats on your blog and tell others about WP-SpamFree? Simply add the following code to your WordPress theme where you'd like the stats displayed: `<?php if ( function_exists(spamfree_counter) ) { spamfree_counter(1); } ?>` where '1' is the style. Replace the '1' with a number from 1-6 corresponding to one of the background styles you'd like to use. (See plugin admin page for more info.)

To add stats to individual posts, you'll need to install the Exec-PHP plugin.

= Adding a Comment Form to Your Blog =
First create a page (not post) where you want to have your comment form. Then, insert the following tag (using the HTML editing tab) and you're done: `<!--spamfree-contact-->`

There is no need to configure the form. It allows you to simply drop it into the page you want to install it on. However, there are a few basic configuration options. You can choose whether or not to include Phone and Website fields, whether they should be required, add a drop down menu with up to 10 options, set the width and height of the Message box, and the minimum message length.

**What the Contact Form feature IS:** A simple drop-in contact form that won't get spammed. 
**What the Contact Form feature is NOT:** A configurable and full-featured plugin like some other contact form plugins out there. 
**Note:** Please do not request new features for the contact form, as the main focus of the plugin is spam protection. Thank you.

== Other Notes ==

= Troubleshooting Guide / Support =
If you're having trouble getting things to work after installing the plugin, here are a few things to check:

1. If you haven't yet, please upgrade to the latest version.

2. Check to make sure the plugin is installed properly. Many support requests for this plugin originate from improper installation and can be easily prevented. To check proper installation status, go to the WP-SpamFree page in your Admin. It's a submenu link on the Plugins page. Go the the 'Installation Status' area near the top and it will tell you if the plugin is installed correctly. If it tells you that the plugin is not installed correctly, please double-check what directory you have installed WP-SpamFree in, delete any WP-SpamFree files you have uploaded to your server, re-read the Installation Instructions, and start the Installation process over from step 1.

3. Clear your browser's cache, clear your cookies, and restart your browser. Then reload the page.

4. If you are receiving the error message: "Sorry, there was an error. Please enable JavaScript and Cookies in your browser and try again." then you need to make sure JavaScript and cookies are enabled in your browser. (JavaScript is different from Java. Java is not required.) These are enabled by default in web browsers. The status display will let you know if these are turned on or off (as best the page can detect - occasionally the detection does not work.) If this message comes up consistently even after JavaScript and cookies are enabled, then there most likely is an installation problem, plugin conflict, or JavaScript conflict. Read on for possible solutions.

5. Check your WordPress Version. If you are using a release earlier than 2.3, you may want to upgrade for a whole slew of reasons, including features and security.

6. Check the options you have selected to make sure they are not disabling a feature you want to use.

7. Make sure that you are not using other front-end anti-spam plugins (CAPTCHA's, challenge questions, etc) since there's no longer a need for them, and these could likely conflict. (Back-end anti-spam plugins like Akismet are fine, although unnecessary.)

8. Visit http://www.yourblog.com/wp-content/plugins/wp-spamfree/js/wpSpamFreeJS.php (where yourblog.com is your blog url) and check two things. **First, see if the file comes up normally or if it comes up blank or with errors.** That would indicate a problem. Submit a support request (see last troubleshooting step) and copy and past any error messages on the page into your message. **Second, check for a 403 Forbidden error.** That means there is a problem with your file permissions. If the files in the wp-spamfree folder don't have standard permissions (at least 644 or higher) they won't work. This usually only happens by manual modification, but strange things do happen. The **AskApache Password Protect Plugin** is known to cause this error. Users have reported that using its feature to protect the /wp-content/ directory creates an .htaccess file in that directory that creates improper permissions and conflicts with WP-SpamFree (and most likely other plugins as well). You'll need to disable this feature, or disable the AskApache Password Protect Plugin and delete any .htaccess files it has created in your /wp-content/ directory before using WP-SpamFree.

9. Check for conflicts with other JavaScripts installed on your site. This usually occurs with with JavaScripts unrelated to WordPress or plugins.

10. Check for conflicts with other WordPress plugins installed on your blog. This isn't common but does happen from time to time. I can't guarantee how well-written other plugins will be. First, see the [Known Plugin Conflicts list](http://www.hybrid6.com/webgeek/plugins/wp-spamfree#wpsf_known_conflicts). If you've disabled any plugins on that list and still have a problem, then proceed. To start testing for conflicts, temporarily deactivate all other plugins except WP-SpamFree. Then check to see if WP-SpamFree works by itself. (For best results make sure you are logged out and clear your cookies. Alternatively you can use another browser for testing.) If WP-SpamFree allows you to post a comment with no errors, then you know there is a plugin conflict. The next step is to activate each plugin, one at a time, log out, and try to post a comment. Then log in, deactivate that plugin, and repeat with the next plugin. (If possible, use a second browser to make it easier. Then you don't have to keep logging in and out with the first browser.) Be sure to clear cookies between attempts (before loading the page you want to comment on). If you do identify a plugin that conflicts, please let me know so I can work on bridging the compatibility issues.

11. If have checked these, and still can't quite get it working, please submit a support request at the [WP-SpamFree Support Page](http://www.hybrid6.com/webgeek/plugins/wp-spamfree/support).

= Version History / Changelog =

For a complete list of changes to the plugin, view the [Version History](http://www.hybrid6.com/webgeek/plugins/wp-spamfree/version-history).

= Updates / Documentation =
For updates and documentation, visit the [homepage of the WP-SpamFree Comment Spam Plugin for WordPress](http://www.hybrid6.com/webgeek/plugins/wp-spamfree).

= WordPress Security Note =
As with any WordPress plugin, for security reasons, you should only download plugins from the author's site and from official WordPress repositories. When other sites host a plugin that is developed by someone else, they may inject code into that could compromise the security of your blog. We cannot endorse a version of this that you may have downloaded from another site. If you have downloaded the "WP-SpamFree" plugin from another site, please download the current release from the [official WP-SpamFree site](http://www.hybrid6.com/webgeek/plugins/wp-spamfree) or from the [official WP-SpamFree page on WordPress.org](http://wordpress.org/extend/plugins/wp-spamfree/).