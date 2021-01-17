<?php
namespace DM_ZCRM\SDK;

use com\zoho\api\authenticator\OAuthToken;
use com\zoho\api\authenticator\TokenType;
use com\zoho\api\authenticator\store\DBStore;
use com\zoho\api\authenticator\store\FileStore;
use com\zoho\crm\api\Initializer;
use com\zoho\crm\api\UserSignature;
use com\zoho\crm\api\dc\USDataCenter;
use com\zoho\api\logger\Logger;
use com\zoho\api\logger\Levels;
use com\zoho\crm\api\SDKConfigBuilder;

class Initialize
{
    public static function initialize()
    {
		$configuration = static::configuration();

        /*
		 * Create an instance of Logger Class that takes two parameters
		 * 1 -> Level of the log messages to be logged. Can be configured by typing Levels "." and choose any level from the list displayed.
		 * 2 -> Absolute file path, where messages need to be logged.
		 */
        $logger = Logger::getInstance(Levels::INFO, $configuration["token_persistence_path"] . "/php_sdk_log.log");

        //Create an UserSignature instance that takes user Email as parameter
		$user = new UserSignature("support@gofleet.ca");
		
        /*
		 * Configure the environment
		 * which is of the pattern Domain.Environment
		 * Available Domains: USDataCenter, EUDataCenter, INDataCenter, CNDataCenter, AUDataCenter
		 * Available Environments: PRODUCTION, DEVELOPER, SANDBOX
		 */
		$environment = USDataCenter::PRODUCTION();
		

        /*
            * Create a Token instance
            * 1 -> OAuth client id.
            * 2 -> OAuth client secret.
            * 3 -> OAuth redirect URL.
            * 4 -> REFRESH/GRANT token.
            * 5 -> Token type(REFRESH/GRANT).
		*/
		
		

		// $token = new OAuthToken("clientId", "clientSecret", "REFRESH/GRANT token", TokenType::REFRESH/GRANT, "redirectURL");
		$token = new OAuthToken($configuration["client_id"], $configuration["client_secret"], $configuration["refresh_token"], 'refresh', $configuration["redirect_uri"]);
       
        /*
        * Create an instance of DBStore.
        * 1 -> DataBase host name. Default value "localhost"
        * 2 -> DataBase name. Default  value "zohooauth"
        * 3 -> DataBase user name. Default value "root"
        * 4 -> DataBase password. Default value ""
        * 5 -> DataBase port number. Default value "3306"
        */
        //$tokenstore = new DBStore();
        // $tokenstore = new  DBStore("hostName", "dataBaseName", "userName", "password", "portNumber");
        $tokenstore = new FileStore($configuration["token_persistence_path"] . '/dm-zoho-crm-token.txt');
        
		$autoRefreshFields = false;

		$pickListValidation = false;
		
        // Create an instance of SDKConfig
		$builderInstance = new SDKConfigBuilder();

		$sdkConfig = $builderInstance->setPickListValidation($pickListValidation)->setAutoRefreshFields($autoRefreshFields)->build();

        $resourcePath = $configuration["token_persistence_path"] . '/resource-path';

       /*
		* Call static initialize method of Initializer class that takes the arguments
		* 1 -> UserSignature instance
		* 2 -> Environment instance
		* 3 -> Token instance
		* 4 -> TokenStore instance
		* 5 -> autoRefreshFields 
		* 6 -> The path containing the absolute directory path to store user specific JSON files containing module fields information.
		* 7 -> Logger instance
		*/
		Initializer::initialize($user, $environment, $token, $tokenstore, $sdkConfig, $resourcePath, $logger);
		
		// $tokenstore->deleteTokens();
	}
	
	public static function configuration() {
		$configuration = null;

		// If in Wordpress, read the constant.
        if ( defined( 'DM_ZCRM_CONFIGURATION' ) ) {
            return DM_ZCRM_CONFIGURATION;
        }
		
		// If in Laravel, read the config file
		if ( function_exists('config') && ! empty( config('constants.zcrm_configuration') ) ) {
            return config('constants.zcrm_configuration');
        }
		
		if (empty($configuration)) {
			echo 'ZCRM Configuration coultr not be found.';
			return;
		}
	}
}
?>