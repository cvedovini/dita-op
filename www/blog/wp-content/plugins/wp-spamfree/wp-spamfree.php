<?php
/*
Plugin Name: WP-SpamFree
Plugin URI: http://www.hybrid6.com/webgeek/plugins/wp-spamfree
Description: An extremely powerful anti-spam plugin that virtually eliminates comment spam. Finally, you can enjoy a spam-free WordPress blog! Includes spam-free contact form feature as well.
Author: Scott Allen, aka WebGeek
Version: 1.9.6.6
Author URI: http://www.hybrid6.com/webgeek/
*/

/*  Copyright 2007-2008    Scott Allen  (email : scott [at] hybrid6 [dot] com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Begin the Plugin

function spamfree_init() {
	$wpSpamFreeVer='1.9.6.6';
	update_option('wp_spamfree_version', $wpSpamFreeVer);
	spamfree_update_keys(0);
	}
	
function spamfree_create_random_key() {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    while ($i <= 7) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $keyCode = $keyCode . $tmp;
        $i++;
    	}
		
	if ($keyCode=='') {
		srand((double)74839201183*1000000);
    	$i = 0;
    	$pass = '' ;

    	while ($i <= 7) {
        	$num = rand() % 33;
        	$tmp = substr($chars, $num, 1);
        	$keyCode = $keyCode . $tmp;
        	$i++;
    		}
		}
    return $keyCode;
	}
	
function spamfree_update_keys($reset_keys) {
	$spamfree_options 								= get_option('spamfree_options');

	// Set Random Cookie Name
	$CookieValidationName = $spamfree_options['cookie_validation_name'];
	if (!$CookieValidationName||$reset_keys==1) {
		$randomComValCodeCVN1 = spamfree_create_random_key();
		$randomComValCodeCVN2 = spamfree_create_random_key();
		$CookieValidationName = $randomComValCodeCVN1.$randomComValCodeCVN2;
		}
	// Set Random Cookie Value
	$CookieValidationKey = $spamfree_options['cookie_validation_key'];
	if (!$CookieValidationKey||$reset_keys==1) {
		$randomComValCodeCKV1 = spamfree_create_random_key();
		$randomComValCodeCKV2 = spamfree_create_random_key();
		$CookieValidationKey = $randomComValCodeCKV1.$randomComValCodeCKV2;
		}
	// Set Random Form Field Name
	$FormValidationFieldJS = $spamfree_options['form_validation_field_js'];
	if (!$FormValidationFieldJS||$reset_keys==1) {
		$randomComValCodeJSFFN1 = spamfree_create_random_key();
		$randomComValCodeJSFFN2 = spamfree_create_random_key();
		$FormValidationFieldJS = $randomComValCodeJSFFN1.$randomComValCodeJSFFN2;
		}
	// Set Random Form Field Value
	$FormValidationKeyJS = $spamfree_options['form_validation_key_js'];
	if (!$FormValidationKeyJS||$reset_keys==1) {
		$randomComValCodeJS1 = spamfree_create_random_key();
		$randomComValCodeJS2 = spamfree_create_random_key();
		$FormValidationKeyJS = $randomComValCodeJS1.$randomComValCodeJS2;
		}
	$spamfree_options_update = array (
		'cookie_validation_name' 				=> $CookieValidationName,
		'cookie_validation_key' 				=> $CookieValidationKey,
		'form_validation_field_js' 				=> $FormValidationFieldJS,
		'form_validation_key_js' 				=> $FormValidationKeyJS,
		'wp_cache' 								=> $spamfree_options['wp_cache'],
		'wp_super_cache' 						=> $spamfree_options['wp_super_cache'],
		'use_captcha_backup' 					=> $spamfree_options['use_captcha_backup'],
		'block_all_trackbacks' 					=> $spamfree_options['block_all_trackbacks'],
		'block_all_pingbacks' 					=> $spamfree_options['block_all_pingbacks'],
		'use_trackback_verification'		 	=> $spamfree_options['use_trackback_verification'],
		'form_include_website' 					=> $spamfree_options['form_include_website'],
		'form_require_website' 					=> $spamfree_options['form_require_website'],
		'form_include_phone' 					=> $spamfree_options['form_include_phone'],
		'form_require_phone' 					=> $spamfree_options['form_require_phone'],
		'form_include_drop_down_menu'			=> $spamfree_options['form_include_drop_down_menu'],
		'form_require_drop_down_menu'			=> $spamfree_options['form_require_drop_down_menu'],
		'form_drop_down_menu_title'				=> $spamfree_options['form_drop_down_menu_title'],
		'form_drop_down_menu_item_1'			=> $spamfree_options['form_drop_down_menu_item_1'],
		'form_drop_down_menu_item_2'			=> $spamfree_options['form_drop_down_menu_item_2'],
		'form_drop_down_menu_item_3'			=> $spamfree_options['form_drop_down_menu_item_3'],
		'form_drop_down_menu_item_4'			=> $spamfree_options['form_drop_down_menu_item_4'],
		'form_drop_down_menu_item_5'			=> $spamfree_options['form_drop_down_menu_item_5'],
		'form_drop_down_menu_item_6'			=> $spamfree_options['form_drop_down_menu_item_6'],
		'form_drop_down_menu_item_7'			=> $spamfree_options['form_drop_down_menu_item_7'],
		'form_drop_down_menu_item_8'			=> $spamfree_options['form_drop_down_menu_item_8'],
		'form_drop_down_menu_item_9'			=> $spamfree_options['form_drop_down_menu_item_9'],
		'form_drop_down_menu_item_10'			=> $spamfree_options['form_drop_down_menu_item_10'],
		'form_message_width' 					=> $spamfree_options['form_message_width'],
		'form_message_height' 					=> $spamfree_options['form_message_height'],
		'form_message_min_length'				=> $spamfree_options['form_message_min_length'],
		'form_message_recipient'				=> $spamfree_options['form_message_recipient'],
		);
	update_option('spamfree_options', $spamfree_options_update);		
	}
	
function spamfree_count() {
	$spamfree_count = get_option('spamfree_count');	
	return $spamfree_count;
	}
	

function spamfree_counter($counter_option) {
	$counter_option_max = 6;
	$counter_option_min = 1;
	if ( !$counter_option || $counter_option > $counter_option_max || $counter_option < $counter_option_min ) {
		$spamfree_count = number_format( get_option('spamfree_count') );
		echo '<a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree" style="text-decoration:none;" rel="external" title="WP-SpamFree Anti-Spam Plugin for WordPress" >'.$spamfree_count.' spam killed by WP-SpamFree</a>';
		return;
		}
	// Display Counter
	/* Implementation: <?php if ( function_exists(spamfree_counter) ) { spamfree_counter(1); } ?> */
	$spamfree_count = number_format( get_option('spamfree_count') );
	$counter_div_height = array('0','66','66','66','106','61','67');
	$counter_count_padding_top = array('0','11','11','11','79','14','17');
	?>
	
	<style type="text/css">
	
	#spamfree_counter_wrap {color:#ffffff;text-decoration:none;width:140px;}
	#spamfree_counter {background:url(<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-spamfree/counter/spamfree-counter-bg-<?php echo $counter_option; ?>.png) no-repeat top left;height:<?php echo $counter_div_height[$counter_option]; ?>px;width:140px;overflow:hidden;border-style:none;color:#ffffff;Arial,Helvetica,sans-serif;font-weight:bold;line-height:100%;text-align:center;padding-top:<?php echo $counter_count_padding_top[$counter_option]; ?>px;}
	
	</style>
	
	<div id="spamfree_counter_wrap" >
		<div id="spamfree_counter" >
		<?php 
			if ( $counter_option >= 1 && $counter_option <= 3 ) {
				echo '<strong style="color:#ffffff;font:Arial,Helvetica,sans-serif;font-weight:bold;line-height:100%;text-align:center;text-decoration:none;border-style:none;"><a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree" style="color:#ffffff;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;" rel="external" title="Spam Killed by WP-SpamFree" >';
				echo '<span style="color:#ffffff;font-size:20px;line-height:100%;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;">'.$spamfree_count.'</span><br />'; 
				echo '<span style="color:#ffffff;font-size:14px;line-height:110%;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;">SPAM KILLED</span><br />'; 
				echo '<span style="color:#ffffff;font-size:9px;line-height:120%;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;">BY WP-SPAMFREE</span>';
				echo '</a></strong>'; 
				}
			else if ( $counter_option == 4 ) {
				echo '<strong style="color:#000000;font:Arial,Helvetica,sans-serif;font-weight:bold;line-height:100%;text-align:center;text-decoration:none;border-style:none;"><a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree" style="color:#000000;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;" rel="external" title="Spam Killed by WP-SpamFree" >';
				echo '<span style="color:#000000;font-size:9px;line-height:100%;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;">'.$spamfree_count.' SPAM KILLED</span><br />'; 
				echo '</a></strong>'; 
				}
			else if ( $counter_option == 5 ) {
				echo '<strong style="color:#FEB22B;font:Arial,Helvetica,sans-serif;font-weight:bold;line-height:100%;text-align:center;text-decoration:none;border-style:none;"><a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree" style="color:#FEB22B;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;" rel="external" title="Spam Killed by WP-SpamFree" >';
				echo '<span style="color:#FEB22B;font-size:14px;line-height:100%;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;">'.$spamfree_count.'</span><br />'; 
				echo '</a></strong>'; 
				}
			else if ( $counter_option == 6 ) {
				echo '<strong style="color:#000000;font:Arial,Helvetica,sans-serif;font-weight:bold;line-height:100%;text-align:center;text-decoration:none;border-style:none;"><a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree" style="color:#000000;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;" rel="external" title="Spam Killed by WP-SpamFree" >';
				echo '<span style="color:#000000;font-size:14px;line-height:100%;font:Arial,Helvetica,sans-serif;font-weight:bold;text-decoration:none;border-style:none;">'.$spamfree_count.'</span><br />'; 
				echo '</a></strong>'; 
				}

		?>
		</div>
	</div>
	
	<?php
	}

function spamfree_comment_form() {

	echo '<noscript><p><strong>Currently you have JavaScript disabled. In order to post comments, please make sure JavaScript and Cookies are enabled, and reload the page.</strong></p></noscript>';
	}
	
function spamfree_contact_form($content) {
	$spamfree_contact_form_url = $_SERVER['REQUEST_URI'];
	if ( $_SERVER['QUERY_STRING'] ) {
		$spamfree_contact_form_query_op = '&amp;';
		}
	else {
		$spamfree_contact_form_query_op = '?';
		}
	$spamfree_contact_form_content = '';
	if ( is_page() && ( !is_home() && !is_feed() && !is_archive() && !is_search() && !is_404() ) ) {

		$spamfree_options				= get_option('spamfree_options');
		$CookieValidationName  			= $spamfree_options['cookie_validation_name'];
		$CookieValidationKey 			= $spamfree_options['cookie_validation_key'];
		$WPCommentValidationJS 			= $_COOKIE[$CookieValidationName];
		$FormIncludeWebsite				= $spamfree_options['form_include_website'];
		$FormRequireWebsite				= $spamfree_options['form_require_website'];
		$FormIncludePhone				= $spamfree_options['form_include_phone'];
		$FormRequirePhone				= $spamfree_options['form_require_phone'];
		$FormIncludeDropDownMenu		= $spamfree_options['form_include_drop_down_menu'];
		$FormRequireDropDownMenu		= $spamfree_options['form_require_drop_down_menu'];
		$FormDropDownMenuTitle			= $spamfree_options['form_drop_down_menu_title'];
		$FormDropDownMenuItem1			= $spamfree_options['form_drop_down_menu_item_1'];
		$FormDropDownMenuItem2			= $spamfree_options['form_drop_down_menu_item_2'];
		$FormDropDownMenuItem3			= $spamfree_options['form_drop_down_menu_item_3'];
		$FormDropDownMenuItem4			= $spamfree_options['form_drop_down_menu_item_4'];
		$FormDropDownMenuItem5			= $spamfree_options['form_drop_down_menu_item_5'];
		$FormDropDownMenuItem6			= $spamfree_options['form_drop_down_menu_item_6'];
		$FormDropDownMenuItem7			= $spamfree_options['form_drop_down_menu_item_7'];
		$FormDropDownMenuItem8			= $spamfree_options['form_drop_down_menu_item_8'];
		$FormDropDownMenuItem9			= $spamfree_options['form_drop_down_menu_item_9'];
		$FormDropDownMenuItem10			= $spamfree_options['form_drop_down_menu_item_10'];
		$FormMessageWidth				= $spamfree_options['form_message_width'];
		$FormMessageHeight				= $spamfree_options['form_message_height'];
		$FormMessageMinLength			= $spamfree_options['form_message_min_length'];
		$FormMessageRecipient			= $spamfree_options['form_message_recipient'];
		
		if ( $FormMessageWidth < 40 ) {
			$FormMessageWidth = 40;
			}
			
		if ( $FormMessageHeight < 5 ) {
			$FormMessageHeight = 5;
			}
		else if ( !$FormMessageHeight ) {
			$FormMessageHeight = 10;
			}
			
		if ( $FormMessageMinLength < 15 ) {
			$FormMessageMinLength = 15;
			}
		else if ( !$FormMessageMinLength ) {
			$FormMessageMinLength = 25;
			}

		if ( $_GET['form'] == 'response' ) {
		
			// PROCESSING CONTACT FORM :: BEGIN
			$wpsf_contact_name 				= Trim(stripslashes(strip_tags($_POST['wpsf_contact_name'])));
			$wpsf_contact_email 			= Trim(stripslashes(strip_tags($_POST['wpsf_contact_email'])));
			$wpsf_contact_website 			= Trim(stripslashes(strip_tags($_POST['wpsf_contact_website'])));
			$wpsf_contact_phone 			= Trim(stripslashes(strip_tags($_POST['wpsf_contact_phone'])));
			$wpsf_contact_drop_down_menu	= Trim(stripslashes(strip_tags($_POST['wpsf_contact_drop_down_menu'])));
			$wpsf_contact_subject 			= Trim(stripslashes(strip_tags($_POST['wpsf_contact_subject'])));
			$wpsf_contact_message 			= Trim(stripslashes(strip_tags($_POST['wpsf_contact_message'])));
			/*
			$wpsf_contact_cc 				= Trim(stripslashes(strip_tags($_POST['wpsf_contact_cc'])));
			*/
			// PROCESSING CONTACT FORM :: END
			
			/*
			if ( !$wpsf_contact_cc ) {
				$wpsf_contact_cc ='No';
				}
			*/
			
			// FORM INFO :: BEGIN
			
			if ( $FormMessageRecipient ) {
				$wpsf_contact_form_to			= $FormMessageRecipient;
				}
			else {
				$wpsf_contact_form_to 			= get_option('admin_email');
				}
			//$wpsf_contact_form_to 			= get_option('admin_email');
			//$wpsf_contact_form_cc_to 			= $wpsf_contact_email;
			$wpsf_contact_form_to_name 			= $wpsf_contact_form_to;
			//$wpsf_contact_form_cc_to_name 		= $wpsf_contact_name;
			$wpsf_contact_form_subject 			= '[Website Contact] '.$wpsf_contact_subject;
			//$wpsf_contact_form_cc_subject		= '[Website Contact CC] '.$wpsf_contact_subject;
			$wpsf_contact_form_msg_headers 		= "From: $wpsf_contact_name <$wpsf_contact_email>" . "\r\n" . "X-Mailer: PHP/" . phpversion();
			// FORM INFO :: END
			
			// TEST TO PREVENT CONTACT FORM SPAM FROM BOTS :: BEGIN
			
			$ip = $_SERVER['REMOTE_ADDR'];
			$ReverseDNS = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			$wpsf_contact_message_lc = strtolower( $wpsf_contact_message );
			
			if ( $WPCommentValidationJS == $CookieValidationKey ) { // Contact Form Message Allowed

				// ERROR CHECKING
				
				
				$contact_form_spam_1_count = substr_count( $wpsf_contact_message_lc, 'link'); //10
				$contact_form_spam_1_limit = 7;
				$contact_form_spam_2_count = substr_count( $wpsf_contact_message_lc, 'link building'); //4
				$contact_form_spam_2_limit = 3;
				$contact_form_spam_3_count = substr_count( $wpsf_contact_message_lc, 'link exchange');
				$contact_form_spam_3_limit = 2;
				$contact_form_spam_4_count = substr_count( $wpsf_contact_message_lc, 'link request'); // Subject
				$contact_form_spam_4_limit = 1;
				$contact_form_spam_5_count = substr_count( $wpsf_contact_message_lc, 'link building service');
				$contact_form_spam_5_limit = 2;
				$contact_form_spam_6_count = substr_count( $wpsf_contact_message_lc, 'link building experts india'); // 2
				$contact_form_spam_6_limit = 0;
				$contact_form_spam_7_count = substr_count( $wpsf_contact_message_lc, 'india'); //2
				$contact_form_spam_7_limit = 1;
				
				
				$wpsf_contact_subject_lc = strtolower( $wpsf_contact_subject );
				$contact_form_spam_subj_1_count = substr_count( $wpsf_contact_subject_lc, 'link request'); // Subject
				$contact_form_spam_subj_1_limit = 0;
				$contact_form_spam_subj_2_count = substr_count( $wpsf_contact_subject_lc, 'link exchange'); // Subject
				$contact_form_spam_subj_2_limit = 0;
				
				$contact_form_spam_term_total = $contact_form_spam_1_count + $contact_form_spam_2_count + $contact_form_spam_3_count + $contact_form_spam_4_count + $contact_form_spam_5_count + $contact_form_spam_6_count + $contact_form_spam_7_count + $contact_form_spam_subj_1_count + $contact_form_spam_subj_2_count;
				$contact_form_spam_term_total = 15;
				
				if ( eregi( "\.in$", $ReverseDNS ) ) {
					$contact_form_spam_loc_in = 1;
					}
				if ( ( $contact_form_spam_term_total > $contact_form_spam_term_total_limit || $contact_form_spam_1_count > $contact_form_spam_1_limit || $contact_form_spam_2_count > $contact_form_spam_2_limit || $contact_form_spam_5_count > $contact_form_spam_5_limit || $contact_form_spam_6_count > $contact_form_spam_6_limit ) && ( $contact_form_spam_loc_in || $contact_form_spam_2_count > $contact_form_spam_2_limit ) ) {
					$MessageSpam=1;
					$contact_response_status_message_addendum .= '&bull; Message appears to be spam. Please note that link requests and link exchange requests will be automatically deleted, and are not an acceptable use of this contact form.<br />&nbsp;<br />';
					}
				else if ( $contact_form_spam_subj_1_count > $contact_form_spam_subj_1_limit || $contact_form_spam_subj_2_count > $contact_form_spam_subj_2_limit ) {
					$MessageSpam=1;
					$contact_response_status_message_addendum .= '&bull; Message appears to be spam. Please note that link requests and link exchange requests will be automatically deleted, and are not an acceptable use of this contact form.<br />&nbsp;<br />';
					}
					
				if ( !$wpsf_contact_name || !$wpsf_contact_email || !$wpsf_contact_subject || !$wpsf_contact_message || ( $FormIncludeWebsite && $FormRequireWebsite && !$wpsf_contact_website ) || ( $FormIncludePhone && $FormRequirePhone && !$wpsf_contact_phone ) || ( $FormIncludeDropDownMenu && $FormRequireDropDownMenu && !$wpsf_contact_drop_down_menu ) ) {
					$BlankField=1;
					$contact_response_status_message_addendum .= '&bull; At least one required field was left blank.<br />&nbsp;<br />';
					}
					
				if (!eregi("^([-_\.a-z0-9])+@([-a-z0-9]+\.)+([a-z]{2}|com|net|org|edu|gov|mil|int|biz|pro|info|arpa|aero|coop|name|museum)$",$wpsf_contact_email)) {
					$InvalidValue=1;
					$BadEmail=1;
					$contact_response_status_message_addendum .= '&bull; Please enter a valid email address.<br />&nbsp;<br />';
					}
				
				$wpsf_contact_phone_zero = str_replace( '0', '', $wpsf_contact_phone );
				if ( $FormIncludePhone && $FormRequirePhone && !$wpsf_contact_phone_zero ) {
					$InvalidValue=1;
					$BadPhone=1;
					$contact_response_status_message_addendum .= '&bull; Please enter a valid phone number.<br />&nbsp;<br />';
					}
					
				$MessageLength = strlen( $wpsf_contact_message );
				if ( $MessageLength < $FormMessageMinLength ) {
					$MessageShort=1;
					$contact_response_status_message_addendum .= '&bull; Message too short. Please enter a complete message.<br />&nbsp;<br />';
					}		
				
				if ( !$BlankField && !$InvalidValue && !$MessageShort && !$MessageSpam ) {  
				
					$wpsf_contact_form_msg_1 .= "Message: "."\n";
					$wpsf_contact_form_msg_1 .= $wpsf_contact_message."\n";
					
					$wpsf_contact_form_msg_1 .= "\n";
				
					$wpsf_contact_form_msg_1 .= "Name: ".$wpsf_contact_name."\n";
					$wpsf_contact_form_msg_1 .= "Email: ".$wpsf_contact_email."\n";
					if ( $FormIncludePhone ) {
						$wpsf_contact_form_msg_1 .= "Phone: ".$wpsf_contact_phone."\n";
						}
					if ( $FormIncludeWebsite ) {
						$wpsf_contact_form_msg_1 .= "Website: ".$wpsf_contact_website."\n";
						}
					if ( $FormIncludeDropDownMenu ) {
						$wpsf_contact_form_msg_1 .= $FormDropDownMenuTitle.": ".$wpsf_contact_drop_down_menu."\n";
						}
					
					/*
					$wpsf_contact_form_msg_2 .= "\n";
					$wpsf_contact_form_msg_2 .= "CC Sender: ".$wpsf_contact_cc."\n";	
					*/
					$wpsf_contact_form_msg_2 .= "\n";					
					$wpsf_contact_form_msg_2 .= "User-Agent (Browser/OS): ".$_SERVER['HTTP_USER_AGENT']."\n";
					$wpsf_contact_form_msg_2 .= "\n";
					$wpsf_contact_form_msg_2 .= "Referrer: ".$_SERVER['HTTP_REFERER']."\n";
					$wpsf_contact_form_msg_2 .= "\n";
					$wpsf_contact_form_msg_2 .= "IP Address: ".$_SERVER['REMOTE_ADDR']."\n";
					$wpsf_contact_form_msg_2 .= "Server: ".$_SERVER['REMOTE_HOST']."\n";
					$wpsf_contact_form_msg_2 .= "Reverse DNS: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."\n";
					$wpsf_contact_form_msg_2 .= "IP Address Lookup: http://www.dnsstuff.com/tools/ipall.ch?ip=".$_SERVER['REMOTE_ADDR']."\n";
					
					$wpsf_contact_form_msg_3 .= "\n";
					$wpsf_contact_form_msg_3 .= "\n";
					
					$wpsf_contact_form_msg = $wpsf_contact_form_msg_1.$wpsf_contact_form_msg_2.$wpsf_contact_form_msg_3;
					$wpsf_contact_form_msg_cc = $wpsf_contact_form_msg_1.$wpsf_contact_form_msg_3;
					
					// SEND MESSAGE
					mail( $wpsf_contact_form_to, $wpsf_contact_form_subject, $wpsf_contact_form_msg, $wpsf_contact_form_msg_headers );
										
					$contact_response_status = 'thank-you';
					
					}
				
				}
			
			else {
				update_option( 'spamfree_count', get_option('spamfree_count') + 1 );
				}
			
			// TEST TO PREVENT CONTACT FORM SPAM FROM BOTS :: END
		
			if ( $contact_response_status == 'thank-you' ) {
				$spamfree_contact_form_content .= '<p>Your message was sent successfully. Thank you.<p>&nbsp;</p>'."\n";
				}
			else {
				if ( eregi ( '\&form\=response', $spamfree_contact_form_url ) ) {
					$spamfree_contact_form_back_url = str_replace('&form=response','',$spamfree_contact_form_url );
					}
				else if ( eregi ( '\?form\=response', $spamfree_contact_form_url ) ) {
					$spamfree_contact_form_back_url = str_replace('?form=response','',$spamfree_contact_form_url );
					}
				if ( $MessageSpam ) {
					$contact_response_status_message_addendum .= '<noscript><br />&nbsp;<br />&bull; Currently you have JavaScript disabled.</noscript>'."\n";
					$spamfree_contact_form_content .= '<p><strong>ERROR: <br />&nbsp;<br />'.$contact_response_status_message_addendum.'</strong><p>&nbsp;</p>'."\n";
					}
				else {
					$contact_response_status_message_addendum .= '<noscript><br />&nbsp;<br />&bull; Currently you have JavaScript disabled.</noscript>'."\n";
					$spamfree_contact_form_content .= '<p><strong>ERROR: Please return to the <a href="'.$spamfree_contact_form_back_url.'" >contact form</a> and fill out all required fields. Please make sure JavaScript and Cookies are enabled in your browser.<br />&nbsp;<br />'.$contact_response_status_message_addendum.'</strong><p>&nbsp;</p>'."\n";
					}

				}
			$content_new = str_replace($content, $spamfree_contact_form_content, $content);
			}
		else {		
			$spamfree_contact_form_content .= '<form name="wpsf_contact_form" action="'.$spamfree_contact_form_url.$spamfree_contact_form_query_op.'form=response" method="post" style="text-align:left;" >'."\n";

			$spamfree_contact_form_content .= '<p><label><strong>Name</strong> *<br />'."\n";

			$spamfree_contact_form_content .= '<input type="text" id="wpsf_contact_name" name="wpsf_contact_name" value="" size="40" /> </label></p>'."\n";
			$spamfree_contact_form_content .= '<p><label><strong>Email</strong> *<br />'."\n";
			$spamfree_contact_form_content .= '<input type="text" id="wpsf_contact_email" name="wpsf_contact_email" value="" size="40" /> </label></p>'."\n";
			
			if ( $FormIncludeWebsite ) {
				$spamfree_contact_form_content .= '<p><label><strong>Website</strong> ';
				if ( $FormRequireWebsite ) { 
					$spamfree_contact_form_content .= '*'; 
					}
				$spamfree_contact_form_content .= '<br />'."\n";
				$spamfree_contact_form_content .= '<input type="text" id="wpsf_contact_website" name="wpsf_contact_website" value="" size="40" /> </label></p>'."\n";
				}
				
			if ( $FormIncludePhone ) {
				$spamfree_contact_form_content .= '<p><label><strong>Phone</strong> ';
				if ( $FormRequirePhone ) { 
					$spamfree_contact_form_content .= '*'; 
					}
				$spamfree_contact_form_content .= '<br />'."\n";
				$spamfree_contact_form_content .= '<input type="text" id="wpsf_contact_phone" name="wpsf_contact_phone" value="" size="40" /> </label></p>'."\n";
				}

			if ( $FormIncludeDropDownMenu && $FormDropDownMenuTitle && $FormDropDownMenuItem1 && $FormDropDownMenuItem2 ) {
				$spamfree_contact_form_content .= '<p><label><strong>'.$FormDropDownMenuTitle.'</strong> ';
				if ( $FormRequireDropDownMenu ) { 
					$spamfree_contact_form_content .= '*'; 
					}
				$spamfree_contact_form_content .= '<br />'."\n";
				$spamfree_contact_form_content .= '<select id="wpsf_contact_drop_down_menu" name="wpsf_contact_drop_down_menu" > '."\n";
				$spamfree_contact_form_content .= '<option value="" selected="selected">Please Select</option> '."\n";
				$spamfree_contact_form_content .= '<option value="">--------------------------</option> '."\n";
				if ( $FormDropDownMenuItem1 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem1.'">'.$FormDropDownMenuItem1.'</option> '."\n";
					}
				if ( $FormDropDownMenuItem2 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem2.'">'.$FormDropDownMenuItem2.'</option> '."\n";
					}
				if ( $FormDropDownMenuItem3 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem3.'">'.$FormDropDownMenuItem3.'</option> '."\n";
					}
				if ( $FormDropDownMenuItem4 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem4.'">'.$FormDropDownMenuItem4.'</option> '."\n";
					}
				if ( $FormDropDownMenuItem5 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem5.'">'.$FormDropDownMenuItem5.'</option> '."\n";
					}
				if ( $FormDropDownMenuItem6 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem6.'">'.$FormDropDownMenuItem6.'</option> '."\n";
					}
				if ( $FormDropDownMenuItem7 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem7.'">'.$FormDropDownMenuItem7.'</option> '."\n";
					}
				if ( $FormDropDownMenuItem8 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem8.'">'.$FormDropDownMenuItem8.'</option> '."\n";
					}
				if ( $FormDropDownMenuItem9 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem9.'">'.$FormDropDownMenuItem9.'</option> '."\n";
					}
				if ( $FormDropDownMenuItem10 ) {
					$spamfree_contact_form_content .= '<option value="'.$FormDropDownMenuItem10.'">'.$FormDropDownMenuItem10.'</option> '."\n";
					}
				$spamfree_contact_form_content .= '</select> '."\n";
				$spamfree_contact_form_content .= '</label></p>'."\n";
				}
			
			$spamfree_contact_form_content .= '<p><label><strong>Subject</strong> *<br />'."\n";
    		$spamfree_contact_form_content .= '<input type="text" id="wpsf_contact_subject" name="wpsf_contact_subject" value="" size="40" /> </label></p>'."\n";			

			$spamfree_contact_form_content .= '<p><label><strong>Message</strong> *<br />'."\n";
			$spamfree_contact_form_content .= '<textarea id="wpsf_contact_message" name="wpsf_contact_message" cols="'.$FormMessageWidth.'" rows="'.$FormMessageHeight.'"></textarea> </label></p>'."\n";
			
			$spamfree_contact_form_content .= '<noscript><p><strong>Currently you have JavaScript disabled. In order to use this contact form, please make sure JavaScript and Cookies are enabled, and reload the page.</strong></p></noscript>'."\n";

			$spamfree_contact_form_content .= '<p><input type="submit" value="Send Message" /></p>'."\n";

			$spamfree_contact_form_content .= '<p>* Required Field</p>'."\n";
			$spamfree_contact_form_content .= '<p>&nbsp;</p>'."\n";
			$spamfree_contact_form_content .= '</form>'."\n";
			
			$spamfree_contact_form_ip_bans = array(
													'59.162.251.58',
													'61.24.158.174',
													'64.20.49.178',
													'69.89.31.219',
													'72.249.100.188',
													'74.12.44.78',
													'77.92.88.13',
													'77.92.88.27',
													'78.62.9.58',
													'78.129.202.15',
													'78.129.202.2',
													'78.157.143.202',
													'79.143.176.12',
													'83.7.196.73',
													'86.34.201.86',
													'89.113.78.6',
													'92.241.176.200',
													'92.48.122.2',
													'92.48.122.3',
													'92.48.65.27',
													'92.241.168.216',
													'122.160.70.94',
													'122.162.251.167',
													'123.237.144.189',
													'123.237.144.92',
													'123.237.147.71',
													'193.37.152.242',
													'193.46.236.151',
													'193.46.236.152',
													'193.46.236.234',
													'202.143.112.106',
													'203.190.134.107',
													'206.123.92.245',
													'208.43.196.98',
													'220.224.230.71',
													);
			$commentdata_remote_addr_lc = strtolower($_SERVER['REMOTE_ADDR']);
			$commentdata_remote_host_lc = strtolower($_SERVER['REMOTE_HOST']);
			if ( in_array( $commentdata_remote_addr_lc, $spamfree_contact_form_ip_bans ) || eregi( "^78.129.202.", $commentdata_remote_addr_lc ) || eregi( "^123.237.144.", $commentdata_remote_addr_lc ) || eregi( "^123.237.147.", $commentdata_remote_addr_lc ) || eregi( 'keywordspy.com', $commentdata_remote_host_lc ) || eregi( 'keywordspy.com', $ReverseDNS ) ) {
				$spamfree_contact_form_content = '<strong>Your location has been identified as part of a known spam network. Contact form has been disabled to prevent spam.</strong>';
				}
			
			
		
			$content_new = str_replace('<!--spamfree-contact-->', $spamfree_contact_form_content, $content);
			}

		}
	if ( $_GET['form'] == response ) {
		$content_new = str_replace($content, $spamfree_contact_form_content, $content);
		}
	else {
		$content_new = str_replace('<!--spamfree-contact-->', $spamfree_contact_form_content, $content);
		}
	return $content_new;
	}
	
