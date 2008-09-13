<?php
/*
Plugin Name: AdSense Manager
PLugin URI: http://wordpress.org/extend/plugins/adsense-manager/
Description: Control and arrange your AdSense & Referral blocks on your Wordpress blog. With Widget and inline post support, configurable colours. 
Author: Martin Fitzpatrick
Version: 3.2.13
Author URI: http://www.mutube.com/
*/
@define("ADSENSEM_VERSION", "3.2.13");

/*
TODO:

Defaults
* highlight the fields for which there are default settings
*/


@define('ADSENSEM_DIRPATH','/wp-content/plugins' . strrchr(dirname(__FILE__),'/') . "/");

/*
	CONSTANTS FOR CONFIGURATION
*/

//Currently not used
//@define("ADSENSEM_MAX_ADS", 7); //Max Google Ad units
//@define("ADSENSEM_MAX_REFERRALS", 7); //Max Google Referral units
		
@define("ADSENSEM_BE_NICE", 3); //Default level only, can be changed in Options/AdSense Manager

		
/*  Copyright 2006  MARTIN FITZPATRICK  (email : martin.fitzpatrick@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


//Now ad networks are managed by dedicated classes. How modern.
		
require_once('class-generic.php');
	
require_once('class-adsense.php'); //Adsense (using Slots, etc.)
require_once('class-adsense-classic.php'); //AdSense units pre-slot style

require_once('class-adbrite.php'); //AdBrite
require_once('class-adgridwork.php'); //AdGridWork
require_once('class-adpinion.php'); //Adpinion
require_once('class-adroll.php'); //AdRoll
require_once('class-cj.php'); //Commission Junction
require_once('class-code.php'); //HTML Code
require_once('class-crispads.php'); //Crisp Ads
require_once('class-shoppingads.php'); //ShoppingAds
require_once('class-ypn.php'); //Yahoo! Publisher Network - not yet supported
require_once('class-widgetbucks.php'); //Widgetbucks

$_adsensem = get_option('plugin_adsensem');
$_adsensem_notices = array();
$_adsensem_counters = array();

//Array ( [ads] => Array ( [ad-1] => Ad_AdSense Object ( [title] => [p] => Array ( [html-before] => [html-after] => [show-home] => [show-post] => [show-page] => [show-archive] => [show-search] => [adformat] => [code] => [notes] => 160x90, created 4/16/08 5 Link for side bar [height] => [width] => [slot] => 6983861882 [adtype] => link ) [name] => ad-1 ) [ad-2] => Ad_AdSense Object ( [title] => [p] => Array ( [html-before] => [html-after] => [show-home] => [show-post] => [show-page] => [show-archive] => [show-search] => [adformat] => [code] => [notes] => 160x90, created 4/16/08 5 Link for side bar [height] => [width] => [slot] => 6983861882 [adtype] => link ) [name] => ad-2 ) [co-1] => Ad_Code Object ( [title] => [p] => Array ( [html-before] => [html-after] => [show-home] => [show-post] => [show-page] => [show-archive] => [show-search] => [adformat] => [code] => [notes] => [height] => [width] => ) [name] => co-1 ) ) [defaults] => Array ( [ad_adsense] => Array ( [html-before] => [html-after] => [show-home] => yes [show-post] => yes [show-page] => yes [show-archive] => yes [show-search] => yes [adformat] => 120x240 [code] => [notes] => 120x240, created 4/15/08 [height] => 240 [width] => 120 [slot] => [adtype] => ad ) [ad_adsense_classic] => Array ( [show-home] => yes [show-post] => yes [show-page] => yes [show-archive] => yes [show-search] => yes [html-before] => [html-after] => [color-border] => FFFFFF [color-title] => 0000FF [color-bg] => FFFFFF [color-text] => 000000 [color-link] => 008000 [channel] => [uistyle] => [slot] => [adformat] => 250x250 [adtype] => text_image [linkformat] => 120x90 [linktype] => _0ads_al_s ) [ad_code] => Array ( [show-home] => yes [show-post] => yes [show-page] => yes [show-archive] => yes [show-search] => yes [html-before] => [html-after] => ) ) [account-ids] => Array ( [ad_adsense] => 3904618150325763 ) [be-nice] => 3 [version] => 3.2.11 [default-ad] => ad-1 )

/*

   STANDARD OUTPUT FUNCTIONS
   These are out of the main function block below so they can be called
   from outside "widget-space".  This means we can re-use code for widget
   and non-widget versions

*/

