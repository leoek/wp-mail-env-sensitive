<?php
/**
 * Set Mail from ENV
 *
 * @package WPMailFromEnvSensitive\Main
 *
 * @wordpress-plugin
 * Plugin Name: wp-mail-env-sensitive
 * Version:     1.1.0
 * Author:      Leonard Krause, Matchwerk Solutions GmbH
 * Author URI:  https://matchwerk.de
 * Description: This Plugin reads the env vars MTA_HOSTNAME, WP_MAIL_FROMNAME and WP_MAIL_FROMNAME and updates the WordPress mail sender accordingly.
 */

define( 'ENV_MTA_HOSTNAME', 'MTA_HOSTNAME' );
define( 'ENV_WP_MAIL_FROM', 'WP_MAIL_FROM' );
define( 'ENV_WP_MAIL_FROMNAME', 'WP_MAIL_FROMNAME' );

/**
 * Filters the "from" e-mail address
 *
 * @param string $original_from Original from e-mail address.
 * @return string
 */
function wp_mail_docker_env_from( string $original_from ): string {
	$from = defined( ENV_WP_MAIL_FROM )
		? WP_MAIL_FROM
		: getenv( ENV_WP_MAIL_FROM );
	if ( $from ) {
		return $from;
	}

	if ( defined( 'FILTER_VALIDATE_EMAIL' )
		&& filter_var( $original_from, FILTER_VALIDATE_EMAIL )
	) {
		return $original_from;
	}

	$mta_hostname = defined( ENV_MTA_HOSTNAME )
		? MTA_HOSTNAME
		: getenv( ENV_MTA_HOSTNAME );
	if ( $mta_hostname ) {
		return 'wordpress@' . $mta_hostname;
	}

	return 'doNotReply@wordpress';
}

/**
 * Filters the "from" name
 *
 * @param string $original_from_name Original from name.
 * @return string
 */
function wp_mail_docker_env_from_name( string $original_from_name ): string {
	$from_name = defined( ENV_WP_MAIL_FROMNAME )
		? WP_MAIL_FROMNAME
		: getenv( ENV_WP_MAIL_FROMNAME );
	if ( $from_name ) {
		return $from_name;
	}
	return $original_from_name;
}

add_filter( 'wp_mail_from', 'wp_mail_docker_env_from', 99999 );
add_filter( 'wp_mail_from_name', 'wp_mail_docker_env_from_name', 99999 );

/**
 * Log mail errors if they happen!
 */
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	add_action(
		'wp_mail_failed',
		function ( $e ) {
			error_log( print_r( $e, true ) );
		}
	);
}

