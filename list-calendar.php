<?php
/*
Plugin Name: List Calendar
Version: 0.0.3
Description: Calendar is showed by using shortcorde. Calendar style is simple list.
Author: Hiroshi Sawai
Author URI: http://www.info-town.jp
Plugin URI: http://www.creationlabs.net/list-calendar
Text Domain: ltcr
Domain Path: /languages
*/
/*  Copyright 2015  Hiroshi Sawai (email : info@info-town.jp)

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

/*
 * List Calendarの定数
 * 定数のプレフィックスはLCRです。
 */
$ltcr_plugin_data = get_file_data( __FILE__, array( 'version' => 'Version' ) );
if ( ! defined( 'LTCR_VERSION' ) ) {
	define( 'LTCR_VERSION', $ltcr_plugin_data['version'] );
}
if ( ! defined( 'LTCR_PLUGIN_BASENAME' ) ) {
	define( 'LTCR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'LTCR_PLUGIN_NAME' ) ) {
	define( 'LTCR_PLUGIN_NAME', trim( dirname( LTCR_PLUGIN_BASENAME ), '/' ) );
}
if ( ! defined( 'LTCR_PLUGIN_DIR' ) ) {
	define( 'LTCR_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
}
if ( ! defined( 'LTCR_PLUGIN_URL' ) ) {
	define( 'LTCR_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
}
/* If you or your client hate to see about donation, set this value false. */
if ( ! defined( 'LTCR_SHOW_DONATION_LINK' ) ) {
	define( 'LTCR_SHOW_DONATION_LINK', true );
}
if ( ! defined( 'LTCR_OPTION_NAME' ) ) {
	define( 'LTCR_OPTION_NAME', 'ltcr' );
}
if ( ! defined( 'LTCR_POST_TYPE' ) ) {
	define( 'LTCR_POST_TYPE', 'ltcr' );
}
if ( ! defined( 'LTCR_POST_TYPES' ) ) {
	define( 'LTCR_POST_TYPES', 'ltcrs' );
}
if ( ! defined( 'LTCR_TEXT_DOMAIN' ) ) {
	define( 'LTCR_TEXT_DOMAIN', 'ltcr' );
}

require_once LTCR_PLUGIN_DIR . '/class-setup.php';

$setup = new LTCR_Setup();
$setup->on();
