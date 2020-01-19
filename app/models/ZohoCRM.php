<?php
namespace DM_ZCRM\Models;

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;

class ZohoCRM { // Not an abstract because we instantiate it at one point.

    static $module; // NOTE: Plural. Ex: Accounts / Contacts / Potentials / Quotes / Leads/ Must be defined in child, before the __construct runs.
    static $module_instance;

    static $user_instance; // Not sure where it is used.

    /**
     * Initializes the Zoho CRM PHP SDK
     */
    // Make sure this constant is defined in the plugin.
    // define('DM_ZCRM_CONFIGURATION', [ 
    //     "client_id"              => 'xxxxxxxxxxxxxxxxxxxxxx',
    //     "client_secret"          => 'xxxxxxxxxxxxxxxxxxxxxx',
    //     "redirect_uri"           => 'xxxxxxxxxxxxxxxxxxxxxx',
    //     "currentUserEmail"       => 'xxxxxxxxxxxxxxxxxxxxxx',
    //     "token_persistence_path" => DM_ROOT_CONSTANT,
    // ]);
    public function __construct() {
        if ( empty( DM_ZCRM_CONFIGURATION ) ) {
            echo 'DM_ZCRM_CONFIGURATION is not defined';
            return;
        }
        $configuration = DM_ZCRM_CONFIGURATION;

        ZCRMRestClient::initialize($configuration);

    }



}
