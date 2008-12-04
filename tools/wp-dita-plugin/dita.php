<?php
/*
Plugin Name: DITA
Plugin URI: http://www.dita-op.org
Description: Embed default DITA CSS into Wordpress templates
Version: 1.0
Author: Claude Vedovini
Author URI: http://www.dita-op.org

Copyright (C) 2008 Claude Vedovini <http://vedovini.net/>.

This software is part of the DITA Open Platform <http://www.dita-op.org/>.

The DITA Open Platform is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

The DITA Open Platform is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with The DITA Open Platform.  If not, see <http://www.gnu.org/licenses/>.
*/

function dita_head() {
	$csspath = get_option('siteurl') . '/' . PLUGINDIR . '/dita/dita.css';
	echo "<link rel='stylesheet' href='$csspath' type='text/css' media='screen, projection, print' />";
}

add_action('wp_head', 'dita_head');
