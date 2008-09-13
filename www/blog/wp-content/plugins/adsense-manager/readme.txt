=== AdSense Manager ===
Contributors: mutube
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=martin%2efitzpatrick%40gmail%2ecom&item_name=Donation%20to%20mutube%2ecom&currency_code=USD&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: adsense, ad, link, referral, manage, widget, google, adbrite, cj, adpinion, shoppingads, ypn, widgetbucks
Requires at least: 2.5.0
Stable tag: 3.2.13

AdSense Manager simplifies managing AdSense and other Ads Networks on your blog. It generates code automatically and allows positioning with widgets, code or inline tags.

== Description ==

AdSense Manager is a Wordpress plugin for managing AdSense ads on your blog. It generates code automatically and allows positioning with Widgets.

Version 3.1.x now supports [AdSense](http://www.google.com/adsense), [AdBrite](http://www.adbrite.com/mb/landing_both.php?spid=51549&afb=120x60-1-blue), [AdGridWork](http://www.adgridwork.com/?r=18501), [Adpinion](http://www.adpinion.com/), [Adroll](http://re.adroll.com/a/D44UNLTJPNH5ZDXTTXII7V/7L73RCFU5VCG7FRNNIGH7O/d6ca1e265e654df2010a2153d5c42ed4.re), [Commission Junction](http://www.cj.com/), [CrispAds](http://www.crispads.com/), [ShoppingAds](http://www.shoppingads.com/refer_1ebff04bf5805f6da1b4), [Yahoo!PN](http://ypn.yahoo.com/) and [WidgetBucks](http://www.widgetbucks.com/home.page?referrer=468034).

Automatic Ad Code Importer for all supported networks.
Widgets & Sidebar Modules compatible (as used in the popular K2 theme).
Automatic limiting of Ads to meet network T&Cs (Google 3 units/page)

[Extended instructions are available here...](http://www.mutube.com/mu/getting-started-with-adsense-manager-3x).

You may opt to support development of this plugin by donating a % of your Ad space to raise funds for AdSense Manager. All ads are hand-selected and guaranteed to be family friendly.  Thanks for your support, it makes a difference!

This plugin is under active development: if you experience problems, please first make sure you have the latest version installed. Feature requests, bug reports and comments can be submitted [here](http://www.mutube.com/mu/getting-started-with-adsense-manager-3x/).

== Installation ==

1. Unzip the downloaded package and upload the Adsense Manager folder into your Wordpress plugins folder
1. Log into your WordPress admin panel
1. Go to Plugins and “Activate” the plugin
1. Previous installations will be updated and a notice displayed. If you have not used AdSense Manager before but have used AdSense Deluxe, you will be offered the change to import those ads.
1. “Adsense Manager” will now be displayed in your Options section and “Ad Units” appears under Manage.
1. For first step instructions, go to Options &raquo; AdSense
1. Import, create and modifty your Ad blocks under Manage &raquo; Ad Units
1. [Complete usage instructions are available here.](http://www.mutube.com/mu/getting-started-with-adsense-manager-3x)

== Frequently Asked Questions ==

= What is Be Nice? =

Be Nice is a way for you to support development of this plugin without donating hard cash. Under Options &raquo; AdSense you can select to donate a percentage of your ad space to development of this plugin. It won't make me rich (I wish) but it does help guarantee development time and played an important role in putting aside time for v3.x rewrites.

= What if I'm Not Nice? =

Nothing. It's entirely up to you: the plugin will function identically whether you donate ad space or not.

If you are able [please consider making a PayPal donation instead](https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=martin%2efitzpatrick%40gmail%2ecom&item_name=Donation%20to%20mutube%2ecom&currency_code=USD&bn=PP%2dDonationsBF&charset=UTF%2d8).

= What do you spend the money on? =

Food, clothes, course books, etc.  I'm currently studying [this](http://www.undergraduate.bham.ac.uk/coursefinder/medicine/medical-sci.shtml), [here](http://www.bham.ac.uk).

= Why does changing Ad Format/Dimensions sometimes not change the size of the ad? =

For some ad networks (e.g. WidgetBucks, Adroll, etc.) the dimensions of ads are managed through the online interface. There is no way to change these settings from within the WordPress system that would work reliably. You do not have to update these dimension settings if you update your Ad online, however, it can be useful in correctly identifying 'Alternate Ads' for AdSense blocks.

= Do I still need AdSense Manager now I can manage ads through Google's system? =

No, and Yes. While the original purpose of being able to modify colours etc. without digging into code is now gone (although still supported) there are other advantages to AdSense Manager. For example: positioning.  Additionally there are some plans afoot to provide intelligent ad placing methods to make all this work even better.

= How do I place Ad code at the top, bottom, left, right, etc. of the page? =

There is a (nice tutorial here)[http://www.tamba2.org.uk/wordpress/adsense/] which explains positioning using code in template files. You can use this together with AdSense Manager: just place the ad code tags <?php adsensem_ad(); ?> where it says "place code here". 

= Upgrading has gone a bit wrong... What can I do? =

To revert to an old copy of your Ad database, go to your Dashboard and add ?adsensem-revert-db=X to your URL. Replace X with the major version that you want to revert to.
 
If the latest version to work was 2.1, enter: ?adsensem-revert-db=2

Load the page and AdSense Manager will revert to that version of the database and re-attempt the upgrade.

= What else do you have planned? =

1. Ad Zones to allow grouping of ads at a particular location, and switching depending on the visitors language, country, etc.
1. Auto-inserting of ads into posts based on configurable rules (i.e. All Posts, 2nd Paragraph)
1. Localisation: multi-language support
1. Support for Amazon Affiliates and any other networks I hear about.

= Where can I get more information? =

[Complete usage instructions are available here.](http://www.mutube.com/mu/getting-started-with-adsense-manager-3x)

== To Do ==

* Revenue sharing, integrate with Author Advertising plugin
* Check bug(?) with ad counting (3 max ads Google)
* Check compatibility with ALinks
* Convert to Ad Zones with drag drop and auto dimensions/etc.
* Auto-insertion of ads into posts, pages, between comments, etc.

== Change Log ==

By popular demand, below are the changes for versions listed. Use this to determine whether it is worth upgrading and also to see when bugs you've reported have been fixed.

As a general rule the version X.Y.Z increments Z with bugfixes, Y with additional features, and X with major overhaul.

* **3.2.13** Fix for WordPress 2.3.3 compatibility.
* **3.2.11** Database/bugfixing code, only neccessary if you're experiencing errors.
* **3.2.10** Database/bugfixing code, only neccessary if you're experiencing errors.
* **3.2.9** Database/bugfixing code, only neccessary if you're experiencing errors.
* **3.2.8** Upgrade fixes, should fix ->network errors, see plugin homepage for instructions how to fix if you're stuck here.
* **3.2.7** Fixes to Javascript errors (minor, will not impact plugin function). Upgrade fix. Prevents error on 2.5>3.2
* **3.2.6** Default ad checking fix. Ads will continue to work even if default-ad not set. Fixed Javascript errors.
* **3.2.5** Fix to widgets to match updated WordPress code. May require replacement of widgets again. Fix to default ad selection, prevents errors in Widgets & ensures ads appear on site.
* **3.2.4** Bugfixes to upgrade path from 2.5, prevents requirement to open/save each ad unit. Account ID is now copied across correctly during upgrades.

