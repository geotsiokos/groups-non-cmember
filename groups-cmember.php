<?php
/**
 * groups-cmember.php
 *
 * Copyright (c) 2015 www.itthinx.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @since 1.0.0
 *
 * Plugin Name: Groups CMember
 * Plugin URI: https://github.com/itthinx/groups-cmember
 * Description: Provides the [groups_cmember] shortcode. Usage: [groups_cmember group="Blue,Red"]Content to show only if user belongs to groups Blue and Red.[/groups_cmember]
 * Version: 1.0.0
 * Author: itthinx
 * Author URI: http://www.itthinx.com
 * Donate-Link: http://www.itthinx.com
 * License: GPLv3
 */

/**
 * Provides a conjunctive conditional shortcode.
 */
class Groups_CMember {

	/**
	 * Registers the [groups_cmember] shortcode handler.
	 */
	public static function init() {
		add_shortcode( 'groups_cmember', array( __CLASS__, 'groups_cmember_shortcode' ) );
	}

	/**
	 * Handles the [groups_cmember] shortcode.
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function groups_cmember_shortcode( $atts, $content = null ) {
		$output ='';
		if ( class_exists( 'Groups_Group' ) && method_exists( 'Groups_Group', 'read_by_name' ) ) {
			$options = shortcode_atts( array( "group" => "" ), $atts );
			$show_content = true;
			if ( $content !== null ) {
				$groups_user = new Groups_User( get_current_user_id() );
				$groups = explode( ",", $options['group'] );
				foreach ( $groups as $group ) {
					$group = trim( $group );
					$current_group = Groups_Group::read( $group );
					if ( !$current_group ) {
						$current_group = Groups_Group::read_by_name( $group );
					}
					if ( $current_group ) {
						if ( !Groups_User_Group::read( $groups_user->user->ID , $current_group->group_id ) ) {
							$show_content = false;
							break;
						}
					}
				}
				if ( $show_content ) {
					remove_shortcode( 'groups_cmember' );
					$content = do_shortcode( $content );
					add_shortcode( 'groups_cmember', array( __CLASS__, 'groups_cmember' ) );
					$output = $content;
				}
			}
		}
		return $output;
	}
}
Groups_CMember::init();
