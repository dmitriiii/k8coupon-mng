<?php
/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    K8coupon_Mng
 * @subpackage K8coupon_Mng/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    K8coupon_Mng
 * @subpackage K8coupon_Mng/includes
 * @author     Your Name <email@example.com>
 */
class K8coupon_Mng_Activator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		#Creating tables with coupons If NOT EXISTS
		global $wpdb;
		$table_name = $wpdb->prefix.'k8_coupon';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		     //table not in database. Create new table
		    $charset_collate = $wpdb->get_charset_collate();
		    $sql = "CREATE TABLE $table_name (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`code` VARCHAR(255) NOT NULL,
					`client_id` INT(11),
					`is_taken` INT(2) DEFAULT '0',
					`reg_date` DATETIME NULL,
					UNIQUE(`code`),
					PRIMARY KEY (`id`)
				) $charset_collate;";
	     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	     dbDelta( $sql );
		}
		$table_name = $wpdb->prefix.'k8_client';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		     //table not in database. Create new table
		    $charset_collate = $wpdb->get_charset_collate();
		    $sql = "CREATE TABLE $table_name (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`phone` VARCHAR(255) NOT NULL,
					UNIQUE (`id`),
					PRIMARY KEY (`id`)
				) $charset_collate;";
	     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	     dbDelta( $sql );
		}
	}
}