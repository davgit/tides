<?php // This code runs whenever in the wp-admin.



/********** MISC **********/



pb_backupbuddy::load();

// Load backupbuddy class with helper functions.
if ( !isset( pb_backupbuddy::$classes['core'] ) ) {
	require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
	pb_backupbuddy::$classes['core'] = new pb_backupbuddy_core();
}

/********** Begin directory checking. **********/
pb_backupbuddy::$classes['core']->verify_directories();
/********** End directory checking. **********/



/********** ACTIONS (admin) **********/



// Set up reminders if enabled.
if ( pb_backupbuddy::$options['backup_reminders'] == '1' ) {
	pb_backupbuddy::add_action( array( 'load-update-core.php', 'wp_update_backup_reminder' ) );
	pb_backupbuddy::add_action( array( 'post_updated_messages', 'content_editor_backup_reminder_on_update' ) );
}

// Display warning to network activate if running in normal mode on a MultiSite Network.
if ( is_multisite() && !pb_backupbuddy::$classes['core']->is_network_activated() ) {
	pb_backupbuddy::add_action( array( 'all_admin_notices', 'multisite_network_warning' ) ); // BB should be network activated while on Multisite.
}



/********** AJAX (admin) **********/


// Backup process.
pb_backupbuddy::add_ajax( 'backup_status' ); // AJAX querying of backup status for manual backups.

// Migrate process.
pb_backupbuddy::add_ajax( 'migration_picker' ); // Remote destination picker.
pb_backupbuddy::add_ajax( 'migrate_status' ); // Magic migration status polling.

// Remote destinations system.
pb_backupbuddy::add_ajax( 'remote_test' ); // Remote destination testing.
pb_backupbuddy::add_ajax( 'remote_save' ); // Remote destination saving.
pb_backupbuddy::add_ajax( 'remote_send' ); // Remote destination picker.
pb_backupbuddy::add_ajax( 'remote_delete' ); // Remote destination deletion.
pb_backupbuddy::add_ajax( 'destination_picker' ); // Remote destination picker.

// Server Info Page.
pb_backupbuddy::add_ajax( 'db_check' ); // Check db integrity of a table.
pb_backupbuddy::add_ajax( 'db_repair' ); // Repair db integrity of a table.
pb_backupbuddy::add_ajax( 'refresh_zip_methods' ); // Server info page available zip methods update.
pb_backupbuddy::add_ajax( 'refresh_site_size' ); // Server info page site size update.
pb_backupbuddy::add_ajax( 'refresh_site_size_excluded' ); // Server info page site size (sans exclusions) update.
pb_backupbuddy::add_ajax( 'refresh_database_size' ); // Server info page database size update.
pb_backupbuddy::add_ajax( 'refresh_database_size_excluded' ); // Server info page site size (sans exclusions) update.
pb_backupbuddy::add_ajax( 'phpinfo' ); // Server info page extended PHPinfo thickbox.
pb_backupbuddy::add_ajax( 'icicle' ); // Server info page icicle for GUI file listing.
pb_backupbuddy::add_ajax( 'php_max_runtime_test' ); // Tests ACTUAL PHP maximum runtime.

// MISC.
pb_backupbuddy::add_ajax( 'exclude_tree' ); // Directory exclusions picker for settings page.
pb_backupbuddy::add_ajax( 'download_archive' ); // Directory exclusions picker for settings page.
pb_backupbuddy::add_ajax( 'set_backup_note' ); // Used for setting a note on a backup archive in the backup listing.
pb_backupbuddy::add_ajax( 'integrity_status' ); // Display backup integrity status.
pb_backupbuddy::add_ajax( 'backup_step_status' ); // Display backup step status in thickbox for recent backups section.
pb_backupbuddy::add_ajax( 'view_status_log' ); // Display status log in thickbox for recent backups section.
pb_backupbuddy::add_ajax( 'importbuddy' ); // ImportBuddy download link.
pb_backupbuddy::add_ajax( 'repairbuddy' ); // RepairBuddy download link.
pb_backupbuddy::add_ajax( 'hash' ); // Obtain MD5 hash of a backup file.
pb_backupbuddy::add_ajax( 'ajax_controller_callback_function' ); // Tell WordPress about this AJAX callback.
pb_backupbuddy::add_ajax( 'disalert' ); // Dismissable alert saving. Currently framework does NOT auto-load this AJAX ability to save disalerts.
pb_backupbuddy::add_ajax( 'importexport_settings' ); // Popup thickbox for importing and exporting settings.


/********** DASHBOARD (admin) **********/



// Display stats in Dashboard.
//if ( pb_backupbuddy::$options['dashboard_stats'] == '1' ) {
	if ( ( !is_multisite() ) || ( is_multisite() && is_network_admin() ) ) { // Only show if standalon OR in main network admin.
		pb_backupbuddy::add_dashboard_widget( 'stats', 'BackupBuddy', 'godmode' );
	}
//}

/********** FILTERS (admin) **********/
pb_backupbuddy::add_filter( 'plugin_row_meta', 10, 2 );


/********** PAGES (admin) **********/

