<?php 
/*
	Plugin Name: Facebook Profile Theme
	Plugin URI: http://vedovini.net/plugins
	Description: This plugin enables you to add your blog to your Facebook profile.
	Author: Claude Vedovini
	Author URI: http://vedovini.net/
	Version: 1.0.4
   
	# Thanks to Malan Joubert for its Facebook theme that inspired the theme
	# included in this plugin (http://www.foxinni.com/) and thanks to the
	# developers of the WPtouch plugin who showed me how to do it (http://bravenewcode.com/wptouch)
	
	# The code in this plugin is free software; you can redistribute the code aspects of
	# the plugin and/or modify the code under the terms of the GNU Lesser General
	# Public License as published by the Free Software Foundation; either
	# version 3 of the License, or (at your option) any later version.
	
	# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	# EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	# MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	# NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
	# LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
	# OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
	# WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. 
	#
	# See the GNU lesser General Public License for more details.
*/
define('FBPROFILE_PLUGIN_NAME', basename(dirname(__FILE__)));


class FBProfilePlugin {
		
	function FBProfilePlugin() {
		add_filter( 'stylesheet', array(&$this, 'get_stylesheet') );
		add_filter( 'theme_root', array(&$this, 'theme_root') );
		add_filter( 'theme_root_uri', array(&$this, 'theme_root_uri') );
		add_filter( 'template', array(&$this, 'get_template') );
	}

	function from_facebook() {
		static $is_facebook;
		
		if (!isset($is_facebook)) {
			$is_facebook = isset($_POST['fb_sig']);
			
			if ($is_facebook) {
				$in_profile_tab = (isset($_POST['fb_sig_in_profile_tab']) && $_POST['fb_sig_in_profile_tab'] == 1);
				$is_added = (isset($_POST['fb_sig_added']) && $_POST['fb_sig_added'] == 1);

				if (!$in_profile_tab && !$is_added) {
			        $app_id = $_POST['fb_sig_app_id'];
			        $install_url = "http://www.facebook.com/install.php?api_key=$app_id&v=1.0";
			        echo "<fb:redirect url='$install_url' />";
			        die();
				}
			} 
		}
		
		return $is_facebook;
	}
	
	function get_stylesheet($stylesheet) {
		if ($this->from_facebook()) {
			return 'default';
		} else {
			return $stylesheet;
		}
	}
		  
	function get_template($template) {
		if ($this->from_facebook()) {
			return 'default';
		} else {	   
			return $template;
		}
	}
		  
	function get_template_directory($value) {
		if ($this->from_facebook()) {
			return WP_PLUGIN_DIR . '/' . FBPROFILE_PLUGIN_NAME . '/themes';
		} else {
			return $value;
		}
	}
		  
	function theme_root($path) {
		if ($this->from_facebook()) {
			return WP_PLUGIN_DIR . '/' . FBPROFILE_PLUGIN_NAME . '/themes';
		} else {
			return $path;
		}
	}
		  
	function theme_root_uri($url) {
		if ($this->from_facebook()) {
			return WP_PLUGIN_URL . '/' . FBPROFILE_PLUGIN_NAME . '/themes';
		} else {
			return $url;
		}
	}
}
  
global $fbprofile_plugin;
$fbprofile_plugin = new FBProfilePlugin();