//Kept external for backward compatibility
if(!function_exists('adsensem_ad')) {
	function adsensem_ad($name=false) {
		global $_adsensem;
		if($name===false)
			{$ad=$_adsensem['ads'][$_adsensem['default-ad']];}	
		else
			{$ad=$_adsensem['ads'][$name];}
		if(is_object($ad)){
			if($ad->show_ad_here()){
				echo $ad->get_ad();
				$ad->counter_click();	
			} 
		}
	}
}


/*

  INITIALISATION
  All functions in here called at startup (after other plugins have loaded, in case
  we need to wait for the widget-plugin).
*/

class adsensem {
	
	//STARTUP INITIALISATION
		function init(){
		
			global $_adsensem;
			
			//Only run main site code if setup & functional
			if(adsensem::setup_is_valid()){
				add_filter('the_content', array('adsensem','filter_ads')); 
				add_action('wp_footer', array('adsensem','footer'));
			}
		}
		
		function init_admin(){
			//Pull in the admin functions before triggering
			require_once('class-admin.php');
			adsensem_admin::init_admin();
		}
	



		function setup_is_valid(){ //Check we have ads, (removed default-ad checks as causes problems where is not set, not essential.
		  global $_adsensem;
		    if(is_array($_adsensem)){ 
		      if(is_array($_adsensem['ads'])){ 
			  return true;
		      }
		    }
		  return false;
		}
		
		function init_widgets()
		{
			global $_adsensem;
			/* SITE SECTION: WIDGET DISPLAY CODE
			/* Add the blocks to the Widget panel for positioning WP2.2+*/
			
			if (function_exists('wp_register_sidebar_widget') || function_exists('register_sidebar_module') )
			{
				
				/* Default Ad widget */
				if(is_object($_adsensem['ads'][$_adsensem['default-ad']])){ adsensem::register_widget('default-ad',$args); }
				
				/* Loop through available ads and generate widget one at a time */
				if(is_array($_adsensem['ads'])){
					foreach($_adsensem['ads'] as $name => $ad){
					    $args = array('name' => $name, 'height' => 80, 'width' => 300);
					    adsensem::register_widget($name,$args);
					}
				}
      }
		}
	
		
		function register_widget($name,$args){

			if(function_exists('wp_register_sidebar_widget')){
				//$id, $name, $output_callback, $options = array()
				wp_register_sidebar_widget('adsensem-' . $name,"Ad#$name", array('adsensem','widget'),$args,$name);
				wp_register_widget_control('adsensem-' . $name,"Ad#$name",  array('adsensem','widget_control'), $args,$name); 
			} else if (function_exists('register_sidebar_module') ){
				register_sidebar_module('Ad #' . $name, 'adsensem_sbm_widget', 'adsensem-' . $name, $args );
				register_sidebar_module_control('Ad #' . $name, array('adsensem','widget_control'), 'adsensem-' . $name);
			}			
		}
		
		
	// This is the function that outputs adsensem widget.
	function widget($args,$n='') {
	  // $args is an array of strings that help widgets to conform to
	  // the active theme: before_widget, before_title, after_widget,
	  // and after_title are the array keys. Default tags: li and h2.
	  extract($args); //nb. $name comes out of this, hence the use of $n
	  global $_adsensem;
      
	  //If name not passed in (Sidebar Modules), extract from the widget-id (WordPress Widgets)
	  if($n==''){ $n=substr($args['widget_id'],9); } //Chop off beginning adsensem- bit
	  if($n!=='default-ad'){$ad = $_adsensem['ads'][$n];} else {$ad = $_adsensem['ads'][$_adsensem['default-ad']];}

	  if($ad->show_ad_here()){
	    echo $before_widget;
	    if($ad->title!=''){ echo $before_title . $ad->title . $after_title; }
	    echo $ad->get_ad(); //Output the selected ad
	    echo $after_widget;
	    $ad->counter_click();
	  }

	}
  