if ( is_multisite() && pb_backupbuddy::$classes['core']->is_network_activated() && !defined( 'PB_DEMO_MODE' ) ) { // Multisite installation.
	if ( is_network_admin() ) { // Network Admin pages
		pb_backupbuddy::add_page( '', 'getting_started', array( pb_backupbuddy::settings( 'name' ), 'Getting Started' ) );
		pb_backupbuddy::add_page( 'getting_started', 'backup', __( 'Backup', 'it-l10n-backupbuddy' ), 'manage_network' );
		pb_backupbuddy::add_page( 'getting_started', 'migrate_restore', __( 'Migrate, Restore', 'it-l10n-backupbuddy' ), 'manage_network' );
		pb_backupbuddy::add_page( 'getting_started', 'destinations', __( 'Remote Destinations', 'it-l10n-backupbuddy' ), 'administrator' );
		pb_backupbuddy::add_page( 'getting_started', 'multisite_import', __( 'MS Import (beta)', 'it-l10n-backupbuddy' ), 'manage_network' );
		pb_backupbuddy::add_page( 'getting_started', 'server_info', __( 'Server Information', 'it-l10n-backupbuddy' ), 'manage_network' );
		pb_backupbuddy::add_page( 'getting_started', 'malware_scan', __( 'Malware Scan', 'it-l10n-backupbuddy' ), 'manage_network' );
		//pb_backupbuddy::add_page( 'getting_started', 'server_tools', __( 'Server Tools', 'it-l10n-backupbuddy' ), 'manage_network' );
		pb_backupbuddy::add_page( 'getting_started', 'scheduling', __( 'Scheduling', 'it-l10n-backupbuddy' ), 'manage_network' );
		pb_backupbuddy::add_page( 'getting_started', 'settings', __( 'Settings', 'it-l10n-backupbuddy' ), 'manage_network' );
	} else { // Subsite pages.
		// TODO: Make the following work so the network admin ALWAYS can export even if admin exports are not enabled. Problem: current_user_can() is not available this early. Not sure best fix yet.
		//if ( current_user_can( 'manage_network' ) || ( ( current_user_can( 'activate_plugins' ) ) && ( pb_backupbuddy::$options[ 'multisite_export' ] == '1' ) ) ) { // Add export menus if: is network admin _OR_ ( is an admin AND exporting is enabled ).
		
		$export_note = '';
		
		$options = get_site_option( 'pb_' . pb_backupbuddy::settings( 'slug' ) );
		$multisite_export = $options[ 'multisite_export' ];
		unset( $options );

		if ( $multisite_export == '1' ) { // Settings enable admins to export. Set capability to admin and higher only.
			$capability = 'administrator';
			$export_title = '<span title="Note: Enabled for both subsite Admins and Network Superadmins based on BackupBuddy settings">' . __( 'MS Export (beta)', 'it-l10n-backupbuddy' ) . '</span>';
		} else { // Settings do NOT allow admins to export; set capability for superadmins only.
			$capability = 'manage_network';
			$export_title = '<span title="Note: Enabled for Network Superadmins only based on BackupBuddy settings">' . __( 'MS Export SA (beta)', 'it-l10n-backupbuddy' ) . '</span>';
		}
				
		//pb_backupbuddy::add_page( '', 'getting_started', array( pb_backupbuddy::settings( 'name' ), 'Getting Started' . $export_note ), $capability );
		pb_backupbuddy::add_page( '', 'multisite_export', $export_title, $capability );
		pb_backupbuddy::add_page( 'multisite_export', 'malware_scan', __( 'Malware Scan', 'it-l10n-backupbuddy' ), $capability );
	}
} else { // Standalone site.
	pb_backupbuddy::add_page( '', 'getting_started', array( pb_backupbuddy::settings( 'name' ), 'Getting Started' ) );
	pb_backupbuddy::add_page( 'getting_started', 'backup', __( 'Backup', 'it-l10n-backupbuddy' ), 'administrator' );
	pb_backupbuddy::add_page( 'getting_started', 'migrate_restore', __( 'Migrate, Restore', 'it-l10n-backupbuddy' ), 'administrator' );
	pb_backupbuddy::add_page( 'getting_started', 'destinations', __( 'Remote Destinations', 'it-l10n-backupbuddy' ), 'administrator' );
	pb_backupbuddy::add_page( 'getting_started', 'server_info', __( 'Server Information', 'it-l10n-backupbuddy' ), 'administrator' );
	pb_backupbuddy::add_page( 'getting_started', 'malware_scan', __( 'Malware Scan', 'it-l10n-backupbuddy' ), 'administrator' );
	//pb_backupbuddy::add_page( 'getting_started', 'server_tools', __( 'Server Tools', 'it-l10n-backupbuddy' ), 'administrator' );
	pb_backupbuddy::add_page( 'getting_started', 'scheduling', __( 'Scheduling', 'it-l10n-backupbuddy' ), 'administrator' );
	pb_backupbuddy::add_page( 'getting_started', 'settings', __( 'Settings', 'it-l10n-backupbuddy' ), 'administrator' );
}



/********** LIBRARIES & CLASSES (admin) **********/



/********** OTHER (admin) **********/



?>