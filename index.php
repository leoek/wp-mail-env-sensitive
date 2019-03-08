<?php
/**
Plugin Name: WP-Mail-Docker-Env
Version: 1.0
Author: Leonard Krause, Matchwerk Solutions GmbH
Description: This Plugin reads the env vars MTA_HOSTNAME, WP_MAIL_FROMNAME and WP_MAIL_FROMNAME and updates the wordpress mail sender accordingly.
*/

define("ENV_MTA_HOSTNAME", 'MTA_HOSTNAME');
define("ENV_WP_MAIL_FROM", 'WP_MAIL_FROMNAME');
define("ENV_WP_MAIL_FROMNAME", 'WP_MAIL_FROMNAME');

function WPMailDockerEnv_from( $originalFrom ) {
    $from = WP_MAIL_FROM ? WP_MAIL_FROM : getenv(ENV_WP_MAIL_FROM);
    if ($from){
        return $from;
    }
    $mtaHostname = MTA_HOSTNAME ? MTA_HOSTNAME : getenv(ENV_MTA_HOSTNAME);
    if ($mtaHostname){
        return "wordpress@".$mtaHostname;
    }
    return $originalFrom;
}
 
function WPMailDockerEnv_fromName( $originalFromName ) {
    $fromName = WP_MAIL_FROMNAME ? WP_MAIL_FROMNAME : getenv(ENV_WP_MAIL_FROMNAME);
    if ($fromName){
        return $fromName;
    }
    return $originalFromName;
}

add_filter( 'wp_mail_from', 'WPMailDockerEnv_from' );
add_filter( 'wp_mail_from_name', 'WPMailDockerEnv_fromName' );

?>