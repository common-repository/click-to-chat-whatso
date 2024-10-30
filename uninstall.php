<?php

// if uninstall.php is not called by WordPress, die
if( !defined('WP_UNINSTALL_PLUGIN') ) {
    die;
}

// remove plugin options
global $wpdb;

if( !function_exists('is_plugin_active_for_network') ) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/*
 * Making WPDB as global
 * to access database information.
 */
global $wpdb;

/*
 * @var $table_name
 * name of table to be dropped
 * prefixed with $wpdb->prefix from the database
 */
$table_name = $wpdb->prefix . 'whatso_abandoned_cart';
$table_name1= $wpdb->prefix . 'whatso_order_notification';
$table_name3= $wpdb->prefix . 'message_notification_details';

// drop the table from the database.
$wpdb->get_results( $wpdb->prepare( "DROP TABLE IF EXISTS $table_name" ));
$wpdb->get_results( $wpdb->prepare( "DROP TABLE IF EXISTS $table_name1" ));
$wpdb->get_results( $wpdb->prepare( "DROP TABLE IF EXISTS $table_name3" ));
delete_option( 'whatso_user_settings' );
delete_option( 'whatso_version_detail' );
delete_option( 'whatso_notifications' );
delete_option( 'whatso_user_plan' );
delete_option( 'whatso_abandoned' );
delete_option( 'whatso_settings' );
delete_option( 'whatso_cf7' );
delete_option( 'whatso_email_login' );
delete_option( 'whatso_save_contact' );
delete_option( 'whatso_dnd_data' );


// Delete posts + data.

$wpdb->get_results( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type IN ('whatso_accounts');" ));
$wpdb->get_results( $wpdb->prepare( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" ));

wp_cache_flush();