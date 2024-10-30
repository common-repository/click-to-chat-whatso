<?php
$plugin_data = get_file_data( WHATSO_PLUGIN_BOOTSTRAP_FILE, array( 'version' ) );

$url = "https://www.whatso.net/svc.asmx/CheckForWordPressPluginVersion?yourVersion=" . $plugin_data[0];
$response = wp_remote_get($url);
if(is_array($response) && !get_option('whatso_version_detail') && isset($response['body'])){
    add_option( 'whatso_version_detail', $response['body'] , '', 'yes' );
}
if(get_option('whatso_version_detail')){
    update_option( 'whatso_version_detail', $response['body'] , true );
}

WHATSO_Utils::setView( 'floating_menu_setup' );