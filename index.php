<?php
/**
Plugin Name: wp-mail-env-sensitive
Version: 1.0
Author: Leonard Krause, Matchwerk Solutions GmbH
Description: This Plugin reads the env vars MTA_HOSTNAME, WP_MAIL_FROMNAME and WP_MAIL_FROMNAME and updates the wordpress mail sender accordingly.
*/

define("ENV_MTA_HOSTNAME", 'MTA_HOSTNAME');
define("ENV_WP_MAIL_FROM", 'WP_MAIL_FROM');
define("ENV_WP_MAIL_FROMNAME", 'WP_MAIL_FROMNAME');

function WPMailDockerEnv_from( $originalFrom ) {
    $from = defined(ENV_WP_MAIL_FROM) ? WP_MAIL_FROM : getenv(ENV_WP_MAIL_FROM);
    if ($from){
        return $from;
    }
    $mtaHostname = defined(ENV_MTA_HOSTNAME) ? MTA_HOSTNAME : getenv(ENV_MTA_HOSTNAME);
    if ($mtaHostname){
        return "wordpress@".$mtaHostname;
    }
    return $originalFrom;
}
 
function WPMailDockerEnv_fromName( $originalFromName ) {
    $fromName = defined(ENV_WP_MAIL_FROMNAME) ? WP_MAIL_FROMNAME : getenv(ENV_WP_MAIL_FROMNAME);
    if ($fromName){
        return $fromName;
    }
    return $originalFromName;
}

add_filter( 'wp_mail_from', 'WPMailDockerEnv_from' );
add_filter( 'wp_mail_from_name', 'WPMailDockerEnv_fromName' );

/**
 * Log mail errors if they happen!
 */
add_action('wp_mail_failed', function($e) {
    error_log( print_r($e, true) );
});

?>