		/* Widget admin block for each Ad element on the page, allows
			movement of them around the sidebar */
	  function widget_control($name)
		{
			global $_adsensem;
							
			if ( $_POST['adsensem-' . $name . '-submit'] ) {
				global $_adsensem;
				// Remember to sanitize and format use input appropriately.
				$_adsensem['ads'][$name]->title = strip_tags(stripslashes($_POST['adsensem-' . $name . '-title']));
				update_option('plugin_adsensem', $_adsensem);
			}

			?>
			<label for="adsensem-<?php echo $name; ?>-title" >Title:</label><input style="width: 200px;" id="adsensem-<?php echo $name; ?>-title" name="adsensem-<?php echo $name; ?>-title" type="text" value="<?php echo htmlspecialchars($_adsensem['ads'][$name]->title, ENT_QUOTES);?>" />
			<input type="hidden" name="adsensem-<?php echo $name; ?>-submit" value="1">
			<?php

        }
			
	function footer(){
	?><!-- AdSense Manager v<?php echo ADSENSEM_VERSION;?> (<?php timer_stop(1); ?> seconds.) --><?php
	}


	function filter_ad_callback($matches){
		global $_adsensem;
			
			if($matches[1]==''){ /* default ad */ $matches[1]=$_adsensem['default-ad']; }
			
			if(isset($_adsensem['ads'][$matches[1]])){
				$ad=$_adsensem['ads'][$matches[1]];
				if($ad->show_ad_here()){
					return $ad->get_ad();
				} 
			}
			return '';
		}
		

		/* This filter parses post content and replaces markup with the correct ad,
			<!--adsense#name--> for named ad or <!--adsense--> for default */
		function filter_ads($content) {
		global $_adsensem;
			if(is_object($_adsensem['ads'][$_adsensem['default-ad']])){
				$content=preg_replace_callback(array("/<!--adsense-->/","/<!--am-->/","/\[ad\]/"),array('adsensem','filter_ad_callback'),$content);
			}
		
			$content=preg_replace_callback(array("/<!--adsense#(.*)-->/","/<!--am#(.*)-->/","/\[ad#(.*)\]/"),array('adsensem','filter_ad_callback'),$content);
			
		return $content;
		}

}















/* SHOW ALTERNATE AD UNITS */
if ($_REQUEST['adsensem-show-ad']){
	?><html><body><?php
	adsensem_ad($_REQUEST['adsensem-show-ad']);
	?></body></html><?php
	die(0);
}
/* END SHOW ALTERNATE AD UNITS */

/* SHOW BENICE UNITS */
if ($_REQUEST['adsensem-benice']){
	?><html><body><?php
	echo adsensem_benice::benice_code($_REQUEST['adsensem-benice']); //contains format
	?></body></html><?php
	die(0);
}
/* END BENICE UNITS */

if(is_admin()){

require_once('class-admin.php');

/* REVERT TO PREVIOUS BACKUP OF AD DATABASE */
if ($_REQUEST['adsensem-revert-db']){
	$backup=get_option('plugin_adsensem_backup');
	$_adsensem=$backup[$_REQUEST['adsensem-revert-db']];
	update_option('plugin_adsensem',$_adsensem);
	if($_REQUEST['adsensem-block-upgrade']){die();}
}
/* END REVERT TO PREVIOUS BACKUP OF AD DATABASE */


/* PRE-OUTPUT PROCESSING - e.g. NOTICEs (upgrade-adsense-deluxe) */
switch ($_POST['adsensem-mode'].':'.$_POST['adsensem-action']){
	case 'notice:upgrade adsense-deluxe':
		if($_POST['adsensem-notice-confirm-yes']){
			require_once('class-upgrade.php');
			adsensem_upgrade::adsense_deluxe_to_3_0();
			adsensem_admin::remove_notice('upgrade adsense-deluxe');
		} else {
			adsensem_admin::remove_notice('upgrade adsense-deluxe');
		}
	break;	
	case 'notice:upgrade adsense-manager':
		adsensem_admin::remove_notice('upgrade adsense-manager');
	break;
}
/* END PRE-OUTPUT PROCESSING */
}



/* SIDEBAR MODULES COMPATIBILITY FUNCTION */
	function adsensem_sbm_widget($args){
		global $k2sbm_current_module;
		adsensem::widget($args,$k2sbm_current_module->options['name']);
	}
/* SIDEBAR MODULES COMPATIBILITY FUNCTION */



add_action('plugins_loaded', array('adsensem','init'), 1);	
add_action('admin_menu', array('adsensem','init_admin'));

add_action('widgets_init',  array('adsensem','init_widgets'), 1);	

function kda_mce_callback(){
//	echo "	handle_event_callback : \"scheduleScan\",\n";
}
add_action('mce_options', 'kda_mce_callback');

?>