function spamfree_check_comment_type($commentdata) {
	global $userdata, $user_login, $user_level, $user_ID, $user_email, $user_url, $user_identity;
	get_currentuserinfo();
	
	if ( $user_level < 9 ) {
		// ONLY IF NOT ADMINS :: BEGIN
		$spamfree_options			= get_option('spamfree_options');
		$BlockAllTrackbacks 		= $spamfree_options['block_all_trackbacks'];
		$BlockAllPingbacks 			= $spamfree_options['block_all_pingbacks'];
		
		$content_short_status		= spamfree_content_short($commentdata);
		
		/*
		if ( !$content_short_status ) {
			$blacklist_filter_status= spamfree_blacklist_filter($commentdata);
			}
		
		if ( !$content_short_status && !$blacklist_filter_status ) {
			$content_filter_status 	= spamfree_content_filter($commentdata);
			}
		*/
		if ( !$content_short_status ) {
			$content_filter_status 	= spamfree_content_filter($commentdata);
			}		
		
		if ( $content_short_status ) {
			add_filter('pre_comment_approved', 'spamfree_denied_post_short', 1);
			}
		//else if ( $blacklist_filter_status ) {
		else if ( $content_filter_status == '2' ) {
			add_filter('pre_comment_approved', 'spamfree_denied_post_blacklist', 1);
			}
		else if ( $content_filter_status ) {
			add_filter('pre_comment_approved', 'spamfree_denied_post', 1);
			}	
		else if ( ( $commentdata['comment_type'] != 'trackback' && $commentdata['comment_type'] != 'pingback' ) || ( $BlockAllTrackbacks && $BlockAllPingbacks ) || ( $BlockAllTrackbacks && $commentdata['comment_type'] == 'trackback' ) || ( $BlockAllPingbacks && $commentdata['comment_type'] == 'pingback' ) ) {
			// If Comment is not a trackback or pingback, or 
			// Trackbacks and Pingbacks are blocked, or 
			// Trackbacks are blocked and comment is Trackback, or 
			// Pingbacks are blocked and comment is Pingback
			add_filter('pre_comment_approved', 'spamfree_allowed_post', 1);
			}
		// ONLY IF NOT ADMINS :: END
		}

	return $commentdata;
	}

function spamfree_allowed_post($approved) {
	// TEST TO PREVENT COMMENT SPAM FROM BOTS :: BEGIN
	$spamfree_options			= get_option('spamfree_options');
	$CookieValidationName  		= $spamfree_options['cookie_validation_name'];
	$CookieValidationKey 		= $spamfree_options['cookie_validation_key'];
	$FormValidationFieldJS 		= $spamfree_options['form_validation_field_js'];
	$FormValidationKeyJS 		= $spamfree_options['form_validation_key_js'];
	$WPCommentValidationJS 		= $_COOKIE[$CookieValidationName];
	//$WPFormValidationPost 		= $_POST[$FormValidationFieldJS]; //Comments Post Verification
	//if( $WPCommentValidationJS == $CookieValidationKey ) { // Comment allowed
	if( $_COOKIE[$spamfree_options['cookie_validation_name']] == $spamfree_options['cookie_validation_key'] ) { // Comment allowed
		// Clear Key Values and Update
		spamfree_update_keys(1);
		return $approved;
		}
	else { // Comment spam killed
	
		// Update Count
		update_option( 'spamfree_count', get_option('spamfree_count') + 1 );
		// Akismet Accuracy Fix :: BEGIN
		// Akismet's counter is currently taking credit for some spams killed by WP-SpamFree - the following ensures accurate reporting.
		// The reason for this fix is that Akismet may have marked the same comment as spam, but WP-SpamFree actually kills it - with or without Akismet.
		$ak_count_pre	= get_option('ak_count_pre');
		$ak_count_post	= get_option('akismet_spam_count');
		if ($ak_count_post > $ak_count_pre) {
			update_option( 'akismet_spam_count', $ak_count_pre );
			}
		// Akismet Accuracy Fix :: END
		$spamfree_jsck_error_ck_test = $_COOKIE['SJECT']; // Default value is 'CKON'
		
		if ( $spamfree_jsck_error_ck_test == 'CKON' ) {
			$spamfree_jsck_error_ck_status = 'PHP detects that cookies appear to be enabled.';
			}
		else {
			$spamfree_jsck_error_ck_status = 'PHP detects that cookies appear to be disabled. <script type="text/javascript">if (navigator.cookieEnabled==true) { document.write(\'(However, JavaScript detects that cookies are enabled.)\'); } else { document.write(\'\(JavaScript also detects that cookies are disabled.\)\'); }; </script>';
			}
		
		$spamfree_jsck_error_message_standard = 'Sorry, there was an error. Please enable JavaScript and Cookies in your browser and try again.';
		
		$spamfree_jsck_error_message_detailed = '<strong>Sorry, there was an error. Please enable JavaScript and Cookies in your browser and try again.</strong><br /><br />'."\n";
		$spamfree_jsck_error_message_detailed .= 'Status:'."\n";
		$spamfree_jsck_error_message_detailed .= '<ul>'."\n";
		$spamfree_jsck_error_message_detailed .= '<li><script type="text/javascript">document.write(\'JavaScript is enabled.\');</script><noscript>JavaScript is disabled.</noscript></li>'."\n";
		$spamfree_jsck_error_message_detailed .= '<li>'.$spamfree_jsck_error_ck_status.'</li>'."\n";		
		$spamfree_jsck_error_message_detailed .= '</ul>'."\n";
		
		$spamfree_jsck_error_message_detailed .= 'This message was generated by <a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree" rel="external nofollow" target="_blank" >WP-SpamFree</a>.<br /><br />'."\n";
		$spamfree_jsck_error_message_detailed .= 'If you feel you have received this message in error (for example <em>if both statuses above indicate that JavaScript and Cookies are in fact enabled</em> and you have tried to post several times), please alert the author of this blog, and let them know they need to view the <a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree#wpsf_troubleshooting" rel="external nofollow" target="_blank" >Technical Support information</a>.<br />'."\n";

    	wp_die( __($spamfree_jsck_error_message_detailed) );
		return false;
		}
	// TEST TO PREVENT COMMENT SPAM FROM BOTS :: END
	}
		
function spamfree_denied_post($approved) {
	// REJECT SPAM :: BEGIN

	// Update Count
	update_option( 'spamfree_count', get_option('spamfree_count') + 1 );
	// Akismet Accuracy Fix :: BEGIN
	// Akismet's counter is currently taking credit for some spams killed by WP-SpamFree - the following ensures accurate reporting.
	// The reason for this fix is that Akismet may have marked the same comment as spam, but WP-SpamFree actually kills it - with or without Akismet.
	$ak_count_pre	= get_option('ak_count_pre');
	$ak_count_post	= get_option('akismet_spam_count');
	if ($ak_count_post > $ak_count_pre) {
		update_option( 'akismet_spam_count', $ak_count_pre );
		}
	// Akismet Accuracy Fix :: END

	$spamfree_filter_error_message_standard = 'Comments have been temporarily disabled to prevent spam. Please try again later.'; // Stop spammers without revealing why.
	
	$spamfree_filter_error_message_detailed = '<strong>Hmmm, your comment seems a bit spammy. We\'re not real big on spam around here.</strong><br /><br />'."\n";
	$spamfree_filter_error_message_detailed .= 'Please go back and try again.'."\n";

	wp_die( __($spamfree_filter_error_message_detailed) );
	return false;
	// REJECT SPAM :: END
	}

function spamfree_denied_post_short($approved) {
	// REJECT SHORT COMMENTS :: BEGIN

	// Update Count
	update_option( 'spamfree_count', get_option('spamfree_count') + 1 );
	// Akismet Accuracy Fix :: BEGIN
	// Akismet's counter is currently taking credit for some spams killed by WP-SpamFree - the following ensures accurate reporting.
	// The reason for this fix is that Akismet may have marked the same comment as spam, but WP-SpamFree actually kills it - with or without Akismet.
	$ak_count_pre	= get_option('ak_count_pre');
	$ak_count_post	= get_option('akismet_spam_count');
	if ($ak_count_post > $ak_count_pre) {
		update_option( 'akismet_spam_count', $ak_count_pre );
		}
	// Akismet Accuracy Fix :: END

	wp_die( __('Your comment was a bit too short. Please go back and try again.') );
	return false;
	// REJECT SHORT COMMENTS :: END
	}
	
function spamfree_denied_post_blacklist($approved) {
	// REJECT BLACKLISTED COMMENTERS :: BEGIN

	// Update Count
	update_option( 'spamfree_count', get_option('spamfree_count') + 1 );
	// Akismet Accuracy Fix :: BEGIN
	// Akismet's counter is currently taking credit for some spams killed by WP-SpamFree - the following ensures accurate reporting.
	// The reason for this fix is that Akismet may have marked the same comment as spam, but WP-SpamFree actually kills it - with or without Akismet.
	$ak_count_pre	= get_option('ak_count_pre');
	$ak_count_post	= get_option('akismet_spam_count');
	if ($ak_count_post > $ak_count_pre) {
		update_option( 'akismet_spam_count', $ak_count_pre );
		}
	// Akismet Accuracy Fix :: END
	
	$spamfree_blacklist_error_message_detailed = '<strong>Your location has been identified as part of a known spam network. Comments have been disabled to prevent spam.</strong><br /><br />'."\n";
	
	//$spamfree_blacklist_error_message_detailed .= 'This message was generated by <a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree?code=bl_error" rel="external nofollow" target="_blank" >WP-SpamFree</a>.<br /><br />'."\n";
	//$spamfree_blacklist_error_message_detailed .= 'If you would like to be removed from the blacklist, you will need to contact the developers of WP-SpamFree.<br />'."\n";

	wp_die( __($spamfree_blacklist_error_message_detailed) );
	return false;
	// REJECT BLACKLISTED COMMENTERS :: END
	}

function spamfree_content_short($commentdata) {
	// COMMENT LENGTH CHECK :: BEGIN
	$commentdata_comment_content					= $commentdata['comment_content'];
	$commentdata_comment_content_lc					= strtolower($commentdata_comment_content);
	
	$commentdata_comment_content_length 			= strlen($commentdata_comment_content_lc);
	$commentdata_comment_content_min_length 		= 10;
	
	$commentdata_comment_type						= $commentdata['comment_type'];
	
	if( $commentdata_comment_content_length < $commentdata_comment_content_min_length && $commentdata_comment_type != 'trackback' && $commentdata_comment_type != 'pingback' ) {
		$content_short_status = true;
		$spamfree_error_code .= ' SHORT10';
		}
		
	if ( !$spamfree_error_code ) {
		$spamfree_error_code = 'No Error';
		}
	$spamfree_error_code = ltrim($spamfree_error_code);
	
	$spamfree_error_data = array( $spamfree_error_code, $blacklist_word_combo, $blacklist_word_combo_total );
	
	update_option( 'spamfree_error_data', $spamfree_error_data );

	return $content_short_status;
	// COMMENT LENGTH CHECK :: END
	}
	
