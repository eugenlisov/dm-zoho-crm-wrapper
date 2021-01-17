<?php
/**
 * The following snippets uses `PLUGIN` to prefix
 * the constants and class names. You should replace
 * it with something that matches your plugin name.
 */
// define test environment
define( 'PLUGIN_PHPUNIT', true );

if ( ! defined('DM_ZCRM_CONFIGURATION')) {
	
	define('DM_ZCRM_CONFIGURATION', [ 
		"client_id"              => '123123123',
		"client_secret"          => '123123123',
		"refresh_token"			 => "123123123",
		"redirect_uri"           => 'https://projects.devmaverick.com/rma-callback',
		"currentUserEmail"       => 'john@doe.com',
		"token_persistence_path" => 'aaaaa',
	]);

} 


require_once __DIR__ . '/../vendor/autoload.php';