function spamfree_content_filter($commentdata) {
	// Supplementary Defense - Blocking the Obvious to Improve Pingback/Trackback Defense
	// FYI, Certain lopps are unrolled because of a weird compatibility issue with certain servers. Works fine on most, but for some unforeseen reason, a few have issues. When I get more time to test, will try to figure it out. for now these have to stay unrolled. Won't require any more server resources, just more lines of code. Overall, still a tiny program for a server to run.
	
	// CONTENT FILTERING :: BEGIN
	$CurrentWordPressVersion = '2.6';
	
	$commentdata_comment_author						= $commentdata['comment_author'];
	$commentdata_comment_author_lc					= strtolower($commentdata_comment_author);
	$commentdata_comment_author_lc_space 			= ' '.$commentdata_comment_author_lc.' ';
	$commentdata_comment_author_email				= $commentdata['comment_author_email'];
	$commentdata_comment_author_email_lc			= strtolower($commentdata_comment_author_email);
	$commentdata_comment_author_url					= $commentdata['comment_author_url'];
	$commentdata_comment_author_url_lc				= strtolower($commentdata_comment_author_url);
	
	$commentdata_comment_content					= $commentdata['comment_content'];
	$commentdata_comment_content_lc					= strtolower($commentdata_comment_content);
	
	$replace_apostrophes							= array('\’','\`','&acute;','&grave;','&#39;','&#96;','&#101;','&#145;','&#146;','&#158;','&#180;','&#207;','&#208;','&#8216;','&#8217;');
	$commentdata_comment_content_lc_norm_apost 		= str_replace($replace_apostrophes,"\'",$commentdata_comment_content_lc);
	
	$commentdata_comment_type						= $commentdata['comment_type'];
	
	// Altered to Accommodate WP 2.5+
	$commentdata_user_agent					= $_SERVER['HTTP_USER_AGENT'];
	$commentdata_user_agent_lc				= strtolower($commentdata_user_agent);
	$commentdata_remote_addr				= $_SERVER['REMOTE_ADDR'];
	$commentdata_remote_addr_lc				= strtolower($commentdata_remote_addr);
	$commentdata_remote_host				= $_SERVER['REMOTE_HOST'];
	$commentdata_remote_host_lc				= strtolower($commentdata_remote_host);
	$commentdata_referrer					= $_SERVER['HTTP_REFERER'];
	$commentdata_referrer_lc				= strtolower($commentdata_referrer);
	$commentdata_blog						= get_option('siteurl');
	$commentdata_blog_lc					= strtolower($commentdata_blog);
	$commentdata_php_self					= $_SERVER['PHP_SELF'];
	$commentdata_php_self_lc				= strtolower($commentdata_php_self);
	
	if ( !$commentdata_remote_host_lc ) {
		$commentdata_remote_host_lc = 'blank';
		}
		
	$BlogServerIP = $_SERVER['SERVER_ADDR'];
	$BlogServerName = $_SERVER['SERVER_NAME'];

	// IP / PROXY INFO :: BEGIN
	$ipBlock=explode('.',$commentdata_remote_addr);
	$ipProxyVIA=$_SERVER['HTTP_VIA'];
	$MaskedIP=$_SERVER['HTTP_X_FORWARDED_FOR']; // Stated Original IP - Can be faked
	$MaskedIPBlock=explode('.',$MaskedIP);
	if (eregi("^([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])",$MaskedIP)&&$MaskedIP!=""&&$MaskedIP!="unknown"&&!eregi("^192.168.",$MaskedIP)) {
		$MaskedIPValid=true;
		$MaskedIPCore=rtrim($MaskedIP," unknown;,");
		}
	$ReverseDNS = gethostbyaddr($commentdata_remote_addr);
	$ReverseDNSIP = gethostbyname($ReverseDNS);
	
	if ( $ReverseDNSIP != $commentdata_remote_addr || $commentdata_remote_addr == $ReverseDNS ) {
		$ReverseDNSAuthenticity = '[Possibly Forged]';
		} 
	else {
		$ReverseDNSAuthenticity = '[Verified]';
		}
	// Detect Use of Proxy
	if ($_SERVER['HTTP_VIA']||$_SERVER['HTTP_X_FORWARDED_FOR']) {
		$ipProxy='PROXY DETECTED';
		$ipProxyShort='PROXY';
		$ipProxyData=$commentdata_remote_addr.' | MASKED IP: '.$MaskedIP;
		$ProxyStatus='TRUE';
		}
	else {
		$ipProxy='No Proxy';
		$ipProxyShort=$ipProxy;
		$ipProxyData=$commentdata_remote_addr;
		$ProxyStatus='FALSE';
		}
	// IP / PROXY INFO :: END

	// Simple Filters
	
	$blacklist_word_combo_total_limit = 10;
	$blacklist_word_combo_total = 0;
	
	// Filter 1: Number of occurrences of 'http://' in comment_content
	$filter_1_count = substr_count($commentdata_comment_content_lc, 'http://');
	$filter_1_limit = 4;
	$filter_1_trackback_limit = 1;
	
	// Medical-Related Filters
	
	/*
	// Filter 2: Number of occurrences of 'viagra' in comment_content
	$filter_2_count = substr_count($commentdata_comment_content_lc, 'viagra');
	$filter_2_limit = 2;
	// Filter 3: Number of occurrences of 'v1agra' in comment_content
	$filter_3_count = substr_count($commentdata_comment_content_lc, 'v1agra');
	$filter_3_limit = 1;
	// Filter 4: Number of occurrences of 'cialis' in comment_content
	$filter_4_count = substr_count($commentdata_comment_content_lc, 'cialis');
	$filter_4_limit = 2;
	// Filter 5: Number of occurrences of 'c1alis' in comment_content
	$filter_5_count = substr_count($commentdata_comment_content_lc, 'c1alis');
	$filter_5_limit = 1;
	// Filter 6: Number of occurrences of 'levitra' in comment_content
	$filter_6_count = substr_count($commentdata_comment_content_lc, 'levitra');
	$filter_6_limit = 2;
	// Filter 7: Number of occurrences of 'lev1tra' in comment_content
	$filter_7_count = substr_count($commentdata_comment_content_lc, 'lev1tra');
	$filter_7_limit = 1;
	// Filter 8: Number of occurrences of 'erectile dysfunction ' in comment_content
	$filter_8_count = substr_count($commentdata_comment_content_lc, 'erectile dysfunction ');
	$filter_8_limit = 2;
	// Filter 9: Number of occurrences of 'erection' in comment_content
	$filter_9_count = substr_count($commentdata_comment_content_lc, 'erection');
	$filter_9_limit = 2;
	// Filter 10: Number of occurrences of 'erectile' in comment_content
	$filter_10_count = substr_count($commentdata_comment_content_lc, 'erectile');
	$filter_10_limit = 2;
	// Filter 11: Number of occurrences of 'xanax' in comment_content
	$filter_11_count = substr_count($commentdata_comment_content_lc, 'xanax');
	$filter_11_limit = 5;
	// Filter 12: Number of occurrences of 'valium' in comment_content
	$filter_12_count = substr_count($commentdata_comment_content_lc, 'valium');
	$filter_12_limit = 5;
	*/
	
	$filter_2_term = 'viagra';
	$filter_2_count = substr_count($commentdata_comment_content_lc, $filter_2_term);
	$filter_2_limit = 2;
	$filter_2_trackback_limit = 1;
	$filter_2_author_count = substr_count($commentdata_comment_author_lc, $filter_2_term);
	$filter_2_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_2_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_2_author_count;
	// Filter 3: Number of occurrences of 'v1agra' in comment_content
	$filter_3_term = 'v1agra';
	$filter_3_count = substr_count($commentdata_comment_content_lc, $filter_3_term);
	$filter_3_limit = 1;
	$filter_3_trackback_limit = 1;
	$filter_3_author_count = substr_count($commentdata_comment_author_lc, $filter_3_term);
	$filter_3_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_3_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_3_author_count;
	// Filter 4: Number of occurrences of 'cialis' in comment_content
	$filter_4_term = 'cialis';
	$filter_4_count = substr_count($commentdata_comment_content_lc, $filter_4_term);
	$filter_4_limit = 2;
	$filter_4_trackback_limit = 1;
	$filter_4_author_count = substr_count($commentdata_comment_author_lc, $filter_4_term);
	$filter_4_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_4_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_4_author_count;
	// Filter 5: Number of occurrences of 'c1alis' in comment_content
	$filter_5_term = 'c1alis';
	$filter_5_count = substr_count($commentdata_comment_content_lc, $filter_5_term);
	$filter_5_limit = 1;
	$filter_5_trackback_limit = 1;
	$filter_5_author_count = substr_count($commentdata_comment_author_lc, $filter_5_term);
	$filter_5_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_5_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_5_author_count;
	// Filter 6: Number of occurrences of 'levitra' in comment_content
	$filter_6_term = 'levitra';
	$filter_6_count = substr_count($commentdata_comment_content_lc, $filter_6_term);
	$filter_6_limit = 2;
	$filter_6_trackback_limit = 1;
	$filter_6_author_count = substr_count($commentdata_comment_author_lc, $filter_6_term);
	$filter_6_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_6_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_6_author_count;
	// Filter 7: Number of occurrences of 'lev1tra' in comment_content
	$filter_7_term = 'lev1tra';
	$filter_7_count = substr_count($commentdata_comment_content_lc, $filter_7_term);
	$filter_7_limit = 1;
	$filter_7_trackback_limit = 1;
	$filter_7_author_count = substr_count($commentdata_comment_author_lc, $filter_7_term);
	$filter_7_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_7_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_7_author_count;
	// Filter 8: Number of occurrences of 'erectile dysfunction' in comment_content
	$filter_8_term = 'erectile dysfunction';
	$filter_8_count = substr_count($commentdata_comment_content_lc, $filter_8_term);
	$filter_8_limit = 2;
	$filter_8_trackback_limit = 1;
	$filter_8_author_count = substr_count($commentdata_comment_author_lc, $filter_8_term);
	$filter_8_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_8_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_8_author_count;
	// Filter 9: Number of occurrences of 'erection' in comment_content
	$filter_9_term = 'erection';
	$filter_9_count = substr_count($commentdata_comment_content_lc, $filter_9_term);
	$filter_9_limit = 2;
	$filter_9_trackback_limit = 1;
	$filter_9_author_count = substr_count($commentdata_comment_author_lc, $filter_9_term);
	$filter_9_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_9_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_9_author_count;
	// Filter 10: Number of occurrences of 'erectile' in comment_content
	$filter_10_term = 'erectile';
	$filter_10_count = substr_count($commentdata_comment_content_lc, $filter_10_term);
	$filter_10_limit = 2;
	$filter_10_trackback_limit = 1;
	$filter_10_author_count = substr_count($commentdata_comment_author_lc, $filter_10_term);
	$filter_10_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_10_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_10_author_count;
	// Filter 11: Number of occurrences of 'xanax' in comment_content
	$filter_11_term = 'xanax';
	$filter_11_count = substr_count($commentdata_comment_content_lc, $filter_11_term);
	$filter_11_limit = 3;
	$filter_11_trackback_limit = 2;
	$filter_11_author_count = substr_count($commentdata_comment_author_lc, $filter_11_term);
	$filter_11_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_11_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_11_author_count;
	// Filter 12: Number of occurrences of 'zithromax' in comment_content
	$filter_12_term = 'zithromax';
	$filter_12_count = substr_count($commentdata_comment_content_lc, $filter_12_term);
	$filter_12_limit = 3;
	$filter_12_trackback_limit = 2;
	$filter_12_author_count = substr_count($commentdata_comment_author_lc, $filter_12_term);
	$filter_12_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_12_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_12_author_count;
	// Filter 13: Number of occurrences of 'phentermine' in comment_content
	$filter_13_term = 'phentermine';
	$filter_13_count = substr_count($commentdata_comment_content_lc, $filter_13_term);
	$filter_13_limit = 3;
	$filter_13_trackback_limit = 2;
	$filter_13_author_count = substr_count($commentdata_comment_author_lc, $filter_13_term);
	$filter_13_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_13_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_13_author_count;
	// Filter 14: Number of occurrences of ' soma ' in comment_content
	$filter_14_term = ' soma ';
	$filter_14_count = substr_count($commentdata_comment_content_lc, $filter_14_term);
	$filter_14_limit = 3;
	$filter_14_trackback_limit = 2;
	$filter_14_author_count = substr_count($commentdata_comment_author_lc, $filter_14_term);
	$filter_14_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_14_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_14_author_count;
	// Filter 15: Number of occurrences of ' soma.' in comment_content
	$filter_15_term = ' soma.';
	$filter_15_count = substr_count($commentdata_comment_content_lc, $filter_15_term);
	$filter_15_limit = 3;
	$filter_15_trackback_limit = 2;
	$filter_15_author_count = substr_count($commentdata_comment_author_lc, $filter_15_term);
	$filter_15_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_15_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_15_author_count;
	// Filter 16: Number of occurrences of 'prescription' in comment_content
	$filter_16_term = 'prescription';
	$filter_16_count = substr_count($commentdata_comment_content_lc, $filter_16_term);
	$filter_16_limit = 3;
	$filter_16_trackback_limit = 2;
	$filter_16_author_count = substr_count($commentdata_comment_author_lc, $filter_16_term);
	$filter_16_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_16_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_16_author_count;
	// Filter 17: Number of occurrences of 'tramadol' in comment_content
	$filter_17_term = 'tramadol';
	$filter_17_count = substr_count($commentdata_comment_content_lc, $filter_17_term);
	$filter_17_limit = 3;
	$filter_17_trackback_limit = 2;
	$filter_17_author_count = substr_count($commentdata_comment_author_lc, $filter_17_term);
	$filter_17_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_17_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_17_author_count;
	// Filter 18: Number of occurrences of 'penis enlargement' in comment_content
	$filter_18_term = 'penis enlargement';
	$filter_18_count = substr_count($commentdata_comment_content_lc, $filter_18_term);
	$filter_18_limit = 2;
	$filter_18_trackback_limit = 1;
	$filter_18_author_count = substr_count($commentdata_comment_author_lc, $filter_18_term);
	$filter_18_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_18_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_18_author_count;
	// Filter 19: Number of occurrences of 'buy pills' in comment_content
	$filter_19_term = 'buy pills';
	$filter_19_count = substr_count($commentdata_comment_content_lc, $filter_19_term);
	$filter_19_limit = 3;
	$filter_19_trackback_limit = 2;
	$filter_19_author_count = substr_count($commentdata_comment_author_lc, $filter_19_term);
	$filter_19_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_19_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_19_author_count;
	// Filter 20: Number of occurrences of 'diet pill' in comment_content
	$filter_20_term = 'diet pill';
	$filter_20_count = substr_count($commentdata_comment_content_lc, $filter_20_term);
	$filter_20_limit = 3;
	$filter_20_trackback_limit = 2;
	$filter_20_author_count = substr_count($commentdata_comment_author_lc, $filter_20_term);
	$filter_20_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_20_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_20_author_count;
	// Filter 21: Number of occurrences of 'weight loss pill' in comment_content
	$filter_21_term = 'weight loss pill';
	$filter_21_count = substr_count($commentdata_comment_content_lc, $filter_21_term);
	$filter_21_limit = 3;
	$filter_21_trackback_limit = 2;
	$filter_21_author_count = substr_count($commentdata_comment_author_lc, $filter_21_term);
	$filter_21_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_21_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_21_author_count;
	// Filter 22: Number of occurrences of 'pill' in comment_content
	$filter_22_term = 'pill';
	$filter_22_count = substr_count($commentdata_comment_content_lc, $filter_22_term);
	$filter_22_limit = 10;
	$filter_22_trackback_limit = 2;
	$filter_22_author_count = substr_count($commentdata_comment_author_lc, $filter_22_term);
	$filter_22_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_22_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_22_author_count;
	// Filter 23: Number of occurrences of ' pill,' in comment_content
	$filter_23_term = ' pill,';
	$filter_23_count = substr_count($commentdata_comment_content_lc, $filter_23_term);
	$filter_23_limit = 5;
	$filter_23_trackback_limit = 2;
	$filter_23_author_count = substr_count($commentdata_comment_author_lc, $filter_23_term);
	$filter_23_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_23_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_23_author_count;
	// Filter 24: Number of occurrences of ' pills,' in comment_content
	$filter_24_term = ' pills,';
	$filter_24_count = substr_count($commentdata_comment_content_lc, $filter_24_term);
	$filter_24_limit = 5;
	$filter_24_trackback_limit = 2;
	$filter_24_author_count = substr_count($commentdata_comment_author_lc, $filter_24_term);
	$filter_24_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_24_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_24_author_count;
	// Filter 25: Number of occurrences of 'propecia' in comment_content
	$filter_25_term = 'propecia';
	$filter_25_count = substr_count($commentdata_comment_content_lc, $filter_25_term);
	$filter_25_limit = 2;
	$filter_25_trackback_limit = 1;
	$filter_25_author_count = substr_count($commentdata_comment_author_lc, $filter_25_term);
	$filter_25_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_25_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_25_author_count;
	// Filter 26: Number of occurrences of 'propec1a' in comment_content
	$filter_26_term = 'propec1a';
	$filter_26_count = substr_count($commentdata_comment_content_lc, $filter_26_term);
	$filter_26_limit = 1;
	$filter_26_trackback_limit = 1;
	$filter_26_author_count = substr_count($commentdata_comment_author_lc, $filter_26_term);
	$filter_26_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_26_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_26_author_count;
	// Filter 27: Number of occurrences of 'online pharmacy' in comment_content
	$filter_27_term = 'online pharmacy';
	$filter_27_count = substr_count($commentdata_comment_content_lc, $filter_27_term);
	$filter_27_limit = 5;
	$filter_27_trackback_limit = 2;
	$filter_27_author_count = substr_count($commentdata_comment_author_lc, $filter_27_term);
	$filter_27_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_27_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_27_author_count;
	// Filter 28: Number of occurrences of 'medication' in comment_content
	$filter_28_term = 'medication';
	$filter_28_count = substr_count($commentdata_comment_content_lc, $filter_28_term);
	$filter_28_limit = 7;
	$filter_28_trackback_limit = 3;
	$filter_28_author_count = substr_count($commentdata_comment_author_lc, $filter_28_term);
	$filter_28_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_28_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_28_author_count;
	// Filter 29: Number of occurrences of 'buy now' in comment_content
	$filter_29_term = 'buy now';
	$filter_29_count = substr_count($commentdata_comment_content_lc, $filter_29_term);
	$filter_29_limit = 7;
	$filter_29_trackback_limit = 3;
	$filter_29_author_count = substr_count($commentdata_comment_author_lc, $filter_29_term);
	$filter_29_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_29_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_29_author_count;
	// Filter 30: Number of occurrences of 'ephedrin' in comment_content
	$filter_30_term = 'ephedrin';
	$filter_30_count = substr_count($commentdata_comment_content_lc, $filter_30_term);
	$filter_30_limit = 3;
	$filter_30_trackback_limit = 2;
	$filter_30_author_count = substr_count($commentdata_comment_author_lc, $filter_30_term);
	$filter_30_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_30_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_30_author_count;
	// Filter 31: Number of occurrences of 'ephedrin' in comment_content
	$filter_31_term = 'ephedrine';
	$filter_31_count = substr_count($commentdata_comment_content_lc, $filter_31_term);
	$filter_31_limit = 3;
	$filter_31_trackback_limit = 2;
	$filter_31_author_count = substr_count($commentdata_comment_author_lc, $filter_31_term);
	$filter_31_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_31_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_31_author_count;
	// Filter 32: Number of occurrences of 'ephedrin' in comment_content
	$filter_32_term = 'ephedr1n';
	$filter_32_count = substr_count($commentdata_comment_content_lc, $filter_32_term);
	$filter_32_limit = 1;
	$filter_32_trackback_limit = 1;
	$filter_32_author_count = substr_count($commentdata_comment_author_lc, $filter_32_term);
	$filter_32_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_32_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_32_author_count;
	// Filter 33: Number of occurrences of 'ephedrin' in comment_content
	$filter_33_term = 'ephedr1ne';
	$filter_33_count = substr_count($commentdata_comment_content_lc, $filter_33_term);
	$filter_33_limit = 1;
	$filter_33_trackback_limit = 1;
	$filter_33_author_count = substr_count($commentdata_comment_author_lc, $filter_33_term);
	$filter_33_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_33_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_33_author_count;
	// Filter 34: Number of occurrences of 'ephedra' in comment_content
	$filter_34_term = 'ephedra';
	$filter_34_count = substr_count($commentdata_comment_content_lc, $filter_34_term);
	$filter_34_limit = 3;
	$filter_34_trackback_limit = 2;
	$filter_34_author_count = substr_count($commentdata_comment_author_lc, $filter_34_term);
	$filter_34_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_34_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_34_author_count;
	// Filter 35: Number of occurrences of 'valium' in comment_content
	$filter_35_term = 'valium';
	$filter_35_count = substr_count($commentdata_comment_content_lc, $filter_35_term);
	$filter_35_limit = 3;
	$filter_35_trackback_limit = 2;
	$filter_35_author_count = substr_count($commentdata_comment_author_lc, $filter_35_term);
	$filter_35_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_35_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_35_author_count;
	// Filter 36: Number of occurrences of 'adipex' in comment_content
	$filter_36_term = 'adipex';
	$filter_36_count = substr_count($commentdata_comment_content_lc, $filter_36_term);
	$filter_36_limit = 3;
	$filter_36_trackback_limit = 2;
	$filter_36_author_count = substr_count($commentdata_comment_author_lc, $filter_36_term);
	$filter_36_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_36_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_36_author_count;
	// Filter 37: Number of occurrences of 'accutane' in comment_content
	$filter_37_term = 'accutane';
	$filter_37_count = substr_count($commentdata_comment_content_lc, $filter_37_term);
	$filter_37_limit = 3;
	$filter_37_trackback_limit = 2;
	$filter_37_author_count = substr_count($commentdata_comment_author_lc, $filter_37_term);
	$filter_37_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_37_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_37_author_count;
	// Filter 38: Number of occurrences of 'acomplia' in comment_content
	$filter_38_term = 'acomplia';
	$filter_38_count = substr_count($commentdata_comment_content_lc, $filter_38_term);
	$filter_38_limit = 3;
	$filter_38_trackback_limit = 2;
	$filter_38_author_count = substr_count($commentdata_comment_author_lc, $filter_38_term);
	$filter_38_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_38_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_38_author_count;
	// Filter 39: Number of occurrences of 'rimonabant' in comment_content
	$filter_39_term = 'rimonabant';
	$filter_39_count = substr_count($commentdata_comment_content_lc, $filter_39_term);
	$filter_39_limit = 3;
	$filter_39_trackback_limit = 2;
	$filter_39_author_count = substr_count($commentdata_comment_author_lc, $filter_39_term);
	$filter_39_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_39_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_39_author_count;
	// Filter 40: Number of occurrences of 'zimulti' in comment_content
	$filter_40_term = 'zimulti';
	$filter_40_count = substr_count($commentdata_comment_content_lc, $filter_40_term);
	$filter_40_limit = 3;
	$filter_40_trackback_limit = 2;
	$filter_40_author_count = substr_count($commentdata_comment_author_lc, $filter_40_term);
	$filter_40_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_40_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_40_author_count;


	// Non-Medical Author Tests
	// Filter 210: Number of occurrences of 'drassyassut' in comment_content
	$filter_210_term = 'drassyassut'; //DrassyassuT
	$filter_210_count = substr_count($commentdata_comment_content_lc, $filter_210_term);
	$filter_210_limit = 1;
	$filter_210_trackback_limit = 1;
	$filter_210_author_count = substr_count($commentdata_comment_author_lc, $filter_210_term);
	$filter_210_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_210_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_210_author_count;

	// Sex-Related Filter
	// Filter 104: Number of occurrences of 'porn' in comment_content
	$filter_104_count = substr_count($commentdata_comment_content_lc, 'porn');
	$filter_104_limit = 5;
	$filter_104_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_104_count;
	// Filter 105: Number of occurrences of 'teen porn' in comment_content
	$filter_105_count = substr_count($commentdata_comment_content_lc, 'teen porn');
	$filter_105_limit = 1;
	$filter_105_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_105_count;
	// Filter 106: Number of occurrences of 'rape porn' in comment_content
	$filter_106_count = substr_count($commentdata_comment_content_lc, 'rape porn');
	$filter_106_limit = 1;
	$filter_106_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_106_count;
	// Filter 107: Number of occurrences of 'incest porn' in comment_content
	$filter_107_count = substr_count($commentdata_comment_content_lc, 'incest porn');
	$filter_107_limit = 1;
	$filter_107_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_107_count;
	// Filter 108: Number of occurrences of 'hentai' in comment_content
	$filter_108_count = substr_count($commentdata_comment_content_lc, 'hentai');
	$filter_108_limit = 2;
	$filter_108_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_108_count;
	// Filter 109: Number of occurrences of 'sex movie' in comment_content
	$filter_109_count = substr_count($commentdata_comment_content_lc, 'sex movie');
	$filter_109_limit = 2;
	$filter_109_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_109_count;
	// Filter 110: Number of occurrences of 'sex tape' in comment_content
	$filter_110_count = substr_count($commentdata_comment_content_lc, 'sex tape');
	$filter_110_limit = 2;
	$filter_110_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_110_count;
	// Filter 111: Number of occurrences of 'sex' in comment_content
	$filter_111_count = substr_count($commentdata_comment_content_lc, 'sex');
	$filter_111_limit = 5;
	$filter_111_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_111_count;
	// Filter 112: Number of occurrences of 'sex' in comment_content
	$filter_112_count = substr_count($commentdata_comment_content_lc, 'pussy');
	$filter_112_limit = 3;
	$filter_112_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_112_count;
	// Filter 113: Number of occurrences of 'penis' in comment_content
	$filter_113_count = substr_count($commentdata_comment_content_lc, 'penis');
	$filter_113_limit = 3;
	$filter_113_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_113_count;
	// Filter 114: Number of occurrences of 'vagina' in comment_content
	$filter_114_count = substr_count($commentdata_comment_content_lc, 'vagina');
	$filter_114_limit = 3;
	$filter_114_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_114_count;
	// Filter 115: Number of occurrences of 'gay porn' in comment_content
	$filter_115_count = substr_count($commentdata_comment_content_lc, 'gay porn');
	$filter_115_limit = 2;
	$filter_115_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_115_count;
	// Filter 116: Number of occurrences of 'torture porn' in comment_content
	$filter_116_count = substr_count($commentdata_comment_content_lc, 'torture porn');
	$filter_116_limit = 1;
	$filter_116_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_116_count;
	// Filter 117: Number of occurrences of 'masturbation' in comment_content
	$filter_117_count = substr_count($commentdata_comment_content_lc, 'masturbation');
	$filter_117_limit = 3;
	$filter_117_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_117_count;
	// Filter 118: Number of occurrences of 'masterbation' in comment_content
	$filter_118_count = substr_count($commentdata_comment_content_lc, 'masterbation');
	$filter_118_limit = 2;
	$filter_118_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_118_count;
	// Filter 119: Number of occurrences of 'masturbate' in comment_content
	$filter_119_count = substr_count($commentdata_comment_content_lc, 'masturbate');
	$filter_119_limit = 3;
	$filter_119_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_119_count;
	// Filter 120: Number of occurrences of 'masterbate' in comment_content
	$filter_120_count = substr_count($commentdata_comment_content_lc, 'masterbate');
	$filter_120_limit = 2;
	$filter_120_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_120_count;
	// Filter 121: Number of occurrences of 'masturbating' in comment_content
	$filter_121_count = substr_count($commentdata_comment_content_lc, 'masturbating');
	$filter_121_limit = 3;
	$filter_121_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_121_count;
	// Filter 122: Number of occurrences of 'masterbating' in comment_content
	$filter_122_count = substr_count($commentdata_comment_content_lc, 'masterbating');
	$filter_122_limit = 2;
	$filter_122_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_122_count;
	// Filter 123: Number of occurrences of 'anal sex' in comment_content
	$filter_123_count = substr_count($commentdata_comment_content_lc, 'anal sex');
	$filter_123_limit = 3;
	$filter_123_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_123_count;
	// Filter 124: Number of occurrences of 'xxx' in comment_content
	$filter_124_count = substr_count($commentdata_comment_content_lc, 'xxx');
	$filter_124_limit = 5;
	$filter_124_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_124_count;
	// Filter 125: Number of occurrences of 'naked' in comment_content
	$filter_125_count = substr_count($commentdata_comment_content_lc, 'naked');
	$filter_125_limit = 5;
	$filter_125_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_125_count;
	// Filter 126: Number of occurrences of 'nude' in comment_content
	$filter_126_count = substr_count($commentdata_comment_content_lc, 'nude');
	$filter_126_limit = 5;
	$filter_126_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_126_count;
	// Filter 127: Number of occurrences of 'fucking' in comment_content
	$filter_127_count = substr_count($commentdata_comment_content_lc, 'fucking');
	$filter_127_limit = 5;
	$filter_127_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_127_count;
	// Filter 128: Number of occurrences of 'orgasm' in comment_content
	$filter_128_count = substr_count($commentdata_comment_content_lc, 'orgasm');
	$filter_128_limit = 5;
	$filter_128_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_128_count;
	// Filter 129: Number of occurrences of 'pron' in comment_content
	$filter_129_count = substr_count($commentdata_comment_content_lc, 'pron');
	$filter_129_limit = 5;
	$filter_129_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_129_count;
	// Filter 130: Number of occurrences of 'bestiality' in comment_content
	$filter_130_count = substr_count($commentdata_comment_content_lc, 'bestiality');
	$filter_130_limit = 2;
	$filter_130_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_130_count;
	// Filter 131: Number of occurrences of 'animal sex' in comment_content
	$filter_131_count = substr_count($commentdata_comment_content_lc, 'animal sex');
	$filter_131_limit = 2;
	$filter_131_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_131_count;
	// Filter 132: Number of occurrences of 'dildo' in comment_content
	$filter_132_count = substr_count($commentdata_comment_content_lc, 'dildo');
	$filter_132_limit = 4;
	$filter_132_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_132_count;
	// Filter 133: Number of occurrences of 'ejaculate' in comment_content
	$filter_133_count = substr_count($commentdata_comment_content_lc, 'ejaculate');
	$filter_133_limit = 3;
	$filter_133_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_133_count;
	// Filter 134: Number of occurrences of 'ejaculation' in comment_content
	$filter_134_count = substr_count($commentdata_comment_content_lc, 'ejaculation');
	$filter_134_limit = 3;
	$filter_134_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_134_count;
	// Filter 135: Number of occurrences of 'ejaculating' in comment_content
	$filter_135_count = substr_count($commentdata_comment_content_lc, 'ejaculating');
	$filter_135_limit = 3;
	$filter_135_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_135_count;
	// Filter 136: Number of occurrences of 'lesbian' in comment_content
	$filter_136_count = substr_count($commentdata_comment_content_lc, 'lesbian');
	$filter_136_limit = 7;
	$filter_136_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_136_count;
	// Filter 137: Number of occurrences of 'sex video' in comment_content
	$filter_137_count = substr_count($commentdata_comment_content_lc, 'sex video');
	$filter_137_limit = 2;
	$filter_137_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_137_count;
	// Filter 138: Number of occurrences of ' anal ' in comment_content
	$filter_138_count = substr_count($commentdata_comment_content_lc, ' anal ');
	$filter_138_limit = 5;
	$filter_138_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_138_count;
	// Filter 139: Number of occurrences of '>anal ' in comment_content
	$filter_139_count = substr_count($commentdata_comment_content_lc, '>anal ');
	$filter_139_limit = 5;
	$filter_139_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_139_count;
	// Filter 140: Number of occurrences of 'desnuda' in comment_content
	$filter_140_count = substr_count($commentdata_comment_content_lc, 'desnuda');
	$filter_140_limit = 5;
	$filter_140_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_140_count;
	// Filter 141: Number of occurrences of 'cumshots' in comment_content
	$filter_141_count = substr_count($commentdata_comment_content_lc, 'cumshots');
	$filter_141_limit = 2;
	$filter_141_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_141_count;
	// Filter 142: Number of occurrences of 'porntube' in comment_content
	$filter_142_count = substr_count($commentdata_comment_content_lc, 'porntube');
	$filter_142_limit = 2;
	$filter_142_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_142_count;
	// Filter 143: Number of occurrences of 'fuck' in comment_content
	$filter_143_count = substr_count($commentdata_comment_content_lc, 'fuck');
	$filter_143_limit = 6;
	$filter_143_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_143_count;
	// Filter 144: Number of occurrences of 'celebrity' in comment_content
	$filter_144_count = substr_count($commentdata_comment_content_lc, 'celebrity');
	$filter_144_limit = 6;
	$filter_144_trackback_limit = 6;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_144_count;
	// Filter 145: Number of occurrences of 'celebrities' in comment_content
	$filter_145_count = substr_count($commentdata_comment_content_lc, 'celebrities');
	$filter_145_limit = 6;
	$filter_145_trackback_limit = 6;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_145_count;
	// Filter 146: Number of occurrences of 'erotic' in comment_content
	$filter_146_count = substr_count($commentdata_comment_content_lc, 'erotic');
	$filter_146_limit = 6;
	$filter_146_trackback_limit = 4;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_146_count;
	// Filter 147: Number of occurrences of 'gay' in comment_content
	$filter_147_count = substr_count($commentdata_comment_content_lc, 'gay');
	$filter_147_limit = 7;
	$filter_147_trackback_limit = 4;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_147_count;
	// Filter 148: Number of occurrences of 'heterosexual' in comment_content
	$filter_148_count = substr_count($commentdata_comment_content_lc, 'heterosexual');
	$filter_148_limit = 7;
	$filter_148_trackback_limit = 4;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_148_count;
	// Filter 149: Number of occurrences of 'blowjob' in comment_content
	$filter_149_count = substr_count($commentdata_comment_content_lc, 'blowjob');
	$filter_149_limit = 2;
	$filter_149_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_149_count;
	// Filter 150: Number of occurrences of 'blow job' in comment_content
	$filter_150_count = substr_count($commentdata_comment_content_lc, 'blow job');
	$filter_150_limit = 2;
	$filter_150_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_150_count;
	// Filter 151: Number of occurrences of 'rape' in comment_content
	$filter_151_count = substr_count($commentdata_comment_content_lc, 'rape');
	$filter_151_limit = 5;
	$filter_151_trackback_limit = 3;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_151_count;
	// Filter 152: Number of occurrences of 'prostitute' in comment_content
	$filter_152_count = substr_count($commentdata_comment_content_lc, 'prostitute');
	$filter_152_limit = 7;
	$filter_152_trackback_limit = 5;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_152_count;
	// Filter 153: Number of occurrences of 'call girl' in comment_content
	$filter_153_count = substr_count($commentdata_comment_content_lc, 'call girl');
	$filter_153_limit = 7;
	$filter_153_trackback_limit = 5;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_153_count;
	// Filter 154: Number of occurrences of 'escort service' in comment_content
	$filter_154_count = substr_count($commentdata_comment_content_lc, 'escort service');
	$filter_154_limit = 7;
	$filter_154_trackback_limit = 5;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_154_count;
	// Filter 155: Number of occurrences of 'sexual service' in comment_content
	$filter_155_count = substr_count($commentdata_comment_content_lc, 'sexual service');
	$filter_155_limit = 7;
	$filter_155_trackback_limit = 5;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_155_count;
	// Filter 156: Number of occurrences of 'adult movie' in comment_content
	$filter_156_count = substr_count($commentdata_comment_content_lc, 'adult movie');
	$filter_156_limit = 4;
	$filter_156_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_156_count;
	// Filter 157: Number of occurrences of 'adult video' in comment_content
	$filter_157_count = substr_count($commentdata_comment_content_lc, 'adult video');
	$filter_157_limit = 4;
	$filter_157_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_157_count;
	// Filter 158: Number of occurrences of 'clitoris' in comment_content
	$filter_158_count = substr_count($commentdata_comment_content_lc, 'clitoris');
	$filter_158_limit = 3;
	$filter_158_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_158_count;
	
	// Pingback/Trackback Filters
	// Filter 200: Pingback: Blank data in comment_content: [...]  [...]
	$filter_200_count = substr_count($commentdata_comment_content_lc, '[...]  [...]');
	$filter_200_limit = 1;
	$filter_200_trackback_limit = 1;

	// SEO/WebDev/Offshore-Related Filter - Authors Only - Non-Trackback
	// Filter 300: Number of occurrences of 'web development' in comment_content
	$filter_300_term = 'web development'; //'web development'
	$filter_300_count = substr_count($commentdata_comment_content_lc, $filter_300_term);
	$filter_300_limit = 8;
	$filter_300_trackback_limit = 8;
	$filter_300_author_count = substr_count($commentdata_comment_author_lc, $filter_300_term);
	$filter_300_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_300_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_300_author_count;
	// Filter 301: Number of occurrences of 'website development' in comment_content
	$filter_301_term = 'website development';
	$filter_301_count = substr_count($commentdata_comment_content_lc, $filter_301_term);
	$filter_301_limit = 8;
	$filter_301_trackback_limit = 8;
	$filter_301_author_count = substr_count($commentdata_comment_author_lc, $filter_301_term);
	$filter_301_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_301_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_301_author_count;
	// Filter 302: Number of occurrences of 'web site development' in comment_content
	$filter_302_term = 'web site development';
	$filter_302_count = substr_count($commentdata_comment_content_lc, $filter_302_term);
	$filter_302_limit = 8;
	$filter_302_trackback_limit = 8;
	$filter_302_author_count = substr_count($commentdata_comment_author_lc, $filter_302_term);
	$filter_302_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_302_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_302_author_count;
	// Filter 303: Number of occurrences of 'web design' in comment_content
	$filter_303_term = 'web design';
	$filter_303_count = substr_count($commentdata_comment_content_lc, $filter_303_term);
	$filter_303_limit = 8;
	$filter_303_trackback_limit = 8;
	$filter_303_author_count = substr_count($commentdata_comment_author_lc, $filter_303_term);
	$filter_303_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_303_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_303_author_count;
	// Filter 304: Number of occurrences of 'website design' in comment_content
	$filter_304_term = 'website design';
	$filter_304_count = substr_count($commentdata_comment_content_lc, $filter_304_term);
	$filter_304_limit = 8;
	$filter_304_trackback_limit = 8;
	$filter_304_author_count = substr_count($commentdata_comment_author_lc, $filter_304_term);
	$filter_304_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_304_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_304_author_count;
	// Filter 305: Number of occurrences of 'web site design' in comment_content
	$filter_305_term = 'web site design';
	$filter_305_count = substr_count($commentdata_comment_content_lc, $filter_305_term);
	$filter_305_limit = 8;
	$filter_305_trackback_limit = 8;
	$filter_305_author_count = substr_count($commentdata_comment_author_lc, $filter_305_term);
	$filter_305_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_305_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_305_author_count;
	// Filter 306: Number of occurrences of 'search engine optimization' in comment_content
	$filter_306_term = 'search engine optimization';
	$filter_306_count = substr_count($commentdata_comment_content_lc, $filter_306_term);
	$filter_306_limit = 8;
	$filter_306_trackback_limit = 8;
	$filter_306_author_count = substr_count($commentdata_comment_author_lc, $filter_306_term);
	$filter_306_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_306_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_306_author_count;
	// Filter 307: Number of occurrences of 'link building' in comment_content
	$filter_307_term = 'link building';
	$filter_307_count = substr_count($commentdata_comment_content_lc, $filter_307_term);
	$filter_307_limit = 8;
	$filter_307_trackback_limit = 8;
	$filter_307_author_count = substr_count($commentdata_comment_author_lc, $filter_307_term);
	$filter_307_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_307_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_307_author_count;
	// Filter 308: Number of occurrences of 'india offshore' in comment_content
	$filter_308_term = 'india offshore';
	$filter_308_count = substr_count($commentdata_comment_content_lc, $filter_308_term);
	$filter_308_limit = 8;
	$filter_308_trackback_limit = 8;
	$filter_308_author_count = substr_count($commentdata_comment_author_lc, $filter_308_term);
	$filter_308_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_308_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_308_author_count;
	// Filter 309: Number of occurrences of 'offshore india' in comment_content
	$filter_309_term = 'offshore india';
	$filter_309_count = substr_count($commentdata_comment_content_lc, $filter_309_term);
	$filter_309_limit = 8;
	$filter_309_trackback_limit = 8;
	$filter_309_author_count = substr_count($commentdata_comment_author_lc, $filter_309_term);
	$filter_309_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_309_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_309_author_count;
	// Filter 310: Number of occurrences of ' seo ' in comment_content & comment_author
	$filter_310_term = ' seo ';
	$filter_310_count = substr_count($commentdata_comment_content_lc, $filter_310_term);
	$filter_310_limit = 8;
	$filter_310_trackback_limit = 8;
	$filter_310_author_count = substr_count($commentdata_comment_author_lc_space, $filter_310_term);
	$filter_310_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_310_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_310_author_count;
	// Filter 311: Number of occurrences of 'search engine marketing' in comment_content
	$filter_311_term = 'search engine marketing';
	$filter_311_count = substr_count($commentdata_comment_content_lc, $filter_311_term);
	$filter_311_limit = 8;
	$filter_311_trackback_limit = 8;
	$filter_311_author_count = substr_count($commentdata_comment_author_lc, $filter_311_term);
	$filter_311_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_311_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_311_author_count;
	// Filter 312: Number of occurrences of 'internet marketing' in comment_content
	$filter_312_term = 'internet marketing';
	$filter_312_count = substr_count($commentdata_comment_content_lc, $filter_312_term);
	$filter_312_limit = 8;
	$filter_312_trackback_limit = 8;
	$filter_312_author_count = substr_count($commentdata_comment_author_lc, $filter_312_term);
	$filter_312_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_312_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_312_author_count;
	// Filter 313: Number of occurrences of 'social media optimization' in comment_content
	$filter_313_term = 'social media optimization';
	$filter_313_count = substr_count($commentdata_comment_content_lc, $filter_313_term);
	$filter_313_limit = 8;
	$filter_313_trackback_limit = 8;
	$filter_313_author_count = substr_count($commentdata_comment_author_lc, $filter_313_term);
	$filter_313_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_313_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_313_author_count;
	// Filter 314: Number of occurrences of 'social media marketing' in comment_content
	$filter_314_term = 'social media marketing';
	$filter_314_count = substr_count($commentdata_comment_content_lc, $filter_314_term);
	$filter_314_limit = 8;
	$filter_314_trackback_limit = 8;
	$filter_314_author_count = substr_count($commentdata_comment_author_lc, $filter_314_term);
	$filter_314_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_314_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_314_author_count;
	// Filter 315: Number of occurrences of 'web developer' in comment_content
	$filter_315_term = 'web developer'; //'web development'
	$filter_315_count = substr_count($commentdata_comment_content_lc, $filter_315_term);
	$filter_315_limit = 8;
	$filter_315_trackback_limit = 8;
	$filter_315_author_count = substr_count($commentdata_comment_author_lc, $filter_315_term);
	$filter_315_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_315_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_315_author_count;
	// Filter 316: Number of occurrences of 'website developer' in comment_content
	$filter_316_term = 'website developer';
	$filter_316_count = substr_count($commentdata_comment_content_lc, $filter_316_term);
	$filter_316_limit = 8;
	$filter_316_trackback_limit = 8;
	$filter_316_author_count = substr_count($commentdata_comment_author_lc, $filter_316_term);
	$filter_316_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_316_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_316_author_count;
	// Filter 317: Number of occurrences of 'web site developer' in comment_content
	$filter_317_term = 'web site developer';
	$filter_317_count = substr_count($commentdata_comment_content_lc, $filter_317_term);
	$filter_317_limit = 8;
	$filter_317_trackback_limit = 8;
	$filter_317_author_count = substr_count($commentdata_comment_author_lc, $filter_317_term);
	$filter_317_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_317_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_317_author_count;
	// Filter 318: Number of occurrences of 'javascript' in comment_content
	$filter_318_term = 'javascript';
	$filter_318_count = substr_count($commentdata_comment_content_lc, $filter_318_term);
	$filter_318_limit = 8;
	$filter_318_trackback_limit = 8;
	$filter_318_author_count = substr_count($commentdata_comment_author_lc, $filter_318_term);
	$filter_318_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_318_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_318_author_count;
	// Filter 319: Number of occurrences of 'search engine optimizer' in comment_content
	$filter_319_term = 'search engine optimizer';
	$filter_319_count = substr_count($commentdata_comment_content_lc, $filter_319_term);
	$filter_319_limit = 8;
	$filter_319_trackback_limit = 8;
	$filter_319_author_count = substr_count($commentdata_comment_author_lc, $filter_319_term);
	$filter_319_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_319_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_319_author_count;
	// Filter 320: Number of occurrences of 'link builder' in comment_content
	$filter_320_term = 'link builder';
	$filter_320_count = substr_count($commentdata_comment_content_lc, $filter_320_term);
	$filter_320_limit = 8;
	$filter_320_trackback_limit = 8;
	$filter_320_author_count = substr_count($commentdata_comment_author_lc, $filter_320_term);
	$filter_320_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_320_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_320_author_count;
	// Filter 321: Number of occurrences of 'search engine marketer' in comment_content
	$filter_321_term = 'search engine marketer';
	$filter_321_count = substr_count($commentdata_comment_content_lc, $filter_321_term);
	$filter_321_limit = 8;
	$filter_321_trackback_limit = 8;
	$filter_321_author_count = substr_count($commentdata_comment_author_lc, $filter_321_term);
	$filter_321_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_321_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_321_author_count;
	// Filter 322: Number of occurrences of 'internet marketer' in comment_content
	$filter_322_term = 'internet marketer';
	$filter_322_count = substr_count($commentdata_comment_content_lc, $filter_322_term);
	$filter_322_limit = 8;
	$filter_322_trackback_limit = 8;
	$filter_322_author_count = substr_count($commentdata_comment_author_lc, $filter_322_term);
	$filter_322_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_322_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_322_author_count;
	// Filter 323: Number of occurrences of 'social media optimizer' in comment_content
	$filter_323_term = 'social media optimizer';
	$filter_323_count = substr_count($commentdata_comment_content_lc, $filter_323_term);
	$filter_323_limit = 8;
	$filter_323_trackback_limit = 8;
	$filter_323_author_count = substr_count($commentdata_comment_author_lc, $filter_323_term);
	$filter_323_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_323_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_323_author_count;
	// Filter 324: Number of occurrences of 'social media marketer' in comment_content
	$filter_324_term = 'social media marketer';
	$filter_324_count = substr_count($commentdata_comment_content_lc, $filter_324_term);
	$filter_324_limit = 8;
	$filter_324_trackback_limit = 8;
	$filter_324_author_count = substr_count($commentdata_comment_author_lc, $filter_324_term);
	$filter_324_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_324_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_324_author_count;
	// Filter 325: Number of occurrences of 'social media consultant' in comment_content
	$filter_325_term = 'social media consultant';
	$filter_325_count = substr_count($commentdata_comment_content_lc, $filter_325_term);
	$filter_325_limit = 8;
	$filter_325_trackback_limit = 8;
	$filter_325_author_count = substr_count($commentdata_comment_author_lc, $filter_325_term);
	$filter_325_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_325_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_325_author_count;
	// Filter 326: Number of occurrences of 'social media consulting' in comment_content
	$filter_326_term = 'social media consulting';
	$filter_326_count = substr_count($commentdata_comment_content_lc, $filter_326_term);
	$filter_326_limit = 8;
	$filter_326_trackback_limit = 8;
	$filter_326_author_count = substr_count($commentdata_comment_author_lc, $filter_326_term);
	$filter_326_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_326_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_326_author_count;
	// Filter 327: Number of occurrences of 'web promotion' in comment_content
	$filter_327_term = 'web promotion'; 
	$filter_327_count = substr_count($commentdata_comment_content_lc, $filter_327_term);
	$filter_327_limit = 8;
	$filter_327_trackback_limit = 8;
	$filter_327_author_count = substr_count($commentdata_comment_author_lc, $filter_327_term);
	$filter_327_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_327_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_327_author_count;
	// Filter 328: Number of occurrences of 'website promotion' in comment_content
	$filter_328_term = 'website promotion';
	$filter_328_count = substr_count($commentdata_comment_content_lc, $filter_328_term);
	$filter_328_limit = 8;
	$filter_328_trackback_limit = 8;
	$filter_328_author_count = substr_count($commentdata_comment_author_lc, $filter_328_term);
	$filter_328_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_328_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_328_author_count;
	// Filter 329: Number of occurrences of 'web site promotion' in comment_content
	$filter_329_term = 'web site promotion';
	$filter_329_count = substr_count($commentdata_comment_content_lc, $filter_329_term);
	$filter_329_limit = 8;
	$filter_329_trackback_limit = 8;
	$filter_329_author_count = substr_count($commentdata_comment_author_lc, $filter_329_term);
	$filter_329_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_329_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_329_author_count;
	// Filter 330: Number of occurrences of 'search engine ranking' in comment_content
	$filter_330_term = 'search engine ranking';
	$filter_330_count = substr_count($commentdata_comment_content_lc, $filter_330_term);
	$filter_330_limit = 8;
	$filter_330_trackback_limit = 8;
	$filter_330_author_count = substr_count($commentdata_comment_author_lc, $filter_330_term);
	$filter_330_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_330_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_330_author_count;
	// Filter 331: Number of occurrences of 'modulesoft' in comment_content
	$filter_331_term = 'modulesoft';
	$filter_331_count = substr_count($commentdata_comment_content_lc, $filter_331_term);
	$filter_331_limit = 8;
	$filter_331_trackback_limit = 8;
	$filter_331_author_count = substr_count($commentdata_comment_author_lc, $filter_331_term);
	$filter_331_author_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_331_count;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_331_author_count;

	// General Spam Terms
	// Filter 500: Number of occurrences of ' loan' in comment_content
	$filter_500_count = substr_count($commentdata_comment_content_lc, ' loan');
	$filter_500_limit = 7;
	$filter_500_trackback_limit = 3;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_500_count;
	// Filter 501: Number of occurrences of 'student ' in comment_content
	$filter_501_count = substr_count($commentdata_comment_content_lc, 'student ');
	$filter_501_limit = 11;
	$filter_501_trackback_limit = 6;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_501_count;
	// Filter 502: Number of occurrences of 'loan consolidation' in comment_content
	$filter_502_count = substr_count($commentdata_comment_content_lc, 'loan consolidation');
	$filter_502_limit = 5;
	$filter_502_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_502_count;
	// Filter 503: Number of occurrences of 'credit card' in comment_content
	$filter_503_count = substr_count($commentdata_comment_content_lc, 'credit card');
	$filter_503_limit = 5;
	$filter_503_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_503_count;
	// Filter 504: Number of occurrences of 'health insurance' in comment_content
	$filter_504_count = substr_count($commentdata_comment_content_lc, 'health insurance');
	$filter_504_limit = 5;
	$filter_504_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_504_count;
	// Filter 505: Number of occurrences of 'student loan' in comment_content
	$filter_505_count = substr_count($commentdata_comment_content_lc, 'student loan');
	$filter_505_limit = 4;
	$filter_505_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_505_count;
	// Filter 506: Number of occurrences of 'student credit card' in comment_content
	$filter_506_count = substr_count($commentdata_comment_content_lc, 'student credit card');
	$filter_506_limit = 4;
	$filter_506_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_506_count;
	// Filter 507: Number of occurrences of 'consolidation student' in comment_content
	$filter_507_count = substr_count($commentdata_comment_content_lc, 'consolidation student');
	$filter_507_limit = 4;
	$filter_507_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_507_count;
	// Filter 508: Number of occurrences of 'student health insurance' in comment_content
	$filter_508_count = substr_count($commentdata_comment_content_lc, 'student health insurance');
	$filter_508_limit = 4;
	$filter_508_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_508_count;
	// Filter 509: Number of occurrences of 'student loan consolidation' in comment_content
	$filter_509_count = substr_count($commentdata_comment_content_lc, 'student loan consolidation');
	$filter_509_limit = 4;
	$filter_509_trackback_limit = 2;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_509_count;

	/*
	// Medical-Related Filters
	$filter_set_2 = array(
						'viagra[::wpsf::]2[::wpsf::]2',
						'v1agra[::wpsf::]1[::wpsf::]1',
						'cialis[::wpsf::]2[::wpsf::]2',
						'c1alis[::wpsf::]1[::wpsf::]1',
						'levitra[::wpsf::]2[::wpsf::]2',
						'lev1tra[::wpsf::]1[::wpsf::]1',
						'erectile[::wpsf::]3[::wpsf::]3',
						'erectile dysfuntion[::wpsf::]2[::wpsf::]2',
						'erection[::wpsf::]2[::wpsf::]2',
						'valium[::wpsf::]5[::wpsf::]5',
						'xanax[::wpsf::]5[::wpsf::]5'
						);
	
	// Sex-Related Filters - Common Words occuring in Sex/Porn Spam
	$filter_set_3 = array(
						'porn[::wpsf::]5[::wpsf::]5',
						'teen porn[::wpsf::]1[::wpsf::]1',
						'rape porn[::wpsf::]1[::wpsf::]1',
						'incest porn[::wpsf::]1[::wpsf::]1',
						'torture porn[::wpsf::]1[::wpsf::]1',
						'hentai[::wpsf::]2[::wpsf::]2',
						'sex movie[::wpsf::]3[::wpsf::]3',
						'sex tape[::wpsf::]3[::wpsf::]3',
						'sex[::wpsf::]5[::wpsf::]5',
						'xxx[::wpsf::]5[::wpsf::]5',
						'nude[::wpsf::]5[::wpsf::]5',
						'naked[::wpsf::]5[::wpsf::]5',
						'fucking[::wpsf::]6[::wpsf::]6',
						'pussy[::wpsf::]3[::wpsf::]3',
						'penis[::wpsf::]3[::wpsf::]3',
						'vagina[::wpsf::]3[::wpsf::]3',
						'gay porn[::wpsf::]3[::wpsf::]3',
						'anal sex[::wpsf::]3[::wpsf::]3',
						'masturbation[::wpsf::]3[::wpsf::]3',
						'masterbation[::wpsf::]2[::wpsf::]2',
						'masturbating[::wpsf::]3[::wpsf::]3',
						'masterbating[::wpsf::]2[::wpsf::]2',
						'masturbate[::wpsf::]3[::wpsf::]3',
						'masterbate[::wpsf::]2[::wpsf::]2',
						'bestiality[::wpsf::]2[::wpsf::]2',
						'animal sex[::wpsf::]3[::wpsf::]3',
						'orgasm[::wpsf::]5[::wpsf::]5',
						'ejaculating[::wpsf::]3[::wpsf::]3',
						'ejaculation[::wpsf::]3[::wpsf::]3',
						'ejaculate[::wpsf::]3[::wpsf::]3',
						'dildo[::wpsf::]4[::wpsf::]4'
						);

	// Pingback/Trackback Filters
	$filter_set_4 = array( 
						'[...]  [...][::wpsf::]0[::wpsf::]1'
						);
		
	// Test Filters
	$filter_set_5 = array( 
						'wpsfteststring-3n44j57kkdsmks39248sje83njd839[::wpsf::]1[::wpsf::]1'
						);
	
	$filter_set_master = array_merge( $filter_set_1, $filter_set_2, $filter_set_3, $filter_set_4, $filter_set_5 );
	$filter_set_master_count = count($filter_set_master);
	*/
	
	// Complex Filters
	// Check for Optimized URL's and Keyword Phrases Ocurring in Author Name and Content
	
	// Filter 10001: Number of occurrences of 'this is something special' in comment_content
	$filter_10001_count = substr_count($commentdata_comment_content_lc, 'this is something special');
	$filter_10001_limit = 1;
	$filter_10001_trackback_limit = 1;
	// Filter 10002: Number of occurrences of 'http://groups.google.com/group/' in comment_content
	$filter_10002_count = substr_count($commentdata_comment_content_lc, 'http://groups.google.com/group/');
	$filter_10002_limit = 1;
	$filter_10002_trackback_limit = 1;
	// Filter 10003: Number of occurrences of 'youporn' in comment_content
	$filter_10003_count = substr_count($commentdata_comment_content_lc, 'youporn');
	$filter_10003_limit = 1;
	$filter_10003_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_10003_count;
	// Filter 10004: Number of occurrences of 'pornotube' in comment_content
	$filter_10004_count = substr_count($commentdata_comment_content_lc, 'pornotube');
	$filter_10004_limit = 1;
	$filter_10004_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_10004_count;
	// Filter 10005: Number of occurrences of 'porntube' in comment_content
	$filter_10005_count = substr_count($commentdata_comment_content_lc, 'porntube');
	$filter_10005_limit = 1;
	$filter_10005_trackback_limit = 1;
	$blacklist_word_combo_total = $blacklist_word_combo_total + $filter_10005_count;
	// Filter 10006: Number of occurrences of 'http://groups.google.us/group/' in comment_content
	$filter_10006_count = substr_count($commentdata_comment_content_lc, 'http://groups.google.us/group/');
	$filter_10006_limit = 1;
	$filter_10006_trackback_limit = 1;
	
	// Filter 20001: Number of occurrences of 'groups.google.com' in comment_author_url
	$filter_20001_count = substr_count($commentdata_comment_author_url_lc, 'groups.google.com');
	$filter_20001C_count = substr_count($commentdata_comment_content_lc, 'groups.google.com');
	$filter_20001_limit = 1;
	$filter_20001_trackback_limit = 1;
	// Filter 20002: Number of occurrences of 'groups.yahoo.com' in comment_author_url
	$filter_20002_count = substr_count($commentdata_comment_author_url_lc, 'groups.yahoo.com');
	$filter_20002C_count = substr_count($commentdata_comment_content_lc, 'groups.yahoo.com');
	$filter_20002_limit = 1;
	$filter_20002_trackback_limit = 1;
	// Filter 20003: Number of occurrences of '.phpbbserver.com' in comment_author_url
	$filter_20003_count = substr_count($commentdata_comment_author_url_lc, '.phpbbserver.com');
	$filter_20003C_count = substr_count($commentdata_comment_content_lc, '.phpbbserver.com');
	$filter_20003_limit = 1;
	$filter_20003_trackback_limit = 1;
	// Filter 20004: Number of occurrences of '.freehostia.com' in comment_author_url
	$filter_20004_count = substr_count($commentdata_comment_author_url_lc, '.freehostia.com');
	$filter_20004C_count = substr_count($commentdata_comment_content_lc, '.freehostia.com');
	$filter_20004_limit = 1;
	$filter_20004_trackback_limit = 1;
	// Filter 20005: Number of occurrences of 'groups.google.us' in comment_author_url
	$filter_20005_count = substr_count($commentdata_comment_author_url_lc, 'groups.google.us');
	$filter_20005C_count = substr_count($commentdata_comment_content_lc, 'groups.google.us');
	$filter_20005_limit = 1;
	$filter_20005_trackback_limit = 1;
	// Filter 20006: Number of occurrences of 'groups.google.us' in comment_author_url
	$filter_20006_count = substr_count($commentdata_comment_author_url_lc, 'www.google.com/notebook/public/');
	$filter_20006C_count = substr_count($commentdata_comment_content_lc, 'www.google.com/notebook/public/');
	$filter_20006_limit = 1;
	$filter_20006_trackback_limit = 1;
	// Filter 20007: Number of occurrences of 'groups.google.us' in comment_author_url
	$filter_20007_count = substr_count($commentdata_comment_author_url_lc, '.free-site-host.com');
	$filter_20007C_count = substr_count($commentdata_comment_content_lc, '.free-site-host.com');
	$filter_20007_limit = 1;
	$filter_20007_trackback_limit = 1;
	// Filter 20008: Number of occurrences of 'youporn736.vox.com' in comment_author_url
	$filter_20008_count = substr_count($commentdata_comment_author_url_lc, 'youporn736.vox.com');
	$filter_20008C_count = substr_count($commentdata_comment_content_lc, 'youporn736.vox.com');
	$filter_20008_limit = 1;
	$filter_20008_trackback_limit = 1;
	// Filter 20009: Number of occurrences of 'keywordspy.com' in comment_author_url
	$filter_20009_count = substr_count($commentdata_comment_author_url_lc, 'keywordspy.com');
	$filter_20009C_count = substr_count($commentdata_comment_content_lc, 'keywordspy.com');
	$filter_20009_limit = 1;
	$filter_20009_trackback_limit = 1;
	// Filter 20010: Number of occurrences of '.t35.com' in comment_author_url
	$filter_20010_count = substr_count($commentdata_comment_author_url_lc, '.t35.com');
	$filter_20010C_count = substr_count($commentdata_comment_content_lc, '.t35.com');
	$filter_20010_limit = 1;
	$filter_20010_trackback_limit = 1;
	// Filter 20011: Number of occurrences of '.150m.com' in comment_author_url
	$filter_20011_count = substr_count($commentdata_comment_author_url_lc, '.150m.com');
	$filter_20011C_count = substr_count($commentdata_comment_content_lc, '.150m.com');
	$filter_20011_limit = 1;
	$filter_20011_trackback_limit = 1;
	// Filter 20012: Number of occurrences of '.250m.com' in comment_author_url
	$filter_20012_count = substr_count($commentdata_comment_author_url_lc, '.250m.com');
	$filter_20012C_count = substr_count($commentdata_comment_content_lc, '.250m.com');
	$filter_20012_limit = 1;
	$filter_20012_trackback_limit = 1;
	// Filter 20013: Number of occurrences of 'blogs.ign.com' in comment_author_url
	$filter_20013_count = substr_count($commentdata_comment_author_url_lc, 'blogs.ign.com');
	$filter_20013C_count = substr_count($commentdata_comment_content_lc, 'blogs.ign.com');
	$filter_20013_limit = 1;
	$filter_20013_trackback_limit = 1;
	// Filter 20014: Number of occurrences of 'members.lycos.co.uk' in comment_author_url
	$filter_20014_count = substr_count($commentdata_comment_author_url_lc, 'members.lycos.co.uk');
	$filter_20014C_count = substr_count($commentdata_comment_content_lc, 'members.lycos.co.uk');
	$filter_20014_limit = 1;
	$filter_20014_trackback_limit = 1;
	// Filter 20015: Number of occurrences of '/christiantorrents.ru' in comment_author_url
	$filter_20015_count = substr_count($commentdata_comment_author_url_lc, '/christiantorrents.ru');
	$filter_20015C_count = substr_count($commentdata_comment_content_lc, '/christiantorrents.ru');
	$filter_20015_limit = 1;
	$filter_20015_trackback_limit = 1;
	// Filter 20016: Number of occurrences of '.christiantorrents.ru' in comment_author_url
	$filter_20016_count = substr_count($commentdata_comment_author_url_lc, '.christiantorrents.ru');
	$filter_20016C_count = substr_count($commentdata_comment_content_lc, '.christiantorrents.ru');
	$filter_20016_limit = 1;
	$filter_20016_trackback_limit = 1;
	// Filter 20017: Number of occurrences of '/lifecity.tv' in comment_author_url
	$filter_20017_count = substr_count($commentdata_comment_author_url_lc, '/lifecity.tv');
	$filter_20017C_count = substr_count($commentdata_comment_content_lc, '/lifecity.tv');
	$filter_20017_limit = 1;
	$filter_20017_trackback_limit = 1;
	// Filter 20018: Number of occurrences of '.lifecity.tv' in comment_author_url
	$filter_20018_count = substr_count($commentdata_comment_author_url_lc, '.lifecity.tv');
	$filter_20018C_count = substr_count($commentdata_comment_content_lc, '.lifecity.tv');
	$filter_20018_limit = 1;
	$filter_20018_trackback_limit = 1;
	// Filter 20019: Number of occurrences of '/lifecity.info' in comment_author_url
	$filter_20019_count = substr_count($commentdata_comment_author_url_lc, '/lifecity.info');
	$filter_20019C_count = substr_count($commentdata_comment_content_lc, '/lifecity.info');
	$filter_20019_limit = 1;
	$filter_20019_trackback_limit = 1;
	// Filter 20020: Number of occurrences of '.lifecity.info' in comment_author_url
	$filter_20020_count = substr_count($commentdata_comment_author_url_lc, '.lifecity.info');
	$filter_20020C_count = substr_count($commentdata_comment_content_lc, '.lifecity.info');
	$filter_20020_limit = 1;
	$filter_20020_trackback_limit = 1;
	
	$commentdata_comment_author_lc_spam_strong = '<strong>'.$commentdata_comment_author_lc.'</strong>'; // Trackbacks
	$commentdata_comment_author_lc_spam_strong_dot1 = '...</strong>'; // Trackbacks
	$commentdata_comment_author_lc_spam_strong_dot2 = '...</b>'; // Trackbacks
	$commentdata_comment_author_lc_spam_a1 = $commentdata_comment_author_lc.'</a>'; // Trackbacks/Pingbacks
	$commentdata_comment_author_lc_spam_a2 = $commentdata_comment_author_lc.' </a>'; // Trackbacks/Pingbacks
	
	$WPCommentsPostURL = $commentdata_blog_lc.'/wp-comments-post.php';

	$Domains = array('.aero','.arpa','.asia','.biz','.cat','.com','.coop','.edu','.gov','.info','.int','.jobs','.mil','.mobi','.museum','.name','.net','.org','.pro','.tel','.travel','.ac','.ad','.ae','.af','.ai','.al','.am','.an','.ao','.aq','.ar','.as','.at','.au','.aw','.ax','.az','.ba','.bb','.bd','.be','.bf','.bg','.bh','.bi','.bj','.bl','.bm','.bn','.bo','.br','.bs','.bt','.bv','.bw','.by','.bz','.ca','.cc','.cf','.cg','.ch','.ci','.ck','.cl','.cm','.cn','.co','.cr','.cu','.cv','.cx','.cy','.cz','.de','.dj','.dk','.dm','.do','.dz','.ec','.ee','.eg','.eh','.er','.es','.et','.eu','.fi','.fj','.fk','.fm','.fo','.fr','.ga','.gb','.gd','.ge','.gf','.gg','.gh','.gi','.gl','.gm','.gn','.gp','.gq','.gr','.gs','.gt','.gu','.gw','.gy','.hk','.hm','.hn','.hr','.ht','.hu','.id','.ie','.il','.im','.in','.io','.iq','.ir','.is','.it','.je','.jm','.jo','.jp','.ke','.kg','.kh','.ki','.km','.km','.kp','.kr','.kw','.ky','.kz','.la','.lb','.lc','.li','.lk','.lr','.ls','.lt','.lu','.lv','.ly','.ma','.mc','.mc','.md','.me','.mf','.mg','.mh','.mk','.ml','.mm','.mn','.mo','.mq','.mr','.ms','.mt','.mu','.mv','.mw','.mx','.my','.mz','.na','.nc','.ne','.nf','.ng','.ni','.nl','.no','.np','.nr','.nu','.nz','.om','.pa','.pe','.pf','.pg','.ph','.pk','.pl','.pm','.pn','.pr','.ps','.pt','.pw','.py','.qa','.re','.ro','.rs','.ru','.rw','.sa','.sb','.sc','.sd','.se','.sg','.sh','.si','.sj','.sk','.sl','.sm','.sn','.so','.sr','.st','.su','.sv','.sy','.sz','.tc','.td','.tf','.tg','.th','.tj','.tk','.tl','.tm','.tn','.to','.tp','.tr','.tt','.tv','.tw','.tz','.ua','.ug','.uk','.um','.us','.uy','.uz','.va','.vc','.ve','.vg','.vi','.vn','.vu','.wf','.ws','.ye','.yt','.yu','.za','.zm','.zw');
	// from http://www.iana.org/domains/root/db/
	$ConversionSeparator = '-';
	$ConversionSeparators = array('-','_');
	$FilterElementsPrefix = array('http://www.','http://');
	$FilterElementsPage = array('.php','.asp','.cfm','.jsp','.html','.htm','.shtml');
	$FilterElementsNum = array('1','2','3','4','5','6','7','8','9','0');
	$FilterElementsSlash = array('////','///','//');
	$TempPhrase1 = str_replace($FilterElementsPrefix,'',$commentdata_comment_author_url_lc);
	$TempPhrase2 = str_replace($FilterElementsPage,'',$TempPhrase1);
	$TempPhrase3 = str_replace($Domains,'',$TempPhrase2);
	$TempPhrase4 = str_replace($FilterElementsNum,'',$TempPhrase3);
	$TempPhrase5 = str_replace($FilterElementsSlash,'/',$TempPhrase4);
	$TempPhrase6 = strtolower(str_replace($ConversionSeparators,' ',$TempPhrase5));
	$KeywordURLPhrases = explode('/',$TempPhrase6);
	$KeywordURLPhrasesCount = count($KeywordURLPhrases);
	$KeywordCommentAuthorPhrasePunct = array('\:','\;','\+','\-','\!','\.','\,','\[','\]','\@','\#','\$','\%','\^','\&','\*','\(','\)','\/','\\','\|','\=','\_');
	$KeywordCommentAuthorTempPhrase = str_replace($KeywordCommentAuthorPhrasePunct,'',$commentdata_comment_author_lc);
	$KeywordCommentAuthorPhrase1 = str_replace(' ','',$KeywordCommentAuthorTempPhrase);
	$KeywordCommentAuthorPhrase2 = str_replace(' ','-',$KeywordCommentAuthorTempPhrase);
	$KeywordCommentAuthorPhrase3 = str_replace(' ','_',$KeywordCommentAuthorTempPhrase);
	$KeywordCommentAuthorPhraseURLVariation = $FilterElementsPage;
	$KeywordCommentAuthorPhraseURLVariation[] = '/';
	$KeywordCommentAuthorPhraseURLVariationCount = count($KeywordCommentAuthorPhraseURLVariation);
	
	$SplogTrackbackPhrase1 		= 'an interesting post today.here\'s a quick excerpt';
	$SplogTrackbackPhrase1a 	= 'an interesting post today.here&#8217;s a quick excerpt';
	$SplogTrackbackPhrase2 		= 'an interesting post today. here\'s a quick excerpt';
	$SplogTrackbackPhrase2a 	= 'an interesting post today. here&#8217;s a quick excerpt';
	$SplogTrackbackPhrase3 		= 'an interesting post today onhere\'s a quick excerpt';
	$SplogTrackbackPhrase3a		= 'an interesting post today onhere&#8217;s a quick excerpt';
	$SplogTrackbackPhrase4 		= 'read the rest of this great post here';
	$SplogTrackbackPhrase5 		= 'here to see the original:';
		
	$SplogTrackbackPhrase20a 	= 'an interesting post today on';
	$SplogTrackbackPhrase20b 	= 'here\'s a quick excerpt';
	$SplogTrackbackPhrase20c 	= 'here&#8217;s a quick excerpt';
	
	$blacklist_word_combo_limit = 7;
	$blacklist_word_combo = 0;

	$i = 0;
	
	// Execute Simple Filter Test(s)
	if ( $filter_1_count >= $filter_1_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 1';
		}
	if ( $filter_2_count >= $filter_2_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 2';
		}
	if ( $filter_2_count ) { $blacklist_word_combo++; }
	if ( $filter_3_count >= $filter_3_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 3';
		}
	if ( $filter_3_count ) { $blacklist_word_combo++; }
	if ( $filter_4_count >= $filter_4_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 4';
		}
	if ( $filter_4_count ) { $blacklist_word_combo++; }
	if ( $filter_5_count >= $filter_5_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 5';
		}
	if ( $filter_5_count ) { $blacklist_word_combo++; }
	if ( $filter_6_count >= $filter_6_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 6';
		}
	if ( $filter_6_count ) { $blacklist_word_combo++; }
	if ( $filter_7_count >= $filter_7_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 7';
		}
	if ( $filter_7_count ) { $blacklist_word_combo++; }
	if ( $filter_8_count >= $filter_8_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 8';
		}
	if ( $filter_8_count ) { $blacklist_word_combo++; }
	if ( $filter_9_count >= $filter_9_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 9';
		}
	if ( $filter_9_count ) { $blacklist_word_combo++; }
	if ( $filter_10_count >= $filter_10_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 10';
		}
	if ( $filter_10_count ) { $blacklist_word_combo++; }
	if ( $filter_11_count >= $filter_11_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 11';
		}
	if ( $filter_11_count ) { $blacklist_word_combo++; }
	if ( $filter_12_count >= $filter_12_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 12';
		}
	if ( $filter_12_count ) { $blacklist_word_combo++; }
	if ( $filter_13_count >= $filter_13_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 13';
		}
	if ( $filter_13_count ) { $blacklist_word_combo++; }	
	if ( $filter_14_count >= $filter_14_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 14';
		}
	if ( $filter_14_count ) { $blacklist_word_combo++; }	
	if ( $filter_15_count >= $filter_15_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 15';
		}
	if ( $filter_15_count ) { $blacklist_word_combo++; }	
	if ( $filter_16_count >= $filter_16_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 16';
		}
	if ( $filter_16_count ) { $blacklist_word_combo++; }
	if ( $filter_17_count >= $filter_17_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 17';
		}
	if ( $filter_17_count ) { $blacklist_word_combo++; }
	if ( $filter_18_count >= $filter_18_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 18';
		}
	if ( $filter_18_count ) { $blacklist_word_combo++; }
	if ( $filter_19_count >= $filter_19_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 19';
		}
	if ( $filter_19_count ) { $blacklist_word_combo++; }
	if ( $filter_20_count >= $filter_20_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20';
		}
	if ( $filter_20_count ) { $blacklist_word_combo++; }
	if ( $filter_21_count >= $filter_21_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 21';
		}
	if ( $filter_21_count ) { $blacklist_word_combo++; }
	if ( $filter_22_count >= $filter_22_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 22';
		}
	if ( $filter_22_count ) { $blacklist_word_combo++; }
	if ( $filter_23_count >= $filter_23_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 23';
		}
	if ( $filter_23_count ) { $blacklist_word_combo++; }
	if ( $filter_24_count >= $filter_24_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 24';
		}
	if ( $filter_24_count ) { $blacklist_word_combo++; }
	if ( $filter_25_count >= $filter_25_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 25';
		}
	if ( $filter_25_count ) { $blacklist_word_combo++; }
	if ( $filter_26_count >= $filter_26_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 26';
		}
	if ( $filter_26_count ) { $blacklist_word_combo++; }
	if ( $filter_27_count >= $filter_27_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 27';
		}
	if ( $filter_27_count ) { $blacklist_word_combo++; }
	if ( $filter_28_count >= $filter_28_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 28';
		}
	if ( $filter_28_count ) { $blacklist_word_combo++; }
	if ( $filter_29_count >= $filter_29_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 29';
		}
	if ( $filter_29_count ) { $blacklist_word_combo++; }
	if ( $filter_30_count >= $filter_30_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 30';
		}
	if ( $filter_30_count ) { $blacklist_word_combo++; }
	if ( $filter_31_count >= $filter_31_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 31';
		}
	if ( $filter_31_count ) { $blacklist_word_combo++; }
	if ( $filter_32_count >= $filter_32_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 32';
		}
	if ( $filter_32_count ) { $blacklist_word_combo++; }
	if ( $filter_33_count >= $filter_33_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 33';
		}
	if ( $filter_33_count ) { $blacklist_word_combo++; }
	if ( $filter_34_count >= $filter_34_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 34';
		}
	if ( $filter_34_count ) { $blacklist_word_combo++; }
	if ( $filter_35_count >= $filter_35_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 35';
		}
	if ( $filter_35_count ) { $blacklist_word_combo++; }
	if ( $filter_36_count >= $filter_36_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 36';
		}
	if ( $filter_36_count ) { $blacklist_word_combo++; }
	if ( $filter_37_count >= $filter_37_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 37';
		}
	if ( $filter_37_count ) { $blacklist_word_combo++; }
	if ( $filter_38_count >= $filter_38_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 38';
		}
	if ( $filter_38_count ) { $blacklist_word_combo++; }
	if ( $filter_39_count >= $filter_39_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 39';
		}
	if ( $filter_39_count ) { $blacklist_word_combo++; }
	if ( $filter_40_count >= $filter_40_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 40';
		}
	if ( $filter_40_count ) { $blacklist_word_combo++; }
		
	if ( $filter_104_count >= $filter_104_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 104';
		}
	if ( $filter_104_count ) { $blacklist_word_combo++; }
	if ( $filter_105_count >= $filter_105_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 105';
		}
	if ( $filter_105_count ) { $blacklist_word_combo++; }
	if ( $filter_106_count >= $filter_106_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 106';
		}
	if ( $filter_106_count ) { $blacklist_word_combo++; }
	if ( $filter_107_count >= $filter_107_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 107';
		}
	if ( $filter_107_count ) { $blacklist_word_combo++; }
	if ( $filter_108_count >= $filter_108_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 108';
		}
	if ( $filter_108_count ) { $blacklist_word_combo++; }
	if ( $filter_109_count >= $filter_109_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 109';
		}
	if ( $filter_109_count ) { $blacklist_word_combo++; }
	if ( $filter_110_count >= $filter_110_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 110';
		}
	if ( $filter_110_count ) { $blacklist_word_combo++; }
	if ( $filter_111_count >= $filter_111_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 111';
		}
	if ( $filter_111_count ) { $blacklist_word_combo++; }
	if ( $filter_112_count >= $filter_112_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 112';
		}
	if ( $filter_112_count ) { $blacklist_word_combo++; }
	if ( $filter_113_count >= $filter_113_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 113';
		}
	if ( $filter_113_count ) { $blacklist_word_combo++; }
	if ( $filter_114_count >= $filter_114_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 114';
		}
	if ( $filter_114_count ) { $blacklist_word_combo++; }
	if ( $filter_115_count >= $filter_115_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 115';
		}
	if ( $filter_115_count ) { $blacklist_word_combo++; }
	if ( $filter_116_count >= $filter_116_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 116';
		}
	if ( $filter_116_count ) { $blacklist_word_combo++; }
	if ( $filter_117_count >= $filter_117_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 117';
		}
	if ( $filter_117_count ) { $blacklist_word_combo++; }
	if ( $filter_118_count >= $filter_118_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 118';
		}
	if ( $filter_118_count ) { $blacklist_word_combo++; }
	if ( $filter_119_count >= $filter_119_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 119';
		}
	if ( $filter_119_count ) { $blacklist_word_combo++; }
	if ( $filter_120_count >= $filter_120_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 120';
		}
	if ( $filter_120_count ) { $blacklist_word_combo++; }
	if ( $filter_121_count >= $filter_121_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 121';
		}
	if ( $filter_121_count ) { $blacklist_word_combo++; }
	if ( $filter_122_count >= $filter_122_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 122';
		}
	if ( $filter_122_count ) { $blacklist_word_combo++; }
	if ( $filter_123_count >= $filter_123_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 123';
		}
	if ( $filter_123_count ) { $blacklist_word_combo++; }
	if ( $filter_124_count >= $filter_124_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 124';
		}
	if ( $filter_124_count ) { $blacklist_word_combo++; }
	if ( $filter_125_count >= $filter_125_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 125';
		}
	if ( $filter_125_count ) { $blacklist_word_combo++; }
	if ( $filter_126_count >= $filter_126_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 126';
		}
	if ( $filter_126_count ) { $blacklist_word_combo++; }
	if ( $filter_127_count >= $filter_127_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 127';
		}
	if ( $filter_127_count ) { $blacklist_word_combo++; }
	if ( $filter_128_count >= $filter_128_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 128';
		}
	if ( $filter_128_count ) { $blacklist_word_combo++; }
	if ( $filter_129_count >= $filter_129_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 129';
		}
	if ( $filter_129_count ) { $blacklist_word_combo++; }
	if ( $filter_130_count >= $filter_130_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 130';
		}
	if ( $filter_130_count ) { $blacklist_word_combo++; }
	if ( $filter_131_count >= $filter_131_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 131';
		}
	if ( $filter_131_count ) { $blacklist_word_combo++; }
	if ( $filter_132_count >= $filter_132_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 132';
		}
	if ( $filter_132_count ) { $blacklist_word_combo++; }
	if ( $filter_133_count >= $filter_133_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 133';
		}
	if ( $filter_133_count ) { $blacklist_word_combo++; }
	if ( $filter_134_count >= $filter_134_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 134';
		}
	if ( $filter_134_count ) { $blacklist_word_combo++; }
	if ( $filter_135_count >= $filter_135_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 135';
		}
	if ( $filter_135_count ) { $blacklist_word_combo++; }
	if ( $filter_136_count >= $filter_136_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 136';
		}
	if ( $filter_136_count ) { $blacklist_word_combo++; }
	if ( $filter_137_count >= $filter_137_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 137';
		}
	if ( $filter_137_count ) { $blacklist_word_combo++; }
	if ( $filter_138_count >= $filter_138_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 138';
		}
	if ( $filter_138_count ) { $blacklist_word_combo++; }
	if ( $filter_139_count >= $filter_139_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 139';
		}
	if ( $filter_139_count ) { $blacklist_word_combo++; }
	if ( $filter_140_count >= $filter_140_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 140';
		}
	if ( $filter_140_count ) { $blacklist_word_combo++; }
	if ( $filter_141_count >= $filter_141_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 141';
		}
	if ( $filter_141_count ) { $blacklist_word_combo++; }
	if ( $filter_142_count >= $filter_142_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 142';
		}
	if ( $filter_142_count ) { $blacklist_word_combo++; }
	if ( $filter_143_count >= $filter_143_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 143';
		}
	if ( $filter_143_count ) { $blacklist_word_combo++; }
	if ( $filter_144_count >= $filter_144_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 144';
		}
	if ( $filter_144_count ) { $blacklist_word_combo++; }
	if ( $filter_145_count >= $filter_145_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 145';
		}
	if ( $filter_145_count ) { $blacklist_word_combo++; }
	if ( $filter_146_count >= $filter_146_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 146';
		}
	if ( $filter_146_count ) { $blacklist_word_combo++; }
	if ( $filter_147_count >= $filter_147_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 147';
		}
	if ( $filter_147_count ) { $blacklist_word_combo++; }
	if ( $filter_148_count >= $filter_148_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 148';
		}
	if ( $filter_148_count ) { $blacklist_word_combo++; }
	if ( $filter_149_count >= $filter_149_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 149';
		}
	if ( $filter_149_count ) { $blacklist_word_combo++; }
	if ( $filter_150_count >= $filter_150_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 150';
		}
	if ( $filter_150_count ) { $blacklist_word_combo++; }
	if ( $filter_151_count >= $filter_151_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 151';
		}
	if ( $filter_151_count ) { $blacklist_word_combo++; }
	if ( $filter_152_count >= $filter_152_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 152';
		}
	if ( $filter_152_count ) { $blacklist_word_combo++; }
	if ( $filter_153_count >= $filter_153_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 153';
		}
	if ( $filter_153_count ) { $blacklist_word_combo++; }
	if ( $filter_154_count >= $filter_154_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 154';
		}
	if ( $filter_154_count ) { $blacklist_word_combo++; }
	if ( $filter_155_count >= $filter_155_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 155';
		}
	if ( $filter_155_count ) { $blacklist_word_combo++; }
	if ( $filter_156_count >= $filter_156_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 156';
		}
	if ( $filter_156_count ) { $blacklist_word_combo++; }
	if ( $filter_157_count >= $filter_157_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 157';
		}
	if ( $filter_157_count ) { $blacklist_word_combo++; }
	if ( $filter_158_count >= $filter_158_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 158';
		}
	if ( $filter_158_count ) { $blacklist_word_combo++; }


	if ( $filter_500_count >= $filter_500_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 500';
		}
	if ( $filter_500_count ) { $blacklist_word_combo++; }
	if ( $filter_501_count >= $filter_501_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 501';
		}
	if ( $filter_501_count ) { $blacklist_word_combo++; }
	if ( $filter_502_count >= $filter_502_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 502';
		}
	if ( $filter_502_count ) { $blacklist_word_combo++; }
	if ( $filter_503_count >= $filter_503_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 503';
		}
	if ( $filter_503_count ) { $blacklist_word_combo++; }
	if ( $filter_504_count >= $filter_504_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 504';
		}
	if ( $filter_504_count ) { $blacklist_word_combo++; }
	if ( $filter_505_count >= $filter_505_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 505';
		}
	if ( $filter_505_count ) { $blacklist_word_combo++; }
	if ( $filter_506_count >= $filter_506_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 506';
		}
	if ( $filter_506_count ) { $blacklist_word_combo++; }
	if ( $filter_507_count >= $filter_507_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 507';
		}
	if ( $filter_507_count ) { $blacklist_word_combo++; }
	if ( $filter_508_count >= $filter_508_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 508';
		}
	if ( $filter_508_count ) { $blacklist_word_combo++; }
	if ( $filter_509_count >= $filter_509_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 509';
		}
	if ( $filter_509_count ) { $blacklist_word_combo++; }

	/*
	// Execute Filter Test(s)

	$i = 0;
	while ( $i <= $filter_set_master_count ) {
		$filter_phrase_parameters = explode( '[::wpsf::]', $filter_set_master[$i] );
		$filter_phrase 					= $filter_phrase_parameters[0];
		$filter_phrase_limit 			= $filter_phrase_parameters[1];
		$filter_phrase_trackback_limit 	= $filter_phrase_parameters[2];
		$filter_phrase_count			= substr_count( $commentdata_comment_content_lc, $filter_phrase );
		if ( ( $filter_phrase_limit != 0 && $filter_phrase_count >= $filter_phrase_limit ) || ( $filter_phrase_limit == 1 && eregi( $filter_phrase, $commentdata_comment_author_lc ) ) || ( $commentdata_comment_author_lc == $filter_phrase ) ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			}
		$i++;
		}
	*/
	
	// Regular Expression Tests
	
	if ( eregi( "<a href=\"http://([a-z0-9.-]+).com/\">([a-z0-9.-]+)</a>, \[url=http://([a-z0-9.-]+).com/\]([a-z0-9.-]+)\[/url\], \[link=http://([a-z0-9.-]+).com/\]([a-z0-9.-]+)\[/link\], http://([a-z0-9.-]+).com/", $commentdata_comment_content_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' RE10001';
		}
	if ( eregi( "<a href=\\\"http://([a-z0-9.-]+).com/\\\">([a-z0-9.-]+)</a>, \[url=http://([a-z0-9.-]+).com/\]([a-z0-9.-]+)\[/url\], \[link=http://([a-z0-9.-]+).com/\]([a-z0-9.-]+)\[/link\], http://([a-z0-9.-]+).com/", $commentdata_comment_content_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' RE10001';
		}
	if ( eregi( "<a href=\\\"http://([a-z0-9.-]+).com/\\\">([a-z0-9.-]+)</a>, \[url=http://([a-z0-9.-]+).com/\]([a-z0-9.-]+)\[/url\], \[link=http://([a-z0-9.-]+).com/]([a-z0-9.-\ ]+)\[/link\], http://([a-z0-9.-]+).com/", $commentdata_comment_content_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' RE10002';
		}
	if ( eregi( "<a href=", $commentdata_comment_content_lc ) && eregi( "</a>,", $commentdata_comment_content_lc ) && eregi( "\[url=http://", $commentdata_comment_content_lc ) && eregi( ".com/\]", $commentdata_comment_content_lc ) && eregi( "\[/url\],", $commentdata_comment_content_lc ) && eregi( "\[link=http://", $commentdata_comment_content_lc )  && eregi( "\[/link\],", $commentdata_comment_content_lc ) && substr_count(  $commentdata_comment_content_lc, "http://" ) > 2 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' RE10003';
		}
	
	// Test Comment Author 
	// Words in Comment Author Repeated in Content - With Keyword Density
	$RepeatedTermsFilters = array('.','-',':');
	$RepeatedTermsTempPhrase = str_replace($RepeatedTermsFilters,'',$commentdata_comment_author_lc);
	$RepeatedTermsTest = explode(' ',$RepeatedTermsTempPhrase);
	$RepeatedTermsTestCount = count($RepeatedTermsTest);
	$CommentContentTotalWords = count( explode( ' ', $commentdata_comment_content_lc ) );
	$i = 0;
	while ( $i <= $RepeatedTermsTestCount ) {
		if ( $RepeatedTermsTest[$i] ) {
			$RepeatedTermsInContentCount = substr_count( $commentdata_comment_content_lc, $RepeatedTermsTest[$i] );
			$RepeatedTermsInContentStrLength = strlen($RepeatedTermsTest[$i]);
			if ( $RepeatedTermsInContentCount > 1 && $CommentContentTotalWords < $RepeatedTermsInContentCount ) {
				$RepeatedTermsInContentCount = 1;
				}
			$RepeatedTermsInContentDensity = ( $RepeatedTermsInContentCount / $CommentContentTotalWords ) * 100;
			//$spamfree_error_code .= ' 9000-'.$i.' KEYWORD: '.$RepeatedTermsTest[$i].' DENSITY: '.$RepeatedTermsInContentDensity.'% TIMES WORD OCCURS: '.$RepeatedTermsInContentCount.' TOTAL WORDS: '.$CommentContentTotalWords;
			if ( $RepeatedTermsInContentCount >= 5 && $RepeatedTermsInContentStrLength >= 4 && $RepeatedTermsInContentDensity > 40 ) {		
				if ( !$content_filter_status ) { $content_filter_status = '1'; }
				$spamfree_error_code .= ' 9000-'.$i;
				}
			}
		$i++;
		}
	if ( $commentdata_comment_author_email_lc == 'aaron@yahoo.com' || $commentdata_comment_author_email_lc == 'asdf@yahoo.com' || $commentdata_comment_author_email_lc == 'bill@berlin.com' || $commentdata_comment_author_email_lc == 'capricanrulz@hotmail.com' || $commentdata_comment_author_email_lc == 'dominic@mail.com' || $commentdata_comment_author_email_lc == 'fuck@you.com' || $commentdata_comment_author_email_lc == 'heel@mail.com' || $commentdata_comment_author_email_lc == 'jane@mail.com' || $commentdata_comment_author_email_lc == 'neo@hotmail.com' || $commentdata_comment_author_email_lc == 'nick76@mailbox.com' || $commentdata_comment_author_email_lc == '12345@yahoo.com' || eregi( '\.seo@gmail\.com', $commentdata_comment_author_email_lc ) || eregi( '@keywordspy.com', $commentdata_comment_author_email_lc ) || eregi( '@fuckyou.com', $commentdata_comment_author_email_lc ) || eregi( 'fuckyou@', $commentdata_comment_author_email_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 9200';
		}
	// Test Referrers
	if ( eregi( $commentdata_php_self_lc, $WPCommentsPostURL ) && $commentdata_referrer_lc == $WPCommentsPostURL ) {
		// Often spammers send the referrer as the URL for the wp-comments-post.php page. Nimrods.
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 1000';
		}
	// Include in Blacklist :: BEGIN
	// Test User-Agents
	if ( !$commentdata_user_agent_lc  ) {
		// There is no reason for a blank UA String, unless it's been altered.
		$content_filter_status = '2';
		$spamfree_error_code .= ' 1001';
		}
	$commentdata_user_agent_lc_word_count = count( explode( " ", $commentdata_user_agent_lc ) );
	if ( $commentdata_user_agent_lc_word_count < 3 ) {
		if ( $commentdata_comment_type != 'trackback' && $commentdata_comment_type != 'pingback' || ( !eregi( 'movabletype', $commentdata_user_agent_lc ) && ( $commentdata_comment_type == 'trackback' || $commentdata_comment_type == 'pingback' ) ) ) {
			// Another test for altered UA's.
			$content_filter_status = '2';
			$spamfree_error_code .= ' 1001.1-'.$commentdata_user_agent_lc;
			}
		}
	// Test IPs
	//if ( $commentdata_remote_addr_lc == '64.20.49.178' || $commentdata_remote_addr_lc == '206.123.92.245' || $commentdata_remote_addr_lc == '72.249.100.188' || $commentdata_remote_addr_lc == '61.24.158.174' || $commentdata_remote_addr_lc == '77.92.88.27' || $commentdata_remote_addr_lc == '89.113.78.6' || $commentdata_remote_addr_lc == '92.48.65.27' || $commentdata_remote_addr_lc == '92.48.122.2' || $commentdata_remote_addr_lc == '92.241.176.200' || $commentdata_remote_addr_lc == '78.129.202.2' || $commentdata_remote_addr_lc == '78.129.202.15' || eregi( "^78.129.202.", $commentdata_remote_addr_lc ) || eregi( "^123.237.144.", $commentdata_remote_addr_lc ) || eregi( "^123.237.147.", $commentdata_remote_addr_lc ) ) {
	$spamfree_ip_bans = array(
								'59.162.251.58',
								'61.24.158.174',
								'64.20.49.178',
								'69.89.31.219',
								'72.249.100.188',
								'74.12.44.78',
								'77.92.88.13',
								'77.92.88.27',
								'78.62.9.58',
								'78.129.202.15',
								'78.129.202.2',
								'78.157.143.202',
								'79.143.176.12',
								'83.7.196.73',
								'86.34.201.86',
								'89.113.78.6',
								'92.241.176.200',
								'92.48.122.2',
								'92.48.122.3',
								'92.48.65.27',
								'92.241.168.216',
								'122.160.70.94',
								'122.162.251.167',
								'123.237.144.189',
								'123.237.144.92',
								'123.237.147.71',
								'193.37.152.242',
								'193.46.236.151',
								'193.46.236.152',
								'193.46.236.234',
								'202.143.112.106',
								'203.190.134.107',
								'206.123.92.245',
								'208.43.196.98',
								'220.224.230.71',
								);
	if ( in_array( $commentdata_remote_addr, $spamfree_ip_bans ) || eregi( "^78.129.202.", $commentdata_remote_addr_lc ) || eregi( "^123.237.144.", $commentdata_remote_addr_lc ) || eregi( "^123.237.147.", $commentdata_remote_addr_lc ) ) {

		// KeywordSpy caught using IP's in the range 123.237.144. and 123.237.147.
		$content_filter_status = '2';
		$spamfree_error_code .= ' 1002-'.$commentdata_remote_addr_lc;
		}
	// Test Remote Hosts
	if ( eregi( 'keywordspy.com', $commentdata_remote_host_lc ) ) {
		$content_filter_status = '2';
		$spamfree_error_code .= ' 1003-'.$commentdata_remote_host_lc;
		}
	/*
	// Following is causing errors on some systems. 06/17/08
	if ( $commentdata_remote_host_lc == 'blank' && $commentdata_comment_type != 'trackback' && $commentdata_comment_type != 'pingback' ) {
		// Experimental - However, have never seen a human comment where this occurs.
		$content_filter_status = '2';
		$spamfree_error_code .= ' 1004';
		}
	*/
	// Test Reverse DNS Hosts
	if ( eregi( 'keywordspy.com', $ReverseDNS ) ) {
		$content_filter_status = '2';
		$spamfree_error_code .= ' 1023-'.$ReverseDNS;
		}
	// Test Reverse DNS IP's
	// If faked to Match blog Server IP
	if ( $ReverseDNSIP == $BlogServerIP && $commentdata_comment_type != 'trackback' && $commentdata_comment_type != 'pingback' ) {
		$content_filter_status = '2';
		$spamfree_error_code .= ' 1031';
		}
	// If faked to be single dot
	if ( $ReverseDNSIP == '.' ) {
		$content_filter_status = '2';
		$spamfree_error_code .= ' 1032';
		}
	// Include in Blacklist :: END	
	// Test Pingbacks and Trackbacks
	if ( $commentdata_comment_type == 'pingback' || $commentdata_comment_type == 'trackback' ) {
	
		if ( $filter_1_count >= $filter_1_trackback_limit ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T1';
			}
		if ( $filter_200_count >= $filter_200_trackback_limit ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T200';
			}
		if ( $filter_200_count ) { $blacklist_word_combo++; }
		if ( $commentdata_comment_type == 'trackback' && eregi( 'WordPress', $commentdata_user_agent_lc ) ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T3000';
			}
		if ( eregi( 'Incutio XML-RPC -- WordPress/', $commentdata_user_agent_lc ) ) {
			$commentdata_user_agent_lc_explode = explode( '/', $commentdata_user_agent_lc );
			if ( $commentdata_user_agent_lc_explode[1] > $CurrentWordPressVersion ) {
				if ( !$content_filter_status ) { $content_filter_status = '1'; }
				$spamfree_error_code .= ' T1001';
				}
			}
		if ( $commentdata_comment_author == $commentdata_comment_author_lc ) {
			// Check to see if Comment Author is lowercase. Normal blog pings Authors are properly capitalized. No brainer.
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T1010';
			}
		if ( $ipProxy == 'PROXY DETECTED' ) {
			// Check to see if Trackback/Pingback is using proxy. Red flag.
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T1011';
			}
		if ( $commentdata_comment_content == '[...] read more [...]' ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T1020';
			}
		if ( eregi( $SplogTrackbackPhrase1, $commentdata_comment_content_lc_norm_apost ) || eregi( $SplogTrackbackPhrase1a, $commentdata_comment_content_lc ) || eregi( $SplogTrackbackPhrase2, $commentdata_comment_content_lc_norm_apost ) || eregi( $SplogTrackbackPhrase2a, $commentdata_comment_content_lc ) || eregi( $SplogTrackbackPhrase3, $commentdata_comment_content_lc_norm_apost ) || eregi( $SplogTrackbackPhrase3a, $commentdata_comment_content_lc ) || eregi( $SplogTrackbackPhrase4, $commentdata_comment_content_lc_norm_apost ) || eregi( $SplogTrackbackPhrase5, $commentdata_comment_content_lc_norm_apost ) || ( eregi( $SplogTrackbackPhrase20a, $commentdata_comment_content_lc_norm_apost ) && ( eregi( $SplogTrackbackPhrase20b, $commentdata_comment_content_lc_norm_apost ) || eregi( $SplogTrackbackPhrase20c, $commentdata_comment_content_lc ) ) ) ) {
			// Check to see if common patterns exist in comment content.
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T2002';
			}
		if ( eregi( $commentdata_comment_author_lc_spam_strong, $commentdata_comment_content_lc ) ) {
			// Check to see if Comment Author is repeated in content, enclosed in <strong> tags.
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T2003';
			}
		if ( eregi( $commentdata_comment_author_lc_spam_a1, $commentdata_comment_content_lc ) || eregi( $commentdata_comment_author_lc_spam_a2, $commentdata_comment_content_lc ) ) {
			// Check to see if Comment Author is repeated in content, enclosed in <a> tags.
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T2004';
			}
		if ( eregi( $commentdata_comment_author_lc_spam_strong_dot1, $commentdata_comment_content_lc ) ) {
			// Check to see if Phrase... in bold is in content
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T2005';
			}
		if ( eregi( $commentdata_comment_author_lc_spam_strong_dot2, $commentdata_comment_content_lc ) ) {
			// Check to see if Phrase... in bold is in content
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' T2006';
			}
		// Check to see if keyword phrases in url match Comment Author - spammers do this to get links with desired keyword anchor text.
		// Start with url and convert to text phrase for matching against author.
		$i = 0;
		while ( $i <= $KeywordURLPhrasesCount ) {
			if ( $KeywordURLPhrases[$i] == $commentdata_comment_author_lc ) {
				if ( !$content_filter_status ) { $content_filter_status = '1'; }
				$spamfree_error_code .= ' T3001';
				}
			if ( $KeywordURLPhrases[$i] == $commentdata_comment_content_lc ) {
				if ( !$content_filter_status ) { $content_filter_status = '1'; }
				$spamfree_error_code .= ' T3002';
				}
			$i++;
			}
		// Reverse check to see if keyword phrases in url match Comment Author. Start with author and convert to url phrases.
		$i = 0;
		while ( $i <= $KeywordCommentAuthorPhraseURLVariationCount ) {
			$KeywordCommentAuthorPhrase1Version = '/'.$KeywordCommentAuthorPhrase1.$KeywordCommentAuthorPhraseURLVariation[$i];
			$KeywordCommentAuthorPhrase2Version = '/'.$KeywordCommentAuthorPhrase2.$KeywordCommentAuthorPhraseURLVariation[$i];
			$KeywordCommentAuthorPhrase3Version = '/'.$KeywordCommentAuthorPhrase3.$KeywordCommentAuthorPhraseURLVariation[$i];
			$KeywordCommentAuthorPhrase1SubStrCount = substr_count($commentdata_comment_author_url_lc, $KeywordCommentAuthorPhrase1Version);
			$KeywordCommentAuthorPhrase2SubStrCount = substr_count($commentdata_comment_author_url_lc, $KeywordCommentAuthorPhrase2Version);
			$KeywordCommentAuthorPhrase3SubStrCount = substr_count($commentdata_comment_author_url_lc, $KeywordCommentAuthorPhrase3Version);
			if ( $KeywordCommentAuthorPhrase1SubStrCount >= 1 ) {
				if ( !$content_filter_status ) { $content_filter_status = '1'; }
				$spamfree_error_code .= ' T3003-1-'.$KeywordCommentAuthorPhrase1Version;
				}
			else if ( $KeywordCommentAuthorPhrase2SubStrCount >= 1 ) {
				if ( !$content_filter_status ) { $content_filter_status = '1'; }
				$spamfree_error_code .= ' T3003-2-'.$KeywordCommentAuthorPhrase2Version;
				}
			else if ( $KeywordCommentAuthorPhrase3SubStrCount >= 1 ) {
				if ( !$content_filter_status ) { $content_filter_status = '1'; }
				$spamfree_error_code .= ' T3003-3-'.$KeywordCommentAuthorPhrase3Version;
				}
			$i++;
			}
		/*
		$i = 0;
		while ( $i <= $filter_set_master_count ) {
			$filter_phrase_parameters = explode( '[::wpsf::]', $filter_set_master[$i] );
			$filter_phrase 					= $filter_phrase_parameters[0];
			$filter_phrase_limit 			= $filter_phrase_parameters[1];
			$filter_phrase_trackback_limit 	= $filter_phrase_parameters[2];
			$filter_phrase_count			= substr_count( $commentdata_comment_content_lc, $filter_phrase );
			if ( $filter_phrase_count >= $filter_phrase_trackback_limit ) {
				if ( !$content_filter_status ) { $content_filter_status = '1'; }
				}
			$i++;
			}
		*/

		// Test Comment Author 
		// Words in Comment Author Repeated in Content		
		$RepeatedTermsFilters = array('.','-',':');
		$RepeatedTermsTempPhrase = str_replace($RepeatedTermsFilters,'',$commentdata_comment_author_lc);
		$RepeatedTermsTest = explode(' ',$RepeatedTermsTempPhrase);
		$RepeatedTermsTestCount = count($RepeatedTermsTest);
		$i = 0;
		while ( $i <= $RepeatedTermsTestCount ) {
			$RepeatedTermsInContentCount = substr_count( $commentdata_comment_content_lc, $RepeatedTermsTest[$i] );
			$RepeatedTermsInContentStrLength = strlen($RepeatedTermsTest[$i]);
			if ( $RepeatedTermsInContentCount >= 6 && $RepeatedTermsInContentStrLength >= 4 ) {		
				if ( !$content_filter_status ) { $content_filter_status = '1'; }
				$spamfree_error_code .= ' T9000-'.$i;
				}
			$i++;
			}
		}
	// Miscellaneous
	if ( $commentdata_comment_content == '[...]  [...]' ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 5000';
		}
	if ( $commentdata_comment_content == '<new comment>' ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 5001';
		}
	if ( eregi( 'blastogranitic atremata antiviral unteacherlike choruser coccygalgia corynebacterium reason', $commentdata_comment_content ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 5002';
		}


	// Execute Complex Filter Test(s)
	if ( $filter_10001_count >= $filter_10001_limit && $filter_10002_count >= $filter_10002_limit &&  ( $filter_10003_count >= $filter_10003_limit || $filter_10004_count >= $filter_10004_limit ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' CF10000';
		}
	if ( $filter_10003_count ) { $blacklist_word_combo++; }

	// Comment Author URL Tests - Free Websits
	if ( eregi( 'groups.google.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20001';
		}
	if ( $filter_20001_count >= $filter_20001_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20001A';
		}
	if ( $filter_20001C_count >= $filter_20001_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20001C';
		}
	if ( eregi( 'groups.yahoo.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20002';
		}
	if ( $filter_20002_count >= $filter_20002_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20002A';
		}
	if ( $filter_20002C_count >= $filter_20002_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20002C';
		}
	if ( eregi( ".?phpbbserver\.com", $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20003';
		}
	if ( $filter_20003_count >= $filter_20003_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20003A';
		}
	if ( $filter_20003C_count >= $filter_20003_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20003C';
		}
	if ( eregi( '.freehostia.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20004';
		}
	if ( $filter_20004_count >= $filter_20004_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20004A';
		}
	if ( $filter_20004C_count >= $filter_20004_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20004C';
		}
	if ( eregi( 'groups.google.us', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20005';
		}
	if ( $filter_20005_count >= $filter_20005_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20005A';
		}
	if ( $filter_20005C_count >= $filter_20005_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20005C';
		}
	if ( eregi( 'www.google.com/notebook/public/', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20006';
		}
	if ( $filter_20006_count >= $filter_20006_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20006A';
		}
	if ( $filter_20006C_count >= $filter_20006_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20006C';
		}
	if ( eregi( '.free-site-host.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20007';
		}
	if ( $filter_20007_count >= $filter_20007_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20007A';
		}
	if ( $filter_20007C_count >= $filter_20007_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20007C';
		}
	if ( eregi( 'youporn736.vox.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20008';
		}
	if ( $filter_20008_count >= $filter_20008_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20008A';
		}
	if ( $filter_20008C_count >= $filter_20008_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20008C';
		}
	if ( eregi( 'keywordspy.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20009';
		}
	if ( $filter_20009_count >= $filter_20009_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20009A';
		}
	if ( $filter_20009C_count >= $filter_20009_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20009C';
		}
	if ( eregi( '.t35.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20010';
		}
	if ( $filter_20010_count >= $filter_20010_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20010A';
		}
	if ( $filter_20010C_count >= $filter_20010_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20010C';
		}
	if ( eregi( '.150m.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20011';
		}
	if ( $filter_20011_count >= $filter_20011_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20011A';
		}
	if ( $filter_20011C_count >= $filter_20011_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20011C';
		}
	if ( eregi( '.250m.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20012';
		}
	if ( $filter_20012_count >= $filter_20012_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20012A';
		}
	if ( $filter_20012C_count >= $filter_20012_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20012C';
		}
	if ( eregi( 'blogs.ign.com', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20013';
		}
	if ( $filter_20013_count >= $filter_20013_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20013A';
		}
	if ( $filter_20013C_count >= $filter_20013_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20013C';
		}
	if ( eregi( 'members.lycos.co.uk', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20014';
		}
	if ( $filter_20014_count >= $filter_20014_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20014A';
		}
	if ( $filter_20014C_count >= $filter_20014_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20014C';
		}
	if ( eregi( '/christiantorrents.ru', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20015';
		}
	if ( $filter_20015_count >= $filter_20015_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20015A';
		}
	if ( $filter_20015C_count >= $filter_20015_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20015C';
		}
	if ( eregi( '.christiantorrents.ru', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20016';
		}
	if ( $filter_20016_count >= $filter_20016_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20016A';
		}
	if ( $filter_20016C_count >= $filter_20016_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20016C';
		}
	if ( eregi( '/lifecity.tv', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20017';
		}
	if ( $filter_20017_count >= $filter_20017_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20017A';
		}
	if ( $filter_20017C_count >= $filter_20017_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20017C';
		}
	if ( eregi( '.lifecity.tv', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20018';
		}
	if ( $filter_20018_count >= $filter_20018_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20018A';
		}
	if ( $filter_20018C_count >= $filter_20018_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20018C';
		}
	if ( eregi( '/lifecity.info', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20019';
		}
	if ( $filter_20019_count >= $filter_20019_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20019A';
		}
	if ( $filter_20019C_count >= $filter_20019_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20019C';
		}
	if ( eregi( '.lifecity.info', $commentdata_comment_author_url_lc ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20020';
		}
	if ( $filter_20020_count >= $filter_20020_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20020A';
		}
	if ( $filter_20020C_count >= $filter_20020_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20020C';
		}


	// Comment Author Tests
	if ( $filter_2_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 2AUTH';
		}
	if ( $filter_2_count ) { $blacklist_word_combo++; }
	if ( $filter_3_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 3AUTH';
		}
	if ( $filter_3_count ) { $blacklist_word_combo++; }
	if ( $filter_4_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 4AUTH';
		}
	if ( $filter_4_count ) { $blacklist_word_combo++; }
	if ( $filter_5_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 5AUTH';
		}
	if ( $filter_5_count ) { $blacklist_word_combo++; }
	if ( $filter_6_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 6AUTH';
		}
	if ( $filter_6_count ) { $blacklist_word_combo++; }
	if ( $filter_7_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 7AUTH';
		}
	if ( $filter_7_count ) { $blacklist_word_combo++; }
	if ( $filter_8_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 8AUTH';
		}
	if ( $filter_8_count ) { $blacklist_word_combo++; }
	if ( $filter_9_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 9AUTH';
		}
	if ( $filter_9_count ) { $blacklist_word_combo++; }
	if ( $filter_10_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 10AUTH';
		}
	if ( $filter_10_count ) { $blacklist_word_combo++; }
	if ( $filter_11_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 11AUTH';
		}
	if ( $filter_11_count ) { $blacklist_word_combo++; }
	if ( $filter_12_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 12AUTH';
		}
	if ( $filter_12_count ) { $blacklist_word_combo++; }
	if ( $filter_13_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 13AUTH';
		}
	if ( $filter_13_count ) { $blacklist_word_combo++; }	
	if ( $filter_14_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 14AUTH';
		}
	if ( $filter_14_count ) { $blacklist_word_combo++; }	
	if ( $filter_15_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 15AUTH';
		}
	if ( $filter_15_count ) { $blacklist_word_combo++; }
	if ( $filter_16_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 16AUTH';
		}
	if ( $filter_16_count ) { $blacklist_word_combo++; }	
	if ( $filter_17_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 17AUTH';
		}
	if ( $filter_17_count ) { $blacklist_word_combo++; }
	if ( $filter_18_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 18AUTH';
		}
	if ( $filter_18_count ) { $blacklist_word_combo++; }
	if ( $filter_19_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 19AUTH';
		}
	if ( $filter_19_count ) { $blacklist_word_combo++; }
	if ( $filter_20_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 20AUTH';
		}
	if ( $filter_20_count ) { $blacklist_word_combo++; }
	if ( $filter_21_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 21AUTH';
		}
	if ( $filter_21_count ) { $blacklist_word_combo++; }
	if ( $filter_22_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 22AUTH';
		}
	if ( $filter_22_count ) { $blacklist_word_combo++; }
	if ( $filter_23_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 23AUTH';
		}
	if ( $filter_23_count ) { $blacklist_word_combo++; }
	if ( $filter_24_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 24AUTH';
		}
	if ( $filter_24_count ) { $blacklist_word_combo++; }
	if ( $filter_25_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 25AUTH';
		}
	if ( $filter_25_count ) { $blacklist_word_combo++; }
	if ( $filter_26_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 26AUTH';
		}
	if ( $filter_26_count ) { $blacklist_word_combo++; }
	if ( $filter_27_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 27AUTH';
		}
	if ( $filter_27_count ) { $blacklist_word_combo++; }
	if ( $filter_28_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 28AUTH';
		}
	if ( $filter_28_count ) { $blacklist_word_combo++; }
	if ( $filter_29_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 29AUTH';
		}
	if ( $filter_29_count ) { $blacklist_word_combo++; }
	if ( $filter_30_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 30AUTH';
		}
	if ( $filter_30_count ) { $blacklist_word_combo++; }
	if ( $filter_31_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 31AUTH';
		}
	if ( $filter_31_count ) { $blacklist_word_combo++; }
	if ( $filter_32_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 32AUTH';
		}
	if ( $filter_32_count ) { $blacklist_word_combo++; }
	if ( $filter_33_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 33AUTH';
		}
	if ( $filter_33_count ) { $blacklist_word_combo++; }
	if ( $filter_34_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 34AUTH';
		}
	if ( $filter_34_count ) { $blacklist_word_combo++; }

	if ( eregi( 'buy', $commentdata_comment_author_lc ) && ( eregi( 'online', $commentdata_comment_author_lc ) || eregi( 'pill', $commentdata_comment_author_lc ) ) ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 200AUTH';
		$blacklist_word_combo++;
		}

	// Non-Medical Author Tests
	if ( $filter_210_author_count >= 1 ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' 210AUTH';
		}
	if ( $filter_210_count ) { $blacklist_word_combo++; }
	
	// Comment Author Tests - Non-Trackback - SEO/WebDev/Offshore
	if ( $commentdata_comment_type != 'trackback' && $commentdata_comment_type != 'pingback' ) {
		if ( $filter_300_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 300AUTH';
			}
		if ( $filter_301_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 301AUTH';
			}
		if ( $filter_302_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 302AUTH';
			}
		if ( $filter_303_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 303AUTH';
			}
		if ( $filter_304_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 304AUTH';
			}
		if ( $filter_305_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 305AUTH';
			}
		if ( $filter_306_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 306AUTH';
			}
		if ( $filter_307_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 307AUTH';
			}
		if ( $filter_308_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 308AUTH';
			}
		if ( $filter_309_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 309AUTH';
			}
		if ( $filter_310_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 310AUTH';
			}
		if ( $filter_311_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 311AUTH';
			}
		if ( $filter_312_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 312AUTH';
			}
		if ( $filter_313_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 313AUTH';
			}
		if ( $filter_314_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 314AUTH';
			}
		if ( $filter_315_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 315AUTH';
			}
		if ( $filter_316_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 316AUTH';
			}
		if ( $filter_317_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 317AUTH';
			}
		if ( $filter_318_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 318AUTH';
			}
		if ( $filter_319_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 319AUTH';
			}
		if ( $filter_320_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 320AUTH';
			}
		if ( $filter_321_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 321AUTH';
			}
		if ( $filter_322_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 322AUTH';
			}
		if ( $filter_323_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 323AUTH';
			}
		if ( $filter_324_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 324AUTH';
			}
		if ( $filter_325_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 325AUTH';
			}
		if ( $filter_326_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 326AUTH';
			}
		if ( $filter_327_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 327AUTH';
			}
		if ( $filter_328_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 328AUTH';
			}
		if ( $filter_329_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 329AUTH';
			}
		if ( $filter_330_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 330AUTH';
			}
		if ( $filter_331_author_count >= 1 ) {
			if ( !$content_filter_status ) { $content_filter_status = '1'; }
			$spamfree_error_code .= ' 331AUTH';
			}

		}
	
	// Blacklist Word Combinations
	if ( $blacklist_word_combo >= $blacklist_word_combo_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' BLC1000';
		}
	if ( $blacklist_word_combo_total >= $blacklist_word_combo_total_limit ) {
		if ( !$content_filter_status ) { $content_filter_status = '1'; }
		$spamfree_error_code .= ' BLC1010';
		}
	
	if ( !$spamfree_error_code ) {
		$spamfree_error_code = 'No Error';
		}
	$spamfree_error_code = ltrim($spamfree_error_code);
	
	$spamfree_error_data = array( $spamfree_error_code, $blacklist_word_combo, $blacklist_word_combo_total );
	
	update_option( 'spamfree_error_data', $spamfree_error_data );
		
	return $content_filter_status;
	// CONTENT FILTERING :: END
	}

function spamfree_stats() {
	global $wp_version;
	$BlogWPVersion = $wp_version;
	if ($BlogWPVersion < '2.5') {
		echo '<h3>WP-SpamFree</h3>';
		}
	$spamfree_count = get_option('spamfree_count');
	if ( !$spamfree_count ) {
		echo '<p>No comment spam attempts have been detected yet.</p>';
		}
	else {
		echo '<p>'.sprintf(__('<a href="%1$s" target="_blank">WP-SpamFree</a> has blocked <strong>%2$s</strong> spam comments.'), 'http://www.hybrid6.com/webgeek/plugins/wp-spamfree/',  number_format($spamfree_count) ).'</p>';
		}
	}

if (!class_exists('wpSpamFree')) {
    class wpSpamFree {
	
		/**
		* @var string   The name the options are saved under in the database.
		*/
		var $adminOptionsName = 'wp_spamfree_options';
		
		/**
		* @var string   The name of the database table used by the plugin
		*/	
		var $db_table_name = 'wp_spamfree';
		
		
		/**
		* PHP 4 Compatible Constructor
		*/
		//function wpSpamFree(){$this->__construct();}

		/**
		* PHP 5 Constructor
		*/		
		//function __construct(){

		function wpSpamFree(){
			
			global $wpdb;
			
			error_reporting(0); // Prevents errors when page is accessed directly, outside WordPress
			
			register_activation_hook(__FILE__,array(&$this,'install_on_activation'));
			add_action('init', 'spamfree_init');
			add_action('admin_menu', array(&$this,'add_admin_pages'));
			add_action('wp_head', array(&$this, 'wp_head_intercept'));
			add_filter('the_content', 'spamfree_contact_form', 10);
			add_action('comment_form', 'spamfree_comment_form');
			add_action('preprocess_comment', 'spamfree_check_comment_type',1);
			add_action('activity_box_end', 'spamfree_stats');
        	}
		
		function add_admin_pages(){
			add_submenu_page("plugins.php","WP-SpamFree","WP-SpamFree",10, __FILE__, array(&$this,"output_existing_menu_sub_admin_page"));
			add_submenu_page("options-general.php","WP-SpamFree","WP-SpamFree",1, __FILE__, array(&$this,"output_existing_menu_sub_admin_page"));
			//add_submenu_page("index.php","WP-SpamFree","WP-SpamFree",1, __FILE__, array(&$this,"output_existing_menu_sub_admin_page"));
			}
		
		function output_existing_menu_sub_admin_page(){
			$wpSpamFreeVer=get_option('wp_spamfree_version');
			if ($wpSpamFreeVer!='') {
				$wpSpamFreeVerAdmin='Version '.$wpSpamFreeVer;
				}
			$spamCount=spamfree_count();
			?>
			<div class="wrap">
			<h2>WP-SpamFree</h2>
			
			<?php
			$installation_plugins_get_test_1	= 'wp-spamfree/wp-spamfree.php';
			$installation_file_test_0 			= ABSPATH . 'wp-content/plugins/wp-spamfree/wp-spamfree.php';
			$installation_file_test_1 			= ABSPATH . 'wp-config.php';
			$installation_file_test_2 			= ABSPATH . 'wp-includes/wp-db.php';
			$installation_file_test_3 			= ABSPATH . 'wp-content/plugins/wp-spamfree/js/wpsf-js.php';
			clearstatcache();
			if ($installation_plugins_get_test_1==$_GET['page']&&file_exists($installation_file_test_0)&&file_exists($installation_file_test_1)&&file_exists($installation_file_test_2)&&file_exists($installation_file_test_3)) {
				$wp_installation_status = 1;
				$wp_installation_status_color = 'green';
				$wp_installation_status_bg_color = '#CCFFCC';
				$wp_installation_status_msg_main = 'Installed Correctly';
				$wp_installation_status_msg_text = strtolower($wp_installation_status_msg_main);
				}
			else {
				$wp_installation_status = 0;
				$wp_installation_status_color = 'red';
				$wp_installation_status_bg_color = '#FFCCCC';
				$wp_installation_status_msg_main = 'Not Installed Correctly';
				$wp_installation_status_msg_text = strtolower($wp_installation_status_msg_main);
				}
			?>
			
			<div style='width:600px;border-style:solid;border-width:1px;border-color:<?php echo $wp_installation_status_color; ?>;background-color:<?php echo $wp_installation_status_bg_color; ?>;padding:0px 15px 0px 15px;'>
			<p><strong>Installation Status: <?php echo "<span style='color:".$wp_installation_status_color.";'>".$wp_installation_status_msg_main."</span>"; ?></strong></p>
			</div>
			<br />
			
			<?php
			if ($spamCount) {
				echo "
				<div style='width:600px;border-style:solid;border-width:1px;border-color:#000033;background-color:#CCCCFF;padding:0px 15px 0px 15px;'>
				<p>WP-SpamFree has blocked <strong>".number_format($spamCount)."</strong> spam comments!</p></div>
				";
				}
			$spamfree_options = get_option('spamfree_options');
			if ($_REQUEST['submitted']) {
				$spamfree_options_update = array (
						'cookie_validation_name' 				=> $spamfree_options['cookie_validation_name'],
						'cookie_validation_key' 				=> $spamfree_options['cookie_validation_key'],
						'form_validation_field_js' 				=> $spamfree_options['form_validation_field_js'],
						'form_validation_key_js' 				=> $spamfree_options['form_validation_key_js'],
						'cookie_get_function_name' 				=> $spamfree_options['cookie_get_function_name'],
						'cookie_set_function_name' 				=> $spamfree_options['cookie_set_function_name'],
						'cookie_delete_function_name' 			=> $spamfree_options['cookie_delete_function_name'],
						'comment_validation_function_name' 		=> $spamfree_options['comment_validation_function_name'],
						'wp_cache' 								=> $spamfree_options['wp_cache'],
						'wp_super_cache' 						=> $spamfree_options['wp_super_cache'],
						'use_captcha_backup' 					=> $spamfree_options['use_captcha_backup'],
						'block_all_trackbacks' 					=> $_REQUEST['block_all_trackbacks'],
						'block_all_pingbacks' 					=> $_REQUEST['block_all_pingbacks'],
						'use_trackback_verification' 			=> $spamfree_options['use_trackback_verification'],
						'form_include_website' 					=> $_REQUEST['form_include_website'],
						'form_require_website' 					=> $_REQUEST['form_require_website'],
						'form_include_phone' 					=> $_REQUEST['form_include_phone'],
						'form_require_phone' 					=> $_REQUEST['form_require_phone'],
						'form_include_drop_down_menu'			=> $_REQUEST['form_include_drop_down_menu'],
						'form_require_drop_down_menu'			=> $_REQUEST['form_require_drop_down_menu'],
						'form_drop_down_menu_title'				=> $_REQUEST['form_drop_down_menu_title'],
						'form_drop_down_menu_item_1'			=> $_REQUEST['form_drop_down_menu_item_1'],
						'form_drop_down_menu_item_2'			=> $_REQUEST['form_drop_down_menu_item_2'],
						'form_drop_down_menu_item_3'			=> $_REQUEST['form_drop_down_menu_item_3'],
						'form_drop_down_menu_item_4'			=> $_REQUEST['form_drop_down_menu_item_4'],
						'form_drop_down_menu_item_5'			=> $_REQUEST['form_drop_down_menu_item_5'],
						'form_drop_down_menu_item_6'			=> $_REQUEST['form_drop_down_menu_item_6'],
						'form_drop_down_menu_item_7'			=> $_REQUEST['form_drop_down_menu_item_7'],
						'form_drop_down_menu_item_8'			=> $_REQUEST['form_drop_down_menu_item_8'],
						'form_drop_down_menu_item_9'			=> $_REQUEST['form_drop_down_menu_item_9'],
						'form_drop_down_menu_item_10'			=> $_REQUEST['form_drop_down_menu_item_10'],
						'form_message_width' 					=> $_REQUEST['form_message_width'],
						'form_message_height' 					=> $_REQUEST['form_message_height'],
						'form_message_min_length' 				=> $_REQUEST['form_message_min_length'],
						'form_message_recipient' 				=> $_REQUEST['form_message_recipient'],
						
						);
				update_option('spamfree_options', $spamfree_options_update);
				}
				$spamfree_options = get_option('spamfree_options');
				$SiteURL = get_option('siteurl');
			?>
			
			<p>&nbsp;</p>
			
			<a name="wpsf_top"><strong>Quick Navigation - Contents</strong></a>
			<ol>
				<li><a href="#wpsf_spam_options">Spam Options</a></li>
				<li><a href="#wpsf_contact_form_options">Contact Form Options</a></li>
				<li><a href="#wpsf_installation_instructions">Installation Instructions</a></li>
				<li><a href="#wpsf_displaying_stats">Displaying Spam Stats on Your Blog</a></li>
				<li><a href="#wpsf_adding_comment_form">Adding a Comment Form to Your Blog</a></li>
				<li><a href="#wpsf_known_conflicts">Known Plugin Conflicts</a></li>
				<li><a href="#wpsf_troubleshooting">Troubleshooting Guide / Support</a></li>
				<li><a href="#wpsf_let_others_know">Let Others Know About WP-SpamFree</a></li>
				<li><a href="#wpsf_download_plugin_documentation">Download Plugin / Documentation</a></li>
			</ol>
			
			<p>&nbsp;</p>
			
			<p><a name="wpsf_spam_options"><strong>Spam Options</strong></a></p>

			<form name="wpsf_spam_options" method="post">
			<input type="hidden" name="submitted" value="1" />

			<fieldset class="options">
				<ul>
					<li>
					<label for="block_all_trackbacks">
						<input type="checkbox" id="block_all_trackbacks" name="block_all_trackbacks" <?php echo ($spamfree_options['block_all_trackbacks']==true?"checked=\"checked\"":"") ?> />
						<strong>Disable trackbacks.</strong><br />(Use if trackback spam is excessive.)<br />&nbsp;
					</label>
					</li>
					<li>
					<label for="block_all_pingbacks">
						<input type="checkbox" id="block_all_pingbacks" name="block_all_pingbacks" <?php echo ($spamfree_options['block_all_pingbacks']==true?"checked=\"checked\"":"") ?> />
						<strong>Disable pingbacks.</strong><br />(Use if pingback spam is excessive. Disadvantage is reduction of communication between blogs.)<br />&nbsp;
					</label>
					</li>
				</ul>
			</fieldset>
			<p class="submit">
			<input type="submit" name="Submit" value="Update Options &raquo;" style="float:left;" />
			</p>
			</form>

			<p>&nbsp;</p>

			<p>&nbsp;</p>
			
			<p><div style="float:right;font-size:12px;">[ <a href="#wpsf_top">BACK TO TOP</a> ]</div></p>

			<p>&nbsp;</p>
			
			<p><a name="wpsf_contact_form_options"><strong>Contact Form Options</strong></a></p>

			<form name="wpsf_contact_options" method="post">
			<input type="hidden" name="submitted" value="1" />

			<fieldset class="options">
				<ul>
					<li>
					<label for="form_include_website">
						<input type="checkbox" id="form_include_website" name="form_include_website" <?php echo ($spamfree_options['form_include_website']==true?"checked=\"checked\"":"") ?> />
						<strong>Include "Website" field.</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_require_website">
						<input type="checkbox" id="form_require_website" name="form_require_website" <?php echo ($spamfree_options['form_require_website']==true?"checked=\"checked\"":"") ?> />
						<strong>Require "Website" field.</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_include_phone">
						<input type="checkbox" id="form_include_phone" name="form_include_phone" <?php echo ($spamfree_options['form_include_phone']==true?"checked=\"checked\"":"") ?> />
						<strong>Include "Phone" field.</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_require_phone">
						<input type="checkbox" id="form_require_phone" name="form_require_phone" <?php echo ($spamfree_options['form_require_phone']==true?"checked=\"checked\"":"") ?> />
						<strong>Require "Phone" field.</strong><br />&nbsp;
					</label>
					</li>

					<li>
					<label for="form_include_drop_down_menu">
						<input type="checkbox" id="form_include_drop_down_menu" name="form_include_drop_down_menu" <?php echo ($spamfree_options['form_include_drop_down_menu']==true?"checked=\"checked\"":"") ?> />
						<strong>Include drop-down menu select field.</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_require_drop_down_menu">
						<input type="checkbox" id="form_require_drop_down_menu" name="form_require_drop_down_menu" <?php echo ($spamfree_options['form_require_drop_down_menu']==true?"checked=\"checked\"":"") ?> />
						<strong>Require drop-down menu select field.</strong><br />&nbsp;
					</label>
					</li>					
					<li>
					<label for="form_drop_down_menu_title">
						<?php $FormDropDownMenuTitle = $spamfree_options['form_drop_down_menu_title']; ?>
						<input type="text" size="40" id="form_drop_down_menu_title" name="form_drop_down_menu_title" value="<?php if ( $FormDropDownMenuTitle ) { echo $FormDropDownMenuTitle; } else { echo '';} ?>" />
						<strong>Title of drop-down select menu. (Menu won't be shown if empty.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_1">
						<?php $FormDropDownMenuItem1 = $spamfree_options['form_drop_down_menu_item_1']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_1" name="form_drop_down_menu_item_1" value="<?php if ( $FormDropDownMenuItem1 ) { echo $FormDropDownMenuItem1; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 1. (Menu won't be shown if empty.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_2">
						<?php $FormDropDownMenuItem2 = $spamfree_options['form_drop_down_menu_item_2']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_2" name="form_drop_down_menu_item_2" value="<?php if ( $FormDropDownMenuItem2 ) { echo $FormDropDownMenuItem2; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 2. (Menu won't be shown if empty.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_3">
						<?php $FormDropDownMenuItem3 = $spamfree_options['form_drop_down_menu_item_3']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_3" name="form_drop_down_menu_item_3" value="<?php if ( $FormDropDownMenuItem3 ) { echo $FormDropDownMenuItem3; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 3. (Leave blank if not using.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_4">
						<?php $FormDropDownMenuItem4 = $spamfree_options['form_drop_down_menu_item_4']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_4" name="form_drop_down_menu_item_4" value="<?php if ( $FormDropDownMenuItem4 ) { echo $FormDropDownMenuItem4; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 4. (Leave blank if not using.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_5">
						<?php $FormDropDownMenuItem5 = $spamfree_options['form_drop_down_menu_item_5']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_5" name="form_drop_down_menu_item_5" value="<?php if ( $FormDropDownMenuItem5 ) { echo $FormDropDownMenuItem5; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 5. (Leave blank if not using.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_6">
						<?php $FormDropDownMenuItem6 = $spamfree_options['form_drop_down_menu_item_6']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_6" name="form_drop_down_menu_item_6" value="<?php if ( $FormDropDownMenuItem6 ) { echo $FormDropDownMenuItem6; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 6. (Leave blank if not using.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_7">
						<?php $FormDropDownMenuItem7 = $spamfree_options['form_drop_down_menu_item_7']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_7" name="form_drop_down_menu_item_7" value="<?php if ( $FormDropDownMenuItem7 ) { echo $FormDropDownMenuItem7; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 7. (Leave blank if not using.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_8">
						<?php $FormDropDownMenuItem8 = $spamfree_options['form_drop_down_menu_item_8']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_8" name="form_drop_down_menu_item_8" value="<?php if ( $FormDropDownMenuItem8 ) { echo $FormDropDownMenuItem8; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 8. (Leave blank if not using.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_9">
						<?php $FormDropDownMenuItem9 = $spamfree_options['form_drop_down_menu_item_9']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_9" name="form_drop_down_menu_item_9" value="<?php if ( $FormDropDownMenuItem9 ) { echo $FormDropDownMenuItem9; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 9. (Leave blank if not using.)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_drop_down_menu_item_10">
						<?php $FormDropDownMenuItem10 = $spamfree_options['form_drop_down_menu_item_10']; ?>
						<input type="text" size="40" id="form_drop_down_menu_item_10" name="form_drop_down_menu_item_10" value="<?php if ( $FormDropDownMenuItem10 ) { echo $FormDropDownMenuItem10; } else { echo '';} ?>" />
						<strong>Drop-down select menu item 10. (Leave blank if not using.)</strong><br />&nbsp;
					</label>
					</li>
					

					<li>
					<label for="form_message_width">
						<?php $FormMessageWidth = $spamfree_options['form_message_width']; ?>
						<input type="text" size="4" id="form_message_width" name="form_message_width" value="<?php if ( $FormMessageWidth && $FormMessageWidth >= 40 ) { echo $FormMessageWidth; } else { echo '40';} ?>" />
						<strong>"Message" field width. (Minimum 40)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_message_height">
						<?php $FormMessageHeight = $spamfree_options['form_message_height']; ?>
						<input type="text" size="4" id="form_message_height" name="form_message_height" value="<?php if ( $FormMessageHeight && $FormMessageHeight >= 5 ) { echo $FormMessageHeight; } else if ( !$FormMessageHeight ) { echo '10'; } else { echo '5';} ?>" />
						<strong>"Message" field height. (Minimum 5, Default 10)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_message_min_length">
						<?php $FormMessageMinLength = $spamfree_options['form_message_min_length']; ?>
						<input type="text" size="4" id="form_message_min_length" name="form_message_min_length" value="<?php if ( $FormMessageMinLength && $FormMessageMinLength >= 15 ) { echo $FormMessageMinLength; } else if ( !$FormMessageWidth ) { echo '25'; } else { echo '15';} ?>" />
						<strong>Minimum message length (# of characters). (Minimum 15, Default 25)</strong><br />&nbsp;
					</label>
					</li>
					<li>
					<label for="form_message_recipient">
						<?php $FormMessageRecipient = $spamfree_options['form_message_recipient']; ?>
						<input type="text" size="40" id="form_message_recipient" name="form_message_recipient" value="<?php if ( !$FormMessageRecipient ) { echo get_option('admin_email'); } else { echo $FormMessageRecipient; } ?>" />
						<strong>Optional: Enter alternate form recipient. Default is blog admin email.</strong><br />&nbsp;
					</label>
					</li>
				</ul>
			</fieldset>
			<p class="submit">
			<input type="submit" name="Submit" value="Update Options &raquo;" style="float:left;" />
			</p>
			</form>
			
			<p>&nbsp;</p>
			
			<p>&nbsp;</p>
			
			<p><div style="float:right;font-size:12px;">[ <a href="#wpsf_top">BACK TO TOP</a> ]</div></p>

			<p>&nbsp;</p>
			
			<p><a name="wpsf_installation_instructions"><strong>Installation Instructions</strong></a></p>

			<ol>
			    <li>After downloading, unzip file and upload the enclosed 'wp-spamfree' directory to your WordPress plugins directory: '/wp-content/plugins/'.<br />&nbsp;</li>
				<li>As always, <strong>activate</strong> the plugin on your WordPress plugins page.<br />&nbsp;</li>
				<li>Check to make sure the plugin is installed properly. Many support requests for this plugin originate from improper installation and can be easily prevented. To check proper installation status, go to the WP-SpamFree page in your Admin. It's a submenu link on the Plugins page. Go the the 'Installation Status' area near the top and it will tell you if the plugin is installed correctly. If it tells you that the plugin is not installed correctly, please double-check what directory you have installed WP-SpamFree in, delete any WP-SpamFree files you have uploaded to your server, re-read the Installation Instructions, and start the Installation process over from step 1. If it is installed correctly, then move on to the next step.<br />&nbsp;<br />Currently your plugin is: <?php echo "<span style='color:".$wp_installation_status_color.";'>".$wp_installation_status_msg_main."</span>"; ?><br />&nbsp;</li>
				<li>Select desired configuration options. Due to popular request, I've added the option to block trackbacks and pingbacks if the user feels they are excessive. I'd recommend not doing this, but the choice is yours.<br />&nbsp;</li>
				<li>If you are using front-end anti-spam plugins (CAPTCHA's, challenge questions, etc), be sure they are disabled since there's no longer a need for them, and these could likely conflict. (Back-end anti-spam plugins like Akismet are fine, although unnecessary.)</li>
			</ol>	
			<p>&nbsp;</p>
			<p>You're done! Sit back and see what it feels like to blog without comment spam!</p>
					
			<p><div style="float:right;font-size:12px;">[ <a href="#wpsf_top">BACK TO TOP</a> ]</div></p>

			<p>&nbsp;</p>

			<p><a name="wpsf_displaying_stats"><strong>Displaying Spam Stats on Your Blog</strong></a></p>

			Want to show off your spam stats on your blog and tell others about WP-SpamFree? Simply add the following code to your WordPress theme where you'd like the stats displayed: <br />&nbsp;<br /><code>&lt;?php if ( function_exists(spamfree_counter) ) { spamfree_counter(1); } ?&gt;</code><br />&nbsp;<br /> where '1' is the style. Replace the '1' with a number from 1-6 that corresponds to one of the following sample styles you'd like to use. To simply display text stats on your site (no graphic), replace the '1' with '0'.</code>
			
			<ol>
			    <li>&nbsp;<br />&nbsp;
				<img src='<?php echo $SiteURL; ?>/wp-content/plugins/wp-spamfree/counter/spamfree-counter-bg-1-preview.png' style="margin-right: 10px; margin-top: 7px; margin-bottom: 7px;  width: 140px; height: 66px" border="0" width="140" height="66" /></li>
				
			    <li>&nbsp;<br />&nbsp;
				<img src='<?php echo $SiteURL; ?>/wp-content/plugins/wp-spamfree/counter/spamfree-counter-bg-2-preview.png' style="margin-right: 10px; margin-top: 7px; margin-bottom: 7px;  width: 140px; height: 66px" border="0" width="140" height="66" /></li>
				
			    <li>&nbsp;<br />&nbsp;
				<img src='<?php echo $SiteURL; ?>/wp-content/plugins/wp-spamfree/counter/spamfree-counter-bg-3-preview.png' style="margin-right: 10px; margin-top: 7px; margin-bottom: 7px; width: 140px; height: 66px" border="0" width="140" height="66" /></li>
				
			    <li>&nbsp;<br />&nbsp;
				<img src='<?php echo $SiteURL; ?>/wp-content/plugins/wp-spamfree/counter/spamfree-counter-bg-4-preview.png' style="margin-right: 10px; margin-top: 7px; margin-bottom: 7px; width: 140px; height: 106px" border="0" width="140" height="106" /></li>
				
			    <li>&nbsp;<br />&nbsp;
				<img src='<?php echo $SiteURL; ?>/wp-content/plugins/wp-spamfree/counter/spamfree-counter-bg-5-preview.png' style="margin-right: 10px; margin-top: 7px; margin-bottom: 7px; width: 140px; height: 61px" border="0" width="140" height="61" /></li>
				
			    <li>&nbsp;<br />&nbsp;
				<img src='<?php echo $SiteURL; ?>/wp-content/plugins/wp-spamfree/counter/spamfree-counter-bg-6-preview.png' style="margin-right: 10px; margin-top: 7px; margin-bottom: 7px; width: 140px; height: 67px" border="0" width="140" height="67" /></li>
			</ol>
						
			To add stats to individual posts, you'll need to install the <a href="http://wordpress.org/extend/plugins/exec-php/" rel="external" target="_blank" >Exec-PHP</a> plugin.	
			<p><div style="float:right;font-size:12px;">[ <a href="#wpsf_top">BACK TO TOP</a> ]</div></p>

			<p>&nbsp;</p>
			
			<p><a name="wpsf_adding_comment_form"><strong>Adding a Comment Form to Your Blog</strong></a></p>

			First create a page (not post) where you want to have your comment form. Then, insert the following tag (using the HTML editing tab) and you're done: <code>&lt;!--spamfree-contact--&gt;</code><br />&nbsp;<br />
			
			There is no need to configure the form. It allows you to simply drop it into the page you want to install it on. However, there are a few basic configuration options. You can choose whether or not to include Phone and Website fields, whether they should be required, add a drop down menu with up to 10 options, set the width and height of the Message box, and the minimum message length.<br />&nbsp;<br />

			<strong>What the Contact Form feature IS:</strong> A simple drop-in contact form that won't get spammed.<br />
			<strong>What the Contact Form feature is NOT:</strong> A configurable and full-featured plugin like some other contact form plugins out there.<br />
			<strong>Note:</strong> Please do not request new features for the contact form, as the main focus of the plugin is spam protection. Thank you.<br />
			
			<p><div style="float:right;font-size:12px;">[ <a href="#wpsf_top">BACK TO TOP</a> ]</div></p>

			<p>&nbsp;</p>	

			<p><a name="wpsf_known_conflicts"><strong>Known Plugin Conflicts</strong></a></p>
			Plugins that are known to be incompatible with WP-SpamFree:
			<ol>
				<li><strong>AskApache Password Protect</strong><br />&nbsp;<br />Users have reported that using its feature to protect the /wp-content/ directory creates an .htaccess file in that directory that creates improper permissions and conflicts with WP-SpamFree (and most likely other plugins as well). You'll need to disable this feature, or disable the <em>AskApache Password Protect Plugin</em> and delete any .htaccess files it has created in your /wp-content/ directory before using WP-SpamFree.<br />&nbsp;</li>
				<li><strong>WP-OpenID</strong><br />&nbsp;</li>
				<li><strong>Some front-end anti-spam plugins, including CAPTCHA's, challenge questions, etc.</strong><br />&nbsp;<br />There's no longer a need for them, and these could likely conflict. (Back-end anti-spam plugins like Akismet are fine, although unnecessary.)</li>
			</ol>
			
			<p><div style="float:right;font-size:12px;">[ <a href="#wpsf_top">BACK TO TOP</a> ]</div></p>

			<p>&nbsp;</p>	

			<p><a name="wpsf_troubleshooting"><strong>Troubleshooting Guide / Support</strong></a></p>
			If you're having trouble getting things to work after installing the plugin, here are a few things to check:
			<ol>
				<li>If you haven't yet, please upgrade to the latest version.<br />&nbsp;</li>
				<li>Check to make sure the plugin is installed properly. Many support requests for this plugin originate from improper installation and can be easily prevented. To check proper installation status, go to the WP-SpamFree page in your Admin. It's a submenu link on the Plugins page. Go the the 'Installation Status' area near the top and it will tell you if the plugin is installed correctly. If it tells you that the plugin is not installed correctly, please double-check what directory you have installed WP-SpamFree in, delete any WP-SpamFree files you have uploaded to your server, re-read the Installation Instructions, and start the Installation process over from step 1.<br />&nbsp;<br />Currently your plugin is: <?php echo "<span style='color:".$wp_installation_status_color.";'>".$wp_installation_status_msg_main."</span>"; ?><br />&nbsp;</li>
				<li>Clear your browser's cache, clear your cookies, and restart your browser. Then reload the page.<br />&nbsp;</li>
				<li>If you are receiving the error message: "Sorry, there was an error. Please enable JavaScript and Cookies in your browser and try again." then you need to make sure <em>JavaScript</em> and <em>cookies</em> are enabled in your browser. (JavaScript is different from Java. Java is not required.) These are enabled by default in web browsers. The status display will let you know if these are turned on or off (as best the page can detect - occasionally the detection does not work.) If this message comes up consistently even after JavaScript and cookies are enabled, then there most likely is an installation problem, plugin conflict, or JavaScript conflict. Read on for possible solutions.<br />&nbsp;</li>
				<li>Check your WordPress Version. If you are using a release earlier than 2.3, you may want to upgrade for a whole slew of reasons, including features and security.<br />&nbsp;</li>
				<li>Check the options you have selected to make sure they are not disabling a feature you want to use.<br />&nbsp;</li>
				<li>Make sure that you are not using other front-end anti-spam plugins (CAPTCHA's, challenge questions, etc) since there's no longer a need for them, and these could likely conflict. (Back-end anti-spam plugins like Akismet are fine, although unnecessary.)<br />&nbsp;</li>
				<li>Visit http://www.yourblog.com/wp-content/plugins/wp-spamfree/js/wpsf-js.php (where <em>yourblog.com</em> is your blog url) and check two things. <br />&nbsp;<br /><strong>First, see if the file comes normally or if it comes up blank or with errors.</strong> That would indicate a problem. Submit a support request (see last troubleshooting step) and copy and past any error messages on the page into your message. <br />&nbsp;<br /><strong>Second, check for a 403 Forbidden error.</strong> That means there is a problem with your file permissions. If the files in the wp-spamfree folder don't have standard permissions (at least 644 or higher) they won't work. This usually only happens by manual modification, but strange things do happen. <strong>The <em>AskApache Password Protect Plugin</em> is known to cause this error.</strong> Users have reported that using its feature to protect the /wp-content/ directory creates an .htaccess file in that directory that creates improper permissions and conflicts with WP-SpamFree (and most likely other plugins as well). You'll need to disable this feature, or disable the <em>AskApache Password Protect Plugin</em> and delete any .htaccess files it has created in your /wp-content/ directory before using WP-SpamFree.<br />&nbsp;</li>
        <li>Check for conflicts with other JavaScripts installed on your site. This usually occurs with with JavaScripts unrelated to WordPress or plugins.<br />&nbsp;</li>
        <li>Check for conflicts with other WordPress plugins installed on your blog. This isn't common but does happen from time to time. I can't guarantee how well-written other plugins will be. First, see the <a href="#wpsf_known_conflicts">Known Plugin Conflicts</a> list. If you've disabled any plugins on that list and still have a problem, then proceed. <br />&nbsp;<br />To start testing for conflicts, temporarily deactivate all other plugins except WP-SpamFree. Then check to see if WP-SpamFree works by itself. (For best results make sure you are logged out and clear your cookies. Alternatively you can use another browser for testing.) If WP-SpamFree allows you to post a comment with no errors, then you know there is a plugin conflict. The next step is to activate each plugin, one at a time, log out, and try to post a comment. Then log in, deactivate that plugin, and repeat with the next plugin. (If possible, use a second browser to make it easier. Then you don't have to keep logging in and out with the first browser.) Be sure to clear cookies between attempts (before loading the page you want to comment on). If you do identify a plugin that conflicts, please let me know so I can work on bridging the compatibility issues.<br />&nbsp;</li>
				<li>Check the options you have selected to make sure they are not disabling a feature you want to use.<br />&nbsp;</li>
				<li>If have checked these, and still can't quite get it working, please either submit a support request at the <a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree/support" target="_blank" rel="external" >WP-SpamFree Support Page</a>, or <a href="mailto:scott@hybrid6.com?subject=WP-SpamFree Support Request [<?php echo $wpSpamFreeVerAdmin; ?>]">send a support email</a>.</li>
			</ol>
			
			<p><div style="float:right;font-size:12px;">[ <a href="#wpsf_top">BACK TO TOP</a> ]</div></p>

			<p>&nbsp;</p>
			
  			<p><a name="wpsf_let_others_know"><strong>Let Others Know About WP-SpamFree</strong></a></p>
	
			<strong>How does it feel to blog without being bombarded by automated comment spam?</strong> If you're happy with WP-SpamFree, there's a few things you can do to let others know:
			
			<ul>
				<li><a href="http://www.hybrid6.com/webgeek/2007/11/wp-spamfree-1-wordpress-plugin-released.php#comments" target="_blank" >Post a comment.</a></li>
				<li><a href="http://wordpress.org/extend/plugins/wp-spamfree/" target="_blank" >Rate WP-SpamFree</a> on WordPress.org.</li>
				<li><a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree/end-blog-spam" target="_blank" >Place a graphic link</a>  on your site letting others know how they can help end blog spam. ( &lt/BLOGSPAM&gt; )</li>
			</ul>
			
			<p><div style="float:right;font-size:12px;">[ <a href="#wpsf_top">BACK TO TOP</a> ]</div></p>

			<p>&nbsp;</p>			
			
			<a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree" target="_blank" rel="external" style="border-style:none;text-decoration:none;" ><img src="http://www.hybrid6.com/webgeek/images/wp-spamfree/end-blog-spam-button-01-black.png" alt="End Blog Spam! WP-SpamFree Comment Spam Protection for WordPress" border="0" style="border-style:none;text-decoration:none;" /></a><br />&nbsp;<br />
			
			<a name="wpsf_download_plugin_documentation"><strong>Download Plugin / Documentation</strong></a><br />
			Latest Version: <a href="http://www.hybrid6.com/webgeek/downloads/wp-spamfree.zip" target="_blank" rel="external" >Download Now</a><br />
			Plugin Homepage / Documentation: <a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree" target="_blank" rel="external" >WP-SpamFree</a><br />
			Leave Comments: <a href="http://www.hybrid6.com/webgeek/2007/11/wp-spamfree-1-wordpress-plugin-released.php" target="_blank" rel="external" >WP-SpamFree Release Announcement Blog Post</a><br />
			WordPress.org Page: <a href="http://wordpress.org/extend/plugins/wp-spamfree/" target="_blank" rel="external" >WP-SpamFree</a><br />
			Tech Support/Questions: <a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree/support" target="_blank" rel="external" >WP-SpamFree Support Page</a><br />
			End Blog Spam: <a href="http://www.hybrid6.com/webgeek/plugins/wp-spamfree/end-blog-spam" target="_blank" rel="external" >Let Others Know About WP-SpamFree!</a>
	
			<p>&nbsp;</p>

			<p><em><?php echo $wpSpamFreeVerAdmin; ?></em></p>
	
			<p><div style="float:right;font-size:12px;">[ <a href="#wpsf_top">BACK TO TOP</a> ]</div></p>

			<p>&nbsp;</p>

			<p>&nbsp;</p>
			</div>
			<?php
			}

		function wp_head_intercept(){
			$wpSpamFreeVer=get_option('wp_spamfree_version');
			if ($wpSpamFreeVer!='') {
				$wpSpamFreeVerJS=' v'.$wpSpamFreeVer;
				}
			echo "\n";
			echo '<!-- Protected by WP-SpamFree'.$wpSpamFreeVerJS.' :: JS BEGIN -->'."\n";
			echo '<script type="text/javascript" src="'.get_option('siteurl').'/wp-content/plugins/wp-spamfree/js/wpsf-js.php"></script> '."\n";
			echo '<!-- Protected by WP-SpamFree'.$wpSpamFreeVerJS.' :: JS END -->'."\n";
			echo "\n";
			}
			
		function install_on_activation() {
			global $wpdb;
			$plugin_db_version = "1.9.6.6";
			$installed_ver = get_option('wp_spamfree_version');
			$spamfree_options = get_option('spamfree_options');
			//only run installation if not installed or if previous version installed
			if ( ( $installed_ver === false || $installed_ver != $plugin_db_version ) && !$spamfree_options ) {
			
				//add a database version number for future upgrade purposes
				update_option('wp_spamfree_version', $plugin_db_version);
				
				// Set Random Cookie Name
				$randomComValCodeCVN1 = spamfree_create_random_key();
				$randomComValCodeCVN2 = spamfree_create_random_key();
				$CookieValidationName = strtoupper($randomComValCodeCVN1.$randomComValCodeCVN2);
				// Set Random Cookie Value
				$randomComValCodeCKV1 = spamfree_create_random_key();
				$randomComValCodeCKV2 = spamfree_create_random_key();
				$CookieValidationKey = $randomComValCodeCKV1.$randomComValCodeCKV2;
				// Set Random Form Field Name
				$randomComValCodeJSFFN1 = spamfree_create_random_key();
				$randomComValCodeJSFFN2 = spamfree_create_random_key();
				$FormValidationFieldJS = $randomComValCodeJSFFN1.$randomComValCodeJSFFN2;
				// Set Random Form Field Value
				$randomComValCodeJS1 = spamfree_create_random_key();
				$randomComValCodeJS2 = spamfree_create_random_key();
				$FormValidationKeyJS = $randomComValCodeJS1.$randomComValCodeJS2;

				// Options array
				$spamfree_options_update = array (
					'cookie_validation_name' 				=> $CookieValidationName,
					'cookie_validation_key' 				=> $CookieValidationKey,
					'form_validation_field_js' 				=> $FormValidationFieldJS,
					'form_validation_key_js' 				=> $FormValidationKeyJS,
					'wp_cache' 								=> 0,
					'wp_super_cache' 						=> 0,
					'use_captcha_backup' 					=> 0,
					'block_all_trackbacks' 					=> 0,
					'block_all_pingbacks' 					=> 0,
					'use_trackback_verification'		 	=> 0,
					'form_include_website' 					=> 1,
					'form_require_website' 					=> 0,
					'form_include_phone' 					=> 1,
					'form_require_phone' 					=> 0,
					'form_include_drop_down_menu'			=> 0,
					'form_require_drop_down_menu'			=> 0,
					'form_drop_down_menu_title'				=> '',
					'form_drop_down_menu_item_1'			=> '',
					'form_drop_down_menu_item_2'			=> '',
					'form_drop_down_menu_item_3'			=> '',
					'form_drop_down_menu_item_4'			=> '',
					'form_drop_down_menu_item_5'			=> '',
					'form_drop_down_menu_item_6'			=> '',
					'form_drop_down_menu_item_7'			=> '',
					'form_drop_down_menu_item_8'			=> '',
					'form_drop_down_menu_item_9'			=> '',
					'form_drop_down_menu_item_10'			=> '',
					'form_message_width' 					=> 40,
					'form_message_height' 					=> 10,
					'form_message_min_length'				=> 25,
					'form_message_recipient'				=> get_option('admin_email'),
					);
					
				$spamfree_count = get_option('spamfree_count');
				if (!$spamfree_count) {
					update_option('spamfree_count', 0);
					}
				update_option('spamfree_options', $spamfree_options_update);
				update_option('ak_count_pre', get_option('akismet_spam_count'));
				// Turn on Comment Moderation
				//update_option('comment_moderation', 1);
				//update_option('moderation_notify', 1);

				}
			}
					
		}
	}

//instantiate the class
if (class_exists('wpSpamFree')) {
	$wpSpamFree = new wpSpamFree();
	}

?>